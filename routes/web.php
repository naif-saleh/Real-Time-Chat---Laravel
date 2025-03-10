<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users', [ChatController::class, 'index'])->name('users');
    Route::get('/chat/{receiver_id}', [ChatController::class, 'chat'])->name('chat');
    Route::post('/chat/{receiver_id}/send', [ChatController::class, 'sendMessage'])->name('sendMessage');
    Route::post('/chat/typing', [ChatController::class, 'typing']);
    Route::post('/online', [ChatController::class, 'setOnline']);
    Route::post('/offline', [ChatController::class, 'setOffline']);

});

require __DIR__.'/auth.php';
