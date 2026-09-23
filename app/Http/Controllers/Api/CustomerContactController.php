<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException as ExceptionApiStatusZeroException;
use App\Exceptions\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\CustomerContact;
use Illuminate\Http\Request;

class CustomerContactController extends Controller
{
    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'customer_id' => 'required|integer|exists:customers,id',
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'designation' => 'nullable|string|max:255',
                'is_primary' => 'nullable|in:0,1',
                'status' => 'nullable|in:0,1',
            ]);

            $contact = CustomerContact::create([
                'customer_id' => $request->post('customer_id'),
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'phone' => $request->post('phone'),
                'designation' => $request->post('designation'),
                'is_primary' => $request->post('is_primary', 0),
                'status' => $request->post('status', 1),
            ]);

            $this->response['msg'] = 'customer contact saved successfully';
            $this->response['data'] = $contact;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'customer_id' => 'nullable|integer|exists:customers,id',
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'status' => 'nullable|in:0,1',
                'per_page' => 'nullable|integer|min:1|max:100',
                'designation' => 'nullable|string|max:255',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = CustomerContact::with('customer');

            if ($request->post('designation')) {
                $query->where(
                    'designation',
                    'like',
                    '%' . $request->post('designation') . '%'
                );
            }

            if ($request->post('customer_id')) {
                $query->where(
                    'customer_id',
                    $request->post('customer_id')
                );
            }

            if ($request->post('name')) {
                $query->where(
                    'name',
                    'like',
                    '%' . $request->post('name') . '%'
                );
            }

            if ($request->post('phone')) {
                $query->where(
                    'phone',
                    'like',
                    '%' . $request->post('phone') . '%'
                );
            }

            if ($request->post('status') !== null) {
                $query->where(
                    'status',
                    $request->post('status')
                );
            }

            $contacts = $query
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'customer contact list';
            $this->response['data'] = $contacts;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:customer_contacts,id',
            ]);

            $contact = CustomerContact::with('customer')
                ->find($request->post('id'));

            if (!$contact) {
                throw new ExceptionApiStatusZeroException(
                    'customer contact not found'
                );
            }

            $this->response['msg'] = 'customer contact detail';
            $this->response['data'] = $contact;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:customer_contacts,id',
                'customer_id' => 'required|integer|exists:customers,id',
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'designation' => 'nullable|string|max:255',
                'is_primary' => 'nullable|in:0,1',
                'status' => 'nullable|in:0,1',
            ]);

            $contact = CustomerContact::find($request->post('id'));

            if (!$contact) {
                throw new ExceptionApiStatusZeroException(
                    'customer contact not found'
                );
            }

            $contact->customer_id = $request->post('customer_id');
            $contact->name = $request->post('name');
            $contact->email = $request->post('email');
            $contact->phone = $request->post('phone');
            $contact->designation = $request->post('designation');
            $contact->is_primary = $request->post('is_primary', 0);
            $contact->status = $request->post('status', 1);

            $contact->save();

            $this->response['msg'] = 'customer contact updated successfully';
            $this->response['data'] = $contact;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:customer_contacts,id',
            ]);

            $contact = CustomerContact::find($request->post('id'));

            if (!$contact) {
                throw new ExceptionApiStatusZeroException(
                    'customer contact not found'
                );
            }

            $contact->delete();

            $this->response['msg'] = 'customer contact deleted successfully';

            return response()->json($this->response);
        });
    }

    public function setPrimary(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:customer_contacts,id',
            ]);

            $contact = CustomerContact::find($request->post('id'));

            if (!$contact) {
                throw new ExceptionApiStatusZeroException(
                    'customer contact not found'
                );
            }

            CustomerContact::where(
                'customer_id',
                $contact->customer_id
            )->update([
                'is_primary' => 0,
            ]);

            $contact->is_primary = 1;
            $contact->save();

            $this->response['msg'] = 'primary contact updated successfully';
            $this->response['data'] = $contact;

            return response()->json($this->response);
        });
    }
}
