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
            ]);

            $perPage = $request->get('per_page', 10);

            $query = FollowUp::with('lead', 'customer')
                ->orderBy('id', 'desc');

            if ($request->post('lead_id') !== null) {
                $query->where('lead_id', $request->post('lead_id'));
            }

            if ($request->post('customer_id') !== null) {
                $query->where('customer_id', $request->post('customer_id'));
            }

            if ($request->post('status') !== null) {
                $query->where('status', $request->post('status'));
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

            $followUp = Followup::with('lead', 'customer')
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
}
