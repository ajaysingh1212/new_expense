<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Restricted</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f8fafc; font-family: Arial, sans-serif; color: #1f2937; }
        .box { width: min(92vw, 460px); background: #fff; border-radius: 12px; box-shadow: 0 20px 60px rgba(15, 23, 42, .15); padding: 28px; text-align: center; border-top: 4px solid #ef4444; }
        .icon { width: 58px; height: 58px; border-radius: 50%; display: inline-grid; place-items: center; background: #fee2e2; color: #dc2626; font-size: 24px; margin-bottom: 14px; }
        h1 { font-size: 22px; margin: 0 0 10px; }
        p { color: #64748b; line-height: 1.6; margin: 0 0 14px; }
        .meta { background: #f1f5f9; border-radius: 8px; padding: 10px; font-size: 13px; color: #475569; margin-bottom: 18px; word-break: break-word; }
        a { display: inline-block; background: #4f46e5; color: #fff; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon"><i class="fas fa-lock"></i></div>
        <h1>Access Restricted</h1>
        <p>Your login configuration is not approved for this device or network. Please contact the developer or system administrator to restore access.</p>
        <div class="meta">
            IP: {{ $ip }}
            @if($reason)
                <br>Reason: {{ $reason }}
            @endif
        </div>
        <a href="{{ route('login') }}">Back to Login</a>
    </div>
    <script>
        alert('Your login configuration is not approved for this device or network. Please contact the developer or system administrator.');
    </script>
</body>
</html>
