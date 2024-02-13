<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Nova;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('/logout', 'LoginController@logout');

Route::middleware('guest')->group(function () {
    Route::get('/register', 'RegistrationController@create');
    Route::post('/register', 'RegistrationController@store');
    Route::get('/login', 'LoginController@index')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::get('/register/{client:register_code}', 'RegistrationController@fromCode');
    Route::post('/register/{client:register_code}', 'RegistrationController@storeFromCode');

    Route::view('/password/reset', 'reset-password');
    Route::post('/password/reset', 'ResetPasswordController@reset')->middleware(['throttle:reset-password']);
    Route::get('/password/reset/{token}', 'ResetPasswordController@change')->name('password-reset.change');
    Route::post('/password/change/{token}', 'ResetPasswordController@update')->name('password-reset.update');
});

Route::middleware('auth:app')->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('app.home');

    Route::get('/bookings/schedule', 'BookingController@schedule');
    Route::post('/bookings', 'BookingController@store');
    Route::delete('/bookings/{shift}', 'BookingController@cancel');
    Route::post('/bookings/{shift}/close', 'BookingController@close');

    Route::get('/shifts/{shift}/pay', 'ShiftController@pay');
    Route::get('/shifts/alert', 'ShiftController@alert');

    Route::get('scan', 'ScanController@index')->name('scan.index')->name('app.scan');
    Route::post('scan', 'ScanController@scan')->name('scan.scan');

    Route::match(['GET', 'POST'], 'history', 'HistoryController@index')->name('history.index');

    Route::get('/entrance', 'EntranceController@index')->name('entrance.index');
    Route::post('/entrance/open', 'EntranceController@open')->name('entrance.open');

    Route::get('settings', 'SettingsController@index');
    Route::post('settings', 'SettingsController@update');

    Route::get('/documents', 'DocumentController@index');
    Route::post('/documents', 'DocumentController@store');
    Route::get('/documents/{id}/download', 'DocumentController@download');
    Route::put('/documents/{id}/rename', 'DocumentController@rename');

    Route::get('/mail-notifications', 'MailNotificationController@index');

    Route::get('/support', 'SupportController@index');
    Route::post('/support', 'SupportController@store');


});

// Route::get('/imunzbtvrcexwyq/{client}', function (\App\Models\Client $client) {
//     auth('app')->login($client);

//     return redirect('/cp');
// });
// Route::get('cp/resources/urls/new/{asid}', 'urlController@index');
Route::get('cp/resources/urls/new/{asid}', 'urlController@index');
Route::get('cp/resources/paragraph/new/{asid}', 'paragraphController@index');
Route::get('cp/resources/docs/new/{asid}', 'docsController@index');
