<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #D9F2F2; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #333; margin: 0;">CHASTE</h1>
    </div>
    
    <div style="background-color: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 10px 10px;">
        <h2 style="color: #333; margin-top: 0;">Reset Password</h2>
        
        <p>Halo <strong>{{ $name }}</strong>,</p>
        
        <p>Kami menerima permintaan untuk mereset password akun Anda. Klik tombol di bawah ini untuk mereset password Anda:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/reset-password?token=' . $token . '&email=' . urlencode($email)) }}" 
               style="background-color: #D9F2F2; color: #333; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Reset Password
            </a>
        </div>
        
        <p style="color: #666; font-size: 14px;"><strong>Catatan:</strong> Link ini berlaku selama 60 menit. Jika Anda tidak melakukan permintaan ini, silakan abaikan email ini.</p>
        
        <p style="color: #666; font-size: 12px; margin-top: 20px;">Atau salin link berikut ke browser Anda:<br>
        <span style="word-break: break-all;">{{ url('/reset-password?token=' . $token . '&email=' . urlencode($email)) }}</span></p>
        
        <p style="margin-top: 30px;">Salam,<br><strong>Tim CHASTE</strong></p>
    </div>
</body>
</html>

