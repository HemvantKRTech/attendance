@extends('admin.layouts.master')

@section('main')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $company->name }} - Advance Payment Records</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5>Advance Payment for {{ $company->name }}</h5>
                    <div class="row mb-3 align-items-end">
                        <div class="col">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" id="start_date" class="form-control">
                        </div>
                        <div class="col">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" id="end_date" class="form-control">
                        </div>
                        <div class="col-auto">
                            <button id="filter" class="btn btn-primary mt-4">Filter</button>
                            <button id="reset" class="btn btn-secondary mt-4">Reset</button>
                        </div>
                    </div>
                    
                    <table class="table table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee Name</th>
                                <th>Employee Code</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Interest</th>
                                <th>Total Payable Amount</th>
                                <th>Emi Amount</th>
                                <th>Total Emi's</th>
                                <th>Pending Emi's</th>
                                <th>Payment Type</th>
                                {{-- <th>Remarks</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
   
@endsection
@push('scripts')
<script type="text/javascript">
    jQuery(document).ready(function(){
        var table2 = $('#datatable').DataTable({
         "processing": true,
         "serverSide": true,
        'ajax': {
        'url': '{{ route('admin.'.request()->segment(2).'.all', $company->id) }}',

        'data': function(d) {
            d._token = '{{ csrf_token() }}';
            d._method = 'PATCH';
            d.start_date = $('#start_date').val();
            d.end_date = $('#end_date').val();
        }
    
        },
        "columns": [
            { "data": "sn" },
            { "data": "name" },
            {"data":"code"},
            { "data": "date_taken" },
            {"data":"amount"},
            {"data":"interest"},
            {"data":"total_payable_amount"},
            {"data":"emi_amount"},
            {"data":"total_emi_count"},
            {"data":"pending_emi_count"},
            { "data": "payment_type" },
            
           
            ]
    
    });
    $('#filter').click(function() {
        table2.ajax.reload(); // Reload the table with the new date filter
    });

    // Reset button click event
    $('#reset').click(function() {
        $('#start_date').val('');
        $('#end_date').val('');
        table2.ajax.reload(); // Reload the table without any filters
    });
    });
        </script>
        @endpush