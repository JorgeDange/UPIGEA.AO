<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UploadController extends Controller
{
    /**
     * Lista de imagens ou documentos. Admin vê tudo; user vê apenas o seu.
     * Filtro por categoria: ?categoria=autores|noticias|usuarios|publicidade
     */
    public function index(Request $request, string $type)
    {
        abort_unless(in_array($type, ['images', 'documents']), 404);

        $uploadType = $type === 'images' ? 'image' : 'document';
        $user = $request->user();

        $query = $user->isAdmin()
            ? Upload::query()->with(['user', 'categoryRef'])->where('type', $uploadType)
            : $user->uploads()->with('categoryRef')->where('type', $uploadType);

        $categoria = $request->input('categoria');
        if ($categoria && Category::where('slug', $categoria)->exists()) {
            $query->where('category', $categoria);
        }

        $uploads = $query->latest()->paginate(15)->withQueryString();

        // Abas: todas as categorias geridas pelo admin + contagem por tipo
        $categories = Category::query()
            ->withCount(['uploads as uploads_count' => fn ($q) => $q->where('type', $uploadType)])
            ->orderBy('name')
            ->get();

        $total = $user->isAdmin()
            ? Upload::query()->where('type', $uploadType)->count()
            : $user->uploads()->where('type', $uploadType)->count();

        $view = $type === 'images' ? 'uploads.images' : 'uploads.documents';

        return view($view, [
            'uploads' => $uploads,
            'type' => $uploadType,
            'categoria' => $categoria,
            'categories' => $categories,
            'total' => $total,
            'prefix' => $user->isAdmin()
                ? Setting::current()->{'prefix_' . ($uploadType === 'image' ? 'image' : 'document')}
                : null,
        ]);
    }

    /**
     * Nome sugerido para o próximo ficheiro: prefixo definido pelo admin + hash.
     */
    public function suggest(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:image,document'],
            'extension' => ['required', 'string', 'max:10'],
        ]);

        $setting = Setting::current();
        $prefix = $request->type === 'image' ? $setting->prefix_image : $setting->prefix_document;

        // Prefixo + hash curto: único e identificável.
        $hash = strtoupper(substr(hash('sha256', microtime(true) . Str::random(16)), 0, 10));

        return response()->json([
            'name' => $prefix . '-' . $hash . '.' . strtolower($request->extension),
        ]);
    }

    /**
     * Upload de um ficheiro (via XHR, com barra de progresso no cliente).
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:image,document'],
            'category' => ['required', Rule::exists('categories', 'slug')],
            'file' => [
                'required',
                'file',
                'max:' . config('files.max_size'),
                'mimes:' . implode(',', $request->type === 'image'
                    ? explode(',', config('files.image_mimes'))
                    : explode(',', config('files.document_mimes'))),
            ],
        ], [
            'file.required' => 'Escolha um ficheiro para carregar.',
            'file.max' => 'O ficheiro excede o limite de 10 MB.',
            'file.mimes' => 'Tipo de ficheiro não permitido para este tipo de upload.',
            'file.uploaded' => 'O ficheiro excede o limite de 10 MB.',
            'category.required' => 'Escolha a categoria do ficheiro.',
            'category.exists' => 'Categoria inválida.',
        ]);

        $file = $request->file('file');
        $setting = Setting::current();
        $prefix = $request->type === 'image' ? $setting->prefix_image : $setting->prefix_document;

        // Geração do nome: prefixo do admin + hash único + extensão original.
        $extension = strtolower($file->getClientOriginalExtension());
        $hash = strtoupper(substr(hash('sha256', microtime(true) . $file->getClientOriginalName() . Str::random(16)), 0, 10));
        $fileName = $prefix . '-' . $hash . ($extension ? '.' . $extension : '');

        $directory = config('files.base_path') . '/' . $request->category; // ex.: img/noticias
        $path = $file->storeAs($directory, $fileName, config('files.disk'));

        $baseUrl = rtrim(config('files.base_url'), '/');

        $upload = $request->user()->uploads()->create([
            'type' => $request->type,
            'category' => $request->category,
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'url' => $baseUrl . '/' . $path,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => ($request->type === 'image' ? 'Imagem' : 'Documento') . ' carregado com sucesso!',
                'upload' => [
                    'id' => $upload->id,
                    'name' => $upload->file_name,
                    'url' => $upload->url,
                    'size' => $upload->sizeHuman(),
                    'type' => $upload->type,
                ],
            ], 201);
        }

        return back()->with('success', 'Ficheiro carregado com sucesso!');
    }

    public function destroy(Request $request, Upload $upload)
    {
        if (! $request->user()->isAdmin() && $upload->user_id !== $request->user()->id) {
            abort(403);
        }

        Storage::disk(config('files.disk'))->delete($upload->path);
        $upload->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Ficheiro eliminado.']);
        }

        return back()->with('success', 'Ficheiro eliminado com sucesso.');
    }
}
