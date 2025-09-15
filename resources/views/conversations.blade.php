@extends('tablar::page')

@section('content')
   
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="col">
                
                <div class="page-pretitle mb-1">
                    Overview
                </div>
                <h2 class="page-title mb-1">
                    ✅ Client Requests and Progress Form
                </h2>
            </div>
      <div class="page">
  
  <div class="page-wrapper">
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-deck row-cards">
          
          
          </div>
          <div class="col-12">
            <div class="card">
              <div class="card-body">
@if ($errors->any())
<div class="alert alert-danger" role="alert">
    <div class="alert-icon">
        <!-- Icono -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2">
            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
            <path d="M12 8v4"></path>
            <path d="M12 16h.01"></path>
        </svg>
    </div>
    <div>
        <h4 class="alert-heading">Oops, hubo errores en el formulario</h4>
        <div class="alert-description">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

    <form action="{{route('requests')}}" method="POST">
          @csrf
  <div class="mb-3">
    <label class="form-label">Customer email</label>
    <input type="email" class="form-control" name="customerEmail" placeholder="client@example.com" required />
  </div>

  <div class="mb-3">
    <label class="form-label">Application Title</label>
    <input type="text" class="form-control" name="requestTitle" placeholder="E.g. Technical support request" required />
  </div>

  <div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description" rows="4" placeholder="Please write your request in detail and leave your email address or the best way to receive the information or updates...." required></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Deadline</label>
    <input type="datetime-local" class="form-control" name="dueDate" required />
  </div>
  <button type="submit" class="btn btn-primary">Send</button>
</form>
<br>
@if (session('error'))
<div class="alert alert-danger" role="alert">
    <div class="alert-icon">
        
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2">
            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
            <path d="M12 8v4"></path>
            <path d="M12 16h.01"></path>
        </svg>
    </div>
    <div>
        <h4 class="alert-heading">Lo sentimos…</h4>
        <div class="alert-description">
            {{ session('error') }}
        </div>
    </div>
</div>
@elseif (session('success'))
<div class="alert alert-success" role="alert">
    <div class="alert-icon">
        
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2">
            <path d="M12 20a8 8 0 1 1 0 -16a8 8 0 0 1 0 16z" />
            <path d="M9 12l2 2l4 -4" />
        </svg>
    </div>
    <div>
        <h4 class="alert-heading">Success!</h4>
        <div class="alert-description">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif

       
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

        
            
    </div>
    

@endsection
