@extends('tablar::page')

@section('content')

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Referred Partners
                    </h2>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    @can('crear-user')
                        <div class="btn-list">
                            <a href="#" class="btn btn-primary d-sm-inline-block mb-2" data-bs-toggle="modal"
                               data-bs-target="#createUserModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Create Partners User
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="icon alert-icon">
                <path d="M12 20a8 8 0 1 1 0 -16a8 8 0 0 1 0 16z"/>
                <path d="M9 12l2 2l4 -4"/>
            </svg>
            <div>
                <h4 class="alert-heading">Success!</h4>
                <div class="alert-description">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif
    
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Referred Partners</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    @can('view email')
                                        <th>Email</th>
                                    @endcan
                                    @can('view phone')
                                        <th>Phone</th>
                                    @endcan
                                    @can('view company')
                                        <th>Company</th>
                                    @endcan
                                    <th>CREATION DATE</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($referrals as $referral)
                                    <tr>
                                        <td>{{ $referral->name }}</td>
                                        @can('view email')
                                            <td><a href="{{ route('partners.users.show', $referral->email)}}" class="text-reset"
                                               tabindex="-1">{{  ucwords(strtolower($referral->email))  ?? '—' }}</a></td>
                                        @endcan
                                        @can('view phone')
                                            <td>{{ $referral->phone ?? '—' }}</td>
                                        @endcan
                                        @can('view company')
                                            <td>{{ $referral->company ?? '—' }}</td>
                                        @endcan
                                        <td>{{ $referral->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('partners.users.destroy', $referral->id) }}" method="POST">
                                                 @csrf
                                                 @method('DELETE')
                                                 <button type="submit" class="btn btn-ghost-danger btn-icon" onclick="return confirm('¿Estás seguro de que quieres desvincular a este usuario de tu lista de referidos?');">
                                                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                     <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                     <line x1="4" y1="7" x2="20" y2="7"></line>
                                                     <line x1="10" y1="11" x2="10" y2="17"></line>
                                                     <line x1="14" y1="11" x2="14" y2="17"></line>
                                                     <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                     <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                     </svg>
                                                 </button>
                                             </form>
                                         </td>
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Para que la columna se muestre vacía, ajustamos el colspan si los campos no se muestran --}}
                                        <td colspan="{{ (Auth::user()->can('view email') ? 1 : 0) + (Auth::user()->can('view phone') ? 1 : 0) + (Auth::user()->can('view company') ? 1 : 0) + 2 }}" class="text-center text-muted">You haven't referred any users yet.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
           <form action="{{ route('partners.users.store') }}" method="POST">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel">Crear Usuario Referido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="8">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="8">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="company" class="form-label">Company (Opcional)</label>
            <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company') }}">
            @error('company')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Partner</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');

        phoneInput.addEventListener('input', function (e) {
            const x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    });
</script>
        </div>
    </div>
</div>


@if ($errors->any())
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var createUserModal = new bootstrap.Modal(document.getElementById('createUserModal'));
            createUserModal.show();
        });
    </script>
@endif


@endsection