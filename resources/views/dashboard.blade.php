@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('breadcrumb-active', 'Dashboard')

@section('content')
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-card-icon"><i class="fa fa-upload"></i></div>
      <div class="stat-card-body">
        <div class="stat-card-number">{{ number_format($stats['total']) }}</div>
        <div class="stat-card-label">Total de uploads</div>
        <div class="stat-card-footer">{{ auth()->user()->isAdmin() ? 'Toda a plataforma' : 'Os seus ficheiros' }}</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-card-icon"><i class="fa fa-images"></i></div>
      <div class="stat-card-body">
        <div class="stat-card-number">{{ number_format($stats['images']) }}</div>
        <div class="stat-card-label">Imagens</div>
        <div class="stat-card-footer"><a href="{{ route('uploads.images') }}">Ver secção</a></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-card-icon"><i class="fa fa-file"></i></div>
      <div class="stat-card-body">
        <div class="stat-card-number">{{ number_format($stats['documents']) }}</div>
        <div class="stat-card-label">Documentos</div>
        <div class="stat-card-footer"><a href="{{ route('uploads.documents') }}">Ver secção</a></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-card-icon"><i class="fa fa-chart-bar"></i></div>
      <div class="stat-card-body">
        <div class="stat-card-number">{{ number_format($stats['week']) }}</div>
        <div class="stat-card-label">Últimos 7 dias</div>
        <div class="stat-card-footer">Novos uploads</div>
      </div>
    </div>
  </div>

  <div class="card" style="margin-top:22px">
    <div class="card-head">
      <h3 class="card-title">Ações rápidas</h3>
    </div>
    <div class="card-body">
      <div class="dashboard-form-actions" style="margin-top:0">
        <a href="{{ route('uploads.images') }}" class="btn-primary"><i class="fa fa-cloud-upload-alt"></i> Imagem</a>
        <a href="{{ route('uploads.documents') }}" class="btn-primary"><i class="fa fa-file-pdf"></i> Documento</a>
        @if(auth()->user()->isAdmin())
          <a href="{{ route('admin.users') }}" class="btn-ghost"><i class="fa fa-users"></i> Utilizadores</a>
          <a href="{{ route('admin.settings') }}" class="btn-ghost"><i class="fa fa-cog"></i> Definições</a>
        @endif
      </div>
    </div>
  </div>

  <div class="card" style="margin-top:22px">
    <div class="card-head">
      <h3 class="card-title">Uploads recentes</h3>
      <a href="{{ route('uploads.images') }}" class="btn-ghost btn-sm">Ver todos</a>
    </div>
    <div class="card-body" style="padding:0">
      @if($recent->isEmpty())
        <div class="empty-state">
          <i class="fa fa-folder"></i>
          <p>Sem uploads ainda.</p>
        </div>
      @else
        <div class="table-wrap">
          <table class="dashboard-table">
            <thead>
              <tr>
                <th>Ficheiro</th>
                <th>Categoria</th>
                @if(auth()->user()->isAdmin())<th>Enviado por</th>@endif
                <th>Tipo</th>
                <th>Data</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recent as $u)
                <tr>
                  <td>
                    <div class="table-file">
                      <span class="table-icon"><i class="{{ $u->isImage() ? 'fa fa-image' : 'fa fa-file' }}"></i></span>
                      <div>
                        <strong>{{ $u->file_name }}</strong>
                        <small>{{ $u->sizeHuman() }}</small>
                      </div>
                    </div>
                  </td>
                  @if(auth()->user()->isAdmin())<td>{{ $u->user->name }}</td>@endif
                  <td><span class="status-badge pending">{{ $u->categoryRef->name ?? '—' }}</span></td>
                  <td><span class="status-badge {{ $u->isImage() ? 'active' : 'pending' }}">{{ $u->isImage() ? 'Imagem' : 'Documento' }}</span></td>
                  <td>{{ $u->created_at->format('d/m/Y H:i') }}</td>
                  <td>
                    <div class="row-actions">
                      <a href="{{ $u->url }}" target="_blank" rel="noopener" class="icon-btn" title="Abrir"><i class="fa fa-eye"></i></a>
                      <button type="button" class="icon-btn" data-copy="{{ $u->url }}" title="Copiar URL"><i class="fa fa-copy"></i></button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
@endsection
