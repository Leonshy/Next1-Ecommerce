<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MediaUsage extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['media_id', 'entity_type', 'entity_id', 'field_name'];

    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class, 'media_id');
    }
}
