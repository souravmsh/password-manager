<div class="modal-dialog {{ $data->modal_size ?? '' }}">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">{{ $data->method ?? '' }} {{ $data->page ?? '' }} </strong> </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"></span>
            </button>
        </div>
        {!! Form::model($data->item ?? '', ['url'=> ($data->action ?? ''), 'class' => ' ajaxifyForm validator']) !!}

        {{ Form::hidden('url', url()->previous()) }}

        @if (!empty($data->method) && $data->method=='Update')
            {{ Form::hidden('_method', 'PUT') }}
            {{ Form::hidden('id') }}
        @endif
        <div class="modal-body">
            @yield('form')
        </div>
        <div class="modal-footer"> 
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>

            <button type="submit" class="btn btn-sm btn-outline-{{ (strtolower(($data->method ?? ''))=='create'?'primary':'success') }} btn-rounded"><i class="{{ (strtolower(($data->method ?? ''))=='create'?'fa fa-plus':'fa fa-pencil-square-o') }}"></i> {{ ucfirst(($data->method ?? '')) }}</button>
        </div>
        {!! Form::close() !!}
    </div>
</div> 
