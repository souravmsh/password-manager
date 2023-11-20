@extends('password-manager::_common.modal', ['data' => $data ?? ''])

@section('form') 
    <div class="form-group col-lg-12 ">
        <label class="control-label" for="user_id">User @required</label>
        {{Form::select('user_id', ($data->users ?? []), !empty($data->item)?$data->item->user_id:old('user_id'),['id' => 'user_id' ,'class' => 'form-control form-control-sm select2-option','placeholder' => ' Select User'])}}
    </div>

    <div class="form-group col-lg-12 ">
        <label class="control-label" for="expiry_days">Expiry Days @required</label>
        {{Form::number('expiry_days', !empty($data->item)?$data->item->expiry_days:(!empty($data->rules->value)?$data->rules->value:1), [ 'min'=>'1', 'id' => 'expiry_days','class' => 'form-control form-control-sm', 'placeholder'=>'Enter Expiry Days'])}}
    </div>

    <div class="form-group col-lg-12 ">
        <label class="control-label" for="updated_at">Update Date @required</label>
        {{Form::text('updated_at', !empty($data->item)?(\Carbon\Carbon::parse($data->item->updated_at)->format('d/m/Y')):date('d/m/Y'), ['id' => 'updated_at','class' => 'form-control form-control-sm datepicker', 'placeholder'=>'Enter Update Date'])}}
    </div>
@endsection
