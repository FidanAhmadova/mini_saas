@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Checkout') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Complete Your Purchase</h1>
                    <p class="text-gray-600 mt-2">{{ $plan->name }} Plan - ${{ $plan->price }}/month</p>
                </div>

                <!-- Plan Summary -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $plan->name }} Plan Features:</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>• {{ $plan->hasUnlimitedProjects() ? 'Unlimited' : $plan->max_projects }} Projects</li>
                        <li>• {{ $plan->hasUnlimitedTeamMembers() ? 'Unlimited' : $plan->max_team_members }} Team Members</li>
                        @if($plan->has_api_access)
                            <li>• API Access</li>
                        @endif
                        @if($plan->has_real_time_notifications)
                            <li>• Real-time Notifications</li>
                        @endif
                    </ul>
                </div>

                <!-- Payment Form (Simulation) -->
                <form action="{{ route('subscriptions.process-payment', $plan) }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Demo Mode</h3>
                                <p class="text-sm text-yellow-700 mt-1">This is a demo payment form. Use any test card number like 4242424242424242</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                        <input type="text" name="card_number" id="card_number" 
                               placeholder="4242 4242 4242 4242"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               required>
                        @error('card_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="card_expiry" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                            <input type="text" name="card_expiry" id="card_expiry" 
                                   placeholder="MM/YY"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   required>
                            @error('card_expiry')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="card_cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                            <input type="text" name="card_cvv" id="card_cvv" 
                                   placeholder="123"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   required>
                            @error('card_cvv')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-medium text-gray-900">Total:</span>
                            <span class="text-lg font-bold text-gray-900">${{ $plan->price }}/month</span>
                        </div>

                        <div class="flex space-x-4">
                            <a href="{{ route('subscriptions.plans') }}" 
                               class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                                Back to Plans
                            </a>
                            <button type="submit" 
                                    class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Complete Payment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
