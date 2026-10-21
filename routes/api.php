<?php

use App\Http\Controllers\Api\InvitationTemplateApiController;
use App\Http\Controllers\Api\EventRegistrationController; // Tambahkan ini
use Illuminate\Support\Facades\Route;

// API for getting templates by category (for event form)
Route::get('/invitation-templates/by-category/{category}', [InvitationTemplateApiController::class, 'getByCategory']);

// Rute baru untuk registrasi event
Route::post('/event-registration', [EventRegistrationController::class, 'register']);
