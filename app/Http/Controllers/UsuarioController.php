<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\{JsonResponse, Request};
use Spatie\Permission\Models\{Role, Permission};

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles', 'permissions')->orderBy('name')->get();

        $rolePerms = Role::with('permissions')->get()->mapWithKeys(
            fn($role) => [$role->name => $role->permissions->pluck('name')->values()]
        );

        $admins   = $usuarios->filter(fn($u) => $u->roles->contains('name', 'admin'))->count();
        $regulars = $usuarios->count() - $admins;
        $authId   = Auth::id();

        $usuariosProcessados = $usuarios->map(function ($u) use ($authId) {
            $u->role_name  = $u->roles->first()?->name ?? 'usuario';
            $u->user_perms = $u->getAllPermissions()->pluck('name');
            $u->perm_tags  = $u->user_perms->map(fn($p) => ['full' => $p, 'action' => \Illuminate\Support\Str::after($p, '.')])->values();
            $u->is_self    = $u->id === $authId;
            $u->initials   = strtoupper(substr($u->name, 0, 2));
            return $u;
        });

        return view('usuarios.index', [
            'usuarios'    => $usuariosProcessados,
            'roles'       => Role::all(),
            'permissions' => Permission::orderBy('name')->get(),
            'rolePerms'   => $rolePerms,
            'totalUsers'  => $usuarios->count(),
            'admins'      => $admins,
            'regulars'    => $regulars,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:191',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->syncRoles([$validated['role']]);

        if (!empty($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        }

        return response()->json($user->load('roles', 'permissions'), 201);
    }

    public function update(Request $request, User $usuario): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'sometimes|string|max:191',
            'email'         => "sometimes|email|unique:users,email,{$usuario->id}",
            'password'      => 'nullable|string|min:6',
            'role'          => 'sometimes|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if (isset($validated['password']) && $validated['password']) {
            $usuario->update(collect($validated)->only(['name', 'email', 'password'])->toArray());
        } else {
            $usuario->update(collect($validated)->only(['name', 'email'])->toArray());
        }

        if (isset($validated['role'])) {
            $usuario->syncRoles([$validated['role']]);
        }

        if (array_key_exists('permissions', $validated)) {
            $usuario->syncPermissions($validated['permissions'] ?? []);
        }

        return response()->json($usuario->load('roles', 'permissions'));
    }

    public function destroy(User $usuario): JsonResponse
    {
        if ($usuario->id === Auth::id()) {
            return response()->json(['message' => 'Você não pode excluir sua própria conta.'], 422);
        }

        if ($usuario->hasRole('admin') && User::role('admin')->count() <= 1) {
            return response()->json(['message' => 'Não é possível excluir o último administrador.'], 422);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuário excluído']);
    }

    public function restore(int $id): JsonResponse
    {
        $usuario = User::onlyTrashed()->findOrFail($id);
        $usuario->restore();

        return response()->json(['message' => 'Usuário restaurado']);
    }
}
