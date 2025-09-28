<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;

class TaskController extends Controller
{
    // Get tasks for a project
    public function index(Request $request, Project $project)
    {
        // Check if user has access to this project
        if (!$project->hasMember($request->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tasks = $project->tasks()
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json([
            'tasks' => $tasks,
            'project' => $project->only(['id', 'name'])
        ]);
    }

    // Get specific task
    public function show(Request $request, Task $task)
    {
        // Check if user has access to this task's project
        if (!$task->project->hasMember($request->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->load('project');

        return response()->json([
            'task' => $task
        ]);
    }

    // Create new task
    public function store(Request $request, Project $project)
    {
        // Check if user has access to this project
        if (!$project->hasMember($request->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'project_id' => $project->id,
        ]);

        return response()->json([
            'task' => $task,
            'message' => 'Task created successfully'
        ], 201);
    }

    // Update task
    public function update(Request $request, Task $task)
    {
        // Check if user has access to this task's project
        if (!$task->project->hasMember($request->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'task' => $task,
            'message' => 'Task updated successfully'
        ]);
    }

    // Delete task
    public function destroy(Request $request, Task $task)
    {
        // Check if user has access to this task's project
        if (!$task->project->hasMember($request->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    // Get all user's tasks across projects
    public function userTasks(Request $request)
    {
        $user = $request->user();
        
        $tasks = Task::whereHas('project', function($query) use ($user) {
            $query->whereHas('members', function($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id);
            });
        })
        ->with('project:id,name')
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'tasks' => $tasks,
            'total_count' => $tasks->count(),
        ]);
    }
}