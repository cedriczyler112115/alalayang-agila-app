<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuickResponseController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/create-symlink', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');
    
    if (file_exists($link)) {
        return 'The "public/storage" directory already exists.';
    }
    
    try {
        symlink($target, $link);
        return 'The [public/storage] directory has been linked successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/google', [GoogleController::class , 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class , 'callback']);

Route::post('/logout', function () {
    auth()->logout();
    return redirect('https://tfoe-alalayangagila.org');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/pending', function () {
            // Double check status so valid users aren't stuck on pending view
            if (request()->user()->status === 1) {
                return redirect('/dashboard');
            }
            return view('auth.pending');
        }
        )->name('pending');

        Route::get('/profile/complete', [ProfileController::class , 'edit'])->name('profile.complete');
        Route::post('/profile/complete', [ProfileController::class , 'update']);

        Route::middleware('user.ready')->group(function () {
            Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');
            Route::get('/location', [ProfileController::class , 'location'])->name('profile.location');
            // Libraries
            Route::get('/libraries', [LibraryController::class , 'index'])->name('libraries.index');
            Route::post('/libraries/regions', [LibraryController::class , 'storeRegion'])->name('libraries.region.store');
            Route::get('/libraries/regions/{region}/edit', [LibraryController::class , 'editRegion'])->name('libraries.region.edit');
            Route::put('/libraries/regions/{region}', [LibraryController::class , 'updateRegion'])->name('libraries.region.update');
            Route::post('/libraries/regions/{region}/officers', [LibraryController::class, 'assignOfficer'])->name('libraries.region.assign_officer');
            Route::delete('/libraries/regions/{region}', [LibraryController::class , 'destroyRegion'])->name('libraries.region.destroy');
            
            // National Officers
            Route::post('/libraries/national-officers', [LibraryController::class , 'storeNationalOfficer'])->name('libraries.national_officer.store');
            Route::put('/libraries/national-officers/{officer}', [LibraryController::class , 'updateNationalOfficer'])->name('libraries.national_officer.update');
            Route::delete('/libraries/national-officers/{officer}', [LibraryController::class , 'destroyNationalOfficer'])->name('libraries.national_officer.destroy');
            
            Route::post('/libraries/clubs', [LibraryController::class , 'storeClub'])->name('libraries.club.store');
            Route::get('/libraries/clubs/{club}/edit', [LibraryController::class, 'editClub'])->name('libraries.club.edit');
            Route::put('/libraries/clubs/{club}', [LibraryController::class , 'updateClub'])->name('libraries.club.update');
            Route::post('/libraries/clubs/{club}/officers', [LibraryController::class, 'assignClubOfficer'])->name('libraries.club.assign_officer');
            Route::delete('/libraries/clubs/{club}', [LibraryController::class , 'destroyClub'])->name('libraries.club.destroy');
            Route::post('/libraries/help', [LibraryController::class , 'storeHelp'])->name('libraries.help.store');
            Route::put('/libraries/help/{help}', [LibraryController::class , 'updateHelp'])->name('libraries.help.update');
            Route::delete('/libraries/help/{help}', [LibraryController::class , 'destroyHelp'])->name('libraries.help.destroy');
            Route::post('/libraries/positions', [LibraryController::class , 'storePosition'])->name('libraries.position.store');
            Route::put('/libraries/positions/{position}', [LibraryController::class , 'updatePosition'])->name('libraries.position.update');
            Route::delete('/libraries/positions/{position}', [LibraryController::class , 'destroyPosition'])->name('libraries.position.destroy');
            Route::post('/libraries/regional-positions', [LibraryController::class, 'storeRegionalPosition'])->name('libraries.regional_position.store');
            Route::put('/libraries/regional-positions/{position}', [LibraryController::class, 'updateRegionalPosition'])->name('libraries.regional_position.update');
            Route::delete('/libraries/regional-positions/{position}', [LibraryController::class, 'destroyRegionalPosition'])->name('libraries.regional_position.destroy');
            Route::get('/libraries/clubs-by-region/{region}', [LibraryController::class , 'getClubsByRegion'])->name('libraries.clubs.byRegion');

            Route::get('/quick-response', [QuickResponseController::class , 'index'])->name('quick.response');
            Route::post('/quick-response', [QuickResponseController::class , 'store']);

            Route::get('/search-kuya', [MemberController::class , 'index'])->name('search.kuya');

            Route::get('/organizational-structure', function() {
                $regions = \App\Models\LibRegion::all();
                
                $regional_officers_all = \App\Models\User::whereNotNull('lib_region_id')
                    ->whereNotNull('lib_regional_position_id')
                    ->with('club')
                    ->get()
                    ->groupBy('lib_region_id');

                $clubs_all = \App\Models\LibClubName::all()->groupBy('lib_region_id');

                $all_club_officers = \App\Models\User::whereNotNull('lib_club_name_id')
                    ->whereNotNull('lib_position_id')
                    ->get()
                    ->groupBy('lib_club_name_id');

                $regional_positions = \App\Models\LibRegionalPosition::orderBy('id')->get();
                $club_positions = \App\Models\LibPosition::orderBy('id')->get();
                $national_officers = \App\Models\LibNationalOfficer::orderBy('id')->get();

                return view('structure', compact('regions', 'regional_officers_all', 'clubs_all', 'all_club_officers', 'regional_positions', 'club_positions', 'national_officers'));
            })->name('org.structure');

            // Users
            Route::get('/users', [UserController::class , 'index'])->name('users.index');
            Route::post('/users/{user}/status', [UserController::class , 'updateStatus'])->name('users.updateStatus');

            // Announcements CRUD
            Route::resource('announcements', AnnouncementController::class);
            Route::post('/announcements/{announcement}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');

            // Generic Group Chat Routing
            Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
            Route::get('/chat/{conversation}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
            Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        }
        );
    });
