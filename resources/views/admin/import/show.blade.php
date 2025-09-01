@extends('layouts.app')
@section('title','Import Products CSV')
@section('content')
<div class="container" style="height: 400px;">
    <div class="row justify-content-center">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Bulk Import CSV</h5>
          <form method="POST" enctype="multipart/form-data" action="{{ route('admin.import.store') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">CSV File</label>
              <input name="file" type="file" accept=".csv" class="form-control" required>
            </div>
            <div class="mb-3">
              <small class="text-muted">Expected headers: <code>name,description,price,image,category,stock</code>. Large files are chunked and queued.</small>
            </div>
            <button class="btn btn-primary">Upload & Queue</button>
          </form>
        </div>
      </div>
    </div>
</div>
@endsection
