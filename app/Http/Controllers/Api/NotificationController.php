<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'is_read' => 'nullable|in:0,1',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->get('per_page', 10);

            $query = Notification::where('user_id', $request->post('user_id'))
                ->orderBy('id', 'desc');

            if ($request->post('is_read') !== null) {
                $query->where('is_read', $request->post('is_read'));
            }

            $notifications = $query->paginate($perPage);

            $this->response['msg'] = 'notification list';
            $this->response['data'] = $notifications;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'id' => 'required|integer',
            ]);

            $notification = Notification::where('id', $request->post('id'))
                ->where('user_id', $request->post('user_id'))
                ->first();

            if (!$notification) {
                throw new \RuntimeException('notification not found');
            }

            $this->response['msg'] = 'notification detail';
            $this->response['data'] = $notification;

            return response()->json($this->response);
        });
    }

    public function markAsRead(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'id' => 'required|integer',
            ]);

            $notification = Notification::where('id', $request->post('id'))
                ->where('user_id', $request->post('user_id'))
                ->first();

            if (!$notification) {
                throw new \RuntimeException('notification not found');
            }

            $notification->is_read = 1;
            $notification->save();

            $this->response['msg'] = 'notification marked as read';
            $this->response['data'] = $notification;

            return response()->json($this->response);
        });
    }

    public function markAllAsRead(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            Notification::where('user_id', $request->post('user_id'))
                ->where('is_read', 0)
                ->update([
                    'is_read' => 1,
                ]);

            $this->response['msg'] = 'all notifications marked as read';
            $this->response['data'] = [];

            return response()->json($this->response);
        });
    }

    public function unreadCount(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $count = Notification::where('user_id', $request->post('user_id'))
                ->where('is_read', 0)
                ->count();

            $this->response['msg'] = 'unread notification count';
            $this->response['data'] = [
                'count' => $count,
            ];

            return response()->json($this->response);
        });
    }

    public function myNotifications(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'is_read' => 'nullable|in:0,1',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = Notification::where(
                'user_id',
                $request->user()->id
            );

            if ($request->post('is_read') !== null) {
                $query->where(
                    'is_read',
                    $request->post('is_read')
                );
            }

            $notifications = $query
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'my notifications';
            $this->response['data'] = $notifications;

            return response()->json($this->response);
        });
    }

    public function myNotificationsUnreadCount(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $count = Notification::where(
                'user_id',
                $request->user()->id
            )
                ->where('is_read', 0)
                ->count();

            $this->response['msg'] = 'my unread notifications count';
            $this->response['data'] = [
                'unread_count' => $count,
            ];

            return response()->json($this->response);
        });
    }

    public function myMarkAllAsRead(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            Notification::where(
                'user_id',
                $request->user()->id
            )
                ->where('is_read', 0)
                ->update([ 
                    'is_read' => 1,
                ]);

            $this->response['msg'] = 'all notifications marked as read';

            return response()->json($this->response);
        });
    }
}
