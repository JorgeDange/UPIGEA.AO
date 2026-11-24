<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings', [
            'setting' => Setting::current(),
            'categories' => Category::query()->withCount('uploads')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'prefix_image' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9_-]+$/'],
            'prefix_document' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9_-]+$/'],
        ], [
            'prefix_image.required' => 'Defina o prefixo das imagens.',
            'prefix_image.regex' => 'O prefixo só pode conter letras, números, hífen e underscore.',
            'prefix_document.required' => 'Defina o prefixo dos documentos.',
            'prefix_document.regex' => 'O prefixo só pode conter letras, números, hífen e underscore.',
        ]);

        $setting = Setting::current();
        $setting->update($validated);

        return back()->with('success', 'Definições guardadas. Os próximos uploads usarão estes prefixos.');
    }

    /**
     * Criar nova categoria de uploads.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60'],
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.max' => 'O nome da categoria é demasiado longo.',
        ]);

        $slug = Str::slug($validated['name']);

        if (Category::where('slug', $slug)->exists()) {
            return back()->with('error', 'Já existe uma categoria com esse nome.');
        }

        Category::create(['name' => $validated['name'], 'slug' => $slug]);

        return back()->with('success', 'Categoria criada.');
    }

    /**
     * Eliminar categoria (bloqueada se tiver ficheiros associados).
     */
    public function destroyCategory(Category $category)
    {
        if ($category->uploads()->exists()) {
            return back()->with('error', 'Não pode eliminar "' . $category->name . '" — tem ficheiros associados.');
        }

        $category->delete();

        return back()->with('success', 'Categoria eliminada.');
    }
}
