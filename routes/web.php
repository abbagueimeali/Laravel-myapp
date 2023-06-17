<?php
use App\Http\controllers\HomeController;
use App\Http\controllers\LoginController;
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

Route::controller(Homecontroller::class)->group(function(){
    Route::get('/', 'home')->name('app_home');
    Route::get('/about', 'about')->name('app_about');
    Route::match(['get', 'post'], '/dashboard', 'dashboard')->middleware('auth')->name('app_dashboard');
});

Route::controller(LoginController::class)->group(function(){
    Route::get('/logout', 'logout')->name('app_lougout');
    Route::post('/exist_email', 'existEmail')->name('app_exist_email');
    Route::match(['get', 'post'], '/activation_code/{token}', 'activationCode')->name('app_activation_code');
    Route::get('/user_checker', 'userChecker')->name('app_user_checker');
    Route::get('/resend_activation_code/{token}', 'resendActivationCode')->name('app_resend_activation_code');
    Route::get('/activation_account_link/{token}', 'activationAccountLink')->name('app_activation_account_link');
    Route::match(['get','post'], '/activation_account_change_email/{token}', 'ActivationAccoutChangeEmail')->name('app_activation_account_change_email');
});







