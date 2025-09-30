<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status', // active, canceled, expired, trial
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'canceled_at',
        'stripe_subscription_id',
        'stripe_customer_id'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    // İstifadəçi əlaqəsi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Plan əlaqəsi
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Abunəlik aktivdirmi?
    public function isActive()
    {
        return $this->status === 'active' && 
               ($this->ends_at === null || $this->ends_at->isFuture());
    }

    // Sınaq dövründəmi?
    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    // Ləğv edilib?
    public function isCanceled()
    {
        return $this->status === 'canceled';
    }

    // Müddəti bitib?
    public function isExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    // Abunəliyi ləğv et
    public function cancel()
    {
        $this->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);
    }

    // Abunəliyi yenilə
    public function renew($months = 1)
    {
        $this->update([
            'status' => 'active',
            'ends_at' => $this->ends_at ? $this->ends_at->addMonths($months) : now()->addMonths($months),
            'canceled_at' => null,
        ]);
    }
}