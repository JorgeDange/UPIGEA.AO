@if($paginator->hasPages())
  <ul class="pag-style" style="display:flex;gap:6px;list-style:none;margin:0;padding:0;justify-content:center">
    @if($paginator->onFirstPage())
      <li style="opacity:.4"><span style="padding:8px 14px;border:1px solid #E0E0E0;border-radius:4px;color:#999">&laquo;</span></li>
    @else
      <li><a href="{{ $paginator->previousPageUrl() }}" style="padding:8px 14px;border:1px solid #E0E0E0;border-radius:4px">&laquo;</a></li>
    @endif

    @foreach($elements as $element)
      @if(is_string($element))
        <li style="padding:8px 6px;color:#999">{{ $element }}</li>
      @endif
      @if(is_array($element))
        @foreach($element as $page => $url)
          @if($page == $paginator->currentPage())
            <li><span class="current-page" style="padding:8px 14px;border-radius:4px;background:#C8102E;color:#fff;display:inline-block">{{ $page }}</span></li>
          @else
            <li><a href="{{ $url }}" style="padding:8px 14px;border:1px solid #E0E0E0;border-radius:4px;display:inline-block">{{ $page }}</a></li>
          @endif
        @endforeach
      @endif
    @endforeach

    @if($paginator->hasMorePages())
      <li><a href="{{ $paginator->nextPageUrl() }}" style="padding:8px 14px;border:1px solid #E0E0E0;border-radius:4px">&raquo;</a></li>
    @else
      <li style="opacity:.4"><span style="padding:8px 14px;border:1px solid #E0E0E0;border-radius:4px;color:#999">&raquo;</span></li>
    @endif
  </ul>
@endif
