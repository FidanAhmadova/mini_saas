
<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Test registration page
Route::get('/test-register', function () {
    return view('test-register');
});

// Test registration route (temporary - without CSRF)
Route::post('/test-register', function (Illuminate\Http\Request $request) {
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully!',
            'user' => $user->only(['name', 'email'])
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 422);
    }
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::middleware(['auth'])->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Subscription routes
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])->name('plans');
        Route::post('/subscribe/{plan}', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::get('/checkout/{plan}', [App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/process-payment/{plan}', [App\Http\Controllers\SubscriptionController::class, 'processPayment'])->name('process-payment');
        Route::post('/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel');
        Route::get('/history', [App\Http\Controllers\SubscriptionController::class, 'history'])->name('history');
    });

    // Team invitation routes
    Route::prefix('team')->name('team.')->group(function () {
        Route::post('/invite/{project}', [App\Http\Controllers\TeamInvitationController::class, 'invite'])->name('invite');
        Route::get('/invitations/{project}', [App\Http\Controllers\TeamInvitationController::class, 'pending'])->name('invitations');
        Route::post('/cancel-invitation/{invitation}', [App\Http\Controllers\TeamInvitationController::class, 'cancel'])->name('cancel-invitation');
    });
});

// Public invitation routes (no auth required)
Route::get('/team/accept/{token}', [App\Http\Controllers\TeamInvitationController::class, 'accept'])->name('team.accept');
Route::get('/team/decline/{token}', [App\Http\Controllers\TeamInvitationController::class, 'decline'])->name('team.decline');

require __DIR__.'/auth.php';
