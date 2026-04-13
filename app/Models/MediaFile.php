<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['file_name', 'file_path', 'file_url', 'mime_type', 'file_size', 'alt_text', 'uploaded_by'];

    public function usages()
    {
        return $this->hasMany(MediaUsage::class, 'media_id');
    }
}
