@extends('layouts.app')
@section('title','Manage Products')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if (session('status'))
              <div class="alert alert-success" role="alert">
                  {{ session('status') }}
              </div>
            @endif
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Products</h4>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
          </div>

          <form class="mb-3" method="GET">
            <div class="input-group">
              <input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search name or category">
              <button class="btn btn-outline-secondary">Search</button>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light"><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr></thead>
              <tbody>
                @foreach($products as $p)
                <tr>
                  <td>{{ $p->name }}</td>
                  <td>{{ $p->category }}</td>
                  <td>₹ {{ number_format($p->price,2) }}</td>
                  <td>{{ $p->stock }}</td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.edit',$p) }}">Edit</a>
                    <form method="POST" action="{{ route('products.destroy',$p) }}" class="d-inline">@csrf @method('DELETE')
                      <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        {{ $products->links() }}
    </div>
</div>
@endsection
