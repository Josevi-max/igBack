<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'description',
        'user_id'
    ];

    protected $with = ['user', 'comments'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Commentary::class, 'publication_id');
    }

    public function likes()
    {
        return $this->hasMany(likesPublications::class, 'publication_id');
    }

    public static function rules()
    {
        return [
            'image' => 'required|string',
            'description' => 'nullable|string|max:500',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
