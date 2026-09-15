<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException as ExceptionApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Lead;
use App\Models\Customer;
use App\Exceptions\ApiStatusZeroException;
use Illuminate\Foundation\Console\ApiInstallCommand;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Thresholds;

class TaskController extends Controller
{

    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_at' => 'required|date',
                'priority' => 'nullable|in:low,medium,high',
                'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            ]);

            if (!$request->post('lead_id') && !$request->post('customer_id')) {
                throw new ExceptionApiStatusZeroException(
                    'lead or customer is required'
                );
            }

            if ($request->post('lead_id') && $request->post('customer_id')) {
                throw new ExceptionApiStatusZeroException(
                    'task can belong to lead or customer'
                );
            }

            $task = Task::create([
                'lead_id' => $request->post('lead_id'),
                'customer_id' => $request->post('customer_id'),
                'title' => $request->post('title'),
                'description' => $request->post('description'),
                'due_at' => $request->post('due_at'),
                'priority' => $request->post('priority', 'medium'),
                'status' => $request->post('status', 'pending'),
            ]);

            $this->response['msg'] = 'task saved successfully';
            $this->response['data'] = $task;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'priority' => 'nullable|in:low,medium,high',
                'status' => 'nullable|in:pending,in_progress,completed,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100',
                'overdue' => 'nullable|in:0,1',
                'sort_by' => 'nullable|in:id,title,due_at,priority,status,created_at',
                'sort_order' => 'nullable|in:asc,desc',
            ]);

            $perPage = $request->get('per_page', 10);

            $sortBy = $request->post('sort_by', 'id');
            $sortOrder = $request->post('sort_order', 'desc');

            $query = Task::with('lead', 'customer')
                ->orderBy($sortBy, $sortOrder);

            if ($request->post('lead_id') !== null) {
                $query->where('lead_id', $request->post('lead_id'));
            }

            if ($request->post('customer_id') !== null) {
                $query->where('customer_id', $request->post('customer_id'));
            }

            if ($request->post('priority') !== null) {
                $query->where('priority', $request->post('priority'));
            }

            if ($request->post('status') !== null) {
                $query->where('status', $request->post('status'));
            }

            if ($request->post('overdue') == 1) {
                $query->where('due_at', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
            }

            $tasks = $query->paginate($perPage);

            $this->response['msg'] = 'task list';
            $this->response['data'] = $tasks;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer',
            ]);

            $task = Task::with('lead', 'customer')
                ->find($request->post('id'));

            if (!$task) {
                throw new ExceptionApiStatusZeroException('task not found');
            }

            $this->response['msg'] = 'task detail';
            $this->response['data'] = $task;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:tasks,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_at' => 'required|date',
                'priority' => 'nullable|in:low,medium,high',
                'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            ]);

            $task = Task::find($request->post('id'));

            if (!$task) {
                throw new ExceptionApiStatusZeroException('task not found');
            }

            $task->title = $request->post('title');
            $task->description = $request->post('description');
            $task->due_at = $request->post('due_at');

            if ($request->post('priority') !== null) {
                $task->priority = $request->post('priority');
            }

            if ($request->post('status') !== null) {
                $task->status = $request->post('status');
            }

            $task->save();

            $this->response['msg'] = 'task updated successfully';
            $this->response['data'] = $task;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:tasks,id',
            ]);

            $task = Task::find($request->post('id'));

            if (!$task) {
                throw new ExceptionApiStatusZeroException('task not found');
            }

            $task->delete();

            $this->response['msg'] = 'task deleted successfully';
            $this->response['data'] = [];

            return response()->json($this->response);
        });
    }
}
