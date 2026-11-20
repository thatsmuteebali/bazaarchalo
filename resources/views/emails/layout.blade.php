<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('title', 'Bazaar Chalo')</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f3f6f4; color: #273444; font-family: Arial, Helvetica, sans-serif;">
    <div style="display: none; max-height: 0; overflow: hidden; opacity: 0; color: transparent;">
        @yield('preheader')
    </div>
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"
        style="background-color: #f3f6f4;">
        <tr>
            <td align="center" style="padding: 36px 16px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="600" border="0"
                    style="width: 100%; max-width: 600px; border: 1px solid #e3eae5; border-radius: 12px; background-color: #ffffff;">
                    <tr>
                        <td style="padding: 28px 36px; border-radius: 12px 12px 0 0; background-color: #198754;">
                            <p
                                style="margin: 0; color: #ffffff; font-size: 21px; font-weight: 700; letter-spacing: 0.3px;">
                                Bazaar Chalo
                            </p>
                            <p style="margin: 6px 0 0; color: #e8f5ee; font-size: 13px; line-height: 1.5;">
                                Fresh local shopping, made simple
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 36px;">
                            <p
                                style="margin: 0 0 10px; color: #198754; font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase;">
                                @yield('eyebrow')
                            </p>
                            <h1
                                style="margin: 0 0 24px; color: #1d2b24; font-size: 26px; font-weight: 700; line-height: 1.3;">
                                @yield('heading')
                            </h1>
                            @yield('body')
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="padding: 22px 36px; border-top: 1px solid #edf1ee; background-color: #fbfcfb; border-radius: 0 0 12px 12px;">
                            <p style="margin: 0 0 6px; color: #718096; font-size: 12px; line-height: 1.6;">
                                You're receiving this email because you have an account with Bazaar Chalo.
                            </p>
                            <p style="margin: 0; color: #9aa5ae; font-size: 12px; line-height: 1.6;">
                                &copy; {{ date('Y') }} Bazaar Chalo
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
