<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code', 'amount', 'balance', 'status',
        'buyer_name', 'buyer_email', 'buyer_phone',
        'recipient_name', 'recipient_email', 'message',
        'access_token', 'expires_at', 'purchased_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'balance'      => 'decimal:2',
        'expires_at'   => 'datetime',
        'purchased_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Equivalente al trigger generate_gift_card_access_token
        static::creating(function ($giftCard) {
            if (empty($giftCard->access_token)) {
                $giftCard->access_token = bin2hex(random_bytes(32)); // 64 chars hex
            }
        });
    }
}
