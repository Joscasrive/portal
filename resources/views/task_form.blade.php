@extends('tablar::page')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Create Task Request
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Task Details for Client</h3>
                        </div>
                        <div class="card-body">
                            
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('tasks.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label" for="customerEmail">Client Email</label>
                                        <input type="email" name="customerEmail" id="customerEmail" 
                                               class="form-control @error('customerEmail') is-invalid @enderror" 
                                               value="{{ $customerEmail ?? old('customerEmail') }}" 
                                               required 
                                               @if(isset($customerEmail)) readonly @endif 
                                               placeholder="client@example.com">
                                        
                                        @if(isset($customerEmail))
                                            <small class="form-text text-muted">This field is read-only as the contact was selected from the table.</small>
                                        @endif
                                        @error('customerEmail')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required" for="title">Task Title</label>
                                        <input type="text" name="title" id="title" 
                                               class="form-control @error('title') is-invalid @enderror" 
                                               value="{{ old('title') }}" required placeholder="Ex: Follow-up call">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required" for="dueDateLocal">Due Date and Time</label>
                                        <input type="datetime-local" name="dueDateLocal" id="dueDateLocal" 
                                               class="form-control @error('dueDate') is-invalid @enderror" 
                                               value="{{ old('dueDateLocal', now()->format('Y-m-d\TH:i')) }}" required>
                                        
                                        <input type="hidden" id="dueDate" name="dueDate">

                                        @error('dueDate')
                                            <div class="invalid-feedback">
                                                {{ $message }} Please ensure you select a valid date and time.
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label required" for="description">Detailed Description</label>
                                        <textarea name="description" id="description" rows="4" 
                                                  class="form-control @error('description') is-invalid @enderror" 
                                                  required placeholder="Provide the context or steps to be followed.">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">Create Task</button>
                                    <a href="#" onclick="history.back(); return false;" class="btn btn-secondary">Cancel</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const localInput = document.getElementById('dueDateLocal');
            const hiddenInput = document.getElementById('dueDate');

           
            const formatForGHL = (localDateTime) => {
                if (!localDateTime) return '';

                const date = new Date(localDateTime);
                if (isNaN(date.getTime())) return '';
                
                
                const offset = date.getTimezoneOffset(); 
                const sign = offset < 0 ? '+' : '-';
                const absOffset = Math.abs(offset);
                const hours = String(Math.floor(absOffset / 60)).padStart(2, '0');
                const minutes = String(absOffset % 60).padStart(2, '0');
                const timezone = `${sign}${hours}:${minutes}`;

               
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hour = String(date.getHours()).padStart(2, '0');
                const minute = String(date.getMinutes()).padStart(2, '0');

                
                return `${year}-${month}-${day}T${hour}:${minute}:00${timezone}`;
            };

            
            const updateHiddenInput = () => {
                const localValue = localInput.value;
                hiddenInput.value = formatForGHL(localValue);
            };

            updateHiddenInput();

            localInput.addEventListener('change', updateHiddenInput);
            localInput.addEventListener('input', updateHiddenInput);

            const form = localInput.closest('form');
            if (form) {
                form.addEventListener('submit', updateHiddenInput);
            }
        });
    </script>
@endsection