<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celebration Confirmed - ARTIZEN</title>
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

                    <!-- Hero Greeting -->
                    <tr>
                        <td style="padding: 36px 36px 20px 36px;">
                            @php
                                $id = $booking['id'] ?? $booking['booking_id'] ?? 'BK-XXXX';
                                $name = $booking['name'] ?? $booking['customer_name'] ?? 'Valued Guest';
                                $package = $booking['package'] ?? $booking['package_name'] ?? 'Event Celebration Setup';
                                $tier = $booking['tier'] ?? $booking['tier_name'] ?? 'Standard Setup';
                                $date = $booking['date'] ?? $booking['event_date'] ?? 'Upcoming Date';
                                $time = $booking['time'] ?? $booking['event_time'] ?? 'Evening';
                                $guests = $booking['guest_count'] ?? 25;
                                $address = $booking['address'] ?? '';
                                $area = $booking['area'] ?? '';
                                $city = $booking['city'] ?? 'Indore';
                                $landmark = $booking['landmark'] ?? '';
                                $total = number_format((int)($booking['total'] ?? $booking['total_amount'] ?? 4999));
                            @endphp

                            <!-- Celebration Status Pill -->
                            <div style="text-align: center; margin-bottom: 24px;">
                                <span style="display: inline-block; background-color: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 6px 18px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px;">
                                    🎉 Booking Officially Confirmed!
                                </span>
                            </div>

                            <h1 style="font-size: 24px; font-weight: 800; color: #171719; margin: 0 0 14px 0; text-align: center; letter-spacing: -0.4px;">
                                Your Celebration is Confirmed! 🥳
                            </h1>
                            <p style="font-size: 15px; line-height: 1.7; color: #4B5563; margin: 0 0 20px 0; text-align: center;">
                                Dear <strong>{{ $name }}</strong>,<br>
                                We are absolutely thrilled to let you know that your event booking has been officially <strong>CONFIRMED</strong>! Our creative decorators and event artists have reserved your date and time slot.
                            </p>

                            <!-- Reference ID Callout -->
                            <div style="background-color: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 12px; padding: 14px 20px; text-align: center; margin-bottom: 28px;">
                                <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; color: #166534;">
                                    Confirmed Booking ID
                                </div>
                                <div style="font-size: 24px; font-weight: 900; color: #14532D; letter-spacing: 2px; margin-top: 4px; font-family: monospace;">
                                    #{{ $id }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Confirmed Details Card -->
                    <tr>
                        <td style="padding: 0 36px 28px 36px;">
                            <div style="background-color: #FAF8F5; border: 1px solid #EFECE6; border-radius: 14px; padding: 22px 24px;">
                                <h3 style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #78716C; margin: 0 0 16px 0; border-bottom: 1px solid #E7E5E4; padding-bottom: 10px;">
                                    Confirmed Event Details
                                </h3>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size: 14px;">
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; width: 38%; font-weight: 500;">Event Package:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 700;">{{ $package }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500;">Setup Tier:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 600;">{{ $tier }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500;">Event Date:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 700;">{{ $date }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500;">Setup Time:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 600;">{{ $time }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500;">Guest Count:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 600;">{{ $guests }} Guests</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500; vertical-align: top;">Venue Address:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 600; line-height: 1.5;">
                                            {{ $address }}{{ !empty($landmark) ? ' (Near ' . $landmark . ')' : '' }}, {{ $area }}, {{ $city }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border-top: 1px dashed #E7E5E4; padding-top: 12px; margin-top: 8px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #171719; font-weight: 800; font-size: 15px;">Total Payable:</td>
                                        <td style="padding: 6px 0; color: #16A34A; font-weight: 900; font-size: 18px;">₹{{ $total }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <!-- What to Expect / Reassurance -->
                    <tr>
                        <td style="padding: 0 36px 32px 36px;">
                            <div style="background-color: #FFFDE6; border: 1px solid #FEF08A; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px;">
                                <div style="font-size: 13px; font-weight: 800; color: #854D0E; margin-bottom: 6px;">
                                    ✨ What to expect on your special day:
                                </div>
                                <div style="font-size: 12.5px; line-height: 1.6; color: #713F12;">
                                    &bull; <strong>Punctual Arrival:</strong> Our setup crew will reach your venue promptly before your scheduled start time with all curated props, florals, and lighting.<br>
                                    &bull; <strong>Hassle-Free Experience:</strong> We handle complete venue styling and coordination so you can simply enjoy every moment.<br>
                                    &bull; <strong>Offline Payment:</strong> You can settle the payment directly with our event manager at the venue (Cash, UPI, or Bank Transfer).
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin-top: 24px;">
                                <a href="{{ route('booking.success', ['id' => $id]) }}" style="display: inline-block; background-color: #171719; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 14px 32px; border-radius: 9999px; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                    View Confirmed Booking Status &rarr;
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #FAF8F5; border-top: 1px solid #EFECE6; padding: 24px 36px; text-align: center;">
                            <p style="font-size: 13px; font-weight: 600; color: #4B5563; margin: 0 0 6px 0;">
                                Need Last-Minute Additions or Have Questions?
                            </p>
                            <p style="font-size: 12px; color: #6B7280; margin: 0 0 16px 0; line-height: 1.6;">
                                Feel free to reply directly to this email or reach us anytime:<br>
                                📞 <strong>+91 9131668156</strong> &bull; 💬 <a href="https://wa.me/919131668156" style="color: #10B981; font-weight: 700; text-decoration: none;">Chat on WhatsApp</a>
                            </p>
                            <div style="font-size: 11px; color: #9CA3AF; line-height: 1.5; border-top: 1px solid #E5E7EB; padding-top: 12px;">
                                &copy; {{ date('Y') }} ARTIZEN Events. All rights reserved.<br>
                                Indore, Madhya Pradesh • Premium Celebration Booking Portal
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

