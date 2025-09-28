<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeamInvitationController extends Controller
{
    // Layihə üçün dəvət göndər
    public function invite(Request $request, Project $project)
    {
        // Yalnız layihə sahibi dəvət göndərə bilər
        if (!$project->members()->where('user_id', Auth::id())->where('role', 'owner')->exists()) {
            return back()->with('error', 'Yalnız layihə sahibi komanda üzvü dəvət edə bilər.');
        }

        // Plan limitlərini yoxla
        if (!Auth::user()->canInviteTeamMember($project->id)) {
            return redirect()->route('subscriptions.plans')
                           ->with('error', 'Komanda üzvü limitiniz bitmişdir. Pro plana keçərək limitsiz üzv əlavə edə bilərsiniz.');
        }

        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:member,owner'
        ]);

        // Artıq üzvdürmü yoxla
        $existingMember = $project->members()->whereHas('user', function($query) use ($request) {
            $query->where('email', $request->email);
        })->exists();

        if ($existingMember) {
            return back()->with('error', 'Bu email artıq layihə üzvüdür.');
        }

        // Pending dəvət varmı yoxla
        $existingInvitation = TeamInvitation::where('project_id', $project->id)
                                          ->where('email', $request->email)
                                          ->where('status', 'pending')
                                          ->first();

        if ($existingInvitation && !$existingInvitation->isExpired()) {
            return back()->with('error', 'Bu email üçün artıq aktiv dəvət mövcuddur.');
        }

        // Köhnə dəvətləri ləğv et
        if ($existingInvitation) {
            $existingInvitation->update(['status' => 'expired']);
        }

        // Yeni dəvət yarat
        $invitation = TeamInvitation::create([
            'project_id' => $project->id,
            'invited_by' => Auth::id(),
            'email' => $request->email,
            'role' => $request->role,
            'token' => TeamInvitation::generateToken(),
            'expires_at' => now()->addDays(7) // 7 gün müddət
        ]);

        // Email göndər (hələlik sadə notification)
        try {
            // Bu real tətbiqdə Mail::send istifadə ediləcək
            // İndi sadə notification kimi log
            \Log::info("Team invitation sent to {$request->email} for project {$project->name}");
            
            return back()->with('success', "Dəvət {$request->email} ünvanına göndərildi!");
        } catch (\Exception $e) {
            return back()->with('error', 'Dəvət göndərilərkən xəta baş verdi.');
        }
    }

    // Dəvət linkindən layihəyə qoşul
    public function accept($token)
    {
        $invitation = TeamInvitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('dashboard')->with('error', 'Dəvət tapılmadı.');
        }

        if (!$invitation->isPending()) {
            return redirect()->route('dashboard')->with('error', 'Bu dəvətin müddəti bitib və ya artıq işlənib.');
        }

        // İstifadəçi login olub?
        if (!Auth::check()) {
            // Dəvət token-ni session-da saxla və login səhifəsinə yönləndir
            session(['invitation_token' => $token]);
            return redirect()->route('login')->with('message', 'Dəvəti qəbul etmək üçün əvvəl giriş edin.');
        }

        // Email uyğunmu?
        if (Auth::user()->email !== $invitation->email) {
            return redirect()->route('dashboard')
                           ->with('error', 'Bu dəvət fərqli email ünvanı üçündür. Doğru hesabla giriş edin.');
        }

        // Dəvəti qəbul et
        if ($invitation->accept(Auth::user())) {
            return redirect()->route('projects.show', $invitation->project)
                           ->with('success', "{$invitation->project->name} layihəsinə uğurla qoşuldunuz!");
        }

        return redirect()->route('dashboard')->with('error', 'Dəvət qəbul edilərkən xəta baş verdi.');
    }

    // Dəvəti rədd et
    public function decline($token)
    {
        $invitation = TeamInvitation::where('token', $token)->first();

        if (!$invitation || !$invitation->isPending()) {
            return redirect()->route('dashboard')->with('error', 'Dəvət tapılmadı və ya artıq işlənib.');
        }

        $invitation->decline();

        return redirect()->route('dashboard')->with('success', 'Dəvət rədd edildi.');
    }

    // Layihə üçün pending dəvətləri göstər
    public function pending(Project $project)
    {
        // Yalnız layihə üzvləri görə bilər
        if (!$project->members()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'Bu layihəyə giriş icazəniz yoxdur.');
        }

        $invitations = $project->invitations()
                              ->where('status', 'pending')
                              ->with('invitedBy')
                              ->orderBy('created_at', 'desc')
                              ->get();

        return view('team.invitations', compact('project', 'invitations'));
    }

    // Dəvəti ləğv et (yalnız göndərən və ya layihə sahibi)
    public function cancel(TeamInvitation $invitation)
    {
        $project = $invitation->project;
        
        // Yalnız dəvət edən və ya layihə sahibi ləğv edə bilər
        $canCancel = $invitation->invited_by === Auth::id() || 
                    $project->members()->where('user_id', Auth::id())->where('role', 'owner')->exists();

        if (!$canCancel) {
            return back()->with('error', 'Bu dəvəti ləğv etmək icazəniz yoxdur.');
        }

        $invitation->update(['status' => 'expired']);

        return back()->with('success', 'Dəvət ləğv edildi.');
    }
}