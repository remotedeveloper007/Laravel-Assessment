@extends('layouts.app')
@section('title','My Dashboard')
@section('content')
<div class="container">
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
                <div class="small text-muted">
                  @if($orders->isEmpty())
                    <p class="small text-muted">No orders placed yet.</p>
                  @else
                    <ul class="list-group list-group-flush">
                        @foreach($orders as $order)
                            {{-- <li class="list-group-item">
                                <strong>Order #{{ $order->id }}</strong><br>
                                Status: <span class="badge bg-primary">{{ $order->status }}</span><br>
                                Total: ₹{{ number_format($order->total, 2) }}<br>
                                <small class="text-muted">Placed on: {{ $order->created_at->format('d M Y, H:i') }}</small>
                            </li> --}}

                        <li class="list-group-item">
                            <strong>Order #{{ $order->id }}</strong><br>
                            
                            <!-- Conditional Badge Colors -->
                            @if($order->status == 'Pending')
                                <span class="badge bg-warning">{{ $order->status }}</span>
                            @elseif($order->status == 'Shipped')
                                <span class="badge bg-info">{{ $order->status }}</span>
                            @elseif($order->status == 'Delivered')
                                <span class="badge bg-success">{{ $order->status }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->status }}</span>
                            @endif

                            <br>
                            Total: ₹{{ number_format($order->total, 2) }}<br>
                            <small class="text-muted">Placed on: {{ $order->created_at->format('d M Y, H:i') }}</small>

                            <hr>
                            <h6>Order Items</h6>
                            <ul>
                                @foreach($order->items as $item)
                                    <li>
                                        {{ $item->product->name }} (x{{ $item->qty }}) - ₹{{ number_format($item->subtotal, 2) }}
                                    </li>
                                @endforeach
                            </ul>
                        </li>                            
                        @endforeach
                    </ul>
                    
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                  @endif                
                </div>
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
  toastr.options = {
      "closeButton": true,
      "newestOnTop": true,
      "progressBar": true,
      "positionClass": "toast-bottom-right",
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut",
      "tapToDismiss": true
  };

  const soundSoProud = new Audio('/sounds/so-proud-notification.mp3');
  const soundArpeggio = new Audio('/sounds/arpeggio-467.mp3');

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
    const test = `Order #${e.order_id} status: ${e.status}`;

    toastr.success(test, 'Order Status Updated!');

    soundSoProud.currentTime = 0;
    soundSoProud.play().catch(err => console.log('Sound play blocked:', err));

    const notification = new Notification("Order Status Updated!", {
        body: test,
        icon: "/images/notification-bell.png",
        requireInteraction: false
    });
    /*
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-bg-primary border-0 position-fixed';
    toast.style.right = '20px'; toast.style.top = '20px'; toast.style.zIndex = 9999;
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${t}</div>
      <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    document.body.appendChild(toast);
    new bootstrap.Toast(toast).show(); */
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
