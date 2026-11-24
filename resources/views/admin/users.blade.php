@extends('layouts.dashboard')

@section('title', 'Utilizadores')
@section('breadcrumb')
  <li><a href="{{ route('admin.users') }}">Admin</a></li>
@endsection
@section('breadcrumb-active', 'Utilizadores')

@section('content')
  <div class="card">
    <div class="card-head">
      <h3 class="card-title">Contas</h3>
      <button type="button" class="btn-primary btn-sm" data-modal-open="user-create-modal"><i class="fa fa-user-plus"></i> Nova conta</button>
    </div>
    <div class="card-body" style="padding:0">
      <div class="table-wrap">
        <table class="dashboard-table">
          <thead>
            <tr>
              <th>Utilizador</th>
              <th>E-mail</th>
              <th>Papel</th>
              <th>Uploads</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $u)
              <tr>
                <td>
                  <div class="table-user">
                    <img src="{{ $u->photo() }}" alt="{{ $u->name }}" onerror="this.src='{{ asset('assets/images/avatar/1.jpg') }}'">
                    <div>{{ $u->name }}</div>
                  </div>
                </td>
                <td>{{ $u->email }}</td>
                <td><span class="status-badge {{ $u->isAdmin() ? 'active' : 'pending' }}">{{ $u->isAdmin() ? 'Admin' : 'User' }}</span></td>
                <td>{{ $u->uploads_count }}</td>
                <td>
                  <div class="row-actions">
                    <button type="button" class="icon-btn" data-modal-open="user-edit-{{ $u->id }}" title="Editar"><i class="fa fa-user-edit"></i></button>
                    @if($u->id !== auth()->id())
                      <form method="POST" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Eliminar esta conta e todos os seus ficheiros?" data-confirm-title="Eliminar conta" data-confirm-label="Eliminar" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-btn danger" title="Eliminar"><i class="fa fa-trash"></i></button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="5"><div class="empty-state"><i class="fa fa-users"></i><p>Sem contas registadas.</p></div></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($users->hasPages())
        <div style="padding:16px 22px">{{ $users->links('vendor.pagination.pigea') }}</div>
      @endif
    </div>
  </div>

  {{-- Modal: nova conta --}}
  <div class="dashboard-modal" id="user-create-modal">
    <div class="dashboard-modal-content">
      <div class="dashboard-modal-header">
        <h3>Nova conta</h3>
        <button type="button" class="dashboard-modal-close" data-modal-close>&times;</button>
      </div>
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="dashboard-modal-body">
          <div class="dashboard-form-row">
            <div class="dashboard-form-col">
              <label class="dashboard-form-label">Nome</label>
              <input type="text" name="name" class="dashboard-form-input" value="{{ old('name') }}" required>
            </div>
            <div class="dashboard-form-col">
              <label class="dashboard-form-label">Foto de perfil (URL da imagem)</label>
              <input type="url" name="photo_url" class="dashboard-form-input" value="{{ old('photo_url') }}" placeholder="https://…/foto.jpg">
            </div>
          </div>
          <div class="dashboard-form-group">
            <label class="dashboard-form-label">E-mail</label>
            <input type="email" name="email" class="dashboard-form-input" value="{{ old('email') }}" required>
          </div>
          <div class="dashboard-form-row">
            <div class="dashboard-form-col">
              <label class="dashboard-form-label">Papel</label>
              <select name="role" class="dashboard-form-select">
                <option value="user" selected>User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="dashboard-form-col">
              <label class="dashboard-form-label">Palavra-passe</label>
              <input type="password" name="password" class="dashboard-form-input" minlength="6" required>
            </div>
          </div>
          <div class="dashboard-form-hint">Credenciais de acesso.</div>
        </div>
        <div class="dashboard-modal-footer">
          <button type="button" class="btn-ghost" data-modal-close>Cancelar</button>
          <button type="submit" class="btn-primary"><i class="fa fa-save"></i> Criar conta</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Modais: editar conta --}}
  @foreach($users as $u)
    <div class="dashboard-modal" id="user-edit-{{ $u->id }}">
      <div class="dashboard-modal-content">
        <div class="dashboard-modal-header">
          <h3>Editar — {{ $u->name }}</h3>
          <button type="button" class="dashboard-modal-close" data-modal-close>&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.users.update', $u) }}">
          @csrf
          @method('PUT')
          <div class="dashboard-modal-body">
            <div class="dashboard-form-row">
              <div class="dashboard-form-col">
                <label class="dashboard-form-label">Nome</label>
                <input type="text" name="name" class="dashboard-form-input" value="{{ old('name', $u->name) }}" required>
              </div>
              <div class="dashboard-form-col">
                <label class="dashboard-form-label">Foto de perfil (URL da imagem)</label>
                <input type="url" name="photo_url" class="dashboard-form-input" value="{{ old('photo_url', $u->photo_url) }}" placeholder="https://…/foto.jpg">
              </div>
            </div>
            <div class="dashboard-form-group">
              <label class="dashboard-form-label">E-mail</label>
              <input type="email" name="email" class="dashboard-form-input" value="{{ old('email', $u->email) }}" required>
            </div>
            <div class="dashboard-form-row">
              <div class="dashboard-form-col">
                <label class="dashboard-form-label">Papel</label>
                <select name="role" class="dashboard-form-select">
                  <option value="user" {{ old('role', $u->role) === 'user' ? 'selected' : '' }}>User</option>
                  <option value="admin" {{ old('role', $u->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
              </div>
              <div class="dashboard-form-col">
                <label class="dashboard-form-label">Nova palavra-passe (opcional)</label>
                <input type="password" name="password" class="dashboard-form-input" minlength="6" placeholder="Deixar em branco para manter">
              </div>
            </div>
          </div>
          <div class="dashboard-modal-footer">
            <button type="button" class="btn-ghost" data-modal-close>Cancelar</button>
            <button type="submit" class="btn-primary"><i class="fa fa-save"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach
@endsection
