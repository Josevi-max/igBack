<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class likesCommentaries extends Model
{
    use HasFactory;
    protected $table = 'like_commentaries';
    protected $fillable = [
        'commentary_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function commentary()
    {
        return $this->belongsTo(Commentary::class, 'commentary_id');
    }
}
