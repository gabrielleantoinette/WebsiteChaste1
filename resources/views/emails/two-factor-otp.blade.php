<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi 2FA</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #D9F2F2; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #333; margin: 0;">CHASTE</h1>
    </div>
    
    <div style="background-color: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 10px 10px;">
        <h2 style="color: #333; margin-top: 0;">Kode OTP Verifikasi 2FA</h2>
        
        <p>Halo <strong>{{ $name }}</strong>,</p>
        
        <p>Terima kasih telah menggunakan layanan CHASTE. Untuk melanjutkan proses login/registrasi, silakan masukkan kode OTP berikut:</p>
        
        <div style="background-color: #f5f5f5; padding: 20px; text-align: center; border-radius: 5px; margin: 20px 0;">
            <h1 style="color: #333; font-size: 36px; letter-spacing: 5px; margin: 0;">{{ $otp }}</h1>
        </div>
        
        <p style="color: #666; font-size: 14px;"><strong>Catatan:</strong> Kode OTP ini berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.</p>
        
        <p>Jika Anda tidak melakukan permintaan ini, silakan abaikan email ini.</p>
        
        <p style="margin-top: 30px;">Salam,<br><strong>Tim CHASTE</strong></p>
    </div>
</body>
</html>

