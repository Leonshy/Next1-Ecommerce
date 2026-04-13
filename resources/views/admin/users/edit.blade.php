@extends('layouts.admin')
@section('title', 'Editar Usuario')
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
        <h2 class="text-xl font-bold text-gray-900">Editar Usuario</h2>
        <p class="text-sm text-gray-500">{{ $user->email }}</p>
    </div>
</div>

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

<form method="POST" action="{{ route('admin.usuarios.update', $user->id) }}"
      enctype="multipart/form-data"
      x-data="{ avatarPreview: '{{ $user->profile?->avatar_url }}' }">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Columna principal ────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Datos de acceso --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 text-base">Datos de acceso</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nueva contraseña
                            <span class="text-gray-400 font-normal">(dejar vacío para no cambiar)</span>
                        </label>
                        <input type="password" name="password" autocomplete="new-password"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                               placeholder="Mínimo 8 caracteres">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                               placeholder="Repetí la contraseña">
                    </div>
                </div>
            </div>

            {{-- Datos personales --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-900 text-base">Datos personales y de contacto</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->profile?->full_name) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                           placeholder="Ej: Juan Pérez González">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->profile?->phone) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]"
                           placeholder="Ej: +595 981 123456">
                </div>
            </div>

        </div>

        {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Foto de perfil --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 text-base mb-4">Foto de perfil</h3>

                <div class="flex flex-col items-center gap-4">
                    {{-- Preview --}}
                    <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-gray-200 bg-gray-100 flex items-center justify-center shrink-0">
                        <template x-if="avatarPreview">
                            <img :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!avatarPreview">
                            <span class="text-3xl font-bold text-gray-400">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </template>
                    </div>

                    {{-- Upload --}}
                    <label class="w-full cursor-pointer">
                        <div class="border-2 border-dashed border-gray-200 rounded-lg p-3 text-center hover:border-[#1a4a6b]/40 transition-colors">
                            <p class="text-xs text-gray-500">Hacé clic para subir una imagen</p>
                            <p class="text-xs text-gray-400 mt-0.5">JPG, PNG · Máx. 2 MB</p>
                        </div>
                        <input type="file" name="avatar" accept="image/*" class="hidden"
                               @change="avatarPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : avatarPreview">
                    </label>

                    @if($user->profile?->avatar_url)
                        <p class="text-xs text-gray-400 text-center">Ya tiene foto. Subí una nueva para reemplazarla.</p>
                    @endif
                </div>
            </div>

            {{-- Rol --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-3">
                <h3 class="font-semibold text-gray-900 text-base">Rol</h3>
                <select name="role"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a4a6b]/30 focus:border-[#1a4a6b]">
                    @php $currentRole = $user->roles->first()?->role ?? 'usuario'; @endphp
                    <option value="usuario"  {{ $currentRole === 'usuario'  ? 'selected' : '' }}>Usuario (cliente)</option>
                    <option value="vendedor" {{ $currentRole === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                    <option value="admin"    {{ $currentRole === 'admin'    ? 'selected' : '' }}>Administrador</option>
                </select>
                <p class="text-xs text-gray-400">
                    <strong>Usuario</strong>: solo tienda.
                    <strong>Vendedor</strong>: productos y pedidos.
                    <strong>Admin</strong>: acceso total.
                </p>
            </div>

            {{-- Info de cuenta --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-2">
                <h3 class="font-semibold text-gray-900 text-base mb-1">Info de cuenta</h3>
                <div class="flex items-center gap-2">
                    @if($user->email_verified_at)
                        <span class="inline-flex items-center gap-1 text-xs text-green-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Email verificado
                        </span>
                    @else
                        <span class="text-xs text-yellow-500">⏳ Email pendiente de verificación</span>
                    @endif
                </div>
                <p class="text-xs text-gray-400">Registro: {{ $user->created_at->format('d/m/Y H:i') }}</p>
                @if($user->updated_at != $user->created_at)
                    <p class="text-xs text-gray-400">Actualizado: {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="space-y-2">
                <button type="submit"
                        class="w-full py-2.5 rounded-lg text-sm font-semibold text-white transition-colors hover:opacity-90"
                        style="background:#1a4a6b">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.usuarios.index') }}"
                   class="block w-full text-center py-2.5 rounded-lg text-sm font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
            </div>

        </div>
    </div>
</form>

@endsection
