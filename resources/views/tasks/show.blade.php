@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $task->title }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Description</h3>
                        <p class="text-gray-600">{{ $task->description }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium">Status:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($task->status == 'completed') bg-green-100 text-green-800
                                    @elseif($task->status == 'in_progress') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Project:</span>
                                @if($task->project)
                                    <a href="{{ route('projects.show', $task->project) }}" 
                                       class="text-blue-600 hover:text-blue-800 underline">
                                        {{ $task->project->name }}
                                    </a>
                                @else
                                    <span class="text-gray-500">No project assigned</span>
                                @endif
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Created:</span>
                                <span class="text-gray-600">{{ $task->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Updated:</span>
                                <span class="text-gray-600">{{ $task->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-between">
                    <a href="{{ route('tasks.index') }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to Tasks
                    </a>
                    <a href="{{ route('tasks.edit', $task) }}" 
                       class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit Task
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
