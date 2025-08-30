@extends('layouts.app')
@section('title','Admin Dashboard')
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

    </div>
</div>



@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@^1.11.0/dist/echo.iife.js"></script>

  <!-- jQuery (Toastr depends on jQuery) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>

(function(){
  // Init Echo with Pusher
  window.Echo = new Echo({
      broadcaster: 'pusher',
      key: '{{ config("broadcasting.connections.pusher.key") }}',
      wsHost: '{{ env("PUSHER_HOST", "127.0.0.1") }}',
      wsPort: {{ env("PUSHER_PORT", 6001) }},
      forceTLS: false,
      cluster: 'mt1',
      disableStats: true,
      withCredentials: true,
  });

  // Presence Channel
  // Echo.join('presence.admin-dashboard')
  //   .here(users => renderPresence(users))
  //   .joining(user => renderPresenceAppend(user))
  //   .leaving(user => removePresence(user));

    Echo.join('presence.admin-dashboard')
    .here(users => {
        console.log('Current users:', users);  // Check if this logs correctly
        renderPresence(users);
    })
    .joining(user => {
        console.log('User joining:', user);  // Check if the joining event is fired
        renderPresenceAppend(user);
    })
    .leaving(user => {
        console.log('User leaving:', user);  // Check if the leaving event is fired
        removePresence(user);
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