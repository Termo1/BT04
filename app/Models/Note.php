<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use softDeletes, HasFactory;

    protected $table = 'notes';
    protected $primaryKey = 'id';

    //protected $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'status',
        'is_pinned'
    ];
        //protected $guarded = ['id'];
        
    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function publish()
    {
        $this->status = 'published';
        return $this->save();
    }

    public function archive()
    {
        $this->status = 'archived';
        return $this->save();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

      public static function searchPublished(string $q, int $limit = 20)
    {
        $q = trim($q);

        return static::query()
            ->where('status', 'published')
            ->where(function (Builder $x) use ($q) {
                $x->where('title', 'like', "%{$q}%")
                    ->orWhere('body', 'like', "%{$q}%");
            })
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

   
}
