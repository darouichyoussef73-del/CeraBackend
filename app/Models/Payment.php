<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'amount',
        'method',
        'status',
        'date',
        'notes',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
