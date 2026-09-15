<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;


class LeadController extends Controller
{
    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'source' => 'nullable|in:website,referral,social_media,advertisement,other',
                'status' => 'nullable|in:new,contacted,qualified,lost',
                'notes' => 'nullable|string'
            ]);

            $lead = Lead::create([
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'phone' => $request->post('phone'),
                'company' => $request->post('company'),
                'source' => $request->post('source'),
                'status' => $request->post('status', 'new'),
                'notes' => $request->post('notes'),

            ]);

            $this->response['msg'] = 'lead seved successfully';
            $this->response['data'] = $lead;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'name' => 'nullable|string',
                'status' => 'nullable|string',
                'company' => 'nullable|string',
                'per_page' => 'nullable|integer|min:1|max:100',
                'source' => 'nullable|in:website,referral,social_media,advertisement,other',
                'sort_by' => 'nullable|in:id,name,email,company,status,created_at',
                'sort_order' => 'nullable|in:asc,desc',
            ]);

            $perpage = $request->get('per_Page', 10);
            $sortBy = $request->post('sort_by', 'id');
            $sortOrder = $request->post('sort_order', 'desc');

            $query = Lead::orderBy($sortBy, $sortOrder);

            if ($request->post('name') !== null) {
                $query->where('name', 'like', '%' . $request->post('name') . '%');
            }

            if ($request->post('status') !== null) {
                $query->where('status', $request->post('status'));
            }

            if ($request->post('is_converted') !== null) {
                $query->where('is_converted', $request->post('is_converted'));
            }
            if ($request->post('source') !== null) {
                $query->where('source', $request->post('source'));
            }

            $leads = $query->paginate($perpage);

            $this->response['msg'] = 'lead list';
            $this->response['data'] = $leads;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer',
            ]);

            $lead = Lead::with('customer')->find($request->post('id'));

            if (!$lead) {
                throw new ApiStatusZeroException('lead not found');
            }

            $this->response['msg'] = 'lead detail';
            $this->response['data'] = $lead;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'source' => 'nullable|in:website,referral,social_media,advertisement,other',
                'status' => 'nullable|in:new,contacted,qualified,lost',
                'notes' => 'nullable|string',
            ]);

            $lead = Lead::find($request->post('id'));

            if (!$lead) {
                throw new ApiStatusZeroException('lead not found');
            }

            $lead->name = $request->post('name');
            $lead->email = $request->post('email');
            $lead->phone = $request->post('phone');
            $lead->company = $request->post('company');
            $lead->source = $request->post('source');
            $lead->status = $request->post('status', $lead->status);
            $lead->notes = $request->post('notes');
            $lead->save();

            $this->response['msg'] = 'lead updates successfully';
            $this->response['data'] = $lead;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {

        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer',
            ]);

            $lead = Lead::find($request->post('id'));

            if (!$lead) {
                throw new ApiStatusZeroException('lead not found');
            }

            $lead->delete();

            $this->response['msg'] = 'lead deleted successfully';
            $this->response['data'] = [];

            return response()->json($this->response);
        });
    }

    public function convert(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:leads,id',
            ]);

            $lead = Lead::find($request->post('id'));

            if (!$lead) {
                throw new ApiStatusZeroException('lead not found');
            }

            if ($lead->is_converted == 1) {
                throw new ApiStatusZeroException('lead already converted');
            }

            if ($lead->status !== 'qualified') {
                throw new ApiStatusZeroException(
                    'only qualified lead con be converted'
                );
            }

            DB::beginTransaction();

            try {

                $customer = Customer::create([
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'company' => $lead->company,
                    'status' => 1,
                ]);

                $lead->customer_id = $customer->id;
                $lead->is_converted = 1;
                $lead->save();

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
            $this->response['msg'] = 'lead convert successfully';
            $this->response['data'] = [
                'lead' => $lead,
                'customer' => $customer,
            ];
        });
    }
}
