<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\Customer;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'type' => 'required|in:call,meeting,email,note',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'activity_at' => 'required|date',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'status' => 'nullable|in:pending,completed,cancelled',
            ]);

            if (!$request->post('lead_id') && !$request->post('customer_id')) {
                throw new ApiStatusZeroException(
                    'lead or customer is required'
                );
            }

            if ($request->post('lead_id') && $request->post('customer_id')) {
                throw new ApiStatusZeroException(
                    'activity can belong to lead or customer'
                );
            }

            $activity = Activity::create([
                'lead_id' => $request->post('lead_id'),
                'customer_id' => $request->post('customer_id'),
                'type' => $request->post('type'),
                'title' => $request->post('title'),
                'description' => $request->post('description'),
                'activity_at' => $request->post('activity_at'),
                'assigned_to' => $request->post('assigned_to'),
                'status' => $request->post('status', 'pending'),
            ]);

            $this->response['msg'] = 'activity saved successfully';
            $this->response['data'] = $activity;

            return response()->json($this->response);
        });
    }



    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'type' => 'nullable|in:call,meeting,email,note',
                'status' => 'nullable|in:pending,completed,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->get('per_page', 10);

            $query = Activity::with('lead', 'customer')
                ->orderBy('id', 'desc');

            if ($request->post('lead_id') !== null) {
                $query->where('lead_id', $request->post('lead_id'));
            }

            if ($request->post('customer_id') !== null) {
                $query->where('customer_id', $request->post('customer_id'));
            }

            if ($request->post('type') !== null) {
                $query->where('type', $request->post('type'));
            }

            if ($request->post('status') !== null) {
                $query->where('status', $request->post('status'));
            }

            $activities = $query->paginate($perPage);

            $this->response['msg'] = 'activity list';
            $this->response['data'] = $activities;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer',
            ]);

            $activity = Activity::with('lead', 'customer')
                ->find($request->post('id'));

            if (!$activity) {
                throw new ApiStatusZeroException('activity not found');
            }

            $this->response['msg'] = 'activity detail';
            $this->response['data'] = $activity;

            return response()->json($this->response);
        });
    }


    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:activities,id',
                'type' => 'required|in:call,meeting,email,note',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'activity_at' => 'required|date',
                'status' => 'nullable|in:pending,completed,cancelled',
            ]);

            $activity = Activity::find($request->post('id'));

            if (!$activity) {
                throw new ApiStatusZeroException('activity not found');
            }

            $activity->type = $request->post('type');
            $activity->title = $request->post('titel');
            $activity->description = $request->post('description');
            $activity->activity_at = $request->post('activity_id');

            if ($request->post('status') !== null) {
                $activity->status = $request->post('status');
            }

            $activity->save();

            $this->response['msg'] = 'activity updated successfully';
            $this->response['data'] = $activity;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:activities,id',
            ]);

            $activity = Activity::find($request->post('id'));

            if (!$activity) {
                throw new ApiStatusZeroException('activity not found');
            }

            $activity->delete();

            $this->response['msg'] = 'activity deleted successfully';
            $this->response['data'] = [];

            return response()->json($this->response);
        });
    }
}
