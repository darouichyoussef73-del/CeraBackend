<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type',
        'full_name',
        'email',
        'phone',
        'preferred_date',
        'preferred_time',
        'participants',
        'experience',
        'piece_type',
        'dimensions',
        'glaze_preference',
        'message',
        'newsletter',
        'vision_image',
        'submitted_at',
    ];

    protected $casts = [
        'newsletter' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
