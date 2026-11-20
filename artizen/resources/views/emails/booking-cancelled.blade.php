<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Request Update - ARTIZEN</title>
</head>
<body style="margin: 0; padding: 0; background-color: #FAF9F6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #171719; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #FAF9F6; padding: 36px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #E6E2D8; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.06);" cellspacing="0" cellpadding="0">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #171719; padding: 32px 36px; text-align: center;">
                            <span style="font-size: 26px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff;">
                                ARTIZEN<span style="color: #FFD600;">.</span>
                            </span>
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: #A1A1AA; margin-top: 6px;">
                                Luxury Event & Celebration Platform • Indore
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 36px 24px 36px;">
                            @php
                                $id = $booking['id'] ?? $booking['booking_id'] ?? 'BK-XXXX';
                                $name = $booking['name'] ?? $booking['customer_name'] ?? 'Valued Guest';
                                $package = $booking['package'] ?? $booking['package_name'] ?? 'Event Celebration Setup';
                            @endphp

                            <!-- Status Pill -->
                            <div style="text-align: center; margin-bottom: 24px;">
                                <span style="display: inline-block; background-color: #F3F4F6; border: 1px solid #E5E7EB; color: #4B5563; padding: 6px 16px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px;">
                                    Booking Status Update
                                </span>
                            </div>

                            <h1 style="font-size: 22px; font-weight: 800; color: #171719; margin: 0 0 14px 0; text-align: center; letter-spacing: -0.4px;">
                                Update on Your Booking Request
                            </h1>
                            <p style="font-size: 15px; line-height: 1.7; color: #4B5563; margin: 0 0 20px 0; text-align: center;">
                                Dear <strong>{{ $name }}</strong>,<br>
                                We hope you are doing well. We are writing to update you regarding your booking request <strong>#{{ $id }}</strong> for <strong>{{ $package }}</strong>, which has been cancelled.
                            </p>

                            <div style="background-color: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px;">
                                <div style="font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 6px;">
                                    Would you like to reschedule or choose an alternative date?
                                </div>
                                <div style="font-size: 13px; line-height: 1.6; color: #6B7280;">
                                    If this cancellation was accidental or if your plans have shifted, our event concierge team would be delighted to assist you in selecting a new date or tailoring a custom package to match your budget and theme.
                                </div>
                            </div>

                            <!-- CTA Buttons -->
                            <div style="text-align: center; margin-top: 28px;">
                                <a href="{{ route('events.index') }}" style="display: inline-block; background-color: #171719; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 13px 28px; border-radius: 9999px; margin-right: 8px; margin-bottom: 8px;">
                                    Browse Other Packages &rarr;
                                </a>
                                <a href="https://wa.me/919131668156" style="display: inline-block; background-color: #10B981; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 13px 28px; border-radius: 9999px; margin-bottom: 8px;">
                                    Chat with Concierge
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #FAF8F5; border-top: 1px solid #EFECE6; padding: 24px 36px; text-align: center;">
                            <p style="font-size: 12px; color: #6B7280; margin: 0 0 12px 0; line-height: 1.6;">
                                Artizen Event Platform &bull; Offline Payment Booking Experience<br>
                                Indore, Madhya Pradesh &bull; Helpline: +91 9131668156
                            </p>
                            <div style="font-size: 11px; color: #9CA3AF;">
                                &copy; {{ date('Y') }} ARTIZEN Events. All rights reserved.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

