@extends('layouts.app')
@section('title','Orders')
@section('content')
<div class="container" style="height: 400px;">
    <div class="row justify-content-center">
        <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Orders</h4>
        </div>

        <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light"><tr><th>#</th><th>Customer</th><th>Status</th><th>Total</th><th>Placed</th><th></th></tr></thead>
            <tbody>
            @foreach($orders as $o)
            <tr>
                <td>#{{ $o->id }}</td>
                <td>{{ $o->customer->name }} <div class="small text-muted">{{ $o->customer->email }}</div></td>
                <td>{{ $o->status }}</td>
                <td>₹ {{ number_format($o->total,2) }}</td>
                <td>{{ $o->created_at->diffForHumans() }}</td>
                <td class="text-end">
                <form method="POST" action="{{ route('admin.orders.status',$o) }}" class="d-inline">@csrf @method('PATCH')
                    <div class="input-group">
                    <select name="status" class="form-select">
                        @foreach(['Pending','Shipped','Delivered'] as $s)
                        <option value="{{ $s }}" @selected($o->status===$s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-success">Update</button>
                    </div>
                </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

{{ $orders->links() }}
@endsection
