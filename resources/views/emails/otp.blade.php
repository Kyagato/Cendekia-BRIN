<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Autentikasi MojoPedia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 20px;
        }
        .email-card {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background-color: #dc2626;
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .email-header p {
            margin: 5px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .email-body {
            padding: 32px 28px;
            text-align: center;
        }
        .email-body h2 {
            margin-top: 0;
            color: #1e293b;
            font-size: 20px;
            font-weight: 700;
        }
        .email-body p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .otp-box {
            display: inline-block;
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            padding: 16px 36px;
            border-radius: 12px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 10px;
            color: #dc2626;
            margin: 10px 0 24px 0;
        }
        .warning-text {
            font-size: 12px;
            color: #94a3b8;
            background-color: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-card">
        <div class="email-header">
            <h1>MojoPedia</h1>
            <p>Badan Riset dan Inovasi Nasional</p>
        </div>
        <div class="email-body">
            <h2>Kode Autentikasi Pemulihan Password</h2>
            <p>Halo <strong>{{ $userName }}</strong>,<br>Kami menerima permintaan untuk mengosongkan/mengubah password akun Anda. Gunakan kode autentikasi 4 digit di bawah ini untuk melanjutkan:</p>
            
            <div class="otp-box">
                {{ $otp }}
            </div>

            <p>Masukkan kode ini pada halaman verifikasi di situs MojoPedia.</p>
            
            <div class="warning-text">
                🔒 Demi keamanan akun Anda, jangan berikan kode 4 digit ini kepada siapa pun. Kode ini hanya berlaku selama 15 menit.
            </div>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} MojoPedia — Sistem Informasi Manajemen Pengetahuan. All rights reserved.
        </div>
    </div>
</body>
</html>
