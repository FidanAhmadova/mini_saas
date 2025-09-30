<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->projects()->with('tasks')->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        // Plan limitlərini yoxla
        if (!auth()->user()->canCreateProject()) {
            return redirect()->route('subscriptions.plans')
                           ->with('error', 'Layihə limitiniz bitmişdir. Pro plana keçərək limitsiz layihə yarada bilərsiniz.');
        }

        return view('projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->id()
        ]);

        // Layihə sahibini owner kimi əlavə et
        $project->members()->attach(auth()->id(), ['role' => 'owner']);

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function show(Project $project)
    {
        // İstifadəçi bu layihənin üzvüdürmü yoxla
        if (!$project->hasMember(auth()->user())) {
            abort(403, 'Bu layihəyə giriş icazəniz yoxdur.');
        }

        $project->load(['tasks', 'members', 'invitations']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        // Yalnız layihə sahibi redaktə edə bilər
        if (!$project->isOwnedBy(auth()->user())) {
            abort(403, 'Yalnız layihə sahibi redaktə edə bilər.');
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        // Yalnız layihə sahibi yeniləyə bilər
        if (!$project->isOwnedBy(auth()->user())) {
            abort(403, 'Yalnız layihə sahibi yeniləyə bilər.');
        }

        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project)
    {
        // Yalnız layihə sahibi silə bilər
        if (!$project->isOwnedBy(auth()->user())) {
            abort(403, 'Yalnız layihə sahibi silə bilər.');
        }

        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
}
