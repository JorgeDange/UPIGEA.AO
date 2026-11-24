{{-- Lista de uploads (usada por Imagens e Documentos) --}}
{{-- Abas de categoria --}}
<div class="dashboard-filters" style="margin-bottom:0">
  <a href="{{ route($type === 'image' ? 'uploads.images' : 'uploads.documents') }}"
     class="btn-sm {{ !$categoria ? 'btn-primary' : 'btn-ghost' }}">Todas ({{ $total }})</a>
  @foreach($categories as $cat)
    <a href="{{ route($type === 'image' ? 'uploads.images' : 'uploads.documents', ['categoria' => $cat->slug]) }}"
       class="btn-sm {{ $categoria === $cat->slug ? 'btn-primary' : 'btn-ghost' }}">{{ $cat->name }} ({{ $cat->uploads_count }})</a>
  @endforeach
</div>

<div class="card" style="margin-top:16px">
  <div class="card-head">
    <h3 class="card-title">{{ $type === 'image' ? 'Imagens' : 'Documentos' }} {{ $categoria ? '— ' . ($categories->firstWhere('slug', $categoria)->name ?? '') : 'carregados' }} <span class="spb-categories_counter" style="display:inline-block;margin-left:8px">{{ $uploads->total() }}</span></h3>
    <button type="button" class="btn-primary btn-sm" data-modal-open="upload-modal"><i class="fa fa-plus"></i> Novo upload</button>
  </div>
  <div class="card-body" style="padding:0">
    @if($uploads->isEmpty())
      <div class="empty-state">
        <i class="fa {{ $type === 'image' ? 'fa-images' : 'fa-file' }}"></i>
        <p>Sem {{ $type === 'image' ? 'imagens' : 'documentos' }} carregados.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="dashboard-table">
          <thead>
            <tr>
              <th>Ficheiro</th>
              <th>Categoria</th>
              @if(auth()->user()->isAdmin())<th>Enviado por</th>@endif
              <th>Tamanho</th>
              <th>Data</th>
              <th>URL pública</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($uploads as $u)
              <tr>
                <td>
                  <div class="table-file">
                    @if($u->isImage())
                      <img class="table-thumb" src="{{ $u->url }}" alt="{{ $u->file_name }}" loading="lazy">
                    @else
                      <span class="table-icon"><i class="{{ $u->isPdf() ? 'fa fa-file-pdf' : 'fa fa-file' }}"></i></span>
                    @endif
                    <div>
                      <strong>{{ $u->file_name }}</strong>
                      <small>{{ $u->original_name }}</small>
                    </div>
                  </div>
                </td>
                <td><span class="status-badge pending">{{ $u->categoryRef->name ?? '—' }}</span></td>
                @if(auth()->user()->isAdmin())<td>{{ $u->user->name }}</td>@endif
                <td>{{ $u->sizeHuman() }}</td>
                <td>{{ $u->created_at->format('d/m/Y H:i') }}</td>
                <td>
                  <div class="cell-url">
                    <code title="{{ $u->url }}">{{ $u->url }}</code>
                    <button type="button" class="icon-btn" data-copy="{{ $u->url }}" title="Copiar URL"><i class="fa fa-copy"></i></button>
                  </div>
                </td>
                <td>
                  <div class="row-actions">
                    <button type="button" class="icon-btn" data-modal-open="preview-modal" data-preview-src="{{ $u->url }}" data-preview-kind="{{ $u->isImage() ? 'image' : ($u->isPdf() ? 'pdf' : 'other') }}" data-preview-name="{{ $u->file_name }}" title="Pré-visualizar"><i class="fa fa-eye"></i></button>
                    <a href="{{ $u->url }}" target="_blank" rel="noopener" class="icon-btn" title="Abrir em nova aba"><i class="fa fa-link"></i></a>
                    <form method="POST" action="{{ route('uploads.destroy', $u) }}" data-confirm="Eliminar este ficheiro permanentemente?" data-confirm-title="Eliminar ficheiro" data-confirm-label="Eliminar" style="display:inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="icon-btn danger" title="Eliminar"><i class="fa fa-trash"></i></button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($uploads->hasPages())
        <div style="padding:16px 22px">
          {{ $uploads->links('vendor.pagination.pigea') }}
        </div>
      @endif
    @endif
  </div>
</div>

{{-- Modal de novo upload --}}
<div class="dashboard-modal" id="upload-modal">
  <div class="dashboard-modal-content">
    <div class="dashboard-modal-header">
      <h3>Novo upload — {{ $type === 'image' ? 'Imagem' : 'Documento' }}</h3>
      <button type="button" class="dashboard-modal-close" data-modal-close>&times;</button>
    </div>
    <div class="dashboard-modal-body" data-upload-box data-upload-type="{{ $type }}">
      <label class="file-drop" for="file-input-{{ $type }}" data-file-drop>
        <i class="fa fa-cloud-upload-alt"></i>
        <span class="file-drop-title">Clique para escolher ou arraste o ficheiro aqui</span>
        <span class="file-drop-hint">{{ $type === 'image' ? 'Imagens · JPG, PNG, GIF, WEBP, SVG' : 'Documentos · PDF, DOC, XLS, PPT, TXT, CSV, ZIP' }} · máx. 10 MB</span>
        <input type="file" id="file-input-{{ $type }}" name="file" accept="{{ $type === 'image' ? '.jpg,.jpeg,.png,.gif,.webp,.svg,.avif' : '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.zip' }}" hidden>
      </label>
      <div class="file-selected" data-file-selected hidden>
        <span class="table-icon"><i class="fa fa-file"></i></span>
        <div>
          <strong data-file-name>—</strong>
          <small data-file-size></small>
        </div>
        <button type="button" class="icon-btn" data-file-clear title="Remover ficheiro"><i class="fa fa-times"></i></button>
      </div>

      <div style="margin-top:16px;padding:14px 16px;border:1px solid #E0E0E0;border-radius:6px;background:#FAFAFA">
        <span class="dashboard-form-label">Nome atribuído ao ficheiro</span>
        <div data-suggested-name style="font-family:monospace;font-size:14px;font-weight:600;color:#1A1A1A">—</div>
        <div class="dashboard-form-hint">Prefixo + hash único + extensão.</div>
      </div>

      <div class="progress-wrap">
        <div class="progress-track"><div class="progress-fill"></div></div>
        <div class="progress-meta">
          <span data-progress-label>Pronto para carregar</span>
          <span data-progress-pct>0%</span>
        </div>
      </div>

      <div data-upload-msg style="display:none"></div>

      <form method="POST" action="{{ route('uploads.store') }}">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <div class="dashboard-form-group">
          <label class="dashboard-form-label">Categoria</label>
          <select name="category" class="dashboard-form-select" required>
            <option value="" disabled selected>Escolher…</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->slug }}" {{ old('category', $categoria) === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="dashboard-form-actions">
          <button type="submit" class="btn-primary"><i class="fa fa-upload"></i> Carregar</button>
          <button type="button" class="btn-ghost" data-modal-close>Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal de sucesso --}}
<div class="dashboard-modal" id="upload-success-modal">
  <div class="dashboard-modal-content" style="width:min(440px,100%)">
    <div class="dashboard-modal-body" style="text-align:center;padding:34px 26px">
      <i class="fa fa-check-circle" style="font-size:44px;color:#2E7D32"></i>
      <h3 style="font-family:'Playfair Display',serif;font-size:19px;margin:14px 0 6px">{{ $type === 'image' ? 'Imagem carregada com sucesso' : 'Documento carregado com sucesso' }}</h3>
      <p style="font-size:13px;margin-bottom:14px"><strong data-success-name style="font-family:monospace">—</strong></p>
      <code data-success-url style="display:block;font-size:11.5px;color:#666;word-break:break-all;background:#F5F5F5;padding:10px;border-radius:4px">—</code>
      <div class="dashboard-form-actions" style="justify-content:center;margin-top:18px">
        <button type="button" class="btn-primary" id="success-copy-btn"><i class="fa fa-copy"></i> Copiar URL</button>
        <button type="button" class="btn-ghost" data-modal-close>Fechar</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal de pré-visualização (imagem, PDF ou fallback) --}}
<div class="dashboard-modal" id="preview-modal">
  <div class="dashboard-modal-content modal-lg">
    <div class="dashboard-modal-header">
      <h3 data-preview-name>Pré-visualização</h3>
      <div style="display:flex;align-items:center;gap:10px">
        <a href="#" target="_blank" rel="noopener" class="btn-ghost btn-sm" data-preview-open-link><i class="fa fa-link"></i> Abrir</a>
        <button type="button" class="dashboard-modal-close" data-modal-close>&times;</button>
      </div>
    </div>
    <div class="dashboard-modal-body" style="padding:14px">
      <img class="preview-image" data-preview-image src="" alt="" hidden>
      <iframe class="pdf-preview" data-preview-frame src="" title="Pré-visualização" hidden></iframe>
      <div class="preview-fallback" data-preview-fallback hidden>
        <i class="fa fa-file"></i>
        <p>Pré-visualização não disponível para este tipo de ficheiro.</p>
        <a href="#" class="btn-primary" target="_blank" rel="noopener" data-preview-fallback-link><i class="fa fa-link"></i> Abrir ficheiro</a>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-preview-src]');
    if (btn) {
      var src = btn.getAttribute('data-preview-src');
      var kind = btn.getAttribute('data-preview-kind');
      var modal = document.getElementById('preview-modal');
      var img = modal.querySelector('[data-preview-image]');
      var frame = modal.querySelector('[data-preview-frame]');
      var fallback = modal.querySelector('[data-preview-fallback]');
      var title = modal.querySelector('[data-preview-name]');
      var openLink = modal.querySelector('[data-preview-open-link]');
      var fallbackLink = modal.querySelector('[data-preview-fallback-link]');

      img.hidden = true; img.src = '';
      frame.hidden = true; frame.src = '';
      fallback.hidden = true;

      title.textContent = btn.getAttribute('data-preview-name');
      openLink.href = src;
      fallbackLink.href = src;

      if (kind === 'image') {
        img.src = src; img.hidden = false;
      } else if (kind === 'pdf') {
        frame.src = src; frame.hidden = false;
      } else {
        fallback.hidden = false;
      }
    }

    // limpar ao fechar
    var close = e.target.closest('#preview-modal [data-modal-close]');
    if (close) { clearPreview(); }
    if (e.target.id === 'preview-modal') { clearPreview(); }
  });

  function clearPreview() {
    var modal = document.getElementById('preview-modal');
    modal.querySelector('[data-preview-image]').src = '';
    modal.querySelector('[data-preview-frame]').src = '';
  }
</script>
@endpush
