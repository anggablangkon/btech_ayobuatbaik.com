<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Ayobuatbaik</title>
    <link rel="icon" type="image/png" href="/icon ABBI.png">
    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background: linear-gradient(to bottom, #111827, #1f2937, #111827);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #fff;
        }
        .container {
            text-align: center;
            max-width: 320px;
            margin: 0 auto;
        }
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .icon-wrapper {
            width: 120px;
            height: 120px;
            margin: 0 auto 32px;
            background: linear-gradient(135deg, #374151, #1f2937);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            border: 1px solid #4b5563;
            animation: float 3s ease-in-out infinite;
        }
        /* WiFi Off Icon (Pure CSS) */
        .wifi-icon {
            width: 48px;
            height: 48px;
            position: relative;
        }
        .wifi-icon::before,
        .wifi-icon::after {
            content: '';
            position: absolute;
            border: 4px solid #9ca3af;
            border-radius: 50%;
            border-bottom-color: transparent;
            border-left-color: transparent;
            border-right-color: transparent;
        }
        .wifi-icon::before {
            width: 48px;
            height: 48px;
            top: 0;
            left: 0;
        }
        .wifi-icon::after {
            width: 28px;
            height: 28px;
            top: 10px;
            left: 10px;
        }
        .wifi-dot {
            width: 10px;
            height: 10px;
            background: #9ca3af;
            border-radius: 50%;
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
        }
        .wifi-slash {
            position: absolute;
            width: 56px;
            height: 4px;
            background: #ef4444;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            border-radius: 2px;
        }
        /* Text */
        h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #fff;
        }
        p {
            font-size: 13px;
            color: #9ca3af;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .highlight {
            color: #fbbf24;
            font-weight: 600;
        }
        /* Buttons */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #f59e0b, #d97706, #b45309);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 16px 28px;
            border-radius: 16px;
            text-decoration: none;
            box-shadow: 0 10px 40px -10px rgba(245, 158, 11, 0.5);
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            filter: brightness(1.1);
            transform: scale(1.02);
        }
        .btn-primary:active {
            transform: scale(0.98);
        }
        /* Book Icon (Pure CSS) */
        .book-icon {
            width: 18px;
            height: 14px;
            border: 2px solid #fff;
            border-radius: 0 3px 3px 0;
            position: relative;
        }
        .book-icon::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 0;
            width: 2px;
            height: 100%;
            background: #fff;
            border-radius: 2px 0 0 2px;
        }
        /* Retry Button */
        .btn-retry {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 14px;
            background: transparent;
            border: none;
            color: #6b7280;
            font-size: 12px;
            cursor: pointer;
            transition: color 0.2s;
        }
        .btn-retry:hover {
            color: #d1d5db;
        }
        /* Footer */
        .footer {
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid rgba(75, 85, 99, 0.3);
        }
        .footer img {
            width: 40px;
            height: 40px;
            opacity: 0.4;
        }
        .footer p {
            font-size: 10px;
            color: #4b5563;
            margin-top: 8px;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Animated Icon -->
        <div class="icon-wrapper">
            <div class="wifi-icon">
                <div class="wifi-dot"></div>
                <div class="wifi-slash"></div>
            </div>
        </div>

        <!-- Message -->
        <h1>Kamu Sedang Offline</h1>
        <p>
            Koneksi internet terputus.<br>
            Tapi tenang, kamu tetap bisa baca<br>
            <span class="highlight">Kitab Kitab</span>!
        </p>

        <!-- Kitab Button -->
        <a href="/kitab" class="btn-primary">
            <span class="book-icon"></span>
            Buka Kitab Kitab
        </a>

        <!-- Retry -->
        <button class="btn-retry" onclick="window.location.reload()">
            ↻ Coba Sambungkan Ulang
        </button>

        <!-- Footer -->
        <div class="footer">
            <img src="/icon ABBI.png" alt="Ayobuatbaik" onerror="this.style.display='none'">
            <p>Ayobuatbaik</p>
        </div>
    </div>
</body>
</html>