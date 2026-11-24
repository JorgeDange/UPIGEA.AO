<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->withCount('uploads');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('admin.users', ['users' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'photo_url' => ['nullable', 'url', 'max:2048'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Já existe uma conta com este e-mail.',
            'role.required' => 'Escolha o papel do utilizador.',
            'photo_url.url' => 'A foto de perfil deve ser um URL de imagem válido.',
            'password.required' => 'A palavra-passe é obrigatória.',
            'password.min' => 'A palavra-passe deve ter pelo menos 6 caracteres.',
        ]);

        User::create($validated + ['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Conta criada com sucesso.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'photo_url' => ['nullable', 'url', 'max:2048'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.unique' => 'Já existe uma conta com este e-mail.',
            'photo_url.url' => 'A foto de perfil deve ser um URL de imagem válido.',
            'password.min' => 'A palavra-passe deve ter pelo menos 6 caracteres.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'photo_url' => $validated['photo_url'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return back()->with('success', 'Conta atualizada com sucesso.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Não pode eliminar a sua própria conta.');
        }

        // Elimina também os ficheiros dos uploads deste utilizador.
        $user->uploads->each(function (Upload $upload) {
            \Illuminate\Support\Facades\Storage::disk(config('files.disk'))->delete($upload->path);
            $upload->delete();
        });

        $user->delete();

        return back()->with('success', 'Conta eliminada com sucesso.');
    }
}
