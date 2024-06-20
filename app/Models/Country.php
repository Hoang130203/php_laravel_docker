<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';

    // Định nghĩa các thuộc tính không được thay đổi (fillable)
    protected $fillable = [
        'name',
        'code',
    ];
}
