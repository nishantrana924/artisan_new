<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celebration Request Received - ARTIZEN</title>
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

                            <!-- Status Pill -->
                            <div style="text-align: center; margin-bottom: 24px;">
                                <span style="display: inline-block; background-color: #FEF3C7; border: 1px solid #FCD34D; color: #92400E; padding: 6px 16px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px;">
                                    ⏳ Request Received • Under Review
                                </span>
                            </div>

                            <h1 style="font-size: 22px; font-weight: 800; color: #171719; margin: 0 0 14px 0; text-align: center; letter-spacing: -0.4px;">
                                Warm Greetings from Artizen! ✨
                            </h1>
                            <p style="font-size: 15px; line-height: 1.7; color: #4B5563; margin: 0 0 20px 0; text-align: center;">
                                Dear <strong>{{ $name }}</strong>,<br>
                                Thank you for choosing Artizen to be a part of your celebration. We are honored to help make your event truly memorable. Your booking request has been successfully registered under Reference ID:
                            </p>

                            <!-- Reference ID Callout -->
                            <div style="background-color: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px; padding: 14px 20px; text-align: center; margin-bottom: 28px;">
                                <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; color: #64748B;">
                                    Booking Reference ID
                                </div>
                                <div style="font-size: 24px; font-weight: 900; color: #0F172A; letter-spacing: 2px; margin-top: 4px; font-family: monospace;">
                                    #{{ $id }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Event Details Card -->
                    <tr>
                        <td style="padding: 0 36px 28px 36px;">
                            <div style="background-color: #FAF8F5; border: 1px solid #EFECE6; border-radius: 14px; padding: 22px 24px;">
                                <h3 style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #78716C; margin: 0 0 16px 0; border-bottom: 1px solid #E7E5E4; padding-bottom: 10px;">
                                    Event Request Summary
                                </h3>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size: 14px;">
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; width: 38%; font-weight: 500;">Package:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 700;">{{ $package }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500;">Setup Tier:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 600;">{{ $tier }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500;">Celebration Date:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 700;">{{ $date }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500;">Preferred Time:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 600;">{{ $time }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500;">Expected Guests:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 600;">{{ $guests }} Guests</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #78716C; font-weight: 500; vertical-align: top;">Venue Location:</td>
                                        <td style="padding: 6px 0; color: #1C1917; font-weight: 600; line-height: 1.5;">
                                            {{ $address }}{{ !empty($landmark) ? ' (Near ' . $landmark . ')' : '' }}, {{ $area }}, {{ $city }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border-top: 1px dashed #E7E5E4; padding-top: 12px; margin-top: 8px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; color: #171719; font-weight: 800; font-size: 15px;">Estimated Total:</td>
                                        <td style="padding: 6px 0; color: #16A34A; font-weight: 900; font-size: 18px;">₹{{ $total }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <!-- Offline Payment Guarantee Notice -->
                    <tr>
                        <td style="padding: 0 36px 28px 36px;">
                            <div style="background-color: #ECFDF5; border-left: 4px solid #10B981; border-radius: 8px; padding: 14px 18px;">
                                <div style="font-size: 13px; font-weight: 700; color: #065F46; margin-bottom: 4px;">
                                    🤝 100% Offline Payment Guarantee
                                </div>
                                <div style="font-size: 12px; line-height: 1.6; color: #047857;">
                                    <strong>No payment is needed right now.</strong> At Artizen, payment is collected offline only after your booking details are finalized with our team.
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- What Happens Next (Creative Roadmap) -->
                    <tr>
                        <td style="padding: 0 36px 32px 36px;">
                            <h4 style="font-size: 14px; font-weight: 800; color: #171719; margin: 0 0 14px 0; letter-spacing: -0.2px;">
                                What happens next?
                            </h4>
                            <div style="font-size: 13px; line-height: 1.7; color: #4B5563;">
                                <div style="margin-bottom: 10px;">
                                    <strong style="color: #171719;">1. Availability & Inventory Review:</strong> Our operations manager is verifying date slots and decor props for your venue.
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <strong style="color: #171719;">2. Personal Call & Coordination:</strong> A dedicated Artizen event specialist will connect with you via WhatsApp or Call to customize themes and preferences.
                                </div>
                                <div>
                                    <strong style="color: #171719;">3. Final Confirmation:</strong> Once you are completely satisfied, your booking is locked in for setup!
                                </div>
                            </div>

                            <!-- CTA Buttons (Download PDF & WhatsApp Concierge) -->
                            @php
                                $pdfUrl = route('booking.success', ['id' => $id, 'download_pdf' => 1]);
                                $clientWaUrl = "https://wa.me/919131668156?text=" . urlencode("Hi Artizen! I received my booking confirmation for Reference ID #{$id} ({$package}). I want to coordinate my event details.");
                            @endphp
                            <div style="text-align: center; margin-top: 30px;">
                                <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
                                    <tr>
                                        <td style="padding: 0 6px 12px 6px;">
                                            <a href="{{ $pdfUrl }}" style="display: inline-block; background-color: #171719; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 14px 30px; border-radius: 9999px; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                                📄 Download Project Brief &amp; Invoice PDF &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0 6px;">
                                            <a href="{{ $clientWaUrl }}" style="display: inline-block; background-color: #25D366; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 700; padding: 11px 24px; border-radius: 9999px;">
                                                💬 Chat with Artizen Concierge on WhatsApp
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #FAF8F5; border-top: 1px solid #EFECE6; padding: 24px 36px; text-align: center;">
                            <p style="font-size: 13px; font-weight: 600; color: #4B5563; margin: 0 0 6px 0;">
                                Questions or Custom Requests?
                            </p>
                            <p style="font-size: 12px; color: #6B7280; margin: 0 0 16px 0; line-height: 1.6;">
                                We are always here to help you celebrate. Connect with our concierge:<br>
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

