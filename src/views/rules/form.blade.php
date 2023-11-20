@extends('password-manager::_common.modal', ['data' => $data ?? ''])

@section('form') 
@foreach($data->attributes as $attr => $value)
<div class="row">
    <div class="form-group col-sm-6">
        <label class="control-label" for="{{ $attr }}">Attribute @required</label>
        {{Form::text(null, $attr, [ 'readonly', 'class' => 'form-control form-control-sm', 'placeholder'=>ucfirst($attr)])}}
    </div>

    @if (is_object($value) || is_array($value))
        @if (array_values($value) == ['true', 'false'])
            <div class="form-group col-sm-6">
                <label class="control-label" for="value{{ $attr }}">Value @required</label>
                {{Form::select("rules[".$attr."]", array_combine($value, $value), null, ['id' =>$attr ,'class' => 'form-control form-control-sm select2-option','placeholder' => ' Select Value'])}}
            </div>
        @else
            <div class="form-group col-sm-3">
                <label class="control-label" for="value{{ $attr }}1">Value @required</label>
                {{Form::select("rules[".$attr."]".'[type]', array_combine(array_keys($value), array_keys($value)), null, ['id' =>$attr.'1' ,'class' => 'form-control form-control-sm select2-option','placeholder' => ' Select Value'])}}
            </div>
            <div class="form-group col-sm-3">
                <label class="control-label" for="value{{ $attr }}2">Min Length @required</label>
                {{Form::number("rules[".$attr."]".'[min_length]', 4, [ 'id' =>$attr.'2', 'class' => 'form-control form-control-sm'])}}
            </div>
        @endif
    @else
        <div class="form-group col-sm-6">
            <label class="control-label" for="value{{ $attr }}">Value @required</label>
            {{Form::number("rules[".$attr."]", $value, [ 'id' =>$attr, 'class' => 'form-control form-control-sm', 'placeholder'=>ucfirst($attr)])}}
        </div>
    @endif
</div>
@endforeach
@endsection
