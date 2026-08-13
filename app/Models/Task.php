<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //use HasFactory;

    public const STATUS_To_do = 'to-do';

    public const STATUS_IN_PROGRESS = 'in-progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_DELAYED = 'delayed';

    public const STATUS_BLOCKED = 'blocked';

    public const STATUSES = [
        self::STATUS_BLOCKED,
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
        self::STATUS_DELAYED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_To_do
    ];

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'due_date',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function users(){
        return $this->belongsToMany(User::class,'task_user');
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
