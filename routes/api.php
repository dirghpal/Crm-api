<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FollowUpController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TaskController;
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
    Route::post('lead/myLeads', [LeadController::class, 'myLeads']);
    Route::post('lead/my-leads-summary', [LeadController::class, 'myLeadsSummary']);

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
    Route::post('follow-up/update', [FollowUpController::class, 'update']);
    Route::post('follow-up/delete', [FollowUpController::class, 'delete']);

    //activity
    Route::post('activity/save', [ActivityController::class, 'save']);
    Route::post('activity/list', [ActivityController::class, 'list']);
    Route::post('activity/detail', [ActivityController::class, 'detail']);
    Route::post('activity/update', [ActivityController::class, 'update']);
    Route::post('activity/delete', [ActivityController::class, 'delete']);

    //notes
    Route::post('note/save', [NoteController::class, 'save']);
    Route::post('note/list', [NoteController::class, 'list']);
    Route::post('note/detail', [NoteController::class, 'detail']);
    Route::post('note/update', [NoteController::class, 'update']);
    Route::post('note/delete', [NoteController::class, 'delete']);

    //task
    Route::post('task/save', [TaskController::class, 'save']);
    Route::post('task/list', [TaskController::class, 'list']);
    Route::post('task/detail', [TaskController::class, 'detail']);
    Route::post('task/update', [TaskController::class, 'update']);
    Route::post('task/delete', [TaskController::class, 'delete']);
    Route::post('task/my-tasks', [TaskController::class, 'myTasks']);
    Route::post('task/my-tasks-summary', [TaskController::class, 'myTasksSummary']);

    //Notification 
    Route::post('notification/list', [NotificationController::class, 'list']);
    Route::post('notification/detail', [NotificationController::class, 'detail']);
    Route::post('notification/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('notification/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('notification/unread-count', [NotificationController::class, 'unreadCount']);

    //Dashboard
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
