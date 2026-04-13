<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['user_id', 'email', 'full_name', 'phone', 'avatar_url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
