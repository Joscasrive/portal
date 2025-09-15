@extends('tablar::page')

@section('content')
  <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="col">
                
                <div class="page-pretitle mb-1">
                    Overview
                </div>
                <h2 class="page-title mb-1">
                    ✅ Select your referred client here
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

    <form  method="POST">
          @csrf
  <div class="mb-3">
    <label class="form-label">Search Client</label>
    <input type="email" class="form-control" name="customerEmail" placeholder="client@example.com" required />
    <div id="resultadosClientes" class="mt-3">
    </div>
  </div>
<div class="mb-3">
    <label class="form-label">Email of the referred client</label>
    <input type="email" class="form-control" name="referredClient" placeholder="someone@example.com" required />
  </div> 
  <div class="mb-3">
    <label class="form-label">Referred By (For Clients)</label>
    <input type="text" class="form-control" name="referredBy" required />
  </div>

  <div class="mb-3">
    <label class="form-label">Partner Email (For Clients)</label>
    <input type="email" class="form-control" name="partnerEmail" placeholder="someone@example.com" required />
  </div>
  <div class="mb-3">
    <label class="form-label">Partner Phone (For Clients)</label>
    <input type="text" class="form-control" name="partnerPhone" required />
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
        <h4 class="alert-heading">¡Éxito!</h4>
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

        
            
    


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputCorreoCliente = document.querySelector('input[name="customerEmail"]');
        const inputReferredBy = document.querySelector('input[name="referredBy"]');
        const inputPartnerEmail = document.querySelector('input[name="partnerEmail"]'); 
        const inputPartnerPhone = document.querySelector('input[name="partnerPhone"]'); 

        const resultadosClientesDiv = document.getElementById('resultadosClientes');
        let temporizadorBusqueda;

        inputCorreoCliente.addEventListener('keyup', function() {
            clearTimeout(temporizadorBusqueda);
            const email = this.value;

            if (email.length >= 3 && email.includes('@')) {
                temporizadorBusqueda = setTimeout(() => {
                    fetch('{{ route('searchClient') }}?email=' + encodeURIComponent(email))
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('La respuesta de red no fue correcta.');
                            }
                            return response.json();
                        })
                        .then(data => {
                            mostrarResultadosBusqueda(data);
                        })
                        .catch(error => {
                            console.error('Error al obtener datos del cliente:', error);
                            resultadosClientesDiv.innerHTML = '<div class="alert alert-danger">Error al buscar clientes. Por favor, inténtelo de nuevo.</div>';
                        });
                }, 500);
            } else {
                resultadosClientesDiv.innerHTML = '';
            }
        });

        function mostrarResultadosBusqueda(data) {
            resultadosClientesDiv.innerHTML = '';

            if (data && data.contacts && data.contacts.length > 0) {
                let html = '<div class="card"><div class="card-header"><h3 class="card-title">Cliente Encontrado</h3></div><div class="list-group list-group-flush">';
                data.contacts.forEach(contact => {
                    const fullName = (contact.firstName || '') + ' ' + (contact.lastName || '');

                    html += `
                        <a href="#" class="list-group-item list-group-item-action"
                           data-email="${contact.email || ''}"
                           data-nombre-completo="${fullName.trim()}"
                           data-phone="${contact.phone || ''}" > <strong>${fullName.trim() || 'Nombre Desconocido'}</strong><br>
                            <small>${contact.email || 'N/A'}</small><br>
                            <small>${contact.phone || 'N/A'}</small>
                        </a>
                    `;
                });
                html += '</div></div>';
                resultadosClientesDiv.innerHTML = html;

                document.querySelectorAll('.list-group-item-action').forEach(item => {
                    item.addEventListener('click', function(event) {
                        event.preventDefault();

                        // Rellena el campo de 'Customer email'
                        inputCorreoCliente.value = this.dataset.email;

                        // Rellena el campo 'Referred By' con el nombre completo
                        inputReferredBy.value = this.dataset.nombreCompleto;

                        // Rellena el campo 'Partner Email' con el email del cliente
                        inputPartnerEmail.value = this.dataset.email;

                        // Rellena el campo 'Partner Phone' con el teléfono del cliente
                        inputPartnerPhone.value = this.dataset.phone; // Usamos el nuevo atributo data-phone

                        resultadosClientesDiv.innerHTML = ''; // Oculta los resultados
                    });
                });

            } else {
                resultadosClientesDiv.innerHTML = '<div class="alert alert-info">No se encontraron clientes con ese correo electrónico.</div>';
            }
        }
    });
</script>

@endsection