@extends('tablar::page')

@section('content')

    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Customers
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a class="btn btn-primary d-sm-inline-block mb-2" data-bs-toggle="modal"
                           data-bs-target="#modal">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
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
        <div class="alert alert-danger" role="alert">
            <div class="alert-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="icon alert-icon icon-2">
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                    <path d="M12 8v4"></path>
                    <path d="M12 16h.01"></path>
                </svg>
            </div>
            <div>
                <h4 class="alert-heading">We're sorry…</h4>
                <div class="alert-description">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @elseif (session('success'))
        <div class="alert alert-success" role="alert">
            <div class="alert-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="icon alert-icon icon-2">
                    <path d="M12 20a8 8 0 1 1 0 -16a8 8 0 0 1 0 16z"/>
                    <path d="M9 12l2 2l4 -4"/>
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

    {{-- Seccion de comision agregada --}}
    @if(auth()->check() && auth()->user()->is_commissionable)
        <div class="page-body">
            <div class="container-xl">
                <div class="alert alert-info" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10l.01 0" /><path d="M11 14h2l-2 4h2" /></svg>
                        </div>
                        <div>
                            <h4 class="alert-title">Your Commission Percentage</h4>
                            <div class="text-muted">Your current commission percentage is <strong>{{ auth()->user()->commission_percentage ?? 0 }}%</strong>.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Page body -->
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
                                    <th class="w-1">No.
                                        <!-- Download SVG icon from http://tabler-icons.io/i/chevron-up -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="icon icon-sm text-dark icon-thick" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="6 15 12 9 18 15"/>
                                        </svg>
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Created</th>
                                    <th>Status</th>
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
                                        <td><span class="text-muted">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span></td>
                                        <td><a href="#" class="text-reset"
                                               tabindex="-1">{{  ucwords(strtolower($contacto['contactName']))  ?? '—' }}</a>
                                        </td>
                                        <td>{{ $contacto['email'] ?? '—' }}</td>
                                        <td>
                                            {{ $contacto['phone'] ?? '—' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($contacto['dateAdded'] ?? '—')->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $esActivo = strtolower($contacto['estado']) === 'active';
                                                $badgeClass = $esActivo ? 'bg-success' : 'bg-danger';
                                                $estadoTexto = $esActivo ? 'Active' : 'Inactive';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} me-1"></span> {{ $estadoTexto }}
                                        </td>
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
                                                </div>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Paginación -->
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
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('notes') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Note</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <i class="bi bi-person-circle me-2 fs-4"></i>
                            <div>
                                <strong>Selected Contact:</strong>
                                <span id="contact-name-display" class="fs-5 fw-bold"></span>
                            </div>
                        </div>
                        <input type="hidden" name="contact_id" id="contact-id-field">
                        <input type="hidden" name="assigned_to" id="assigned-to-field">
                        <div class="mb-3">
                            <label for="note-description" class="form-label">Description</label>
                            <textarea class="form-control" id="note-description" name="description" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Note</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
    {{-- Moda --}}
    <div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('customer') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Search by Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter email address" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary ms-auto">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
