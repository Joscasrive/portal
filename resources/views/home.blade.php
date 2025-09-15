@extends('tablar::page')

@section('content')
   
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="col">
                
                <div class="page-pretitle mb-1">
                    Overview
                </div>
                <h2 class="page-title mb-1">
                    Affiliate Portal Dashboard
                </h2>
            </div>
    <div class="card">
    <div class="card-header">
        <h3 class="card-title">Affiliate Registration Link</h3>
        <div class="card-actions">
            <button class="btn btn-icon btn-ghost-primary" id="copyLinkBtn" title="Copiar enlace">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-copy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
                    <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />
                </svg>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="mb-3 p-3 bg-light rounded" id="linkContainer">
            <code class="text-truncate d-block" id="affiliateLink">https://api.leadconnectorhq.com/widget/bookings/credfixx/freeconsultationreferrals?partner={{ Auth::user()->email }}</code>
        </div>
    </div>
</div>
           
            <div class="row align-items-center mt-0">
                <div class="col-12">
                    
                    <iframe src="https://api.leadconnectorhq.com/widget/booking/IGkImE1js86XxYfUN1EE" 
                            style="width: 100%; border: none; overflow: hidden;" 
                            scrolling="no"
                            id="GQbcGa2cdr06vF6FxDYz_1746672947394">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://link.msgsndr.com/js/form_embed.js" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const copyButton = document.getElementById('copyLinkBtn');
    const affiliateLink = document.getElementById('affiliateLink').textContent;

    copyButton.addEventListener('click', function() {
        // Use the modern Clipboard API
        navigator.clipboard.writeText(affiliateLink)
            .then(() => {
                alert('¡Enlace copiado al portapapeles!');
                // Optional: Provide visual feedback on success (e.g., change icon temporarily)
                copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>';
                setTimeout(() => {
                    // Revert back to the original icon after a short delay
                    copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-copy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>';
                }, 2000);
            })
            .catch(err => {
                console.error('Error al copiar el enlace: ', err);
                alert('Error al copiar el enlace. Por favor, cópielo manualmente.');
            });
    });
});
    </script>
@endsection
