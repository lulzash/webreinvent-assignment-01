<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $task = new Task;
        return response()->json($task->getAllTasks(), 200);
    }

    public function store(TaskCreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $task = Task::create($validated);
            return response()->json($task, 201);
        } catch (\Exception $e) {
            return response()->json($e->errors(), 400);
        }
    }

    public function update(TaskUpdateRequest $request, Task $task)
    {
        try {
            $validated = $request->validated();
            $task->update($validated);
            return response()->json($task, 200);
        } catch (\Exception $e) {
            return response()->json($e->errors(), 400);
        }
    }

    public function complete(Task $task)
    {
        try {
            $task->updateCompletion($task, true);
            return response()->json($task, 200);
        } catch (\Exception $e) {
            return response()->json($e->errors(), 400);
        }
    }

    public function incomplete(Task $task)
    {
        try {
            $task->updateCompletion($task, false);
            return response()->json($task, 200);
        } catch (\Exception $e) {
            return response()->json($e->errors(), 400);
        }
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }
}
