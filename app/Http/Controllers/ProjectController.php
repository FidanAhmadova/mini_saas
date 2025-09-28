<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
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

        Project::create($request->all());

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
}
