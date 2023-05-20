<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telegram extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'name', 'active'];

    public function chats(){
        return $this->belongsToMany(Chat::class);
    }
}
