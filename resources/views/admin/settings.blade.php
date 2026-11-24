@extends('layouts.dashboard')

@section('title', 'Definições')
@section('breadcrumb')
  <li><a href="{{ route('admin.settings') }}">Admin</a></li>
@endsection
@section('breadcrumb-active', 'Definições')

@section('content')
  <div class="card">
    <div class="card-head">
      <h3 class="card-title">Nomenclatura dos ficheiros</h3>
    </div>
    <div class="card-body">
      <p>Nomes gerados automaticamente: <strong>prefixo + hash único + extensão</strong>. Aplicam-se a todos os uploads seguintes.</p>

      <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')
        <div class="dashboard-form-row">
          <div class="dashboard-form-col">
            <label class="dashboard-form-label">Prefixo das imagens</label>
            <input type="text" name="prefix_image" class="dashboard-form-input" value="{{ old('prefix_image', $setting->prefix_image) }}" maxlength="20" required>
            <div class="dashboard-form-hint">Ex.: <code>IMG</code> → <code>IMG-A3F8C21B9D.png</code></div>
          </div>
          <div class="dashboard-form-col">
            <label class="dashboard-form-label">Prefixo dos documentos</label>
            <input type="text" name="prefix_document" class="dashboard-form-input" value="{{ old('prefix_document', $setting->prefix_document) }}" maxlength="20" required>
            <div class="dashboard-form-hint">Ex.: <code>DOC</code> → <code>DOC-7B2E49AD1C.pdf</code></div>
          </div>
        </div>
        <div class="dashboard-form-actions">
          <button type="submit" class="btn-primary"><i class="fa fa-save"></i> Guardar definições</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card" style="margin-top:22px">
    <div class="card-head">
      <h3 class="card-title">Categorias dos uploads</h3>
    </div>
    <div class="card-body">
      <p>As categorias organizam as listas de imagens e documentos em áreas separadas. Não é possível eliminar uma categoria com ficheiros associados.</p>

      <form method="POST" action="{{ route('admin.categories.store') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
        @csrf
        <div class="dashboard-form-group" style="margin-bottom:0;flex:1;min-width:240px">
          <label class="dashboard-form-label">Nova categoria</label>
          <input type="text" name="name" class="dashboard-form-input" placeholder="Ex.: Logótipos" maxlength="60" required>
        </div>
        <button type="submit" class="btn-primary"><i class="fa fa-plus"></i> Criar</button>
      </form>

      <div class="table-wrap" style="margin-top:18px">
        <table class="dashboard-table">
          <thead>
            <tr><th>Categoria</th><th>Ficheiros</th><th>Ações</th></tr>
          </thead>
          <tbody>
            @forelse($categories as $cat)
              <tr>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->uploads_count }}</td>
                <td>
                  <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" data-confirm="Eliminar a categoria “{{ $cat->name }}”?" data-confirm-title="Eliminar categoria" data-confirm-label="Eliminar">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn danger" title="Eliminar" {{ $cat->uploads_count > 0 ? 'disabled style="opacity:.4;cursor:not-allowed" title="Tem ficheiros associados"' : '' }}><i class="fa fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="3"><div class="empty-state"><p>Sem categorias. Crie a primeira acima.</p></div></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card" style="margin-top:22px">
    <div class="card-head">
      <h3 class="card-title">Armazenamento</h3>
    </div>
    <div class="card-body">
      <div class="dashboard-form-group">
        <label class="dashboard-form-label">URL base atual (FILES_BASE_URL)</label>
        <input type="text" class="dashboard-form-input" value="{{ rtrim(config('files.base_url'), '/') }}" readonly>
      </div>
      <div class="dashboard-form-hint">Produção: <code>FILES_BASE_URL=https://files.seudominio.com</code> no <code>.env</code>.</div>
    </div>
  </div>
@endsection
