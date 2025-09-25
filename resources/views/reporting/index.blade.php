{{-- resources/views/reporting.blade.php --}}

@extends('tablar::page')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Client Report
                    </h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- Sección de Datos del Cliente (Mitad Izquierda) --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Customer Information</h3>
                        </div>
                        <div class="card-body">
                            @if ($clienteData && isset($clienteData['contact']))
                                 <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>Nombre:</strong> 
                                             {{ ucfirst($clienteData['contact']['firstName'] ?? '') }} 
                                             {{ ucfirst($clienteData['contact']['lastName'] ?? '') }}
                                    </li>
                                    <li class="list-group-item"><strong>Email:</strong> {{ $clienteData['contact']['email'] ?? '' }}</li>
                                    <li class="list-group-item"><strong>Teléfono:</strong> {{ $clienteData['contact']['phone'] ?? '' }}</li>
                                    <li class="list-group-item"><strong>Empresa:</strong> {{ $clienteData['contact']['companyName'] ?? '' }}</li>
                                    
                                </ul>
                            @else
                                <p>No customer data found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sección de Notas y Citas (Mitad Derecha) --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Client Appointments</h3>
                        </div>
                        <div class="card-body">
                            @if ($citasData && isset($citasData['events']) && count($citasData['events']) > 0)
                                @foreach ($citasData['events'] as $cita)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="me-3">
                                                    {{-- Icono de cita de Tabler --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                       <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                       <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                                       <line x1="16" y1="3" x2="16" y2="7"></line>
                                                       <line x1="8" y1="3" x2="8" y2="7"></line>
                                                       <line x1="4" y1="11" x2="20" y2="11"></line>
                                                       <rect x="8" y="15" width="2" height="2" rx="1"></rect>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="card-title mb-0">
                                                        {{ $cita['title'] ?? 'Untitled' }}
                                                    </h4>
                                                    <p class="text-muted mb-0">
                                                        {{ date('m/d/Y H:i', strtotime($cita['startTime'])) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="mb-0">
                                                <strong>Note:</strong> {{ $cita['notes'] ?? 'No note' }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>Status:</strong> 
                                                
                                                @php
                                                    $status = strtolower($cita['appointmentStatus'] ?? 'unknown');
                                                    $badgeClass = 'bg-secondary';
                                                    if ($status === 'booked' || $status === 'confirmed') {
                                                        $badgeClass = 'bg-green-lt';
                                                    } elseif ($status === 'cancelled' || $status === 'invalid') {
                                                        $badgeClass = 'bg-danger-lt';
                                                    } elseif ($status === 'no show') {
                                                        $badgeClass = 'bg-warning-lt';
                                                    } elseif ($status === 'showed') {
                                                        $badgeClass = 'bg-success-lt';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($cita['appointmentStatus'] ?? 'Desconocido') }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <path d="M10 10l-2 2l2 2m4 -4l2 2l-2 2"></path>
                                        </svg>
                                    </div>
                                    <p class="empty-title">No scheduled appointments</p>
                                    <p class="empty-subtitle text-muted">There are no appointments scheduled for this client.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection