@extends('password-manager::_common.modal', ['data' => $data ?? ''])

@section('form') 
    <div class="form-group col-lg-12 ">
        <label class="control-label" for="user_id">User @required</label>
        {{Form::select('user_id', ($data->users ?? []), !empty($data->item)?$data->item->user_id:old('user_id'),['id' => 'user_id' ,'class' => 'form-control form-control-sm select2-option','placeholder' => ' Select User'])}}
    </div> 
@endsection
