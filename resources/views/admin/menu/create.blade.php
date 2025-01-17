@extends('admin.layouts.master')
@push('links')
@endpush
@section('main')

<div class="content-header row">

    <div class="content-header-left col-md-6 col-12 mb-2">
      <h5 class="content-header-title mb-0">Create Menu</h5>
    </div>

    <div class="content-header-right col-md-6 col-12">
      <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
        <div class="btn-group" role="group">
            @can('add_menu')
                <a href="{{ route('admin.menu.create') }}" class="btn btn-primary btn-sm">Add Menu</a>
            @endcan
       </div>
    </div>
</div>
</div>

<div class="card">
    <div class="card-content">
        <div class="card-body">
           {{-- {!! Form::open(['route'=>'admin.menu.store']) !!} --}}
           {{ html()->form('POST', route('admin.'.request()->segment(2).'.store'))->open() }}
           <div class="form-group">
            {{html()->label('name', 'Menu Name', ['class'=>'control-label']) }}
        {{html()->text('name', null, ['class'=>'form-control']) }}
        
        <b class="text-danger">{{$errors->first('name')}}</b>
        </div>


        <div class="form-group">
            {{html()->label('icon', 'Icon', ['class'=>'control-label']) }}
            {{html()->text('icon', null, ['class'=>'form-control']) }}
            <b class="text-danger">{{$errors->first('icon')}}</b>
        </div>

        <div class="form-group">
            {{html()->label('status', 'Status', ['class'=>'control-label']) }}
        {{html()->select('status', array(1 => 'Active', '0' => 'Deactive'), null, array('class' => 'form-control','id'=>'menu_status')); }}
        <b class="text-danger">{{$errors->first('status')}}</b>
        </div>                       
    
    <div class="form-group">
        <button style=" margin-right: 14px;padding: 7px;width: 71px;background: #dcd7d7;" class="btn btn-success">Create</button>
    </div>
    {{ html()->form()->close() }}

        </div>
             
    </div>
</div>
@endsection
@push('scripts')


@endpush