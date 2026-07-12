<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // これを残す
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory; // これも残す

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}