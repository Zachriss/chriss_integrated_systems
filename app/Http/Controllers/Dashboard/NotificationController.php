<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $source = $this->notificationSource($request);
        $filter = $request->query('filter', 'all');

        if (! $source) {
            return view('dashboard.notifications.index', [
                'notifications' => collect(),
                'filter' => $filter,
                'unreadCount' => 0,
            ]);
        }

        $query = $source->latest();

        if ($filter === 'unread') {
            $query->where('status', 'unread');
        } elseif ($filter === 'read') {
            $query->where('status', 'read');
        }

        return view('dashboard.notifications.index', [
            'notifications' => $query->paginate(15),
            'filter' => $filter,
            'unreadCount' => (clone $source)->where('status', 'unread')->count(),
        ]);
    }

    public function dropdownData(Request $request): JsonResponse
    {
        $source = $this->notificationSource($request);
        $notifications = $source ? $source->latest()->limit(6)->get() : collect();
        $unreadCount = $source ? (clone $source)->where('status', 'unread')->count() : 0;

        return response()->json([
            'unreadCount' => $unreadCount,
            'viewAllUrl' => route('dashboard.notifications.index'),
            'markAllReadUrl' => route('dashboard.notifications.mark-all-read'),
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title ?? 'Notification',
                    'message' => $notification->message ?? '',
                    'type' => $notification->type ?? 'info',
                    'status' => $notification->status ?? 'read',
                    'status_label' => ucfirst($notification->status ?? 'read'),
                    'time' => optional($notification->created_at)?->diffForHumans() ?? 'Just now',
                    'open_url' => route('dashboard.notifications.show', $notification->id),
                ];
            })->values(),
        ]);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $source = $this->notificationSource($request);

        if ($source) {
            (clone $source)->where('status', 'unread')->update(['status' => 'read']);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function show(Request $request, string $notificationId): RedirectResponse|View
    {
        $source = $this->notificationSource($request);

        if (! $source) {
            return redirect()
                ->route('dashboard.notifications.index')
                ->with('error', 'Notification details are not available yet.');
        }

        $notification = $source->find($notificationId);

        if (! $notification) {
            return redirect()
                ->route('dashboard.notifications.index')
                ->with('error', 'Notification not found.');
        }

        if (($notification->status ?? null) === 'unread') {
            $notification->update(['status' => 'read']);
        }

        return view('dashboard.notifications.show', [
            'notification' => $notification,
        ]);
    }

    private function notificationSource(Request $request): mixed
    {
        $user = $request->user();

        return $user && method_exists($user, 'notifications')
            ? $user->notifications()
            : null;
    }
}