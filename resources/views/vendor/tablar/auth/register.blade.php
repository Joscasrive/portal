@extends('tablar::auth.layout')
@section('title', 'Register')
@section('content')
    <div class="container container-tight py-4">
        <div class="text-center mb-1 mt-5">
            <a href="" class="navbar-brand navbar-brand-autodark">
                <img src="{{asset(config('tablar.auth_logo.img.path','assets/logo.svg'))}}" height="36"
                     alt=""></a>
        </div>
        <form class="card card-md" action="{{route('register')}}" method="post" autocomplete="off" novalidate>
            @csrf
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Create new account</h2>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="phone" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter phone">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" id="phone" class="form-control @error('company') is-invalid @enderror" placeholder="Enter phone">
                    @error('company')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group input-group-flat">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                               autocomplete="off">
                        <span class="input-group-text">
                  <a id="togglePassword" class="link-secondary" title="Show password" data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12"
                                                                                                            cy="12"
                                                                                                            r="2"/><path
                            d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/></svg>
                  </a>
                </span>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group input-group-flat">
                        <input type="password" id="passwordConfirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Password"
                               autocomplete="off">
                        <span class="input-group-text">
                  <a id="toggleConfirmPassword" class="link-secondary" title="Show password" data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12"
                                                                                                            cy="12"
                                                                                                            r="2"/><path
                            d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/></svg>
                  </a>
                </span>
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input"/>
                        <span class="form-check-label">Agree the <a href="#" tabindex="-1">terms and policy</a>.</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create new account</button>
                </div>
            </div>
        </form>
        <div class="text-center text-muted mt-3">
            Already have account? <a href="{{route('login')}}" tabindex="-1">Sign in</a>
        </div>
    </div>
     @if (session('error'))
<div class="modal modal-blur fade show" id="errorModal" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <div class="modal-header">
                <h5 class="modal-title">We're sorry…</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 9v2m0 4h.01"></path>
                    <path d="M12 5a7 7 0 1 1 0 14a7 7 0 0 1 0 -14"></path>
                </svg>
                <div class="text-muted">
                    {{ session('error') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Overlay backdrop --}}
<div class="modal-backdrop fade show"></div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        const modal = document.getElementById('errorModal');
        const backdrop = document.querySelector('.modal-backdrop');

        
        const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');

        
        function closeModal() {
            modal.classList.remove('show');
            modal.style.display = 'none';
            backdrop?.remove(); 
        }

        
        closeButtons.forEach(button => {
            button.addEventListener('click', closeModal);
        });
    });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    const confirmPasswordInput = document.getElementById("passwordConfirmation");

    function toggleVisibility(toggleBtn, inputField) {
      const isHidden = inputField.type === "password";
      inputField.type = isHidden ? "text" : "password";
      toggleBtn.setAttribute("title", isHidden ? "Hide password" : "Show password");

      const tooltip = bootstrap.Tooltip.getInstance(toggleBtn);
      if (tooltip) tooltip.setContent({ '.tooltip-inner': toggleBtn.getAttribute("title") });
    }

    togglePassword.addEventListener("click", function (e) {
      e.preventDefault();
      toggleVisibility(togglePassword, passwordInput);
    });

    toggleConfirmPassword.addEventListener("click", function (e) {
      e.preventDefault();
      toggleVisibility(toggleConfirmPassword, confirmPasswordInput);
    });

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
      return new bootstrap.Tooltip(el);
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
