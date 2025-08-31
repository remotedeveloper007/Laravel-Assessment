@extends('layouts.app')
@section('title', isset($product) ? 'Edit Product' : 'Create Product')
@section('content')
@if(isset($product))
<div class="container" style="height: 620px;">
@else
<div class="container" style="height: 550px;">
@endif
    <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">{{ isset($product) ? 'Edit' : 'Create' }} Product</h5>
              <form method="POST" enctype="multipart/form-data" action="{{ isset($product) ? route('products.update',$product) : route('products.store') }}">
                @csrf
                @if(isset($product)) @method('PUT') @endif

                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Category</label>
                  <input name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}">
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Price</label>
                    <input name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Stock</label>
                    <input name="stock" type="number" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Image</label>
                  <input name="image" type="file" class="form-control">
                  @if(!empty($product->image))
                    <img src="{{ asset('storage/'.$product->image) }}" class="img-thumbnail mt-2" style="max-width:50px;">
                  @endif
                </div>

                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                  <a href="{{ route('products.index') }}" class="btn btn-link me-2">Cancel</a>
                  <button class="btn btn-success">{{ isset($product) ? 'Update' : 'Create' }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection
