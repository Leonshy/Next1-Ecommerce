<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminMediaController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaFile::with('usages')->latest();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            match($type) {
                'image'    => $query->where('mime_type', 'like', 'image/%'),
                'video'    => $query->where('mime_type', 'like', 'video/%'),
                'document' => $query->where('mime_type', 'like', 'application/%'),
                default    => null,
            };
        }

        $files = $query->paginate(40)->withQueryString();
        $total = MediaFile::count();

        if ($request->expectsJson()) {
            return response()->json([
                'files' => $files->map(fn($f) => $this->formatFile($f)),
                'total' => $total,
            ]);
        }

        return view('admin.media.index', compact('files', 'total'));
    }

    // MIME types permitidos: tipo real (finfo) → extensión normalizada
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg'    => 'jpg',
        'image/png'     => 'png',
        'image/gif'     => 'gif',
        'image/webp'    => 'webp',
        'image/svg+xml' => 'svg',
        'image/x-icon'  => 'ico',
        'video/mp4'     => 'mp4',
        'video/webm'    => 'webm',
        'application/pdf' => 'pdf',
    ];

    public function upload(Request $request)
    {
        $request->validate([
            'files'   => 'required|array|max:20',
            'files.*' => 'file|max:20480',
        ]);

        $uploaded = [];
        $errors   = [];

        foreach ($request->file('files') as $file) {
            try {
                $media      = $this->processUpload($file);
                $uploaded[] = $this->formatFile($media);
            } catch (\RuntimeException $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $response = ['uploaded' => $uploaded];
        if ($errors) $response['errors'] = $errors;

        return response()->json($response, $uploaded ? 201 : 422);
    }

    private function processUpload(\Illuminate\Http\UploadedFile $file): MediaFile
    {
        // 1. Verificar MIME real con finfo (no confiar en extensión del cliente)
        $realMime = $this->getRealMimeType($file->getRealPath());

        if (! array_key_exists($realMime, self::ALLOWED_MIME_TYPES)) {
            throw new \RuntimeException("Tipo de archivo no permitido ({$realMime}).");
        }

        // 2. Para imágenes: verificar que sea imagen válida y limpiar EXIF
        $isImage = str_starts_with($realMime, 'image/') && $realMime !== 'image/svg+xml';
        $tmpPath = $file->getRealPath();

        if ($isImage) {
            if (! @getimagesize($tmpPath)) {
                throw new \RuntimeException('El archivo no es una imagen válida.');
            }
            $tmpPath = $this->stripExifAndReencode($tmpPath, $realMime);
        }

        // 3. Construir nombre seguro usando extensión del MIME real (no la del cliente)
        $safeExt  = self::ALLOWED_MIME_TYPES[$realMime];
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = ($baseName ?: 'file') . '-' . time() . '-' . Str::random(6) . '.' . $safeExt;

        // 4. Guardar en disco
        $destination = storage_path('app/public/media/' . $safeName);
        copy($tmpPath, $destination);

        // Limpiar archivo temporal de re-encode si fue creado
        if ($tmpPath !== $file->getRealPath() && file_exists($tmpPath)) {
            @unlink($tmpPath);
        }

        return MediaFile::create([
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => 'media/' . $safeName,
            'file_url'    => Storage::disk('public')->url('media/' . $safeName),
            'mime_type'   => $realMime,
            'file_size'   => filesize($destination),
            'alt_text'    => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'uploaded_by' => auth()->id(),
        ]);
    }

    private function getRealMimeType(string $path): string
    {
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $path);
            finfo_close($finfo);
            return $mime ?: 'application/octet-stream';
        }
        return mime_content_type($path) ?: 'application/octet-stream';
    }

    private function stripExifAndReencode(string $path, string $mime): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'upload_');

        try {
            $img = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($path),
                'image/png'  => @imagecreatefrompng($path),
                'image/gif'  => @imagecreatefromgif($path),
                'image/webp' => @imagecreatefromwebp($path),
                default      => null,
            };

            if (! $img) return $path; // no se pudo re-encodear, usar original

            match ($mime) {
                'image/jpeg' => imagejpeg($img, $tmp, 92),
                'image/png'  => imagepng($img, $tmp, 6),
                'image/gif'  => imagegif($img, $tmp),
                'image/webp' => imagewebp($img, $tmp, 90),
                default      => null,
            };

            imagedestroy($img);

            return $tmp;
        } catch (\Throwable) {
            return $path;
        }
    }

    public function updateAlt(Request $request, MediaFile $media)
    {
        $request->validate(['alt_text' => 'nullable|string|max:255']);
        $media->update(['alt_text' => $request->input('alt_text')]);
        return response()->json(['ok' => true]);
    }

    public function destroy(MediaFile $media)
    {
        // Delete from disk
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }
        $media->delete(); // usages cascade via FK
        return response()->json(['ok' => true]);
    }

    public function show(MediaFile $media)
    {
        return response()->json($this->formatFile($media->load('usages')));
    }

    // ─── Picker JSON endpoint (used by MediaPicker component) ─────────────────
    public function picker(Request $request)
    {
        $query = MediaFile::latest();

        if ($q = $request->input('q')) {
            $query->where(function ($qb) use ($q) {
                $qb->where('file_name', 'like', "%{$q}%")
                   ->orWhere('alt_text', 'like', "%{$q}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('mime_type', 'like', $type . '/%');
        }

        $files = $query->limit(200)->get()->map(fn($f) => $this->formatFile($f));
        return response()->json($files);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function formatFile(MediaFile $f): array
    {
        return [
            'id'          => $f->id,
            'file_name'   => $f->file_name,
            'file_url'    => $f->file_url,
            'file_path'   => $f->file_path,
            'mime_type'   => $f->mime_type,
            'file_size'   => $f->file_size,
            'alt_text'    => $f->alt_text,
            'is_image'    => str_starts_with($f->mime_type ?? '', 'image/'),
            'size_label'  => $this->formatSize($f->file_size),
            'created_at'  => $f->created_at?->diffForHumans(),
            'usages_count'=> $f->usages?->count() ?? 0,
            'usages'      => $f->usages?->map(fn($u) => [
                'entity_type' => $u->entity_type,
                'entity_id'   => $u->entity_id,
                'field_name'  => $u->field_name,
            ])->toArray() ?? [],
        ];
    }

    private function formatSize(?int $bytes): string
    {
        if (!$bytes) return '—';
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}
