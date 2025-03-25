<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model {
    use HasFactory;

    protected $table = 'notes';

    protected $fillable = ['user_id', 'title', 'body'];

    // Vlastná metóda na vyhľadávanie podľa názvu a poznámky samotnej
    public static function searchByTitleOrBody($keyword) {
        return self::where('title', 'LIKE', "%$keyword%")
            ->orWhere('body', 'LIKE', "%$keyword%")
            ->get();
    }
}
