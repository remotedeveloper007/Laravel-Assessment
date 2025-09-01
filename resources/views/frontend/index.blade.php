@extends('layouts.app')
@section('title', 'Shop')
@push('head')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
@endpush
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Shop</h4>
            <form method="GET" class="w-50 ms-3">
                <div class="input-group">
                    <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search products or category">
                    <button class="btn btn-outline-secondary">Search</button>
                </div>
            </form>
        </div>
        <div class="row g-3">
            @foreach($products as $p)
            <div class="col-sm-6 col-md-3">
                <div class="card h-100">
                    <img src="{{ asset($p->image) }}" class="card-img-top" style="height:160px;object-fit:cover;">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $p->name }}</h6>
                        <p class="mb-2 small text-muted">{{ $p->category }}</p>
                       
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-success">₹ {{ number_format($p->price, 2) }}</span>
                            <small class="text-muted">{{ $p->stock }} in stock</small>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">          
                            <input type="number" 
                                name="qty" 
                                value="1" 
                                min="1" 
                                max="{{ $p->stock }}" 
                                class="form-control text-center qty-input" 
                                style="max-width: 80px; height: 40px; line-height: 40px;"
                                data-product-id="{{ $p->id }}"
                            />                  
                                                   
                            <form method="POST" action="{{ route('customer.orders.store') }}" class="ms-auto">
                                @csrf
                                <input type="hidden" name="items[0][product_id]" value="{{ $p->id }}">
                                <input type="hidden" name="items[0][qty]" value="1" class="qty-hidden" data-product="{{ $p->id }}">
                                <button class="btn" style="background-color: #FF9900; border: none; width: 120px;">Buy Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $products->links() }}</div>
    </div>
</div>
@endsection
