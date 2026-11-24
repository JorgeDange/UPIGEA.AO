<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Entrar — PIGEA Uploads</title>
  <link rel="icon" type="image/png" href="{{ asset_v('assets/images/favicon.png') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/plugins.css') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/color.css') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/fonts.css') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/custom.css') }}">
</head>
<body class="dashboard-body">
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="auth-logo">
        <img src="{{ asset_v('assets/images/logo.png') }}" alt="PIGEA">
      </div>
      <h1>Painel de uploads</h1>
      <p class="auth-subtitle">Gestão de uploads — entre com as suas credenciais</p>

      <form class="auth-form" method="POST" action="{{ route('login.attempt') }}">
        @csrf

        @if($errors->any())
          <div class="flash-msg is-error" style="margin-top:16px;">
            <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
          </div>
        @endif

        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="o.seu@email.com" required autofocus>
        </div>
        <div class="form-group">
          <label for="password">Palavra-passe</label>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="auth-form-row">
          <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Manter sessão ativa
          </label>
        </div>

        <button type="submit" class="btn-primary btn-block"><i class="fa fa-sign-in"></i> Entrar</button>
      </form>

      <p class="auth-footer">As contas são criadas pelo administrador.</p>
    </div>
  </div>
</body>
</html>
