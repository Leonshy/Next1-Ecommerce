<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $guarded = [];
    protected $casts   = ['payload' => 'array', 'created_at' => 'datetime'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
