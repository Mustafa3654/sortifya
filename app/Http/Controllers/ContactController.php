<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    /**
     * Records the message, then emails it.
     *
     * In that order deliberately: the row is the record and the mail is only
     * the notification. A relay outage must not silently swallow someone
     * writing in about a missing payout.
     */
    public function send(Request $request): RedirectResponse
    {
        // A bot fills every field it finds; a person never sees this one.
        if (filled($request->input('company_website'))) {
            // Answer exactly as success would, so a scraper learns nothing.
            return back()->with('success', __('sortifya.pages.contact.sent'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:20', 'max:4000'],
        ]);

        $contact = ContactMessage::create([
            ...$validated,
            'user_id' => $request->user()?->id,
            'status' => 'new',
            'ip_address' => $request->ip(),
            'locale' => app()->getLocale(),
        ]);

        $recipient = config('sortifya.contact.to');

        if (blank($recipient)) {
            // Saved but undeliverable. Loud in the log, quiet to the visitor:
            // their message really is safe, it is just sitting in /admin.
            Log::warning('Contact message stored but CONTACT_TO is not set.', ['id' => $contact->id]);

            return back()->with('success', __('sortifya.pages.contact.sent'));
        }

        try {
            Mail::to($recipient)->send(new ContactMessageMail($contact));
            $contact->update(['was_emailed' => true]);
        } catch (\Throwable $e) {
            // The row survives, so nothing is lost — but say so honestly
            // rather than claiming a delivery that did not happen.
            Log::error('Contact message could not be emailed.', [
                'id' => $contact->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('warning', __('sortifya.pages.contact.stored_not_sent'));
        }

        return redirect()
            ->route('contact')
            ->with('success', __('sortifya.pages.contact.sent'));
    }
}
