@extends('password-manager::layout')

@section('content')  
<header class="header mt-3">
    <p class="h4"><strong>{{ $page ?? '' }}</strong></p>
    <div class="pull-right" style="margin-top: 0px">
        <a href="{{  route('password-manager.checklist.create')  }}" class="btn btn-sm btn-primary" data-modal="ajaxifyModal">
            <i class="fa fa-plus"></i> Create New Checklist
        </a>
    </div>
</header>
<section class="panel panel-default">
    {{Form::model($_REQUEST, ['method' => 'get'])}}
        <div class="row">
            <div class="col-sm-4">
                {{Form::text('password', request()->password??old('password'), ['id' => 'password' ,'class' => 'form-control', 'placeholder' => 'Search Password'])}}
            </div>

            <div class="col-sm-4">
                {{Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), request()->created_by??old('status'), ['id' => 'status' ,'class' => 'form-control select2-option','placeholder' => ' Select Status'])}}
            </div>

            <div class="col-sm-4">
                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-search"></i>
                </button>
                <a href="{{route('password-manager.checklist')}}" class="btn btn-sm btn-danger">Clear</a>
            </div>
        </div>
    {{Form::close()}}
</section>

<section class="panel panel-default">
    <div class="table-responsive">
        <table class="table table-striped" id="clients-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Password</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if($result->count())
                    @foreach($result as $item)
                        <tr>
                            <td style="width: 5%">{{ $loop->iteration }}</td> 
                            <td style="width: 15%">{{ $item->password }}</td>
                            <td style="width: 10%">
                                @if($item->status==1)
                                    <span class=" badge bg-success">Active</span>
                                @else
                                    <span class=" badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td style="width: 10%">
                                <a class="btn btn-primary btn-sm" data-modal="ajaxifyModal" href="{{ route('password-manager.checklist.edit', $item->id) }}">Edit</a>
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