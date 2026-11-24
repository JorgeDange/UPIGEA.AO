# PIGEA Uploads (UPIGEA.AO)

Aplicação web de gestão de uploads de imagens e documentos, construída em **Laravel 11**, com a identidade visual do protótipo PIGEA (sidebar `#1A1A1A`, primário `#C8102E`, tipografia **Playfair Display** + **Poppins**).

> **Desenvolvido por Jorge Dange** — o Desenvolvedor.

---

## Descrição do sistema

Plataforma com dois tipos de utilizador — **admin** e **user** — que fazem login com credenciais próprias. O sistema reconhece o papel de cada um e apresenta-lhe apenas o que lhe compete.

**Admin** — painel completo com:
- **Dashboard**: totais de uploads (total, imagens, documentos, últimos 7 dias) e lista de uploads recentes.
- **Utilizadores**: criar, editar e eliminar contas; definir papel (admin/user) e **foto de perfil por URL**.
- **Imagens** e **Documentos**: todas as listas (com coluna "Enviado por"), abas por categoria, pré-visualização, cópia de URL e eliminação.
- **Definições**: prefixos de nomenclatura + **gestão de categorias** (criar/eliminar).

**User (normal)** — visão simplificada:
- **Dashboard**: os seus uploads.
- **Imagens** e **Documentos**: carregar ficheiros próprios; vê e elimina apenas os seus ficheiros.

## Funcionalidades

- **Login** com distinção de papéis — as contas são criadas pelo admin (não há registo público).
- **Foto de perfil por URL** no cadastro de utilizadores (usada no header e na lista).
- **Categorias geridas pelo admin** (Autores, Notícias, Usuários, Publicidade por omissão): cada upload é classificado e os painéis mostram **áreas separadas** com abas por categoria e contadores.
- **Nomenclatura automática**: `prefixo + hash único + extensão` (ex.: `IMG-EF3C1804CE.png`). Prefixos configuráveis nas Definições (IMG/DOC).
- **Upload com barra de progresso** em tempo real (XHR), **dropzone personalizada** com arrastar-e-soltar, validação de 10 MB e de tipos, modal de sucesso e mensagens de erro claras.
- **Modal de pré-visualização** própria: imagens embebidas, PDFs em iframe, fallback com "Abrir ficheiro" para outros tipos.
- **Modais de ação próprias** (sem `alert()`/`confirm()` nativos): confirmação de eliminação no estilo do sistema.
- **URLs públicas** com botão copiar; **cache-busting** automático dos assets.
- **Estrutura de armazenamento**: `{base}/{categoria}/{ficheiro}` (ex.: `img/noticias/IMG-EF3C1804CE.png`).

## Estrutura técnica

```
app/
  Http/Controllers/
    Auth/LoginController.php      # login/logout
    DashboardController.php       # dashboard admin/user
    UploadController.php          # listas, upload XHR, sugerir nome, eliminar
    Admin/UserController.php      # CRUD de contas
    Admin/SettingController.php   # prefixos + categorias
  Http/Middleware/EnsureUserIsAdmin.php
  Models/                         # User, Upload, Category, Setting
resources/views/
  layouts/dashboard.blade.php     # layout com sidebar/header da identidade PIGEA
  auth/login.blade.php
  dashboard.blade.php
  uploads/                        # imagens + documentos (lista partilhada)
  admin/                          # utilizadores + definições
  vendor/pagination/pigea.blade.php
public/assets/                    # CSS/fontes/logo do protótipo + custom.css + app.js
```

- **Base de dados**: tabelas `users` (com `role`, `photo_url`), `uploads` (`type`, `category`, `file_name`, `path`, `url`…), `categories` (geridas pelo admin), `settings` (prefixos).
- **Uploads**: via XHR com progresso; endpoint `POST /uploads` (JSON) e `GET /uploads/nome` (nome sugerido antes de carregar).
- **Auth**: session-based, middleware `auth` + `admin`.
- **Assets**: CSS do protótipo (`dashboard.css`, `color.css`, `fonts.css`) + `custom.css` (ícones Font Awesome locais, dropzone, barra de progresso, responsividade); fontes 100% locais.

## Subdomínio de armazenamento

O destino público dos ficheiros é definido na `.env`:

```env
FILES_BASE_URL="https://files.seudominio.com"   # subdomínio em produção
FILES_DISK=public
FILES_MAX_SIZE=10240                            # 10 MB
FILES_BASE_PATH=img                             # {base}/{categoria}/{ficheiro}
```

Em desenvolvimento, `FILES_BASE_URL="${APP_URL}/storage"` funciona com `php artisan storage:link`.
Em produção, o subdomínio `files.seudominio.com` aponta para `storage/app/public` — a estrutura `img/{categoria}/{ficheiro}` aparece diretamente no subdomínio.

## Instalação (desenvolvimento)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

## Instalação em hospedagem (produção)

**Requisitos do servidor:** PHP 8.2+ (extensões: pdo, mbstring, openssl, fileinfo, gd), Apache (mod_rewrite) ou nginx + PHP-FPM, MySQL 5.7+/8 ou SQLite.

1. **Upload do código** (sem `.env`, sem `vendor/`) e `composer install --no-dev --optimize-autoloader` no servidor.
2. **Apontar o domínio para `public/`** (DocumentRoot). Nunca exponha a raiz do projeto.
3. **Criar `.env`** a partir do `.env.example`:

   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://seudominio.com
   APP_KEY=                    # php artisan key:generate

   DB_CONNECTION=mysql         # ou sqlite
   DB_HOST=127.0.0.1
   DB_DATABASE=pigea_upload
   DB_USERNAME=...
   DB_PASSWORD=...

   FILES_BASE_URL="https://files.seudominio.com"
   FILES_BASE_PATH=img
   ```

4. **No servidor:**

   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Permissões (Linux):** `storage/` e `bootstrap/cache` escritas pelo utilizador do PHP (ex.: `chown -R www-data:www-data storage bootstrap/cache`).
6. **Limites PHP:** `upload_max_filesize` e `post_max_size` ≥ 12M no `php.ini`.
7. **Subdomínio de armazenamento:** apontar `files.seudominio.com` para `storage/app/public` (symlink ou DocumentRoot próprio).


---

## Créditos

**Desenvolvido por Jorge Dange** — o Desenvolvedor.
Identidade visual baseada no protótipo PIGEA (gmag).
