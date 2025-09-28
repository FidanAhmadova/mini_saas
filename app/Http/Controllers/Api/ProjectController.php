<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    // Get user's projects
    public function index(Request $request)
    {
        $projects = $request->user()
                           ->projects()
                           ->with(['tasks' => function($query) {
                               $query->select('id', 'title', 'status', 'project_id');
                           }])
                           ->withCount('tasks')
                           ->get();

        return response()->json([
            'projects' => $projects,
            'current_plan' => $request->user()->currentPlan(),
        ]);
    }

    // Get specific project
    public function show(Request $request, Project $project)
    {
        // Check if user has access to this project
        if (!$project->hasMember($request->user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $project->load(['tasks', 'members']);

        return response()->json([
            'project' => $project
        ]);
    }

    // Create new project
    public function store(Request $request)
    {
        // Check plan limits
        if (!$request->user()->canCreateProject()) {
            return response()->json([
                'message' => 'Project limit reached. Upgrade to Pro plan for unlimited projects.',
                'current_plan' => $request->user()->currentPlan(),
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);

        // Add user as owner
        $project->members()->attach($request->user()->id, ['role' => 'owner']);

        return response()->json([
            'project' => $project,
            'message' => 'Project created successfully'
        ], 201);
    }

    // Update project
    public function update(Request $request, Project $project)
    {
        // Only project owner can update
        if (!$project->isOwnedBy($request->user())) {
            return response()->json(['message' => 'Only project owner can update project'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'project' => $project,
            'message' => 'Project updated successfully'
        ]);
    }

    // Delete project
    public function destroy(Request $request, Project $project)
    {
        // Only project owner can delete
        if (!$project->isOwnedBy($request->user())) {
            return response()->json(['message' => 'Only project owner can delete project'], 403);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ]);
    }
}