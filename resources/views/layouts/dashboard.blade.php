<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Painel') — PIGEA</title>
  <link rel="icon" type="image/png" href="{{ asset_v('assets/images/favicon.png') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/plugins.css') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/color.css') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/fonts.css') }}">
  <link rel="stylesheet" href="{{ asset_v('assets/css/custom.css') }}">
</head>
<body class="dashboard-body">
  <div class="dashboard-wrap">
    <!-- SIDEBAR -->
    <aside class="dashboard-sidebar">
      <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}"><img src="{{ asset_v('assets/images/logo2.png') }}" alt="PIGEA"></a>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
          </li>
          @if(auth()->user()->isAdmin())
            <li class="sidebar-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
              <a href="{{ route('admin.users') }}"><i class="fa fa-users"></i> Utilizadores</a>
            </li>
          @endif
          <li class="sidebar-item {{ request()->routeIs('uploads.images') ? 'active' : '' }}">
            <a href="{{ route('uploads.images') }}"><i class="fa fa-images"></i> Imagens</a>
          </li>
          <li class="sidebar-item {{ request()->routeIs('uploads.documents') ? 'active' : '' }}">
            <a href="{{ route('uploads.documents') }}"><i class="fa fa-file"></i> Documentos</a>
          </li>
          @if(auth()->user()->isAdmin())
            <li class="sidebar-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
              <a href="{{ route('admin.settings') }}"><i class="fa fa-cog"></i> Definições</a>
            </li>
          @endif
          <li class="sidebar-item sidebar-logout">
            <a href="#" onclick="document.getElementById('logout-form').submit(); return false;">
              <i class="fa fa-sign-out"></i> Sair
            </a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- MAIN -->
    <div class="dashboard-main">
      <header class="dashboard-header">
        <div class="header-left">
          <button class="sidebar-toggle" aria-label="Menu"><i class="fa fa-bars"></i></button>
        </div>
        <div class="header-right">
          <div class="header-user">
            <img src="{{ auth()->user()->photo() }}" alt="{{ auth()->user()->name }}" onerror="this.src='{{ asset_v('assets/images/avatar/1.jpg') }}'">
            <span class="header-user-name">{{ auth()->user()->name }}<small>{{ auth()->user()->isAdmin() ? 'Admin' : 'User' }}</small></span>
          </div>
          <a href="#" class="header-logout" aria-label="Sair" onclick="document.getElementById('logout-form').submit(); return false;"><i class="fa fa-sign-out"></i></a>
        </div>
      </header>

      <div class="dashboard-breadcrumb">
        <ul>
          <li><a href="{{ route('dashboard') }}">Início</a></li>
          @yield('breadcrumb')
          <li class="active">@yield('breadcrumb-active', 'Painel')</li>
        </ul>
      </div>

      <main class="dashboard-content">
        @if(session('success'))
          <div class="flash-msg"><i class="fa fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="flash-msg is-error"><i class="fa fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
          <div class="flash-msg is-error"><i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}</div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

  {{-- Modal de confirmação de ação (substitui confirm() nativo) --}}
  <div class="dashboard-modal" id="confirm-modal">
    <div class="dashboard-modal-content" style="width:min(420px,100%)">
      <div class="dashboard-modal-body" style="text-align:center;padding:32px 26px">
        <span class="confirm-icon"><i class="fa fa-exclamation-circle"></i></span>
        <h3 style="font-family:'Playfair Display',serif;font-size:18px;margin:12px 0 8px" data-confirm-title>Confirmar ação</h3>
        <p style="font-size:13.5px" data-confirm-message>Tem a certeza?</p>
        <div class="dashboard-form-actions" style="justify-content:center;margin-top:20px">
          <button type="button" class="btn-ghost" data-modal-close>Cancelar</button>
          <button type="button" class="btn-danger" data-confirm-yes><i class="fa fa-check"></i> <span data-confirm-yes-label>Confirmar</span></button>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset_v('assets/js/app.js') }}"></script>
  <script src="{{ asset_v('assets/js/dashboard.js') }}"></script>
  @stack('scripts')
</body>
</html>
