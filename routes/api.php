<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerContactController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\DealStageHistoryController;
use App\Http\Controllers\Api\FollowUpController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoicePaymentController;
use App\Http\Controllers\Api\InvoiceStatusHistoryController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\QuotationStatusHistoryController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TaskController;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//AuthController
Route::post('register', [AuthController::class, 'register']);
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
    Route::post('follow-up/my-follow-ups', [FollowUpController::class, 'myFollowUps']);
    Route::post('follow-up/upcoming', [FollowUpController::class, 'upcoming']);

    //activity 
    Route::post('activity/save', [ActivityController::class, 'save']);
    Route::post('activity/list', [ActivityController::class, 'list']);
    Route::post('activity/detail', [ActivityController::class, 'detail']);
    Route::post('activity/update', [ActivityController::class, 'update']);
    Route::post('activity/delete', [ActivityController::class, 'delete']);
    Route::post('activity/my-activities', [ActivityController::class, 'myActivities']);
    Route::post('activity/my-activities-summary', [ActivityController::class, 'myActivitiesSummary']);

    //notes
    Route::post('note/save', [NoteController::class, 'save']);
    Route::post('note/list', [NoteController::class, 'list']);
    Route::post('note/detail', [NoteController::class, 'detail']);
    Route::post('note/update', [NoteController::class, 'update']);
    Route::post('note/delete', [NoteController::class, 'delete']);
    Route::post('note/my-notes', [NoteController::class, 'myNotes']);
    Route::post('note/my-notes-summary', [NoteController::class, 'myNotesSummary']);

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
    Route::post('notification/my-notifications', [NotificationController::class, 'myNotifications']);
    Route::post('notification/my-unread-count', [NotificationController::class, 'myNotificationsUnreadCount']);
    Route::post('notification/my-mark-all-as-read', [NotificationController::class, 'myMarkAllAsRead']);


    //Deals
    Route::post('deal/save', [DealController::class, 'save']);
    Route::post('deal/list', [DealController::class, 'list']);
    Route::post('deal/detail', [DealController::class, 'detail']);
    Route::post('deal/update', [DealController::class, 'update']);
    Route::post('deal/delete', [DealController::class, 'delete']);
    Route::post('deal/my-deals', [DealController::class, 'myDeals']);
    Route::post('deal/my-deals-summary', [DealController::class, 'myDealsSummary']);
    Route::post('deal/pipeline-summary', [DealController::class, 'pipelineSummary']);

    //dealstagehistory
    Route::post('deal-stage-history/list', [DealStageHistoryController::class, 'list']);
    Route::post('deal-stage-history/detail', [DealStageHistoryController::class, 'detail']);

    //Quotation
    Route::post('Quotation/save', [QuotationController::class, 'save']);
    Route::post('Quotation/list', [QuotationController::class, 'list']);
    Route::post('Quotation/detail', [QuotationController::class, 'detail']);
    Route::post('Quotation/update', [QuotationController::class, 'update']);
    Route::post('Quotation/delete', [QuotationController::class, 'delete']);
    Route::post('Quotation/my-quotations', [QuotationController::class, 'myQuotations']);
    Route::post('Quotation/convert-to-invoice', [QuotationController::class, 'convertToInvoice']);

    //Quotation Status History
    Route::post('quotation-status-history/list', [QuotationStatusHistoryController::class, 'list']);
    Route::post('quotation-status-history/detail', [QuotationStatusHistoryController::class, 'detail']);
    Route::post('quotation-status-history/update', [QuotationStatusHistoryController::class, 'update']);

    //invoice
    Route::post('invoice/save', [InvoiceController::class, 'save']);
    Route::post('invoice/list', [InvoiceController::class, 'list']);
    Route::post('invoice/detail', [InvoiceController::class, 'detail']);
    Route::post('invoice/update', [InvoiceController::class, 'update']);
    Route::post('invoice/delete', [InvoiceController::class, 'delete']);
    Route::post('invoice/my-invoice', [InvoiceController::class, 'myInvoices']);
    Route::post('invoice/my-invoices-summary', [InvoiceController::class, 'myInvoicesSummary']);
    Route::post('invoice/mark-overdue', [InvoiceController::class, 'markOverdue']);

    //Invoice Status History
    Route::post('invoicestatushistory/list', [InvoiceStatusHistoryController::class, 'list']);
    Route::post('invoicestatushistory/detail', [InvoiceStatusHistoryController::class, 'detail']);

    //Invoice payment+5.0 
    Route::post('invoicepayment/save', [InvoicePaymentController::class, 'save']);
    Route::post('invoicepayment/list', [InvoicePaymentController::class, 'list']);
    Route::post('invoicepayment/detail', [InvoicePaymentController::class, 'detail']);
    Route::post('invoicepayment/update', [InvoicePaymentController::class, 'update']);
    Route::post('invoicepayment/delete', [InvoicePaymentController::class, 'delete']);
    Route::post('invoicepayment/summary', [InvoicePaymentController::class, 'summary']);

    //Customer_Contact
    Route::post('customer_contact/save', [CustomerContactController::class, 'save']);
    Route::post('customer_contact/list', [CustomerContactController::class, 'list']);
    Route::post('customer_contact/detail ', [CustomerContactController::class, 'detail']);
    Route::post('customer_contact/update', [CustomerContactController::class, 'update']);
    Route::post('customer_contact/delete', [CustomerContactController::class, 'delete']);
    Route::post('customer_contact/setprimary', [CustomerContactController::class, 'setprimary']);

    //search
    Route::post(
        'search',
        [SearchController::class, 'search']

    );


    //Dashboard
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
    Route::post('dashboard/sales-by-user', [DashboardController::class, 'salesByUser']);
    Route::post('dashboard/sales-by-Customer', [DashboardController::class, 'salesByCustomer']);
    Route::post('dashboard/task-by-user', [DashboardController::class, 'tasksByUser']);
    Route::post('dashboard/follow-ups-by-user', [DashboardController::class, 'followUpsByUser']);
    Route::post('dashboard/quotations-by-user', [DashboardController::class, 'quotationsByUser']);
    Route::post('dashboard/invoices-by-user', [DashboardController::class, 'invoicesByUser']);
    Route::post('dashboard/deal-forecast', [DashboardController::class, 'dealForecast']);
    Route::post('dashboard/deal-forecast-by-user', [DashboardController::class, 'dealForecastByUser']);
    Route::post('dashboard/lead-source-summary', [DashboardController::class, 'leadSourceSummary']);
    Route::post('dashboard/monthly-revenue', [DashboardController::class, 'monthlyRevenue']);
    Route::post('dashboard/activities-by-user', [DashboardController::class, 'activitiesByUser']);
    Route::post('dashboard/notes-by-user', [DashboardController::class, 'notesByUser']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
