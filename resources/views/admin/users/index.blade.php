@extends('layouts.admin')
@section('title', 'Usuarios')
@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div class="mb-5 rounded-xl border bg-green-50 border-green-200 p-4">
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="mb-5 rounded-xl border bg-red-50 border-red-200 p-4">
        <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
    </div>
@endif

<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nombre o email..."
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">Buscar</button>
    </form>
    <a href="{{ route('admin.usuarios.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors hover:opacity-90"
       style="background:#1a4a6b">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo usuario
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Usuario</th>
                <th class="px-5 py-3 text-left">Rol</th>
                <th class="px-5 py-3 text-left">Verificado</th>
                <th class="px-5 py-3 text-left">Registro</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @php $role = $user->roles->first()?->role ?? 'usuario'; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $role === 'admin' ? 'bg-purple-100 text-purple-700' : ($role === 'vendedor' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($role) }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="{{ $user->email_verified_at ? 'text-green-600' : 'text-yellow-500' }} text-xs">
                            {{ $user->email_verified_at ? '✓ Verificado' : '⏳ Pendiente' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 flex space-x-2">
                        <a href="{{ route('admin.usuarios.edit', $user->id) }}" class="text-xs font-medium hover:underline" style="color:#1a4a6b">Editar</a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $user->id) }}" onsubmit="return confirm('¿Eliminar usuario?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-xs">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No hay usuarios</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">{{ $users->links() }}</div>
</div>

@endsection
