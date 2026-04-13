<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'profile'])->latest();

        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => 'required|in:admin,vendedor,usuario',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if ($request->has('verified')) {
            $user->email_verified_at = now();
            $user->save();
        }

        UserRole::create(['user_id' => $user->id, 'role' => $data['role']]);

        return redirect()->route('admin.usuarios.index')->with('success', "Usuario {$user->name} creado correctamente.");
    }

    public function edit(string $id)
    {
        $user = User::with(['roles', 'profile'])->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::with(['roles', 'profile'])->findOrFail($id);

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'password'  => ['nullable', 'confirmed', Password::min(8)],
            'role'      => 'required|in:admin,vendedor,usuario',
            'full_name' => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'avatar'    => 'nullable|image|max:2048',
        ]);

        // Update user base data
        $user->name  = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        // Update role
        UserRole::where('user_id', $user->id)->delete();
        UserRole::create(['user_id' => $user->id, 'role' => $data['role']]);

        // Handle avatar upload
        $avatarUrl = $user->profile?->avatar_url;
        if ($request->hasFile('avatar')) {
            $path      = $request->file('avatar')->store('avatars', 'public');
            $avatarUrl = '/storage/' . $path;
        }

        // Update or create profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name'  => $data['full_name'] ?? null,
                'phone'      => $data['phone']     ?? null,
                'email'      => $data['email'],
                'avatar_url' => $avatarUrl,
            ]
        );

        return redirect()->route('admin.usuarios.index')->with('success', "Usuario {$user->name} actualizado correctamente.");
    }

    public function destroy(string $id)
    {
        if (auth()->id() === $id) {
            return back()->with('error', 'No podés eliminar tu propia cuenta.');
        }

        User::findOrFail($id)->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado.');
    }
}
