<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'shop_description',
        'shop_address',
        'opening_time',
        'closing_time',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
