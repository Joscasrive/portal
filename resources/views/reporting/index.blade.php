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
            <div class="row row-cards">
                
                {{-- Sección de Datos del Cliente (Mitad Izquierda) --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Customer Information & Progress</h3>
                        </div>
                        <div class="card-body">
                            @if ($clienteData && isset($clienteData['contact']))
                                <h4 class="mb-3">General Details</h4>
                                <ul class="list-group list-group-flush mb-4">
                                    <li class="list-group-item">
                                        <strong>Name:</strong> 
                                        {{ ucfirst($clienteData['contact']['firstName'] ?? '') }} 
                                        {{ ucfirst($clienteData['contact']['lastName'] ?? '') }}
                                    </li>
                                    <li class="list-group-item"><strong>Email:</strong> {{ $clienteData['contact']['email'] ?? '' }}</li>
                                    <li class="list-group-item"><strong>Phone:</strong> {{ $clienteData['contact']['phone'] ?? '' }}</li>
                                    <li class="list-group-item"><strong>Company:</strong> {{ $clienteData['contact']['companyName'] ?? 'N/A' }}</li>
                                </ul>

                                <h4 class="mb-3 border-top pt-3">Process Progress (Rounds)</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                         <strong>Initial Analysis Report:</strong> 
                                        @if ($reporteInicialUrl)
                                            <a href="{{ $reporteInicialUrl }}" target="_blank" class="text-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M11 7h-5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-5"></path><path d="M10 14l10 -10"></path><path d="M15 4l5 0l0 5"></path></svg>
                                                View Document
                                            </a>
                                       @else
                                            <span class="text-muted">No document found for this Moment.</span>
                                        @endif
                                        <br>
                                        <strong>Last Completed Round:</strong> 
                    
                                        <span class="badge bg-primary-lt">{{ $lastCompletedRound }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Document Link:</strong> 
                                        @if ($lastCompletedRoundUrl)
                                            <a href="{{ $lastCompletedRoundUrl }}" target="_blank" class="text-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M11 7h-5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-5"></path><path d="M10 14l10 -10"></path><path d="M15 4l5 0l0 5"></path></svg>
                                                View Document
                                            </a>
                                        @else
                                            <span class="text-muted">No document found for this round.</span>
                                        @endif
                                    </li>
                                </ul>
                            @else
                                <p>No customer data found.</p>
                            @endif
                        </div>
                        
                    </div>
                    {{-- ================================================================================= --}}
                {{-- SECCIÓN: REPORTE DE CRÉDITO (Full Width) --}}
                {{-- ================================================================================= --}}

                <div class="col-12 mt-4">
                    @if (isset($creditData['CREDIT_RESPONSE']))
                        @php
                            $response = $creditData['CREDIT_RESPONSE'];
                            $borrower = $response['BORROWER'];
                            $cuentas = $response['CREDIT_LIABILITY'] ?? [];
                            $factors = $response['CREDIT_SCORE_FACTOR_SUMMARY']['CREDIT_SCORE_FACTOR_DETAIL'] ?? [];

                            // Función auxiliar simple para formatear dinero
                            function formatMoney($amount) {
                                // Asegurar que el valor es numérico antes de formatear
                                return number_format((int)$amount, 0, '.', ',');
                            }

                            // EXTRAER DATOS CLAVE DEL SUMMARY
                            $summaryMap = [];
                            foreach ($factors as $factor) {
                                $summaryMap[$factor['@_ID']] = $factor['@_Value'];
                            }

                            // Datos para las tarjetas resumen
                            $score = $summaryMap['PT159'] ?? 'N/D'; // ID PT159 o similar para FICO/Score. Si no está, N/D.
                            $totalBalance = formatMoney($summaryMap['PT202'] ?? 0);
                            $openTrades = $summaryMap['PT201'] ?? 0;
                            $delinquentBalance = formatMoney($summaryMap['PT187'] ?? 0);

                            // Contar cuentas negativas
                            $derogatoryCount = count(array_filter($cuentas, fn($c) => ($c['@_DerogatoryDataIndicator'] ?? 'N') === 'Y'));
                        @endphp

                        {{-- CARD PRINCIPAL: SCORE Y RESUMEN RÁPIDO --}}
                        <div class="row row-cards mb-3">
                            <div class="col-md-3">
                                <div class="card card-sm bg-primary-lt">
                                    <div class="card-body text-center">
                                        <div class="text-uppercase text-muted fw-bold">Credit Score (FICO/Vantage)</div>
                                        <div class="h1 mb-0 mt-1">{{ $score }}</div>
                                        <small class="text-muted">Source: {{ $response['@CreditRatingCodeType'] ?? 'N/D' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body text-center">
                                        <div class="text-uppercase text-muted fw-bold">Total Debt Balance</div>
                                        <div class="h1 mb-0 mt-1">${{ $totalBalance }}</div>
                                        <small class="text-muted">Open & Closed Trades</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body text-center">
                                        <div class="text-uppercase text-muted fw-bold">Open Trades</div>
                                        <div class="h1 mb-0 mt-1">{{ $openTrades }}</div>
                                        <small class="text-muted">Total Accounts</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm bg-{{ $derogatoryCount > 0 ? 'danger-lt' : 'success-lt' }}">
                                    <div class="card-body text-center">
                                        <div class="text-uppercase text-muted fw-bold">Derogatory Accounts</div>
                                        <div class="h1 mb-0 mt-1">{{ $derogatoryCount }}</div>
                                        <small class="text-muted">Collections / Late payments</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DETALLE DEL REPORTE --}}
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <h3 class="card-title text-white">Credit Report Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Borrower Details (Report)</h4>
                                        <ul class="list-unstyled">
                                            <li><strong>Full Name:</strong> {{ $borrower['@_FirstName'] }} {{ $borrower['@_MiddleName'] ?? '' }} {{ $borrower['@_LastName'] }}</li>
                                            <li><strong>DOB:</strong> {{ date('Y-m-d', strtotime($borrower['@_BirthDate'] ?? '')) }}</li>
                                            <li><strong>Address:</strong> {{ $borrower['_RESIDENCE']['@_StreetAddress'] ?? 'N/A' }}, {{ $borrower['_RESIDENCE']['@_City'] ?? 'N/A' }}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Report Metadata</h4>
                                        <ul class="list-unstyled">
                                            <li><strong>Report ID:</strong> {{ $response['@CreditReportIdentifier'] ?? 'N/A' }}</li>
                                            <li><strong>Date Issued:</strong> {{ date('Y-m-d', strtotime($response['@CreditReportFirstIssuedDate'] ?? '')) }}</li>
                                            <li><strong>Bureaus:</strong> 
                                                <span class="badge bg-{{ $response['CREDIT_REPOSITORY_INCLUDED']['@_EquifaxIndicator'] === 'Y' ? 'success' : 'secondary' }}">Equifax</span>
                                                <span class="badge bg-{{ $response['CREDIT_REPOSITORY_INCLUDED']['@_ExperianIndicator'] === 'Y' ? 'success' : 'secondary' }}">Experian</span>
                                                <span class="badge bg-{{ $response['CREDIT_REPOSITORY_INCLUDED']['@_TransUnionIndicator'] === 'Y' ? 'success' : 'secondary' }}">TransUnion</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TABLA DE CUENTAS (LIABILITIES) --}}
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Credit Liabilities ({{ count($cuentas) }} Tradelines)</h3>
                            </div>
                            <div class="card-body p-0">
                                @if (!empty($cuentas))
                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Creditor</th>
                                                    <th>Type / Status</th>
                                                    <th class="text-center">Balance</th>
                                                    <th class="text-center">Limit</th>
                                                    <th class="text-center">Late (30/60/90)</th>
                                                    <th class="text-center">Derogatory</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cuentas as $cuenta)
                                                    @php
                                                        $isDerog = ($cuenta['@_DerogatoryDataIndicator'] ?? 'N') === 'Y';
                                                        $late30 = (int)($cuenta['_LATE_COUNT']['@_30Days'] ?? 0);
                                                        $late60 = (int)($cuenta['_LATE_COUNT']['@_60Days'] ?? 0);
                                                        $late90 = (int)($cuenta['_LATE_COUNT']['@_90Days'] ?? 0);
                                                        $totalLate = $late30 + $late60 + $late90;

                                                        $balance = formatMoney($cuenta['@_UnpaidBalanceAmount'] ?? 0);
                                                        $limit = formatMoney($cuenta['@_CreditLimitAmount'] ?? $cuenta['@_HighCreditAmount'] ?? 0);
                                                        $creditorName = $cuenta['_CREDITOR']['@_Name'] ?? 'N/D';
                                                        $statusType = $cuenta['@_AccountStatusType'] ?? 'N/D';
                                                        $rating = $cuenta['_CURRENT_RATING']['@_Type'] ?? $statusType;
                                                    @endphp
                                                    <tr class="{{ $isDerog ? 'table-danger-lt' : '' }}">
                                                        <td>
                                                            <strong>{{ $creditorName }}</strong>
                                                            <div class="text-muted">Opened: {{ date('Y-m-d', strtotime($cuenta['@_AccountOpenedDate'] ?? '')) }}</div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary-lt me-1">{{ $cuenta['@_AccountType'] ?? 'N/D' }}</span>
                                                            <span class="badge bg-{{ $isDerog ? 'red' : 'green' }}-lt">{{ $rating }}</span>
                                                        </td>
                                                        <td class="text-end">**${{ $balance }}**</td>
                                                        <td class="text-end">${{ $limit }}</td>
                                                        <td class="text-center">
                                                            @if ($totalLate > 0)
                                                                <span class="badge bg-danger">
                                                                    {{ $late30 }} (30) / {{ $late60 }} (60) / {{ $late90 }} (90)
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success-lt">0</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-{{ $isDerog ? 'red' : 'green' }}">{{ $isDerog ? 'YES' : 'NO' }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty">
                                        <p class="empty-title">No Credit Liabilities Found</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif (isset($creditData['error']))
                        {{-- Muestra errores de la API --}}
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-title">Credit Report Error: {{ $creditData['http_code'] ?? 'Unknown' }}</h4>
                            <div class="text-muted">
                                {{ $creditData['error'] }}
                                @if(isset($creditData['response_message']))
                                    <br>API Message: {{ is_string($creditData['response_message']) ? $creditData['response_message'] : json_encode($creditData['response_message']) }}
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- Muestra mensaje si no hay datos --}}
                        <div class="alert alert-info" role="alert">
                            The credit report data is not yet available.
                        </div>
                    @endif
                </div>
                </div>

                {{-- Sección de Citas y TAREAS (Mitad Derecha) --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Appointments & Tasks</h3>
                        </div>
                        {{-- APLICAMOS MAX-HEIGHT Y OVERFLOW PARA SCROLL INDIVIDUAL --}}
                        <div class="card-body **overflow-y-auto**" style="**max-height: 450px;**">
                            
                            {{-- BLOQUE 1: Citas/Appointments --}}
                            <h4 class="mb-3">Scheduled Appointments</h4>
                            @if ($citasData && isset($citasData['events']) && count($citasData['events']) > 0)
                                @foreach ($citasData['events'] as $cita)
                                    <div class="card mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-2">
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
                                                <div class="flex-grow-1">
                                                    <h4 class="card-title mb-0">
                                                        {{ $cita['title'] ?? 'Untitled' }}
                                                    </h4>
                                                    <p class="text-muted mb-0 small">
                                                        {{ date('m/d/Y H:i', strtotime($cita['startTime'] ?? '')) }}
                                                    </p>
                                                </div>
                                                <div>
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
                                                </div>
                                            </div>
                                            <p class="mb-0 small">
                                                <strong>Note:</strong> {{ $cita['notes'] ?? 'No note' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty mb-4">
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

                            {{-- =============================================== --}}
                            {{-- BLOQUE 2: TAREAS/Tasks --}}
                            {{-- =============================================== --}}
                            <h4 class="mt-4 pt-4 mb-3 border-top">Pending Tasks</h4>
                           @if (isset($tasksData['tasks']) && count($tasksData['tasks']) > 0)
                                @foreach ($tasksData['tasks'] as $task)
                                    @php
                                        $taskStatus = $task['isCompleted'] ?? false;
                                        $isPending = $taskStatus === false || (is_string($taskStatus) && strtolower($taskStatus) === 'false');
                                        $taskBadgeClass = $isPending ? 'bg-danger' : 'bg-success';
                                        $taskBorderClass = $isPending ? 'danger' : 'success';
                                        $taskStatusText = $isPending ? 'Incomplete' : 'Completed';
                                    @endphp
                                    
                                    <div class="card mb-3 card-body p-3 border-{{ $taskBorderClass }}-lt">
                                        <div class="d-flex align-items-center">
                                            {{-- Icono de tarea --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list-check me-3 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3.5 5.5l1.5 1.5l2.5 -2.5"></path>
                                                <path d="M3.5 11.5l1.5 1.5l2.5 -2.5"></path>
                                                <path d="M3.5 17.5l1.5 1.5l2.5 -2.5"></path>
                                                <line x1="11" y1="6" x2="20" y2="6"></line>
                                                <line x1="11" y1="12" x2="20" y2="12"></line>
                                                <line x1="11" y1="18" x2="20" y2="18"></line>
                                            </svg>
                                            
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-0">
                                                    {{ $task['title'] ?? 'Task' }}
                                                </h5>
                                                <p class="text-muted mb-0 small">
                                                    Due: {{ date('m/d/Y', strtotime($task['dueDate'] ?? 'N/A')) }}
                                                </p>
                                                <p class="mb-0 text-sm mt-1">{{ $task['description'] ?? 'No description.' }}</p>
                                            </div>
                                            
                                            {{-- DISPLAY STATUS TEXT HERE --}}
                                            <span class="badge ms-auto {{ $taskBadgeClass }}">
                                                {{ $taskStatusText }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4.95 -4.95l-10.5 10.5v4"></path>
                                            <line x1="14.5" y1="5.5" x2="18.5" y2="9.5"></line>
                                            <line x1="8" y1="16" x2="10" y2="16"></line>
                                            <line x1="9" y1="17" x2="10" y2="16"></line>
                                        </svg>
                                    </div>
                                    <p class="empty-title">No Pending Tasks</p>
                                    <p class="empty-subtitle text-muted">This client has no active tasks in GoHighLevel.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                
                
            </div>
        </div>
    </div>
@endsection