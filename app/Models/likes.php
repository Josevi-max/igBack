<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class likes extends Model
{
    use HasFactory;
    protected $table = 'like_publications';
    protected $fillable = [
        'publication_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function publication()
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }
}
