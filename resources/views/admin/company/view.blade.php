@extends('admin.layouts.master')
@push('links')

@endpush




@section('main')



        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{Str::title(str_replace('-', ' ', request()->segment(2)))}}</h4>
                    @can('add_client')
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
        <!-- end page title -->




        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    
                    <div class="card-body">
                        <table class="table align-middle" style="width:100%">

                            <!-- Company Information -->
                            <tr>
                                <th width="25%">Company Name</th>
                                <td>{{ $admin->name }}</td>
                                <th width="25%">Email</th>
                                <td>{{ $admin->email }}</td>
                            </tr>
                        
                            <tr>
                                <th>Contact No.</th>
                                <td>{{ $admin->mobile }}</td>
                                <th>Owner Name</th>
                                <td>{{ $admin->owner_name }}</td>
                            </tr>
                        
                            <tr>
                                <th>Address</th>
                                <td>{{ $adminDetail->address ?? 'N/A' }}</td>
                                <th>City</th>
                                <td>{{ $city}}</td>
                            </tr>
                            
                            <tr>
                                <th>District</th>
                                <td>{{ $district}}</td>
                                <th>State</th>
                                <td>{{$state }}</td>
                            </tr>
                            
                        
                            <tr>
                                <th>GST No.</th>
                                <td>{{ $adminDetail->gst_no }}</td>
                                <th>PAN No.</th>
                                <td>{{ $adminDetail->pan_no }}</td>
                            </tr>
                        
                            <tr>
                                <th>Aadhar No.</th>
                                <td>{{ $adminDetail->aadhar_no }}</td>
                                <th>Udyam No.</th>
                                <td>{{ $adminDetail->udyam_no }}</td>
                            </tr>
                        
                            <tr>
                                <th>CIN No.</th>
                                <td>{{ $adminDetail->cin_no }}</td>
                                <th>EPF No.</th>
                                <td>{{ $adminDetail->epf_no }}</td>
                            </tr>
                        
                            <tr>
                                <th>ESIC No.</th>
                                <td>{{ $adminDetail->esic_no }}</td>
                                <th>Bank Name</th>
                                <td>{{ $adminDetail->bank_name }}</td>
                            </tr>
                        
                            <tr>
                                <th>Account No.</th>
                                <td>{{ $adminDetail->ac_no }}</td>
                                <th>IFSC Code</th>
                                <td>{{ $adminDetail->ifs_code }}</td>
                            </tr>
                        
                        </table>
                        
                        
                    </div>
                </div>
            </div><!--end col-->
        </div><!--end row-->



@endsection


@push('scripts')


@endpush