@extends('admin.layouts.master')
@push('links')

@endpush




@section('main')



<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{Str::title(str_replace('-', ' ', request()->segment(2)))}}</h4>
            @can('add_admin')
            <div class="page-title-right">
                <a href="{{ route('admin.'.request()->segment(2).'.create') }}"  class="btn-sm btn btn-primary btn-label rounded-pill">
                    <i class="bx bx-plus label-icon align-middle rounded-pill fs-16 me-2"></i>
                    Add {{Str::title(str_replace('-', ' ', request()->segment(2)))}}
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
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Mobile No.</th>
                                @can(['edit_company','delete_company', 'read_company'])
                                  <th>Action</th>
                                @endcan
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
$(document).ready(function(){
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
        { "data": "email" },
        {"data":"mobile"},
        // { "data": "status" },
        {
            "data": "action",
            render: function(data, type, row) {
                if (type === 'display') {
                    var btn = '<div class="dropdown d-inline-block"><button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">';

                    @can(['edit_company','delete_company', 'read_company'])

                    @can('edit_company')
                    btn += '<li><a class="dropdown-item" href="{{ request()->url() }}/' + row['id'] + '"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>';
                    @endcan

                    @can('edit_company')
                        btn+='<li><a class="dropdown-item edit-item-btn" href="'+window.location.href+'/'+row['id']+'/edit"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>';
                    @endcan

                    @can('delete_company')
                        btn += '<li><button type="button" onclick="deleteAjax(\''+window.location.href+'/'+row['id']+'/delete\')" class="dropdown-item remove-item-btn"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</button></li>';
                    @endcan

                    @endcan
                     btn += '</ul></div>';
                    return btn;
                }
                return ' ';
            },
    }]

});

});
    </script>

@endpush