<?php

use App\Http\Controllers\MUX\VideoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('upload-video', [VideoController::class, 'uploadVideo'])->name('uploadVideo');
    Route::get('direct-video-upload-config', [VideoController::class, 'directVideoUploadConfig'])->name('directVideoUploadConfig');
    Route::post('upload-video', [VideoController::class, 'storeVideoData'])->name('storeVideoData');
    Route::get('get-uploaded-video/{id}', [VideoController::class, 'getUploadVideoFromUploadId'])->name('getUploadVideoFromUploadId');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
