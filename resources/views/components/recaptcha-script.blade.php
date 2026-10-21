<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
<script>
    function onClickWhatsApp(e, url, slug) {
        e.preventDefault();

        // Show Loading State
        const btn = e.target.closest('a') || e.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifikasi...';
        btn.style.pointerEvents = 'none';

        grecaptcha.ready(function () {
            grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', { action: 'whatsapp_click' }).then(function (token) {
                // Send to Backend
                fetch('{{ route('whatsapp.redirect') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        g_recaptcha_response: token,
                        target_url: url,
                        event_slug: slug,
                        website_hp: document.getElementById('website_hp_global').value // Honeypot
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = data.redirect_url;
                        } else {
                            alert('Akses Ditolak: ' + data.message);
                            btn.innerHTML = originalText;
                            btn.style.pointerEvents = 'auto';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan verifikasi.');
                        btn.innerHTML = originalText;
                        btn.style.pointerEvents = 'auto';
                    });
            });
        });
    }
</script>
<!-- Global Honeypot Field -->
<input type="text" id="website_hp_global" name="website_hp"
    style="display:none !important; visibility:hidden; height:0; width:0; opacity:0; z-index:-1;" tabindex="-1"
    autocomplete="off">