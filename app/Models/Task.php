<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'is_completed',
    ];

    public function getAllTasks()
    {
        if (request('completed') === 'true') {
            return $this->latest()->get();
        }

        return $this->where('is_completed', false)
            ->latest()->get();
    }

    public function updateCompletion($task, $isCompleted)
    {
        $task->is_completed = $isCompleted;
        $task->save();
    }
}
