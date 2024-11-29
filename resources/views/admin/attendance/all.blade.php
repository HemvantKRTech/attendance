@extends('admin.layouts.master')

@section('main')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $company->name }} - Attendance Records</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5>Attendance for {{ $company->name }}</h5>
                <div class="row mb-3 align-items-end">
                    <div class="col">
                        <label for="month" class="form-label">Month</label>
                        <select id="month" class="form-control">
                            <option value="">Select Month</option>
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="year" class="form-label">Year</label>
                        <select id="year" class="form-control">
                            <option value="">Select Year</option>
                            @for ($year = date('Y'); $year >= date('Y') - 10; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto">
                        <button id="filter" class="btn btn-primary mt-4">Filter</button>
                        <button id="reset" class="btn btn-secondary mt-4">Reset</button>
                        <button id="print" class="btn btn-success mt-4">Print</button> 
                    </div>
                </div>

                <h6 id="month-name" class="mb-4"></h6>

                <div class="table-responsive">
                    <table class="table table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Employee Code</th>
                                <th>Overtime (hrs)</th>
                                @for ($day = 1; $day <= 31; $day++)
                                    <th>{{ $day }}</th>
                                @endfor
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    jQuery(document).ready(function() {
        var table2 = $('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                'url': '{{ route('admin.'.request()->segment(2).'.all', $company->id) }}',
                'data': function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.selected_month = $('#month').val();
                    d.selected_year = $('#year').val();
                },
                'dataSrc': function(json) {
                    if (!json.data || json.data.length === 0) {
                        $('#month-name').text('No records found for the selected month.');
                        return [];
                    }
                    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    $('#month-name').text(monthNames[$('#month').val() - 1] + ' ' + $('#year').val());
                    return json.data.map(record => {
                        const daysArray = record.attendance.map((day, index) => {
                            const status = day.status;
                            const overtime = day.overtime;
                            let statusMarkup = '-';
                            if (status === "P") {
                                statusMarkup = `<span class="text-success" title="Overtime: ${overtime} hrs">P</span>`;
                            } else if (status === "A") {
                                statusMarkup = `<span class="text-danger" title="Overtime: ${overtime} hrs">A</span>`;
                            } else if (status === "H") {
                                statusMarkup = `<span class="text-info" title="Overtime: ${overtime} hrs">H</span>`;
                            }
                            return `<span data-bs-toggle="tooltip" title="Overtime: ${overtime} hrs">${statusMarkup}</span>`;
                        });
                        return {
                            employee_name: record.employee_name,
                            employee_code: record.employee_code,
                            total_overtime: record.total_overtime,
                            ...daysArray.reduce((acc, day, i) => {
                                acc[`day_${i + 1}`] = day;
                                return acc;
                            }, {})
                        };
                    });
                }
            },
            "columns": [
                { "data": "employee_name", "title": "Employee Name" },
                { "data": "employee_code", "title": "Employee Code" },
                { "data": "total_overtime", "title": "Overtime (hrs)" },
                ...Array.from({ length: 31 }, (_, i) => ({
                    "data": `day_${i + 1}`,
                    "title": `${i + 1}`
                }))
            ],
            "ordering": false,
            "scrollX": true,
            "drawCallback": function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        // Print functionality
        $('#print').click(function() {
            var printWindow = window.open('', '_blank');
            var tableHtml = document.getElementById('datatable').outerHTML;

            // Styles for printing
            var styles = `
                <style>
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                    th { background-color: #f2f2f2; }
                    .text-success { color: green; }
                    .text-danger { color: red; }
                    .text-info { color: blue; }
                </style>
            `;

            // Generate HTML for printing
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Attendance Report</title>
                    ${styles}
                </head>
                <body>
                    <h3>{{ $company->name }} - Attendance Report</h3>
                    <h4>${$('#month-name').text()}</h4>
                    ${tableHtml}
                </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });

        $('#filter').click(function() {
            table2.ajax.reload();
        });

        $('#reset').click(function() {
            $('#month').val('');
            $('#year').val('');
            $('#month-name').text('');
            table2.ajax.reload();
        });
    });
</script>
@endpush

