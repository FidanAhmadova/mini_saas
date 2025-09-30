@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Team Invitations') }} - {{ $project->name }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">Pending Invitations</h3>
                    <a href="{{ route('projects.show', $project) }}" class="text-blue-500 hover:text-blue-700">
                        ← Back to Project
                    </a>
                </div>

                @if($invitations->count() > 0)
                    <div class="space-y-4">
                        @foreach($invitations as $invitation)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $invitation->email }}</h4>
                                                <p class="text-sm text-gray-500">
                                                    Invited by {{ $invitation->invitedBy->name }} as {{ ucfirst($invitation->role) }}
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    Sent {{ $invitation->created_at->diffForHumans() }} • 
                                                    Expires {{ $invitation->expires_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form action="{{ route('team.cancel-invitation', $invitation) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to cancel this invitation?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No pending invitations</h3>
                        <p class="mt-1 text-sm text-gray-500">All invitations have been accepted or expired.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
