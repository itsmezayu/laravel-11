<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PtnProdiController;
use App\Http\Controllers\Api\EligibleStudentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/ptn/{ptnId}/prodi', [PtnProdiController::class, 'getProdiByPtn']);
Route::get('/eligible-students', [EligibleStudentController::class, 'getEligibleStudents']);

