<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing Store Auth</title>
    <style>
        body {
            background: #fafafa;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .auth-container {
            max-width: 350px;
            margin: 60px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 32px 24px;
        }
        h2 {
            margin-bottom: 24px;
            font-weight: 500;
            text-align: center;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #222;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #444;
        }
        p {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }
        a {
            color: #222;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
