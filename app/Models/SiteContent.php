<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'site_content';

    protected $fillable = ['key', 'title', 'content', 'metadata', 'updated_by'];

    protected $casts = ['metadata' => 'array'];

    public static function getByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }
}
