@extends('password-manager::_common.modal', ['data' => $data ?? ''])

@section('form') 
    <div class="form-group col-lg-12">
	    <label class="control-label" for="password">Password @required</label>
	    {{Form::text('password', (!empty($data->item)?$data->item->password:old('password')), ['id' => 'password','class' => 'form-control form-control-sm', 'placeholder'=>'Enter Password'])}}
	</div>

    <div class="form-group col-lg-12">
        <label class="control-label" for="status">{{__('Status')}} @required</label>
        {{Form::select('status',array('1' => 'Active', '0' => 'Inactive'), (!empty($data->item)?$data->item->status:old('status')), ['id' => 'status' ,'class' => 'form-control form-control-sm select2-option', 'placeholder' => __('Select Status')])}}
    </div>
@endsection
