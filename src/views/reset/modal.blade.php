@extends('password-manager::layout')

@section('content')
<div class="modal in" data-easein="flipInX" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="display: block; padding-left: 7px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $data->method ?? '' }} {{ $data->page ?? '' }} </strong> </h4>
            </div>
            {!! Form::model($data->item ?? '', ['url'=> ($data->action ?? ''), 'class' => '  validator']) !!}
            {{ Form::hidden('_method', 'put') }}
            <div class="modal-body">
                <span id="status"></span>
                <div class="tab-content">
                    <div class="row"> 
                        <div class="form-group col-lg-12 ">
                            <label class="control-label" for="user_id">User @required</label>
                            {{Form::text('user_id', ( auth()->user()->name . ' <'. auth()->user()->email .'>'), [ 'readonly','disabled', 'id' => 'user_id','class' => 'form-control form-control-sm'])}}
                        </div> 
                        <div class="form-group col-lg-12 ">
                            <label class="control-label" for="old_password">Old Password @required</label>
                            {{Form::text('old_password', old('old_password'), [ 'id' => 'old_password','class' => 'form-control form-control-sm', 'placeholder'=> 'Enter Old Password'])}}
                            <span class="text-danger">@error('old_password') {{  $message }} @enderror </span>
                        </div>
                        <div class="form-group col-lg-12 ">
                            <label class="control-label" for="password">New Password @required</label>
                            {{Form::text('password', old('password'), [ 'id' => 'password','class' => 'form-control form-control-sm', 'placeholder'=> 'Enter New Password'])}}
                            <span class="text-danger">@error('password') {{  $message }} @enderror </span>
                        </div>
                        <div class="form-group col-lg-12 ">
                            <label class="control-label" for="password_confirmation">Confirm Password @required</label>
                            {{Form::text('password_confirmation', old('password_confirmation'), [ 'id' => 'password_confirmation','class' => 'form-control form-control-sm', 'placeholder'=> 'Confirm Password'])}}
                            <span class="text-danger">@error('password_confirmation') {{  $message }} @enderror </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-{{ (strtolower(($data->method ?? ''))=='create'?'primary':'success') }} btn-rounded"><i class="{{ (strtolower(($data->method ?? ''))=='create'?'fa fa-plus':'fa fa-pencil-square-o') }}"></i> {{ ucfirst(($data->method ?? '')) }}</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<div class="modal-backdrop in"></div>
@endsection
