<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'pincode' => 'nullable|string|max:10',
                'status' => 'nullable|in:0,1',
            ]);

            $customer = Customer::create([

                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'phone' => $request->post('phone'),
                'company' => $request->post('company'),
                'address' => $request->post('address'),
                'city' => $request->post('city'),
                'state' => $request->post('state'),
                'pincode' => $request->post('pincode'),
                'status' => $request->post('status', 1)
            ]);

            $this->response['msg'] = 'Customer saved successfully';
            $this->response['data'] = $customer;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'name' => 'nullable|string',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'status' => 'nullable|in:0,1',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'pincode' => 'nullable|string',
                'per_page' => 'nullable|integer|min:1|max:100',
                'sort_by' => 'nullable|in:id,name,email,created_at',
                'sort_order' => 'nullable|in:asc,desc',
            ]);

            $perpage = $request->get('per_page', 10);
            $sortBy = $request->post('sort_by', 'id');
            $sortOrder = $request->post('sort_order', 'desc');

            $query = Customer::orderBy($sortBy, $sortOrder);

            if ($request->post('name') !== null) {
                $query->where('name', 'like', '%' . $request->post('name') . '%');
            }

            if ($request->post('email') !== null) {
                $query->where('email', 'like', '%' . $request->post('email') . '%');
            }

            if ($request->post('phone') !== null) {
                $query->where('phone', 'like', '%' . $request->post('phone') . '%');
            }

            if ($request->post('status') !== null) {
                $query->where('status', $request->post('status'));
            }
            if ($request->post('company') !== null) {
                $query->where(
                    'company',
                    'like',
                    '%' . $request->post('company') . '%'
                );
            }
            if ($request->post('city') !== null) {
                $query->where(
                    'city',
                    'like',
                    '%' . $request->post('city') . '%'
                );
            }
            if ($request->post('state') !== null) {
                $query->where(
                    'state',
                    'like',
                    '%' . $request->post('state') . '%'
                );
            }
            if ($request->post('pincode') !== null) {
                $query->where(
                    'pincode',
                    'like',
                    '%' . $request->post('pincode') . '%'
                );
            }

            $customer = $query->paginate($perpage);

            $this->response['msg'] = 'customer list';
            $this->response['data'] = $customer;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer',
            ]);

            $customer = Customer::with(
                'leads',
                'deals',
                'invoices',
                'quotations',
                'followUps',
                'activities',
                'notes',
                'contacts',

            )->find(
                $request->post('id')
            );

            if (!$customer) {
                throw new ApiStatusZeroException('Customer not found');
            }

            $this->response['msg'] = 'customer details';
            $this->response['data'] = $customer;

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
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'pincode' => 'nullable|string|max:10',
                'status' => 'nullable|in:0,1',

            ]);

            $customer = Customer::find($request->post('id'));

            if (!$customer) {
                throw new ApiStatusZeroException('Customer not found');
            }

            $customer->name = $request->post('name');
            $customer->email = $request->post('email');
            $customer->phone = $request->post('phone');
            $customer->company = $request->post('company');
            $customer->address = $request->post('address');
            $customer->city = $request->post('city');
            $customer->state = $request->post('state');
            $customer->pincode = $request->post('pincode');


            if ($request->post('status') !== null) {
                $customer->status = $request->post('status');
            }

            $customer->save();

            $this->response['msg'] = 'customer updated successfully';
            $this->response['data'] = $customer;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:customers,id',
            ]);

            $customer = Customer::find($request->post('id'));

            if (!$request) {
                throw new ApiStatusZeroException('Customer not found');
            }

            $customer->delete();

            $this->response['mag'] = 'customer delete successfully';
            $this->response['data'] = [];

            return response()->json($this->response);
        });
    }
}
