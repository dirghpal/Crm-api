<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException as ExceptionApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\DealStageHistory;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Exceptions\ApiStatusZeroException;

class DealController extends Controller
{

    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'stage' => 'nullable|in:new,qualified,proposal,negotiation,won,lost',
                'probability' => 'nullable|integer|min:0|max:100',
                'expected_close_at' => 'nullable|date',
                'status' => 'nullable|in:open,won,lost,cancelled',
                'notes' => 'nullable|string',
            ]);

            if (!$request->post('lead_id') && !$request->post('customer_id')) {
                throw new ExceptionApiStatusZeroException(
                    'lead or customer is required'
                );
            }

            $deal = Deal::create([
                'lead_id' => $request->post('lead_id'),
                'customer_id' => $request->post('customer_id'),
                'assigned_to' => $request->post('assigned_to'),
                'title' => $request->post('title'),
                'amount' => $request->post('amount'),
                'stage' => $request->post('stage', 'new'),
                'probability' => $request->post('probability', 0),
                'expected_close_at' => $request->post('expected_close_at'),
                'status' => $request->post('status', 'open'),
                'notes' => $request->post('notes'),
            ]);

            DealStageHistory::create([
                'deal_id' => $deal->id,
                'stage' => $deal->stage,
                'probability' => $deal->probability,
                'comment' => 'Deal created',
            ]);


            if ($deal->assigned_to) {
                Notification::create([
                    'user_id' => $deal->assigned_to,
                    'title' => 'New Deal Assigned',
                    'message' => 'A new deal has been assigned to you.',
                ]);
            }

            $this->response['msg'] = 'deal saved successfully';
            $this->response['data'] = $deal;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'stage' => 'nullable|in:new,qualified,proposal,negotiation,won,lost',
                'status' => 'nullable|in:open,won,lost,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = Deal::with('lead', 'customer', 'assignedUser');

            if ($request->post('lead_id')) {
                $query->where('lead_id', $request->post('lead_id'));
            }

            if ($request->post('customer_id')) {
                $query->where('customer_id', $request->post('customer_id'));
            }

            if ($request->post('assigned_to')) {
                $query->where('assigned_to', $request->post('assigned_to'));
            }

            if ($request->post('stage')) {
                $query->where('stage', $request->post('stage'));
            }

            if ($request->post('status')) {
                $query->where('status', $request->post('status'));
            }

            $deals = $query
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'deal list';
            $this->response['data'] = $deals;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:deals,id',
            ]);

            $deal = Deal::with(
                'lead',
                'customer',
                'assignedUser',
                'stageHistories',
                'quotations',
                'invoices',
                
            )->find($request->post('id'));

            if (!$deal) {
                throw new ExceptionApiStatusZeroException('deal not found');
            }

            $this->response['msg'] = 'deal detail';
            $this->response['data'] = $deal;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:deals,id',
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'stage' => 'nullable|in:new,qualified,proposal,negotiation,won,lost',
                'probability' => 'nullable|integer|min:0|max:100',
                'expected_close_at' => 'nullable|date',
                'status' => 'nullable|in:open,won,lost,cancelled',
                'notes' => 'nullable|string',
            ]);

            if (!$request->post('lead_id') && !$request->post('customer_id')) {
                throw new ExceptionApiStatusZeroException(
                    'lead or customer is required'
                );
            }

            $deal = Deal::find($request->post('id'));

            if (!$deal) {
                throw new ExceptionApiStatusZeroException('deal not found');
            }

            $oldStage = $deal->stage;
            $oldProbability = $deal->probability;
            $oldAssignedTo = $deal->assigned_to;
            $deal->lead_id = $request->post('lead_id');
            $deal->customer_id = $request->post('customer_id');
            $deal->assigned_to = $request->post('assigned_to');
            $deal->title = $request->post('title');
            $deal->amount = $request->post('amount');
            $deal->stage = $request->post('stage', 'new');
            $deal->probability = $request->post('probability', 0);
            $deal->expected_close_at = $request->post('expected_close_at');
            $deal->status = $request->post('status', 'open');
            $deal->notes = $request->post('notes');

            $deal->save();

            if ($deal->assigned_to && $deal->assigned_to != $oldAssignedTo) {
                Notification::create([
                    'user_id' => $deal->assigned_to,
                    'title' => 'Deal Assigned',
                    'message' => 'A new deal has been assigned to you.',
                ]);
            }

            if (
                $deal->stage != $oldStage ||
                $deal->probability != $oldProbability
            ) {
                DealStageHistory::create([
                    'deal_id' => $deal->id,
                    'stage' => $deal->stage,
                    'probability' => $deal->probability,
                    'comment' => 'Deal stage updated',
                ]);
            }

            $this->response['msg'] = 'deal updated successfully';
            $this->response['data'] = $deal;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:deals,id',
            ]);

            $deal = Deal::find($request->post('id'));

            if (!$deal) {
                throw new ExceptionApiStatusZeroException('deal not found');
            }

            $deal->delete();

            $this->response['msg'] = 'deal deleted successfully';

            return response()->json($this->response);
        });
    }

    public function myDeals(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'stage' => 'nullable|in:new,qualified,proposal,negotiation,won,lost',
                'status' => 'nullable|in:open,won,lost,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = Deal::with(
                'lead',
                'customer',
                'assignedUser'
            )->where('assigned_to', $request->user()->id);

            if ($request->post('stage')) {
                $query->where('stage', $request->post('stage'));
            }

            if ($request->post('status')) {
                $query->where('status', $request->post('status'));
            }

            $deals = $query
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'my deals';
            $this->response['data'] = $deals;

            return response()->json($this->response);
        });
    }

    public function myDealsSummary(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $userId = $request->user()->id;

            $this->response['msg'] = 'my deals summary';

            $this->response['data'] = [
                'total_deals' => Deal::where('assigned_to', $userId)->count(),

                'open_deals' => Deal::where('assigned_to', $userId)
                    ->where('status', 'open')
                    ->count(),

                'won_deals' => Deal::where('assigned_to', $userId)
                    ->where('status', 'won')
                    ->count(),

                'lost_deals' => Deal::where('assigned_to', $userId)
                    ->where('status', 'lost')
                    ->count(),

                'cancelled_deals' => Deal::where('assigned_to', $userId)
                    ->where('status', 'cancelled')
                    ->count(),

                'total_amount' => Deal::where('assigned_to', $userId)
                    ->sum('amount'),

                'won_amount' => Deal::where('assigned_to', $userId)
                    ->where('status', 'won')
                    ->sum('amount'),
            ];

            return response()->json($this->response);
        });
    }

    public function pipelineSummary(Request $request)
    {
        return handleApiRequest(function () {

            $stages = [
                'new',
                'qualified',
                'proposal',
                'negotiation',
                'won',
                'lost',
            ];

            $pipeline = [];

            foreach ($stages as $stage) {
                $query = Deal::where('stage', $stage);

                $pipeline[] = [
                    'stage' => $stage,
                    'total_deals' => $query->count(),
                    'total_amount' => $query->sum('amount'),
                ];
            }

            $this->response['msg'] = 'deal pipeline summary';
            $this->response['data'] = $pipeline;

            return response()->json($this->response);
        });
    }
}
