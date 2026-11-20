<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - ARTIZEN</title>
</head>
<body style="margin: 0; padding: 0; background-color: #FAF9F6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #171719;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #FAF9F6; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 520px; background-color: #ffffff; border: 1px solid #E6E2D8; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05);" cellspacing="0" cellpadding="0">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #171719; padding: 28px 32px; text-align: center;">
                            <span style="font-size: 24px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff;">
                                ARTIZEN<span style="color: #FFD600;">.</span>
                            </span>
                            <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: #A1A1AA; margin-top: 4px;">
                                Celebration Booking Portal • Indore
                            </div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 36px 32px;">
                            <h2 style="font-size: 20px; font-weight: 800; color: #171719; margin: 0 0 12px 0; letter-spacing: -0.3px;">
                                Verify Your Email Address
                            </h2>
                            <p style="font-size: 14px; line-height: 1.6; color: #4B5563; margin: 0 0 24px 0;">
                                Hello <strong>{{ $user->name }}</strong>,<br>
                                Thank you for registering with Artizen. Please use the verification code below to activate your account and access event booking features:
                            </p>

                            <!-- OTP Box -->
                            <div style="background-color: #FFFDE6; border: 2px dashed #FFD600; border-radius: 12px; padding: 22px; text-align: center; margin: 24px 0;">
                                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #854D0E; margin-bottom: 8px;">
                                    One-Time Verification Code
                                </div>
                                <div style="font-size: 34px; font-weight: 900; letter-spacing: 10px; color: #171719; font-family: monospace;">
                                    {{ $otp }}
                                </div>
                                <div style="font-size: 11px; color: #92400E; margin-top: 8px; font-weight: 500;">
                                    ⏱ Valid for 10 minutes
                                </div>
                            </div>

                            <p style="font-size: 12px; line-height: 1.6; color: #6B7280; margin: 20px 0 0 0;">
                                If you did not create an account on Artizen, please disregard this email or contact support if you suspect unauthorized activity.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #FAF8F5; border-top: 1px solid #EFECE6; padding: 20px 32px; text-align: center;">
                            <p style="font-size: 11px; color: #9CA3AF; margin: 0; line-height: 1.5;">
                                &copy; {{ date('Y') }} ARTIZEN Events. All rights reserved.<br>
                                Indore, Madhya Pradesh • Offline Payment Booking Platform
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

