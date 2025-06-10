<?php

use App\Mail\WelcomeMail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::get('/line-login', function () {
    return view('line-login');
});

// Route::get('/mailable', function () {
//     return new WelcomeMail();
// });


Route::get('/mailable', function () {
    return new App\Mail\PreviewMail();
});