<!DOCTYPE html>
<html>
<head>
    <title>Undangan Event</title>
</head>
<body>
    <p>Halo {{ $invitation->participant->name ?? $invitation->participant->email }},</p>
    <p>Anda diundang untuk menghadiri event: <strong>{{ $invitation->event->title }}</strong>.</p>
    <p>Kode undangan Anda adalah: <strong>{{ $invitation->unique_code }}</strong></p>
    <p>Silakan gunakan kode ini untuk mengakses event atau informasi lebih lanjut.</p>
    <p>Terima kasih.</p>
</body>
</html>