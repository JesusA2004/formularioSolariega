<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/usuarios/Index', [
            'users' => User::query()
                ->orderBy('name')
                ->get()
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                    'role_label' => $user->role->label(),
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at?->toIso8601String(),
                ]),
            'roles' => collect(UserRole::cases())
                ->map(fn (UserRole $role) => ['value' => $role->value, 'label' => $role->label()])
                ->all(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Usuario creado correctamente.']);

        return back();
    }

    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $validated = $request->validated();

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->role = $validated['role'];
        $usuario->is_active = $validated['is_active'] ?? $usuario->is_active;

        if (! empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }

        $usuario->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Usuario actualizado correctamente.']);

        return back();
    }

    public function toggleActive(User $usuario): RedirectResponse
    {
        abort_if($usuario->id === Auth::id(), 403, 'No puedes desactivar tu propia cuenta.');

        $usuario->is_active = ! $usuario->is_active;
        $usuario->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $usuario->is_active ? 'Usuario activado.' : 'Usuario desactivado.',
        ]);

        return back();
    }

    public function destroy(User $usuario): RedirectResponse
    {
        abort_if($usuario->id === Auth::id(), 403, 'No puedes eliminar tu propia cuenta.');

        $usuario->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Usuario eliminado.']);

        return back();
    }
}
