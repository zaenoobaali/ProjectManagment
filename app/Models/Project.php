<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    public const STATUS_ACTIVE ='active';

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in-progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_DELAYED = 'delayed';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_DELAYED
    ];

    protected $fillable = [
        'project_name',
        'description',
        'start_date',
        'end_date',
        'status',
        'created_by'
    ];

    public function tasks(){
        return $this->hasMany(Task::class);
    }

    public function users(){
        return $this->belongsToMany(User::class,'project_user');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
