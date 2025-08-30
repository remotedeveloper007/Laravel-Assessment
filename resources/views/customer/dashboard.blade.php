@extends('layouts.app')
@section('title','My Dashboard')
@section('content')
<div class="container" style="height: 430px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header">{{ __('Customer Dashboard Overview') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- {{ __('You are logged in as Admin!') }} -->
                  <h5>Hello, {{ Auth::guard('customer')->user()->name }}</h5>
                  <p class="small text-muted">Manage orders and browse products.</p>          
                </div>
            </div>

            <div class="card shadow-sm">
              <div class="card-body">
                <h6>Recent Orders</h6>
                <div class="small text-muted">(Orders listing would appear here)</div>
              </div>
            </div>            
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm"><div class="card-body">
            <h6>Account</h6>
            <p class="mb-0"><strong>{{ Auth::guard('customer')->user()->email }}</strong></p>
            <p class="small text-muted">Status: <span class="badge {{ Auth::guard('customer')->user()->online ? 'bg-success' : 'bg-secondary' }}">{{ Auth::guard('customer')->user()->online ? 'Online' : 'Offline' }}</span></p>
          </div></div>
        </div>        
    </div>
</div>
@endsection
