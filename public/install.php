<?php
/*
 * UPIGEA.AO — Instalação pós-upload (servidores sem terminal)
 *
 * UMA ÚNICA VEZ: faça upload deste ficheiro para public/, visite
 * https://SEU-DOMINIO/install.php?token=SEU-TOKEN
 * e ELIMINE o ficheiro do servidor no fim.
 */

define('SETUP_TOKEN', 'TROQUE-ESTE-TOKEN');

if (!isset($_GET['token']) || !hash_equals(SETUP_TOKEN, (string) $_GET['token'])) {
    http_response_code(403);
    exit('Acesso negado: acrescente ?token=... ao URL.');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/plain; charset=utf-8');
echo "UPIGEA.AO — instalação\n" . str_repeat('-', 50) . "\n";

function run(string $command, array $params = []): void
{
    echo "> php artisan {$command} " . implode(' ', $params) . "\n";
    $code = Illuminate\Support\Facades\Artisan::call($command, $params);
    echo Illuminate\Support\Facades\Artisan::output() ?: "(sem saída)\n";
    echo "[código {$code}]\n\n";
}

// 1. Caches fora de data (config antiga com ${...})
run('config:clear');
run('route:clear');
run('view:clear');
run('config:cache');
run('route:cache');
run('view:cache');

// 2. Recriar a ligação public/storage -> raiz do disco de ficheiros
$link = __DIR__ . '/storage';
if (is_link($link)) {
    echo "ligação antiga -> " . readlink($link) . "\n";
    @unlink($link) || @rmdir($link);
} elseif (file_exists($link) && is_dir($link)) {
    echo "AVISO: public/storage é uma pasta real, não uma ligação\n";
}
run('storage:link');
echo is_link($link)
    ? "nova ligação public/storage -> " . readlink($link) . "\n"
    : "public/storage -> NÃO CRIADO (verificar)\n";

// 3. Permissões de escrita nas pastas de dados
$targets = [
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../storage/app/public',
    __DIR__ . '/../storage/logs',
    __DIR__ . '/../bootstrap/cache',
];
foreach ($targets as $dir) {
    echo is_writable($dir) ? "[ok] escrita: {$dir}\n" : "[FALTA] sem escrita: {$dir}\n";
    @chmod($dir, 0775);
}

// 4. Verificações
echo "\n" . str_repeat('-', 50) . "\n";
echo 'APP_URL:     ' . config('app.url') . "\n";
echo 'FILES_BASE_URL: ' . config('files.base_url') . "\n";
echo 'FILES_BASE_PATH: ' . config('files.base_path') . "\n";
echo 'Raiz do disco:  ' . config('filesystems.disks.public.root') . "\n";
echo 'DB:          ' . DB::connection()->getDriverName() . ' @ ' . DB::connection()->getDatabaseName() . "\n";
echo 'Categorias:  ' . App\Models\Category::count() . " | Contas: " . App\Models\User::count() . "\n";

echo "\nConfira que a Raiz do disco é a pasta do subdomínio (FILES_ROOT no .env).\n";
echo "ELIMINE install.php do servidor quando terminar.\n";
