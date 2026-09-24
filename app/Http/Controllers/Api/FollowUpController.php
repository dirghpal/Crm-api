<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\Followup;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{

    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'follow_up_at' => 'required|date',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'status' => 'nullable|in:pending,completed,cancelled',
                'notes' => 'nullable|string',
            ]);

            if (!$request->post('lead_id') && !$request->post('customer_id')) {
                throw new ApiStatusZeroException(
                    'lead or customer is required'
                );
            }

            if ($request->post('lead_id') && $request->post('customer_id')) {
                throw new ApiStatusZeroException(
                    'follow-up can belong to lead or customer'
                );
            }

            $followUp = FollowUp::create([
                'lead_id' => $request->post('lead_id'),
                'customer_id' => $request->post('customer_id'),
                'follow_up_at' => $request->post('follow_up_at'),
                'status' => $request->post('status', 'pending'),
                'notes' => $request->post('notes'),
                'assigned_to' => $request->post('assigned_to'),
            ]);

            $this->response['msg'] = 'follow-up saved successfully';
            $this->response['data'] = $followUp;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'status' => 'nullable|in:pending,completed,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100',
                'overdue' => 'nullable|in:0,1',
                'sort_by' => 'nullable|in:id,follow_up_at,status,created_at',
                'sort_order' => 'nullable|in:asc,desc',
            ]);

            $perPage = $request->get('per_page', 10);
            $sortBy = $request->post('sort_by', 'id');
            $sortOrder = $request->post('sort_order', 'desc');

            $query = FollowUp::with('lead', 'customer', 'assignedUser')
                ->orderBy($sortBy, $sortOrder);

            if ($request->post('lead_id') !== null) {
                $query->where('lead_id', $request->post('lead_id'));
            }

            if ($request->post('customer_id') !== null) {
                $query->where('customer_id', $request->post('customer_id'));
            }

            if ($request->post('status') !== null) {
                $query->where('status', $request->post('status'));
            }
            if ($request->post('overdue') == 1) {
                $query->where('follow_up_at', '<', now())
                    ->where('status', 'pending');
            }

            $followUps = $query->paginate($perPage);

            $this->response['msg'] = 'follow-up list';
            $this->response['data'] = $followUps;

            return response()->json($this->response);
        });
    }


    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer'
            ]);

            $followUp = FollowUp::with('lead', 'customer', 'assignedUser')
                ->find($request->post('id'));

            if (!$followUp) {
                throw new ApiStatusZeroException('follow-up not found');
            }

            $this->response['msg'] = 'follow-up detail';
            $this->response['data'] = $followUp;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:follow_ups,id',
                'follow_up_at' => 'required|data',
                'status' => 'nullable|in:pending,completed,cancelled',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'notes' => 'nullable|string',
            ]);

            $followUp = Followup::find($request->post('id'));

            if (!$followUp) {
                throw new ApiStatusZeroException('follow-up not found');
            }

            $followUp->follow_up_at = $request->post('follow_up_at');
            $followUp->notes = $request->post('notes');

            if ($request->post('status') !== null) {
                $followUp->status = $request->post('status');
            }
            $followUp->save();

            $this->response['msg'] = 'follow-up update successfully';
            $this->response['data'] = $followUp;

            return response()->json($this->response);
        });
    }


    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:follow_ups,id',
            ]);

            $followUp = FollowUp::find($request->post('id'));

            if (!$followUp) {
                throw new ApiStatusZeroException('follow-up not found');
            }

            $followUp->delete();

            $this->response['msg'] = 'follow-up deleted successfully';
            $this->response['data'] = [];

            return response()->json($this->response);
        });
    }

    public function myFollowUps(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'status' => 'nullable|in:pending,completed,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = FollowUp::with(
                'lead',
                'customer',
                'assignedUser'
            )->where('assigned_to', $request->user()->id);

            if ($request->post('status')) {
                $query->where('status', $request->post('status'));
            }

            $followUps = $query
                ->orderBy('follow_up_at', 'asc')
                ->paginate($perPage);

            $this->response['msg'] = 'my follow-ups';
            $this->response['data'] = $followUps;

            return response()->json($this->response);
        });
    }

    public function upcoming(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'days' => 'nullable|integer|min:1|max:30',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $days = $request->post('days', 7);
            $perPage = $request->post('per_page', 10);

            $followUps = FollowUp::with(
                'lead',
                'customer',
                'assignedUser'
            )
                ->where('status', 'pending')
                ->whereBetween('follow_up_at', [
                    now(),
                    now()->addDays($days),
                ])
                ->orderBy('follow_up_at', 'asc')
                ->paginate($perPage);

            $this->response['msg'] = 'upcoming follow-ups';
            $this->response['data'] = $followUps;

            return response()->json($this->response);
        });
    }

    public function overdue(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $followUps = FollowUp::with(
                'lead',
                'customer',
                'assignedUser'
            )
                ->where('status', 'pending')
                ->where('follow_up_at', '<', now())
                ->orderBy('follow_up_at', 'asc')
                ->paginate($perPage);

            $this->response['msg'] = 'overdue follow-ups';
            $this->response['data'] = $followUps;

            return response()->json($this->response);
        });
    }
}
