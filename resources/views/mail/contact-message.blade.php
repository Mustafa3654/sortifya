{{--
    Contact notification. Deliberately plain: this lands in an inbox, gets
    skimmed on a phone, and gets replied to. The reply goes to the visitor
    because the Mailable sets Reply-To.
--}}
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Sortifya contact</title>
</head>
<body style="margin:0; padding:0; background:#f6f8f7; font-family:'Segoe UI',Helvetica,Arial,sans-serif; color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8f7; padding:28px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:560px; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">

                <tr>
                    <td style="padding:22px 28px 0 28px;">
                        <span style="display:inline-block; padding:4px 10px; border-radius:999px;
                                     background:rgba(16,185,129,0.12); color:#047857;
                                     font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase;">
                            New contact message
                        </span>
                    </td>
                </tr>

                <tr>
                    <td style="padding:14px 28px 4px 28px;">
                        <h1 style="margin:0; font-size:19px; line-height:1.35; font-weight:700; color:#0f172a;">
                            {{ $contact->subject }}
                        </h1>
                    </td>
                </tr>

                {{-- Who wrote in --}}
                <tr>
                    <td style="padding:16px 28px 0 28px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #e2e8f0; border-radius:12px;">
                            <tr>
                                <td style="padding:10px 14px; font-size:12px; color:#64748b; width:96px;">From</td>
                                <td style="padding:10px 14px; font-size:13px; color:#0f172a; font-weight:600;">
                                    {{ $contact->name }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px; font-size:12px; color:#64748b; border-top:1px solid #e2e8f0;">Email</td>
                                <td style="padding:10px 14px; font-size:13px; border-top:1px solid #e2e8f0;">
                                    <a href="mailto:{{ $contact->email }}" style="color:#0d9488; text-decoration:none;">{{ $contact->email }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px; font-size:12px; color:#64748b; border-top:1px solid #e2e8f0;">Account</td>
                                <td style="padding:10px 14px; font-size:13px; color:#0f172a; border-top:1px solid #e2e8f0;">
                                    @if ($contact->user)
                                        Registered · balance ${{ number_format($contact->user->balance(), 2) }}
                                    @else
                                        Not signed in
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 14px; font-size:12px; color:#64748b; border-top:1px solid #e2e8f0;">Received</td>
                                <td style="padding:10px 14px; font-size:13px; color:#0f172a; border-top:1px solid #e2e8f0;">
                                    {{ $contact->created_at->format('d M Y, H:i') }}
                                    @if ($contact->locale) · {{ strtoupper($contact->locale) }} @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- The message itself --}}
                <tr>
                    <td style="padding:18px 28px 0 28px;">
                        <p style="margin:0 0 8px 0; font-size:11px; font-weight:700; letter-spacing:0.08em;
                                  text-transform:uppercase; color:#94a3b8;">Message</p>
                        <div style="padding:14px 16px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;
                                    font-size:14px; line-height:1.65; color:#334155; white-space:pre-wrap;">{{ $contact->message }}</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:18px 28px 26px 28px;">
                        <table role="presentation" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="border-radius:12px; background:linear-gradient(90deg,#10b981,#14b8a6);">
                                    <a href="mailto:{{ $contact->email }}?subject={{ rawurlencode('Re: '.$contact->subject) }}"
                                       style="display:inline-block; padding:12px 24px; font-size:14px; font-weight:600;
                                              color:#ffffff; text-decoration:none; border-radius:12px;">
                                        Reply to {{ str($contact->name)->before(' ') }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:12px 0 0 0; font-size:12px; color:#94a3b8;">
                            Replying to this email reaches them directly. Message #{{ $contact->id }} is also saved in the admin panel.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
