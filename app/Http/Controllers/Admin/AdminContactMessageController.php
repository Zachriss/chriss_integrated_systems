<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminContactMessageController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::orderByDesc('created_at')->get();
        $unreadCount = ContactMessage::unread()->count();
        $pendingCount = ContactMessage::pending()->count();
        return view('admin.contact-messages.index', compact('messages', 'unreadCount', 'pendingCount'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        if (!$contactMessage->is_read) {
            $contactMessage->markAsRead();
        }
        return view('admin.contact-messages.show', compact('contactMessage'));
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();
        return redirect()->route('admin.contact-messages.index')->with('success', 'Message deleted successfully.');
    }

    public function markAsRead(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->markAsRead();
        return back()->with('success', 'Message marked as read.');
    }

    /**
     * Approve the contact message (it will become visible on the site as an approved message).
     */
    public function approve(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->approve();
        return back()->with('success', 'Message approved successfully. It can now be viewed publicly if displayed.');
    }

    /**
     * Approve the contact message and convert it into a testimonial.
     * The testimonial will be visible on the homepage.
     */
    public function approveAndConvert(ContactMessage $contactMessage): RedirectResponse
    {
        if ($contactMessage->converted_to_testimonial) {
            return back()->with('info', 'This message has already been converted to a testimonial.');
        }

        $contactMessage->convertToTestimonial();

        return redirect()->route('admin.testimonials.index')->with('success', 'Message approved and converted to testimonial successfully. You can edit it from the testimonials section.');
    }
}