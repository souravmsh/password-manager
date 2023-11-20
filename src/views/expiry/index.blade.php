@extends('password-manager::layout')

@section('content') 
<header class="header mt-3">
    <p class="h4"><strong>{{ $page ?? '' }}</strong></p>
    <div class="pull-right" style="margin-top: 0px">
        <a href="{{  route('password-manager.expiry.create')  }}" class="btn btn-sm btn-primary" data-modal="ajaxifyModal">
            <i class="fa fa-plus"></i> Add User Expiration
        </a>
        <a href="{{  route('password-manager.pretend.show')  }}" class="btn btn-sm btn-danger" data-modal="ajaxifyModal">
            <i class="fa fa-users"></i> Pretend Login
        </a>
    </div>
</header>

<section class="panel panel-default">
    <form action="#" method="get">
        <div class="row">
            <div class="col-sm-4">
                <select name="user_id" class="form-control input-sm select2">
                    <option value="">Select User</option>
                    @foreach($users as $id => $value)
                        <option value="{{ $id }}" {{ (request()->user_id??old('user_id')==$id?'selected':'') }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div> 
            <div class="col-sm-4">
                <select name="status" class="form-control input-sm select2">
                    <option value="">Select Status</option>
                    @php $status = array('valid' => 'Valid', 'expired' => 'Expired'); @endphp
                    @foreach($status as $id => $value)
                        <option value="{{ $id }}" {{ (request()->status??old('status')==$id?'selected':'') }}>{{ $value }}</option>
                    @endforeach
                </select> 
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-search"></i>
                </button>
                <a href="{{ route('password-manager.expiry') }}" class="btn btn-sm btn-danger">Clear</a>
            </div>
        </div>
    </form>
</section>

<section class="panel panel-default">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>User</th>
                    <th>Expiry Days</th>
                    <th>Updated Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if($result->count())
                    @foreach($result as $item)
                        <tr>
                            <td style="width: 5%">{{ $loop->iteration }}</td>
                            <td style="width: 15%">
                                {{ $item->user->name ?? '' }} <br/>({{ $item->user->email ?? ''  }})
                            </td>
                            <td style="width: 10%">{{ $item->expiry_days }}</td>
                            <td style="width: 10%">{{ $item->updated_at }}</td> 
                            <td style="width: 10%">  
                                @php
                                    $days = ($item->expiry_days - \Carbon\Carbon::parse($item->updated_at)->diffInDays(\Carbon\Carbon::now()));
                                @endphp  
                                @if (strtotime($item->updated_at) >  strtotime(date('Y-m-d')))
                                    <span class=" badge bg-success">Valid</span>
                                        will expire on {{ \Carbon\Carbon::parse($item->updated_at)->addDays($item->expiry_days)->format('d M, Y') }}
                                @else 
                                    @if ($days == 0)
                                        <span class=" badge bg-warning">Valid</span>
                                        will expire just before the midnight
                                    @elseif ($days > 0)
                                        <span class=" badge bg-success">Valid</span>
                                        will expire in {{$days}} days
                                    @else
                                        <span class=" badge bg-danger">Expired</span> {{abs($days)}} days ago
                                    @endif
                                @endif
                            </td>
                            <td style="width: 10%">
                                <a class="btn btn-primary btn-sm" data-modal="ajaxifyModal" href="{{ route('password-manager.expiry.edit', $item->id) }}"> <i class="fa fa-edit"></i> Edit</a>
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

    </div>
</section>
@endsection 