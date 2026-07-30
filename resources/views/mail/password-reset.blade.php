{{--
    Hand-rolled rather than markdown-mail: the Arabic version needs dir="rtl"
    on the document and mirrored padding, which the packaged theme does not do.
    Table layout and inline styles because mail clients are what they are.
--}}
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ __('sortifya.auth.reset_subject') }}</title>
</head>
<body style="margin:0; padding:0; background:#f6f8f7; font-family:'Segoe UI',Helvetica,Arial,sans-serif; color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8f7; padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:520px; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">

                {{-- Brand bar --}}
                <tr>
                    <td style="padding:26px 32px 0 32px;" align="{{ $dir === 'rtl' ? 'right' : 'left' }}">
                        <table role="presentation" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-{{ $dir === 'rtl' ? 'left' : 'right' }}:10px;" valign="middle">
                                    <div style="width:30px; height:30px; border-radius:9px; background:linear-gradient(135deg,#10b981,#14b8a6);"></div>
                                </td>
                                <td valign="middle">
                                    <span style="font-size:19px; font-weight:700; letter-spacing:-0.02em; color:#0f172a;">
                                        {{ __('sortifya.brand') }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 32px 8px 32px;" align="{{ $dir === 'rtl' ? 'right' : 'left' }}">
                        <h1 style="margin:0 0 14px 0; font-size:21px; line-height:1.3; font-weight:700; letter-spacing:-0.03em; color:#0f172a;">
                            {{ __('sortifya.auth.forgot_title') }}
                        </h1>
                        <p style="margin:0 0 10px 0; font-size:15px; line-height:1.65; color:#334155;">
                            {{ __('sortifya.auth.reset_greeting', ['name' => $name]) }}
                        </p>
                        <p style="margin:0 0 24px 0; font-size:15px; line-height:1.65; color:#475569;">
                            {{ __('sortifya.auth.reset_line_1') }}
                        </p>
                    </td>
                </tr>

                {{-- Action --}}
                <tr>
                    <td style="padding:0 32px 22px 32px;" align="{{ $dir === 'rtl' ? 'right' : 'left' }}">
                        <table role="presentation" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="border-radius:12px; background:linear-gradient(90deg,#10b981,#14b8a6);">
                                    <a href="{{ $url }}"
                                       style="display:inline-block; padding:13px 26px; font-size:15px; font-weight:600;
                                              color:#ffffff; text-decoration:none; border-radius:12px;">
                                        {{ __('sortifya.auth.reset_action') }}
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:14px 0 0 0; font-size:13px; color:#64748b;">
                            {{ __('sortifya.auth.reset_expiry', ['count' => $minutes]) }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 32px;">
                        <div style="height:1px; background:#e2e8f0;"></div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 32px 28px 32px;" align="{{ $dir === 'rtl' ? 'right' : 'left' }}">
                        <p style="margin:0 0 16px 0; font-size:13px; line-height:1.6; color:#64748b;">
                            {{ __('sortifya.auth.reset_line_2') }}
                        </p>

                        {{-- Fallback for clients that strip the button --}}
                        <p style="margin:0 0 6px 0; font-size:12px; color:#94a3b8;" dir="ltr" align="left">
                            <a href="{{ $url }}" style="color:#0d9488; word-break:break-all; text-decoration:none;">{{ $url }}</a>
                        </p>

                        <p style="margin:18px 0 0 0; font-size:13px; color:#64748b;">
                            {{ __('sortifya.auth.reset_salutation') }}
                        </p>
                    </td>
                </tr>
            </table>

            <p style="max-width:520px; margin:16px auto 0 auto; font-size:11px; color:#94a3b8; text-align:center;">
                {{ __('sortifya.common.copyright', ['year' => now()->year]) }}
            </p>
        </td>
    </tr>
</table>
</body>
</html>
