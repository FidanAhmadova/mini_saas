<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeamInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'invited_by',
        'email',
        'role',
        'token',
        'status',
        'expires_at',
        'accepted_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    // Layihə əlaqəsi
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Dəvət edən istifadəçi
    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // Dəvət edilən istifadəçi (əgər mövcuddursa)
    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    // Token yaradıcı metod
    public static function generateToken()
    {
        return Str::random(64);
    }

    // Müddəti bitibmi?
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    // Qəbul edilibmi?
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    // Pending statusdadır?
    public function isPending()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    // Dəvəti qəbul et
    public function accept($user = null)
    {
        if (!$user) {
            $user = User::where('email', $this->email)->first();
        }

        if ($user && $this->isPending()) {
            // İstifadəçini layihəyə əlavə et
            $this->project->members()->syncWithoutDetaching([
                $user->id => ['role' => $this->role]
            ]);

            // Dəvəti qəbul edilmiş olaraq işarələ
            $this->update([
                'status' => 'accepted',
                'accepted_at' => now()
            ]);

            return true;
        }

        return false;
    }

    // Dəvəti rədd et
    public function decline()
    {
        $this->update(['status' => 'declined']);
    }
}