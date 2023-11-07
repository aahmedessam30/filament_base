<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memberable()
    {
        return $this->morphTo();
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function views()
    {
        return $this->hasMany(MessageView::class);
    }

}
