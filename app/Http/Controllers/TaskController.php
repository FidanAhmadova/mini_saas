<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('project')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('tasks.create', compact('projects'));
    }
    
    public function edit(string $id)
    {
        $task = Task::find($id);
        $projects = Project::all();
        return view('tasks.edit', compact('task', 'projects'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
            'project_id' => 'required|exists:projects,id',
        ]);

        $task = Task::create($request->all());
        
        // Real-time bildiriş göndər
        broadcast(new TaskCreated($task));

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

   
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
            'project_id' => 'required|exists:projects,id',
        ]);

        $task->update($request->all());
        
        // Real-time bildiriş göndər
        broadcast(new TaskUpdated($task));

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }
}
