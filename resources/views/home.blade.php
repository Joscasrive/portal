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

            <div class="row align-items-center mt-3">
                <div class="col-12 text-center mb-3">
                    <button id="showCitaBtn" class="btn btn-primary active">Referral Appointment</button>
                    <button id="showLeadBtn" class="btn btn-secondary">Referral Lead</button>
                </div>
            </div>

            <div class="row align-items-center mt-0">
                <div class="col-12">
                    <div id="cita-form-container">
                        <iframe src="https://api.leadconnectorhq.com/widget/booking/IGkImE1js86XxYfUN1EE"
                                style="width: 100%; border: none; overflow: hidden;"
                                scrolling="no"
                                id="citaIframe">
                        </iframe>
                    </div>

                    <div id="lead-form-container" style="display: none;">
                        <iframe
                            src="https://api.leadconnectorhq.com/widget/form/bhmSZ0ks74f5yEe0FhXb"
                            style="width:100%;height:100%;border:none;border-radius:3px"
                            id="leadIframe"
                            data-layout="{'id':'INLINE'}"
                            data-trigger-type="alwaysShow"
                            data-trigger-value=""
                            data-activation-type="alwaysActivated"
                            data-activation-value=""
                            data-deactivation-type="neverDeactivate"
                            data-deactivation-value=""
                            data-form-name="Referral "
                            data-height="994"
                            data-layout-iframe-id="inline-bhmSZ0ks74f5yEe0FhXb"
                            data-form-id="bhmSZ0ks74f5yEe0FhXb"
                            title="Referral "
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://link.msgsndr.com/js/form_embed.js" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const copyButton = document.getElementById('copyLinkBtn');
            const affiliateLink = document.getElementById('affiliateLink').textContent;
            const showCitaBtn = document.getElementById('showCitaBtn');
            const showLeadBtn = document.getElementById('showLeadBtn');
            const citaFormContainer = document.getElementById('cita-form-container');
            const leadFormContainer = document.getElementById('lead-form-container');

            // Copy link functionality
            copyButton.addEventListener('click', function() {
                navigator.clipboard.writeText(affiliateLink)
                    .then(() => {
                        alert('¡Enlace copiado al portapapeles!');
                        copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>';
                        setTimeout(() => {
                            copyButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-copy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>';
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Error al copiar el enlace: ', err);
                        alert('Error al copiar el enlace. Por favor, cópielo manualmente.');
                    });
            });

            // Toggle form visibility
            showCitaBtn.addEventListener('click', function() {
                citaFormContainer.style.display = 'block';
                leadFormContainer.style.display = 'none';
                showCitaBtn.classList.add('active');
                showCitaBtn.classList.remove('btn-secondary');
                showCitaBtn.classList.add('btn-primary');
                showLeadBtn.classList.remove('active');
                showLeadBtn.classList.remove('btn-primary');
                showLeadBtn.classList.add('btn-secondary');
            });

            showLeadBtn.addEventListener('click', function() {
                citaFormContainer.style.display = 'none';
                leadFormContainer.style.display = 'block';
                showLeadBtn.classList.add('active');
                showLeadBtn.classList.remove('btn-secondary');
                showLeadBtn.classList.add('btn-primary');
                showCitaBtn.classList.remove('active');
                showCitaBtn.classList.remove('btn-primary');
                showCitaBtn.classList.add('btn-secondary');
            });
        });
    </script>
@endsection