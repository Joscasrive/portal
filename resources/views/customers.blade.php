@extends('tablar::page')
@section('content')

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Customers
                    </h2>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a class="btn btn-primary d-sm-inline-block mb-2" data-bs-toggle="modal"
                            data-bs-target="#modal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Advanced Search
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (session('error'))
        @elseif (session('success'))
        @endif

    
    @if(auth()->check() && auth()->user()->is_commissionable)
        @endif

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Customers</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <div class="d-flex">
                                <div class="text-muted">
                                    <form action="{{ request()->url() }}" method="GET" id="perPageForm">
                                        <div class="d-flex align-items-center">
                                            <span class="m-2">Show:</span>
                                            <select name="perPage" class="form-control form-control-sm"
                                                    onchange="document.getElementById('perPageForm').submit();">
                                                <option value="10" {{ request()->get('perPage', 10) == 10 ? 'selected' : '' }}>
                                                    10
                                                </option>
                                                <option value="20" {{ request()->get('perPage', 10) == 20 ? 'selected' : '' }}>
                                                    20
                                                </option>
                                                <option value="50" {{ request()->get('perPage', 10) == 50 ? 'selected' : '' }}>
                                                    50
                                                </option>
                                                <option value="100" {{ request()->get('perPage', 10) == 100 ? 'selected' : '' }}>
                                                    100
                                                </option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="ms-auto text-muted">
                                    <form method="GET" action="{{ route('customers') }}"
                                            class="mb-3 d-flex gap-2 align-items-center">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                                placeholder="Search..." class="form-control"/>
                                        <select name="estado" onchange="this.form.submit()" class="form-select">
                                            <option value="">All</option>
                                            <option value="active" {{ request('estado') === 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive" {{ request('estado') === 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                            <option>Completed</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                <tr>
                                    <th class="w-1"><input class="form-check-input m-0 align-middle" type="checkbox"
                                                                 aria-label="Select all invoices"></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>

                                    
                                    
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th>Payment</th> 
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($contactos as $index => $contacto)
                                    <tr>
                                        <td>
                                            <input class="form-check-input m-0 align-middle" type="checkbox"
                                                    aria-label="Select invoice">
                                        </td>
                                        <td><a href="{{ route('reporting', $contacto['id'])}}" class="text-reset"
                                               tabindex="-1">{{  ucwords(strtolower($contacto['contactName']))  ?? '—' }}</a>
                                        </td>
                                        <td>{{ $contacto['email'] ?? '—' }}</td>
                                        <td>
                                            {{ $contacto['phone'] ?? '—' }}
                                        </td>
                                        
                                        
                                        
                                        <td>{{ \Carbon\Carbon::parse($contacto['dateAdded'] ?? '—')->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $esActivo = strtolower($contacto['estado'] ?? 'inactive') === 'active';
                                                $badgeClass = $esActivo ? 'bg-success' : 'bg-danger';
                                                $estadoTexto = $esActivo ? 'Active' : 'Inactive';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} me-1"></span> {{ $estadoTexto }}
                                        </td>
                                        {{-- NEW PAYMENT STATUS COLUMN LOGIC --}}
                                        <td>
                                            @php
                                                // Usamos el campo procesado en el controlador, que es SIEMPRE un string.
                                                $paymentStatus = strtolower($contacto['payment_status_conditional'] ?? 'n/a');
                                                $paymentText = '';
                                                $paymentBadgeClass = '';

                                                switch ($paymentStatus) {
                                                    case 'paid':
                                                        $paymentText = 'Paid';
                                                        $paymentBadgeClass = 'bg-success';
                                                        break;
                                                    case 'in progress': // Asumiendo este valor de GHL
                                                        $paymentText = 'In Progress';
                                                        $paymentBadgeClass = 'bg-warning';
                                                        break;
                                                    case 'undefined':
                                                        $paymentText = 'Undefined';
                                                        $paymentBadgeClass = 'bg-danger';
                                                        break;
                                                    default: // Incluye 'n/a' y cualquier otro caso
                                                        $paymentText = 'N/A';
                                                        $paymentBadgeClass = 'bg-dark'; 
                                                        break;
                                                }
                                            @endphp
                                            <span class="badge {{ $paymentBadgeClass }}">{{ $paymentText }}</span>
                                        </td>
                                        {{-- END NEW PAYMENT STATUS COLUMN LOGIC --}}
                                        <td class="text-end">
                                            <span class="dropdown">
                                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                                        data-bs-toggle="dropdown">Actions</button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <form id="requestTradelinesForm-{{ $contacto['id'] ?? $index }}"
                                                        action="{{ route('requests') }}" method="POST" style="display: none;">
                                                        @csrf
                                                        <input type="hidden" name="customerEmail"
                                                                value="{{ $contacto['email'] ?? '' }}">
                                                    </form>
                                                    <a class="dropdown-item"
                                                        onclick="event.preventDefault(); document.getElementById('requestTradelinesForm-{{ $contacto['id'] ?? $index }}').submit();">
                                                        Request Tradelines
                                                    </a>
                                                    <a class="dropdown-item btn-create-note"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#noteModal"
                                                        data-contact='@json($contacto)'>
                                                        Create Note
                                                    </a>
                                                    <a class="dropdown-item" 
                                                        href="{{ route('tasks.create', ['email' => $contacto['email'] ?? '']) }}">
                                                        Create Task
                                                    </a>
                                                </div>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            <p class="m-0 text-muted">
                                Showing <span>{{ $contactos->firstItem() }}</span> to <span>{{ $contactos->lastItem() }}</span> of
                                <span>{{ $contactos->total() }}</span> entries
                            </p>
                            <ul class="pagination m-0 ms-auto">
                                {{-- Paginación previa --}}
                                <li class="page-item {{ $contactos->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $contactos->previousPageUrl() }}" aria-label="Previous">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="15 6 9 12 15 18"/>
                                        </svg>
                                        prev
                                    </a>
                                </li>
                                {{-- Páginas --}}
                                @for ($i = 1; $i <= $contactos->lastPage(); $i++)
                                    <li class="page-item {{ $i == $contactos->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $contactos->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                                {{-- Paginación siguiente --}}
                                <li class="page-item {{ !$contactos->hasMorePages() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $contactos->nextPageUrl() }}" aria-label="Next">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="9 6 15 12 9 18"/>
                                        </svg>
                                        next
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Moda --}}
    <div class="modal fade" id="noteModal" tabindex="-1" aria-hidden="true">
        </div>
    </div>
    {{-- Moda --}}
    <div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.btn-create-note');
            const contactIdField = document.getElementById('contact-id-field');
            const contactNameDisplay = document.getElementById('contact-name-display');
            const descriptionField = document.getElementById('note-description');
            const To = document.getElementById('assigned-to-field');

            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const contact = JSON.parse(this.getAttribute('data-contact'));
                    contactIdField.value = contact.id;
                    To.value = contact.assignedTo;
                    contactNameDisplay.textContent = contact.contactName ?? 'Sin nombre';
                    descriptionField.value = ''; // Limpiar el campo al abrir
                });
            });
        });
    </script>
@endsection