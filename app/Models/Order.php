<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_number', 'user_id', 'status', 'customer_name', 'customer_email',
        'customer_phone', 'shipping_address', 'shipping_city', 'subtotal',
        'discount', 'shipping_cost', 'total', 'notes', 'bancard_process_id',
        'guest_access_token',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
            if (empty($order->guest_access_token) && empty($order->user_id)) {
                $order->guest_access_token = Str::random(64);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendiente'   => 'Pendiente',
            'confirmado'  => 'Confirmado',
            'procesando'  => 'Procesando',
            'enviado'     => 'Enviado',
            'entregado'   => 'Entregado',
            'cancelado'   => 'Cancelado',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pendiente'  => 'yellow',
            'confirmado' => 'blue',
            'procesando' => 'purple',
            'enviado'    => 'indigo',
            'entregado'  => 'green',
            'cancelado'  => 'red',
            default      => 'gray',
        };
    }
}
