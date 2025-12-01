@extends('tablar::page')

@section('content')

 <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Customers Referred by {{$user->name}}
                    </h2>
                </div>
                <!-- Page title actions -->
                
            </div>
        </div>
    </div>
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
                                    <form method="GET" 
                                          class="mb-3 d-flex gap-2 align-items-center">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                               placeholder="Search..." class="form-control"/>
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
                                    <th>Commission</th> 
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
                                        <td><a  
                                               tabindex="-1">{{  ucwords(strtolower($contacto['contactName']))  ?? '—' }}</a>
                                        </td>
                                        <td>{{ $contacto['email'] ?? '—' }}</td>
                                        <td>
                                            {{ $contacto['phone'] ?? '—' }}
                                        </td>
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
                                                    case 'pending': // Asumiendo este valor de GHL
                                                        $paymentText = 'Pending';
                                                        $paymentBadgeClass = 'bg-warning';
                                                        break;
                                                    case 'undefined':
                                                        $paymentText = 'Undefined';
                                                        $paymentBadgeClass = 'bg-danger';
                                                        break;
                                                    default: // Incluye 'n/a' y cualquier otro caso
                                                        $paymentText = 'Unqualified';
                                                        $paymentBadgeClass = 'bg-danger'; 
                                                        break;
                                                }
                                            @endphp
                                            <span class="badge {{ $paymentBadgeClass }}  text-white">{{ $paymentText }}</span>
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


    
@endsection