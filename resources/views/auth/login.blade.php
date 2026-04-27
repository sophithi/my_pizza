<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza Happy Family - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #a72121 0%, #09070b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
            padding: 36px 30px;
            text-align: center;
            color: white;
        }

        .login-icon {
            font-size: 44px;
            margin-bottom: 10px;
        }

        .login-header h1 {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
        }

        .login-body {
            padding: 36px 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #1a1d29;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .form-control:focus {
            border-color: #e85d24;
            background: white;
            box-shadow: none;
            outline: none;
        }

        .form-control::placeholder {
            color: #b0b8c4;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
            border: none;
            color: white;
            padding: 11px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 4px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(232, 93, 36, 0.35);
            color: white;
        }

        .error-box {
            background: #fef2f2;
            color: #721c24;
            padding: 11px 14px;
            border-radius: 6px;
            margin-bottom: 18px;
            border-left: 3px solid #dc3545;
            font-size: 13px;
        }

        .error-text {
            color: #dc3545;
            font-size: 11px;
            margin-top: 5px;
            display: block;
        }

        .form-check {
            margin-bottom: 20px;
        }

        .form-check-input {
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #e85d24;
            border-color: #e85d24;
        }

        .form-check-label {
            margin-left: 6px;
            cursor: pointer;
            font-size: 13px;
            color: #555;
            user-select: none;
        }

        .demo-box {
            background: #f9f9f9;
            padding: 13px;
            border-radius: 6px;
            margin-top: 20px;
            text-align: center;
            border: 1px dashed #ddd;
        }

        .demo-title {
            font-size: 11px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .demo-text {
            font-size: 12px;
            color: #777;
            margin: 2px 0;
        }

        .demo-code {
            color: #e85d24;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .login-body {
                padding: 24px;
            }

            .login-header {
                padding: 24px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="login_log"></div>
                <h1>Pizza Happy Family</h1>
            </div>

            <!-- Body -->
            <div class="login-body">
                <!-- Error Messages -->
                @if($errors->any())
                    <div class="error-box">
                        <strong> Login Failed</strong>
                        @foreach($errors->all() as $err)
                            <div style="margin-top: 4px;">{{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="user1@pizzahappyfamily.com" value="{{ old('email') }}" required autofocus
                            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                        @error('email')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="••••••••"
                            required autocomplete="new-password">
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- Submit Button -->
                    <button type="submit" class="btn-login">
                        ចូលប្រើប្រាស់
                    </button>
                </form>

            </div>
        </div>
    </div>

</body>

</html>
