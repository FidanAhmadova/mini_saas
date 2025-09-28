@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $project->name }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Project Info -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
                    <p class="text-gray-600 mt-2">{{ $project->description }}</p>
                    
                    <div class="mt-4 flex space-x-4">
                        @if($project->isOwnedBy(Auth::user()))
                            <a href="{{ route('projects.edit', $project) }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Edit Project
                            </a>
                        @endif
                        
                        <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Add Task
                        </a>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Team Members ({{ $project->members->count() }})</h3>
                        @if($project->isOwnedBy(Auth::user()))
                            <button onclick="document.getElementById('invite-modal').classList.remove('hidden')"
                                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Invite Member
                            </button>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($project->members as $member)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium">{{ $member->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $member->email }}</p>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $member->pivot->role === 'owner' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($member->pivot->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tasks -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Tasks ({{ $project->tasks->count() }})</h3>
                    </div>
                    
                    @if($project->tasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($project->tasks as $task)
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium">
                                                <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $task->title }}
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                                @if($task->status === 'completed') bg-green-100 text-green-800
                                                @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">{{ $task->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>No tasks yet. Create your first task!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invite Member Modal -->
@if($project->isOwnedBy(Auth::user()))
<div id="invite-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Invite Team Member</h3>
            
            <form action="{{ route('team.invite', $project) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="member">Member</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('invite-modal').classList.add('hidden')"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection