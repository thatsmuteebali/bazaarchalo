<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New contact inquiry</title>
</head>

<body
    style="margin: 0; padding: 32px 16px; background-color: #f4f6f8; color: #273444; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
        style="max-width: 640px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e4e9ee; border-radius: 8px;">
        <tr>
            <td style="padding: 28px 32px; background-color: #198754; border-radius: 8px 8px 0 0;">
                <p
                    style="margin: 0 0 6px; color: #e8f5ee; font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase;">
                    Bazaar Chalo</p>
                <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 600;">New contact inquiry</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px 32px;">
                <p style="margin: 0 0 22px; color: #526170; font-size: 15px; line-height: 1.6;">A visitor has sent a
                    message through the website contact form.</p>
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                    style="border-collapse: collapse;">
                    <tr>
                        <td
                            style="width: 120px; padding: 12px 0; color: #718096; font-size: 13px; font-weight: bold; vertical-align: top;">
                            Name</td>
                        <td style="padding: 12px 0; color: #273444; font-size: 14px; border-bottom: 1px solid #edf0f2;">
                            {{ $senderName }}</td>
                    </tr>
                    <tr>
                        <td
                            style="width: 120px; padding: 12px 0; color: #718096; font-size: 13px; font-weight: bold; vertical-align: top;">
                            Email</td>
                        <td style="padding: 12px 0; color: #273444; font-size: 14px; border-bottom: 1px solid #edf0f2;">
                            <a href="mailto:{{ $senderEmail }}"
                                style="color: #198754; text-decoration: none;">{{ $senderEmail }}</a></td>
                    </tr>
                    <tr>
                        <td
                            style="width: 120px; padding: 12px 0; color: #718096; font-size: 13px; font-weight: bold; vertical-align: top;">
                            Received</td>
                        <td style="padding: 12px 0; color: #273444; font-size: 14px;">{{ $receivedAt }}</td>
                    </tr>
                </table>
                <h2 style="margin: 28px 0 10px; color: #273444; font-size: 15px;">Message</h2>
                <div
                    style="padding: 18px; background-color: #f7f9fa; border-left: 3px solid #198754; border-radius: 4px; color: #384656; font-size: 14px; line-height: 1.7; white-space: pre-wrap;">
                    {{ $messageBody }}</div>
                <p style="margin: 28px 0 0; color: #718096; font-size: 12px; line-height: 1.6;">Reply directly to this
                    email to respond to {{ $senderName }}.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 18px 32px; border-top: 1px solid #edf0f2; color: #8a96a3; font-size: 12px;">This
                notification was sent from the Bazaar Chalo contact form.</td>
        </tr>
    </table>
</body>

</html>
