<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\Task;
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
            ];

            return response()->json($this->response);
        });
    }
}
