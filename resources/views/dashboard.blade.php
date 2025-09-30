@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Message -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-semibold mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                
                <!-- Current Plan Info -->
                @php
                    $currentPlan = Auth::user()->currentPlan();
                    $projectCount = Auth::user()->projects()->count();
                @endphp
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-blue-700">
                                <strong>Current Plan:</strong> {{ $currentPlan->name }}
                                @if(!$currentPlan->isFree())
                                    (${{ $currentPlan->price }}/month)
                                @endif
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                Projects: {{ $projectCount }}/{{ $currentPlan->hasUnlimitedProjects() ? '∞' : $currentPlan->max_projects }}
                            </p>
                        </div>
                        @if($currentPlan->isFree())
                            <a href="{{ route('subscriptions.plans') }}" class="text-xs bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">
                                Upgrade to Pro
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Stats -->
                @php
                    $totalTasks = Auth::user()->projects()->withCount('tasks')->get()->sum('tasks_count');
                    $completedTasks = \App\Models\Task::whereHas('project', function($query) {
                        $query->whereHas('members', function($subQuery) {
                            $subQuery->where('user_id', Auth::id());
                        });
                    })->where('status', 'completed')->count();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900">Projects</h4>
                        <p class="text-2xl font-bold text-green-700">{{ $projectCount }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900">Total Tasks</h4>
                        <p class="text-2xl font-bold text-blue-700">{{ $totalTasks }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-purple-900">Completed</h4>
                        <p class="text-2xl font-bold text-purple-700">{{ $completedTasks }}</p>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('projects.index') }}" class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <h3 class="font-semibold text-blue-900">📁 Projects</h3>
                        <p class="text-blue-700">Manage your projects</p>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="block p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                        <h3 class="font-semibold text-green-900">✅ Tasks</h3>
                        <p class="text-green-700">Manage your tasks</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        @php
            $recentProjects = Auth::user()->projects()->with('tasks')->latest()->take(3)->get();
        @endphp
        
        @if($recentProjects->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Recent Projects</h3>
                    
                    <div class="space-y-3">
                        @foreach($recentProjects as $project)
                            <div class="border rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium">
                                            <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $project->name }}
                                            </a>
                                        </h4>
                                        <p class="text-sm text-gray-600">{{ Str::limit($project->description, 100) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $project->tasks->count() }} tasks • 
                                            {{ $project->tasks->where('status', 'completed')->count() }} completed
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $project->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('projects.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                            View All Projects →
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center text-gray-500">
                    <div class="mb-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No projects yet</h3>
                    <p class="text-gray-600 mb-4">Get started by creating your first project</p>
                    <a href="{{ route('projects.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create Your First Project
                    </a>
                </div>
            </div>
        @endif

        <!-- API Information for Pro Users -->
        @if(Auth::user()->hasApiAccess())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">🔗 API Access</h3>
                    <p class="text-gray-600 mb-4">You have API access! Use these endpoints for mobile app integration:</p>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <code class="text-sm">
                            <strong>Base URL:</strong> {{ url('/api') }}<br>
                            <strong>Endpoints:</strong><br>
                            • POST /api/auth/login<br>
                            • GET /api/projects<br>
                            • GET /api/projects/{id}/tasks<br>
                            • GET /api/my-tasks
                        </code>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-2">
                        Generate API tokens from your profile settings.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Real-time bildirişlər üçün Echo dinləyicisi
    @if(Auth::user()->hasRealTimeNotifications())
        document.addEventListener('DOMContentLoaded', function() {
            // Bütün layihələr üçün dinlə
            @foreach(Auth::user()->projects as $project)
                window.Echo.private('project.{{ $project->id }}')
                    .listen('TaskCreated', (e) => {
                        showNotification(e.message, 'success');
                        // Səhifəni yenilə
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    })
                    .listen('TaskUpdated', (e) => {
                        showNotification(e.message, 'info');
                        // Səhifəni yenilə
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    })
                    .listen('TeamMemberInvited', (e) => {
                        showNotification(e.message, 'warning');
                    });
            @endforeach
        });

        function showNotification(message, type = 'info') {
            // Sadə notification göstər
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'warning' ? 'bg-yellow-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // 5 saniyə sonra sil
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    @endif
</script>
@endpush
@endsection