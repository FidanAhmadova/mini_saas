@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Subscription Plans') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Choose Your Plan</h1>
                    <p class="text-gray-600 mt-2">Select the perfect plan for your team's needs</p>
                </div>

                <!-- Current Plan Info -->
                @if($currentPlan)
                    <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Current Plan:</strong> {{ $currentPlan->name }} 
                                    @if($currentPlan->isFree())
                                        (Free)
                                    @else
                                        (${{ $currentPlan->price }}/month)
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Plans Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($plans as $plan)
                        <div class="border rounded-lg p-6 {{ $currentPlan && $currentPlan->id === $plan->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h3>
                                <div class="mt-2">
                                    @if($plan->isFree())
                                        <span class="text-3xl font-bold text-green-600">Free</span>
                                    @else
                                        <span class="text-3xl font-bold text-gray-900">${{ $plan->price }}</span>
                                        <span class="text-gray-600">/month</span>
                                    @endif
                                </div>
                                <p class="text-gray-600 mt-2">{{ $plan->description }}</p>
                            </div>

                            <!-- Features -->
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">
                                        @if($plan->hasUnlimitedProjects())
                                            Unlimited Projects
                                        @else
                                            {{ $plan->max_projects }} Projects
                                        @endif
                                    </span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">
                                        @if($plan->hasUnlimitedTeamMembers())
                                            Unlimited Team Members
                                        @else
                                            {{ $plan->max_team_members }} Team Members
                                        @endif
                                    </span>
                                </li>
                                @if($plan->has_api_access)
                                    <li class="flex items-center">
                                        <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">API Access</span>
                                    </li>
                                @endif
                                @if($plan->has_real_time_notifications)
                                    <li class="flex items-center">
                                        <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">Real-time Notifications</span>
                                    </li>
                                @endif
                            </ul>

                            <!-- Action Button -->
                            <div class="text-center">
                                @if($currentPlan && $currentPlan->id === $plan->id)
                                    <button disabled class="w-full bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed">
                                        Current Plan
                                    </button>
                                @else
                                    <form action="{{ route('subscriptions.subscribe', $plan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full {{ $plan->isFree() ? 'bg-green-500 hover:bg-green-700' : 'bg-blue-500 hover:bg-blue-700' }} text-white font-bold py-2 px-4 rounded">
                                            @if($plan->isFree())
                                                Switch to Free
                                            @else
                                                Start Free Trial
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cancel Subscription -->
                @if($currentPlan && !$currentPlan->isFree())
                    <div class="mt-8 text-center">
                        <form action="{{ route('subscriptions.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?')">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 underline">
                                Cancel Current Subscription
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
