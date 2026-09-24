<?php

namespace App\Http\Controllers\Api;


use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Deal;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\InvoicePayment;


use Illuminate\Http\Request;


class DashboardController extends Controller
{

    public function summary(Request $request)
    {
        return handleApiRequest(function () {

            $this->response['msg'] = 'dashboard summary';

            $this->response['data'] = [
                'total_leads' => Lead::count(),

                'new_leads' => Lead::where('status', 'new')->count(),

                'contacted_leads' => Lead::where('status', 'contacted')->count(),

                'qualified_leads' => Lead::where('status', 'qualified')->count(),

                'converted_leads' => Lead::where('is_converted', 1)->count(),

                'lost_leads' => Lead::where('status', 'lost')->count(),

                'total_customers' => Customer::count(),

                'active_customers' => Customer::where('status', 1)->count(),

                'inactive_customers' => Customer::where('status', 0)->count(),

                'pending_tasks' => Task::where('status', 'pending')->count(),

                'completed_tasks' => Task::where('status', 'completed')->count(),

                'overdue_tasks' => Task::where('due_at', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count(),

                'total_follow_ups' => FollowUp::count(),

                'pending_follow_ups' => FollowUp::where('status', 'pending')->count(),

                'completed_follow_ups' => FollowUp::where('status', 'completed')->count(),

                'total_deals' => Deal::count(),
                'open_deals' => Deal::where('status', 'open')->count(),
                'won_deals' => Deal::where('status', 'won')->count(),

                'total_quotations' => Quotation::count(),
                'pending_quotations' => Quotation::whereIn('status', [
                    'draft',
                    'sent',
                ])->count(),
                'accepted_quotations' => Quotation::where(
                    'status',
                    'accepted'
                )->count(),

                'total_invoices' => Invoice::count(),
                'paid_invoices' => Invoice::where('status', 'paid')->count(),
                'pending_invoices' => Invoice::whereIn('status', [
                    'draft',
                    'sent',
                ])->count(),

                'total_invoice_amount' => Invoice::sum('total_amount'),

                'total_paid_amount' => InvoicePayment::where(
                    'status',
                    'completed'
                )->sum('amount'),

                'new_deal_amount' => Deal::where('stage', 'new')
                    ->sum('amount'),

                'qualified_deal_amount' => Deal::where('stage', 'qualified')
                    ->sum('amount'),

                'proposal_deal_amount' => Deal::where('stage', 'proposal')
                    ->sum('amount'),

                'negotiation_deal_amount' => Deal::where('stage', 'negotiation')
                    ->sum('amount'),

                'won_deal_amount' => Deal::where('stage', 'won')
                    ->sum('amount'),

                'lost_deal_amount' => Deal::where('stage', 'lost')
                    ->sum('amount'),

                'outstanding_invoice_amount' => max(
                    Invoice::sum('total_amount') -
                        InvoicePayment::where('status', 'completed')->sum('amount'),
                    0
                ),

                'lead_conversion_rate' => Lead::count() > 0
                    ? round(
                        (Lead::where('is_converted', 1)->count() / Lead::count()) * 100,
                        2
                    )
                    : 0,

                'overdue_invoices' => Invoice::where('due_date', '<', now()->toDateString())
                    ->whereNotIn('status', ['paid', 'cancelled'])
                    ->count(),
            ];

            return response()->json($this->response);
        });
    }

    public function salesByUser(Request $request)
    {
        return handleApiRequest(function () {

            $users = User::whereHas('deals')
                ->withCount('deals')
                ->get();

            $data = [];

            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_deals' => $user->deals()->count(),
                    'open_deals' => $user->deals()
                        ->where('status', 'open')
                        ->count(),
                    'won_deals' => $user->deals()
                        ->where('status', 'won')
                        ->count(),
                    'total_amount' => $user->deals()->sum('amount'),
                    'won_amount' => $user->deals()
                        ->where('status', 'won')
                        ->sum('amount'),
                ];
            }

            $this->response['msg'] = 'sales by user';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }

    public function salesByCustomer(Request $request)
    {
        return handleApiRequest(function () {

            $customers = Customer::whereHas('deals')
                ->withCount('deals')
                ->get();

            $data = [];

            foreach ($customers as $customer) {
                $data[] = [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'total_deals' => $customer->deals()->count(),
                    'open_deals' => $customer->deals()
                        ->where('status', 'open')
                        ->count(),
                    'won_deals' => $customer->deals()
                        ->where('status', 'won')
                        ->count(),
                    'total_amount' => $customer->deals()->sum('amount'),
                    'won_amount' => $customer->deals()
                        ->where('status', 'won')
                        ->sum('amount'),
                ];
            }

            $this->response['msg'] = 'sales by customer';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }

    public function tasksByUser(Request $request)
    {
        return handleApiRequest(function () {

            $users = User::whereHas('tasks')
                ->withCount('tasks')
                ->get();

            $data = [];

            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_tasks' => $user->tasks()->count(),
                    'pending_tasks' => $user->tasks()
                        ->where('status', 'pending')
                        ->count(),
                    'in_progress_tasks' => $user->tasks()
                        ->where('status', 'in_progress')
                        ->count(),
                    'completed_tasks' => $user->tasks()
                        ->where('status', 'completed')
                        ->count(),
                    'cancelled_tasks' => $user->tasks()
                        ->where('status', 'cancelled')
                        ->count(),
                ];
            }

            $this->response['msg'] = 'tasks by user';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }

    public function followUpsByUser(Request $request)
    {
        return handleApiRequest(function () {

            $users = User::whereHas('followUps')
                ->withCount('followUps')
                ->get();

            $data = [];

            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_follow_ups' => $user->followUps()->count(),
                    'pending_follow_ups' => $user->followUps()
                        ->where('status', 'pending')
                        ->count(),
                    'completed_follow_ups' => $user->followUps()
                        ->where('status', 'completed')
                        ->count(),
                    'cancelled_follow_ups' => $user->followUps()
                        ->where('status', 'cancelled')
                        ->count(),
                ];
            }

            $this->response['msg'] = 'follow-ups by user';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }

    public function quotationsByUser(Request $request)
    {
        return handleApiRequest(function () {

            $users = User::whereHas('quotations')
                ->withCount('quotations')
                ->get();

            $data = [];

            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_quotations' => $user->quotations()->count(),
                    'draft_quotations' => $user->quotations()
                        ->where('status', 'draft')
                        ->count(),
                    'sent_quotations' => $user->quotations()
                        ->where('status', 'sent')
                        ->count(),
                    'accepted_quotations' => $user->quotations()
                        ->where('status', 'accepted')
                        ->count(),
                    'rejected_quotations' => $user->quotations()
                        ->where('status', 'rejected')
                        ->count(),
                    'total_amount' => $user->quotations()
                        ->sum('total_amount'),
                    'accepted_amount' => $user->quotations()
                        ->where('status', 'accepted')
                        ->sum('total_amount'),
                ];
            }

            $this->response['msg'] = 'quotations by user';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }


    public function invoicesByUser(Request $request)
    {
        return handleApiRequest(function () {

            $users = User::whereHas('invoices')
                ->withCount('invoices')
                ->get();

            $data = [];

            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_invoices' => $user->invoices()->count(),

                    'draft_invoices' => $user->invoices()
                        ->where('status', 'draft')
                        ->count(),

                    'sent_invoices' => $user->invoices()
                        ->where('status', 'sent')
                        ->count(),

                    'paid_invoices' => $user->invoices()
                        ->where('status', 'paid')
                        ->count(),

                    'overdue_invoices' => $user->invoices()
                        ->where('status', 'overdue')
                        ->count(),

                    'cancelled_invoices' => $user->invoices()
                        ->where('status', 'cancelled')
                        ->count(),

                    'total_amount' => $user->invoices()
                        ->sum('total_amount'),

                    'paid_amount' => $user->invoices()
                        ->where('status', 'paid')
                        ->sum('total_amount'),
                ];
            }

            $this->response['msg'] = 'invoices by user';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }

    public function dealForecast(Request $request)
    {
        return handleApiRequest(function () {

            $deals = Deal::whereNotIn('status', [
                'won',
                'lost',
                'cancelled',
            ])->get();

            $totalAmount = $deals->sum('amount');

            $weightedAmount = $deals->sum(function ($deal) {
                return ($deal->amount * $deal->probability) / 100;
            });

            $this->response['msg'] = 'deal forecast';
            $this->response['data'] = [
                'total_open_deals' => $deals->count(),
                'total_open_amount' => $totalAmount,
                'weighted_forecast_amount' => round($weightedAmount, 2),
            ];

            return response()->json($this->response);
        });
    }

    public function dealForecastByUser(Request $request)
    {
        return handleApiRequest(function () {

            $users = User::whereHas('deals')
                ->get();

            $data = [];

            foreach ($users as $user) {

                $deals = $user->deals()
                    ->whereNotIn('status', [
                        'won',
                        'lost',
                        'cancelled',
                    ])
                    ->get();

                $openAmount = $deals->sum('amount');

                $weightedAmount = $deals->sum(function ($deal) {
                    return ($deal->amount * $deal->probability) / 100;
                });

                $data[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'open_deals' => $deals->count(),
                    'open_amount' => $openAmount,
                    'weighted_forecast_amount' => round(
                        $weightedAmount,
                        2
                    ),
                ];
            }

            $this->response['msg'] = 'deal forecast by user';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }

    public function leadSourceSummary(Request $request)
    {
        return handleApiRequest(function () {

            $sources = Lead::select('source')
                ->selectRaw('COUNT(*) as total_leads')
                ->selectRaw(
                    'SUM(CASE WHEN is_converted = 1 THEN 1 ELSE 0 END) as converted_leads'
                )
                ->whereNotNull('source')
                ->groupBy('source')
                ->orderBy('total_leads', 'desc')
                ->get();

            $this->response['msg'] = 'lead source summary';
            $this->response['data'] = $sources;

            return response()->json($this->response);
        });
    }

    public function monthlyRevenue(Request $request)
    {
        return handleApiRequest(function () {

            $data = [];

            for ($i = 11; $i >= 0; $i--) {

                $startDate = now()
                    ->subMonths($i)
                    ->startOfMonth();

                $endDate = now()
                    ->subMonths($i)
                    ->endOfMonth();

                $amount = InvoicePayment::where('status', 'completed')
                    ->whereBetween('payment_date', [
                        $startDate,
                        $endDate,
                    ])
                    ->sum('amount');

                $data[] = [
                    'month' => $startDate->format('Y-m'),
                    'revenue' => $amount,
                ];
            }

            $this->response['msg'] = 'monthly revenue';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }

    public function activitiesByUser(Request $request)
    {
        return handleApiRequest(function () {

            $users = User::whereHas('activities')
                ->withCount('activities')
                ->get();

            $data = [];

            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_activities' => $user->activities()->count(),
                    'pending_activities' => $user->activities()
                        ->where('status', 'pending')
                        ->count(),
                    'completed_activities' => $user->activities()
                        ->where('status', 'completed')
                        ->count(),
                    'cancelled_activities' => $user->activities()
                        ->where('status', 'cancelled')
                        ->count(),
                ];
            }

            $this->response['msg'] = 'activities by user';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }

    public function notesByUser(Request $request)
    {
        return handleApiRequest(function () {

            $users = User::whereHas('notes')
                ->withCount('notes')
                ->get();

            $data = [];

            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'total_notes' => $user->notes()->count(),
                ];
            }

            $this->response['msg'] = 'notes by user';
            $this->response['data'] = $data;

            return response()->json($this->response);
        });
    }
}
