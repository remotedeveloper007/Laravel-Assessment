@extends('layouts.app')
@section('title','Admin Dashboard')
@push('head')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
@endpush
@section('content')
<div class="container" style="height: 400px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header">{{ __('Admin Dashboard Overview') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in as Admin!') }}


                  <div class="row">
                    <div class="col-sm-4">
                      <div class="p-3 border rounded">
                        <h3>{{ $productsCount }}</h3>
                        <small>Products</small>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="p-3 border rounded">
                        <h3>{{ $adminsOnline }}</h3>
                        <small>Admins Online</small>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="p-3 border rounded">
                        <h3>{{ $customersOnline }}</h3>
                        <small>Customers Online</small>
                      </div>
                    </div>
                  </div>                    
                </div>
            </div>

            <div class="card shadow-sm">
              <div class="card-body">
                <h5 class="card-title">Presence (Live)</h5>
                <div id="presenceList">Loading presence...</div>
              </div>
            </div>            
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <h6>Quick Actions</h6>
              <a href="{{ route('products.index') }}" class="btn btn-outline-primary w-100 mb-2">Manage Products</a>
              <a href="{{ route('admin.import.show') }}" class="btn btn-outline-secondary w-100 mb-2">Import CSV</a>
              <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-success w-100">View Orders</a>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@^1.11.0/dist/echo.iife.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
(function(){

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
      forceTLS: false,
      cluster: 'mt1',
      disableStats: true,
      withCredentials: true,
      auth: { headers: { 'X-CSRF-TOKEN': window.csrfToken } },
      authEndpoint: '/broadcasting/auth',
  });

  // Presence Channel
  Echo.join('admin-dashboard')
    .here(users => renderPresence(users))
    .joining(user => renderPresenceAppend(user))
    .leaving(user => removePresence(user))
    .listen('.order.placed', (e) => {
      const text = `New Order #${e.order_id} by ${e.customer_name} ₹${e.total}`;
  
      toastr.success(text, 'Order Recieved!');

      soundSoProud.currentTime = 0;
      soundSoProud.play().catch(err => console.log('Sound play blocked:', err));

      const notification = new Notification("Order Recieved!", {
          body: text,
          icon: "/images/notification-bell.png",
          requireInteraction: false
      });

      
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-bg-success border-0 position-fixed';
        toast.style.right = '20px'; toast.style.top = '20px'; toast.style.zIndex = 9999;
        toast.innerHTML = `<div class="d-flex"><div class="toast-body">${t}</div>
          <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        document.body.appendChild(toast);
        new bootstrap.Toast(toast).show(); 
      
    });

  function renderPresence(users){
    const el = document.getElementById('presenceList');
    if(!users || users.length===0) {
      el.innerHTML = '<div class="small text-muted">No active users</div>';
      return;
    }
    el.innerHTML = users.map(u => `
      <div class="d-flex align-items-center mb-2" id="presence-${u.id}">
        <div class="me-2"><strong>${u.name}</strong> <small class="text-muted">(${u.type})</small></div>
      </div>
    `).join('');
  }

  function renderPresenceAppend(user){
    console.log(user)
    const el = document.getElementById('presenceList');
    el.insertAdjacentHTML('beforeend', 
      `<div class="d-flex align-items-center mb-2" id="presence-${user.id}">
        <div class="me-2"><strong>${user.name}</strong> <small class="text-muted">(${user.type})</small></div>
      </div>`
    );
  }

  function removePresence(user){
    const node = document.getElementById('presence-'+user.id);
    if(node) node.remove();
  }
})();
</script>
@endpush