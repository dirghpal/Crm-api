<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\FollowUpController;
use App\Http\Controllers\Api\LeadController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//AuthController
Route::post('registar', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    //auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    //lead
    Route::post('lead/save', [LeadController::class, 'save']);
    Route::post('lead/list', [LeadController::class, 'list']);
    Route::post('lead/detail', [LeadController::class, 'detail']);
    Route::post('lead/update', [LeadController::class, 'update']);
    Route::post('lead/delete', [LeadController::class, 'delete']);
    Route::post('lead/convert', [LeadController::class, 'convert']);

    //Customer
    Route::post('customer/save', [CustomerController::class, 'save']);
    Route::post('customer/list', [CustomerController::class, 'list']);
    Route::post('customer/detail', [CustomerController::class, 'detail']);
    Route::post('customer/update', [CustomerController::class, 'update']);
    Route::post('customer/delete', [CustomerController::class, 'delete']);

    //FollowUp 
    Route::post('follow-up/save', [FollowUpController::class, 'save']);
    Route::post('follow-up/list', [FollowUpController::class, 'list']);
    Route::post('follow-up/detail', [FollowUpController::class, 'detail']);
    Route::post('follow-up/update',[FollowUpController::class, 'update']);
    Route::post('follow-up/delete',[FollowUpController::class, 'delete']);

});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
