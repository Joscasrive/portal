@extends('tablar::page')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Edit User
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Form</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="phone">Phone</label>
                                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="company">Company (Optional)</label>
                                        <input type="text" name="company" id="company" class="form-control @error('company') is-invalid @enderror" value="{{ old('company', $user->company) }}">
                                        @error('company')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="roles">Role</label>
                                        <select name="roles" id="roles" class="form-control @error('roles') is-invalid @enderror" required>
                                            <option value="">Select a role</option>
                                            @foreach ($roles as $roleName => $roleLabel)
                                                <option value="{{ $roleName }}" {{ in_array($roleName, $userRole) ? 'selected' : '' }}>{{ ucwords($roleLabel) }}</option>
                                            @endforeach
                                        </select>
                                        @error('roles')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="is_commissionable">Is Commissionable</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="is_commissionable" name="is_commissionable" value="1" {{ old('is_commissionable', $user->is_commissionable) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_commissionable"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="commissionPercentageGroup" style="{{ old('is_commissionable', $user->is_commissionable) ? '' : 'display:none;' }}">
                                    <div class="col-12 mb-3">
                                        <label class="form-label" for="commission_percentage">Commission Amount</label>
                                        <input type="number" step="0.01" name="commission_percentage" id="commission_percentage" class="form-control @error('commission_percentage') is-invalid @enderror" value="{{ old('commission_percentage', $user->commission_percentage) }}">
                                        @error('commission_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h4>User Permissions</h4>
                                        <div class="mb-3">
                                            @foreach ($permissionsToShow as $permissionName)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="{{ Str::slug($permissionName) }}_permission" name="permissions[]" value="{{ $permissionName }}" {{ in_array($permissionName, $userPermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ Str::slug($permissionName) }}_permission">
                                                        {{ ucwords(str_replace('-', ' ', $permissionName)) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">Update User</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
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
            const commissionableToggle = document.getElementById('is_commissionable');
            const commissionPercentageGroup = document.getElementById('commissionPercentageGroup');

            commissionableToggle.addEventListener('change', function() {
                if (this.checked) {
                    commissionPercentageGroup.style.display = 'block';
                } else {
                    commissionPercentageGroup.style.display = 'none';
                }
            });
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');

        phoneInput.addEventListener('input', function (e) {
            const x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    });
</script>
@endsection