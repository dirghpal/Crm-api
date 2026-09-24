<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Deal;
use App\Models\Task;
use App\Models\FollowUp;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\Activity;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'keyword' => 'required|string|min:2|max:100',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $keyword = $request->post('keyword');
            $perPage = $request->post('per_page', 10);

            $leads = Lead::where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('phone', 'like', '%' . $keyword . '%')
                    ->orWhere('company', 'like', '%' . $keyword . '%');
            })
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'lead_page');

            $customers = Customer::where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('phone', 'like', '%' . $keyword . '%')
                    ->orWhere('company', 'like', '%' . $keyword . '%');
            })
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'customer_page');

            $deals = Deal::where('title', 'like', '%' . $keyword . '%')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'deal_page');

            $tasks = Task::where('title', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'task_page');

            $followUps = FollowUp::where('notes', 'like', '%' . $keyword . '%')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'follow_up_page');

            $activities = Activity::where(function ($query) use ($keyword) {
                $query->where(
                    'title',
                    'like',
                    '%' . $keyword . '%'
                )
                    ->orWhere(
                        'description',
                        'like',
                        '%' . $keyword . '%'
                    )
                    ->orWhere(
                        'type',
                        'like',
                        '%' . $keyword . '%'
                    );
            })
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'activity_page');

            $notes = Note::where(
                'note',
                'like',
                '%' . $keyword . '%'
            )
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'note_page');

            $quotations = Quotation::where(function ($query) use ($keyword) {
                $query->where(
                    'quotation_number',
                    'like',
                    '%' . $keyword . '%'
                )
                    ->orWhere(
                        'title',
                        'like',
                        '%' . $keyword . '%'
                    );
            })
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'quotation_page');

            $invoices = Invoice::where(function ($query) use ($keyword) {
                $query->where(
                    'invoice_number',
                    'like',
                    '%' . $keyword . '%'
                )
                    ->orWhere(
                        'notes',
                        'like',
                        '%' . $keyword . '%'
                    );
            })
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'invoice_page');

            $contacts = CustomerContact::where(function ($query) use ($keyword) {
                $query->where(
                    'name',
                    'like',
                    '%' . $keyword . '%'
                )
                    ->orWhere(
                        'email',
                        'like',
                        '%' . $keyword . '%'
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        '%' . $keyword . '%'
                    )
                    ->orWhere(
                        'designation',
                        'like',
                        '%' . $keyword . '%'
                    );
            })
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'contact_page');

            $users = User::where(function ($query) use ($keyword) {
                $query->where(
                    'name',
                    'like',
                    '%' . $keyword . '%'
                )
                    ->orWhere(
                        'email',
                        'like',
                        '%' . $keyword . '%'
                    );
            })
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'user_page');

            $this->response['msg'] = 'global search';
            $this->response['data'] = [
                'leads' => $leads,
                'customers' => $customers,
                'deals' => $deals,
                'quotations' => $quotations,
                'invoices' => $invoices,
                'tasks' => $tasks,
                'follow_ups' => $followUps,
                'activities' => $activities,
                'notes' => $notes,
                'contacts' => $contacts,
                'users' => $users,
            ];

            return response()->json($this->response);
        });
    }

}
