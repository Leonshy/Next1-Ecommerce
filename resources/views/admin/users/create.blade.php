@extends('layouts.admin')
@section('title', 'Nuevo Usuario')
@section('content')

{{-- Header --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.usuarios.index') }}"
       class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-xl font-bold text-gray-900">Nuevo Usuario</h2>
        <p class="text-sm text-gray-500">Creá una cuenta de acceso al sistema</p>
    </div>
</div>

<div class="max-w-lg">

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-red-700 mb-1">Por favor corregí los errores:</p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">

            {{-- Nombre --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                       placeholder="Ej: Juan Pérez">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                       placeholder="correo@ejemplo.com">
            </div>

            {{-- Contraseña --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                       placeholder="Mínimo 8 caracteres">
            </div>

            {{-- Confirmar contraseña --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                       placeholder="Repetí la contraseña">
            </div>

            {{-- Rol --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select name="role" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    <option value="usuario"  {{ old('role', 'usuario')  === 'usuario'  ? 'selected' : '' }}>Usuario (cliente)</option>
                    <option value="vendedor" {{ old('role') === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                    <option value="admin"    {{ old('role') === 'admin'    ? 'selected' : '' }}>Administrador</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">
                    <strong>Usuario</strong>: solo acceso a la tienda.
                    <strong>Vendedor</strong>: gestión de productos y pedidos.
                    <strong>Admin</strong>: acceso total.
                </p>
            </div>

            {{-- Email verificado --}}
            <div class="flex items-center gap-3 pt-1">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <div class="relative shrink-0">
                        <input type="checkbox" name="verified" value="1"
                               {{ old('verified', '1') === '1' ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-checked:bg-[#1a4a6b] rounded-full transition-colors duration-200 peer-focus:ring-2 peer-focus:ring-[#1a4a6b]/30"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-700 font-medium">Marcar como verificado</span>
                        <p class="text-xs text-gray-400">El usuario no necesitará verificar su email.</p>
                    </div>
                </label>
            </div>

        </div>

        {{-- Acciones --}}
        <div class="flex gap-3 mt-5">
            <a href="{{ route('admin.usuarios.index') }}"
               class="flex-1 text-center py-2.5 rounded-lg text-sm font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors hover:opacity-90"
                    style="background:#1a4a6b">
                Crear usuario
            </button>
        </div>
    </form>

</div>

@endsection
