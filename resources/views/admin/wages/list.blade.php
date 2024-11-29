@extends('admin.layouts.master')
@push('links')

@endpush

@section('main')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ Str::title(str_replace('-', ' ', request()->segment(2))) }}</h4>
            @can('add_admin')
            <div class="page-title-right">
                <a href="{{ route('admin.'.request()->segment(2).'.create') }}" class="btn-sm btn btn-primary btn-label rounded-pill">
                    <i class="bx bx-plus label-icon align-middle rounded-pill fs-16 me-2"></i>
                    Add/Update {{ Str::title(str_replace('-', ' ', request()->segment(2))) }}
                </a>
            </div>
            @endcan
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable" class="datatable table table-bordered nowrap align-middle" style="width:100%">
                    <thead class="gridjs-thead">
                        <tr>
                            <th style="width:12px">Si</th>
                            <th>Skill Level</th>
                            <th>Wages</th>
                            <th>Status</th>
                            <!-- Removed the Action column -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->

@endsection

@push('scripts')

<script type="text/javascript">
$(document).ready(function() {
    var table2 = $('#datatable').DataTable({
        "processing": true,
        "serverSide": true,
        'ajax': {
            'url': '{{ route('admin.'.request()->segment(2).'.index') }}',
            'data': function(d) {
                d._token = '{{ csrf_token() }}';
                d._method = 'PATCH';
            }
        },
        "columns": [
            { "data": "sn" },
            { "data": "name" },
            { "data": "amount" },
            { "data": "status" },
            // Removed the Action column from here as well
        ]
    });
    console.log(table2);
});
</script>

@endpush
