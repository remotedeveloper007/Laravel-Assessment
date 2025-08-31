@extends('layouts.app')
@section('title', 'Shop')
@push('head')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
@endpush
@section('content')
<div class="container" style="height: 400px;">
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
                    <img src="{{ asset('storage/'.$p->image) }}" class="card-img-top" style="height:160px;object-fit:cover;">
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
    </div>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection

@push('scripts')

@if(Auth::guard('customer')->check())
<script>
(async function(){
  if('serviceWorker' in navigator && 'PushManager' in window){
    try {
      const reg = await navigator.serviceWorker.register('/sw.js');
      const publicKey = "{{ env('VAPID_PUBLIC_KEY') }}";
      const converted = Uint8Array.from(atob(publicKey.replace(/-/g,'+').replace(/_/g,'/')), c=>c.charCodeAt(0));
      const sub = await reg.pushManager.subscribe({ userVisibleOnly:true, applicationServerKey: converted });
      
      await fetch('{{ route("push.subscribe") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        body: JSON.stringify(sub)
      });
      //console.log('Push subscription saved');
    } catch(err){
      console.warn('Push subscribe error', err);
    }
  }
})();
</script>
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.2.0/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> 

@if(Auth::guard('customer')->check())
<script>
  window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '{{ config("broadcasting.connections.pusher.key") }}',
    wsHost: '{{ env("PUSHER_HOST", "127.0.0.1") }}',
    wsPort: {{ env("PUSHER_PORT", 6001) }},
    cluster: 'mt1',
    forceTLS: false,
    disableStats: true,
    withCredentials: true,
    auth: { headers: { 'X-CSRF-TOKEN': window.csrfToken } },
    authEndpoint: '/broadcasting/auth',
  });

  Echo.private('orders.{{ Auth::guard("customer")->id() }}')
  .listen('.order.status.updated', (e) => {
    const t = `Order #${e.order_id} status: ${e.status}`;
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-bg-primary border-0 position-fixed';
    toast.style.right = '20px'; toast.style.top = '20px'; toast.style.zIndex = 9999;
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${t}</div>
      <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    document.body.appendChild(toast);
    new bootstrap.Toast(toast).show();
  });
</script>
@endif
<script>
  document.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('input', e => {
        const productId = input.dataset.productId; 
        const hidden = document.querySelector(`.qty-hidden[data-product='${productId}']`);
        if(hidden) {
          hidden.value = input.value;
        }
      });
  });

</script>
@endpush
