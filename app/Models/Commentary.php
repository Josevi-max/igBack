<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class Commentary extends Model
{
    use HasFactory;

    protected $fillable = [
        'commentary',
        'publication_id',
        'user_id',
        'reply_to_id',
        'reply_to_user_id'
    ];
    protected $appends = ['is_liked'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function publication()
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    public function likes()
    {
        return $this->hasMany(likesCommentaries::class, 'commentary_id');
    }

    public function isLiked(): Attribute
    {
        return Attribute::make(
            get: function () {
                $userId = auth()->id(); // o pásalo manualmente si no usas auth()
                if (!$userId) {
                    return false;
                }
                return $this->likes()->where('user_id', $userId)->exists();
            },
        );
    }

    public static function rules()
    {
        return [
            'commentary' => 'required|string|max:500',
            'publication_id' => 'required|exists:publications,id',
            'user_id' => 'required|exists:users,id',
            'reply_to_id' => 'nullable|exists:commentaries,id',
            'reply_to_user_id' => 'nullable|exists:users,id',
        ];
    }
}
