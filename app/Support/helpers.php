<?php

if (! function_exists('asset_v')) {
    /**
     * asset() com cache-busting: anexa a versão do ficheiro (?v=timestamp).
     */
    function asset_v(string $path): string
    {
        $full = public_path($path);

        return asset($path) . (is_file($full) ? '?v=' . filemtime($full) : '');
    }
}
