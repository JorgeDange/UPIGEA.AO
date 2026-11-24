<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Armazenamento de ficheiros
    |--------------------------------------------------------------------------
    |
    | 'base_url' define o destino público dos uploads (o subdomínio de
    | armazenamento, ex.: https://files.seudominio.com). Em desenvolvimento,
    | usa APP_URL/storage depois de `php artisan storage:link`.
    |
    | 'disk' é o disco do Laravel onde os ficheiros são guardados.
    |
    */

    'base_url' => env('FILES_BASE_URL', env('APP_URL') . '/storage'),
    'disk'     => env('FILES_DISK', 'public'),

    // Pasta base dentro do disco: {base}/{categoria}/{ficheiro}
    'base_path' => env('FILES_BASE_PATH', 'img'),

    // Tamanho máximo por ficheiro, em kilobytes (10 MB).
    'max_size' => env('FILES_MAX_SIZE', 10240),

    'image_mimes'     => 'jpg,jpeg,png,gif,webp,svg,avif',
    'document_mimes'  => 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,rtf,zip',
];
