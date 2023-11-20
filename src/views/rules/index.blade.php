@extends('password-manager::layout')

@section('content') 
<header class="header mt-3">
    <p class="h4"><strong>{{ $page ?? '' }}</strong></p>
    <div class="pull-right" style="margin-top: 0px">
        <a href="{{  route('password-manager.rules.create')  }}" class="btn btn-sm btn-primary" data-modal="ajaxifyModal">
            <i class="fa fa-edit"></i> Update Rules
        </a>
    </div>
</header>

<section class="panel panel-default">
    <table class="table table-striped" id="clients-table">
        <thead>
            <tr>
                <th>SL</th>
                <th>Attribute</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @if($result->count())
                @foreach($result as $item)
                    <tr>
                        <td style="width: 5%">{{ $loop->iteration }}</td> 
                        <td style="width: 15%">{{ $item->attribute }}</td>
                        <td style="width: 10%">
                            @if($item->value=='true')
                                <span class="badge bg-success">{{ $item->value }}</span>
                            @elseif($item->value=='false')
                                <span class=" badge bg-danger">{{ $item->value }}</span>
                            @elseif ($item->attribute == 'password_strength')
                                @php 
                                    $obj = json_decode($item->value) ?? '';
                                @endphp
                                <span class="badge bg-primary">{{ $obj->type ?? '' }} ({{ $obj->min_length ?? '' }})</span>&nbsp;
                            @else
                                {{ $item->value }}
                            @endif
                        </td> 
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    @if($result->count())
        <nav aria-label='page navigation' class='pagination-sm my-3'>
            <small class='text-muted'>
                <b>
                    Showing {!! $result->appends(request()->all())->firstItem() !!} to {!! $result->appends(request()->all())->lastItem() !!} of {!! $result->appends(request()->all())->total() !!} entries
                <b>
            </small>
            <div class='mt-2'>
                {!! $result->appends(request()->all())->links() !!}
            </div>
        </nav>
    @endif
</section>
@endsection