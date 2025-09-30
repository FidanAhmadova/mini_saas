<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'user_id'];

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }
    public function members() {
        return $this->belongsToMany(User::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Komanda dəvətləri
    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    // İstifadəçi bu layihənin sahibidirmi?
    public function isOwnedBy($user)
    {
        return $this->members()
                    ->where('user_id', $user->id)
                    ->where('role', 'owner')
                    ->exists();
    }

    // İstifadəçi bu layihənin üzvüdürmü?
    public function hasMember($user)
    {
        return $this->members()
                    ->where('user_id', $user->id)
                    ->exists();
    }
    
}


