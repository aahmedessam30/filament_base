<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function scopPrivate($query)
    {
        return $query->where('type', 'private');
    }

    public function scopPublic($query)
    {
        return $query->where('type', 'public');
    }
}
