<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    // Planlar səhifəsi
    public function plans()
    {
        $plans = Plan::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();

        $currentPlan = Auth::user()->currentPlan();

        return view('subscriptions.plans', compact('plans', 'currentPlan'));
    }

    // Plan seçimi
    public function subscribe(Plan $plan)
    {
        $user = Auth::user();
        $currentSubscription = $user->activeSubscription();

        // Əgər istifadəçinin aktiv abunəliyi varsa, onu ləğv et
        if ($currentSubscription) {
            $currentSubscription->cancel();
        }

        // Yeni abunəlik yarat
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => $plan->isFree() ? 'active' : 'trial',
            'starts_at' => now(),
            'trial_ends_at' => $plan->isFree() ? null : now()->addDays(14), // 14 günlük sınaq
            'ends_at' => $plan->isFree() ? null : now()->addDays(14)
        ]);

        if ($plan->isFree()) {
            return redirect()->route('dashboard')
                           ->with('success', 'Siz Free plana keçdiniz!');
        }

        // Pro plan üçün ödəniş səhifəsinə yönləndir
        return redirect()->route('subscriptions.checkout', $plan)
                        ->with('success', 'Pro planın 14 günlük sınağı başladı. Ödəniş etməyi unutmayın!');
    }

    // Ödəniş səhifəsi (hələlik sadə forma)
    public function checkout(Plan $plan)
    {
        return view('subscriptions.checkout', compact('plan'));
    }

    // Ödəniş təsdiqi (hələlik simulation)
    public function processPayment(Request $request, Plan $plan)
    {
        $request->validate([
            'card_number' => 'required|string|min:16',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string|min:3'
        ]);

        $user = Auth::user();
        $subscription = $user->activeSubscription();

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'ends_at' => now()->addMonth(),
                'stripe_customer_id' => 'cus_simulated_' . $user->id // Simulation
            ]);
        }

        return redirect()->route('dashboard')
                        ->with('success', 'Ödəniş uğurla həyata keçirildi! Pro plan aktivləşdirildi.');
    }

    // Abunəliyi ləğv et
    public function cancel()
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription();

        if ($subscription) {
            $subscription->cancel();
            return redirect()->route('subscriptions.plans')
                           ->with('success', 'Abunəlik ləğv edildi.');
        }

        return redirect()->route('subscriptions.plans')
                        ->with('error', 'Aktiv abunəlik tapılmadı.');
    }

    // Abunəlik tarixçəsi
    public function history()
    {
        $subscriptions = Auth::user()->subscriptions()
                                   ->with('plan')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);

        return view('subscriptions.history', compact('subscriptions'));
    }
}