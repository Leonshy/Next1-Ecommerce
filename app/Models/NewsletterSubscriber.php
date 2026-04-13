<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['email', 'status', 'subscribed_at', 'verified_at'];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'verified_at'   => 'datetime',
    ];
}
