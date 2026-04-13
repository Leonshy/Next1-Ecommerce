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

    public function upload(Request $request)
    {
        $request->validate([
            'files'   => 'required|array|max:20',
            'files.*' => 'file|max:20480', // 20 MB per file
        ]);

        $uploaded = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $safeName     = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . time() . '.' . $extension;
            $path         = $file->storeAs('media', $safeName, 'public');

            $media = MediaFile::create([
                'file_name'   => $originalName,
                'file_path'   => $path,
                'file_url'    => Storage::disk('public')->url($path),
                'mime_type'   => $file->getMimeType(),
                'file_size'   => $file->getSize(),
                'alt_text'    => pathinfo($originalName, PATHINFO_FILENAME),
                'uploaded_by' => auth()->id(),
            ]);

            $uploaded[] = $this->formatFile($media);
        }

        return response()->json(['uploaded' => $uploaded], 201);
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
