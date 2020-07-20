<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// public routes
Route::get('me', 'User\MeController@getMe');

// authenticated only
Route::group(['middleware' => ['auth:api']], function() {
	Route::post('logout', 'Auth\LoginController@logout');

	Route::put('settings/profile', 'User\SettingsController@updateProfile');
	Route::put('settings/password', 'User\SettingsController@updatePassword');
});

// guests only
Route::group(['middleware' => ['guest:api']], function() {
	Route::post('register', 'Auth\RegisterController@register');
	Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
	Route::post('verification/resend/{user}', 'Auth\VerificationController@resend');
	Route::post('login', 'Auth\LoginController@login');
	Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
	Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});