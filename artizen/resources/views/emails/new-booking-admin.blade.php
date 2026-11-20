<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Alert - ARTIZEN Admin</title>
</head>
<body style="margin: 0; padding: 0; background-color: #FAF9F6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #171719;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #FAF9F6; padding: 36px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #E6E2D8; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.06);" cellspacing="0" cellpadding="0">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #171719; padding: 28px 36px; text-align: center;">
                            <span style="font-size: 24px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff;">
                                ARTIZEN<span style="color: #FFD600;">.</span>
                            </span>
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: #FFD600; margin-top: 4px;">
                                ⚡ Admin Fast-Track Booking Alert
                            </div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px 36px 20px 36px;">
                            @php
                                $id = $booking['id'] ?? 'BK-XXXX';
                                $name = $booking['name'] ?? 'Customer';
                                $phone = $booking['mobile'] ?? ($booking['whatsapp'] ?? 'N/A');
                                $wa = $booking['whatsapp'] ?? $phone;
                                $email = $booking['email'] ?? 'Not provided';
                                $package = $booking['package'] ?? 'Package';
                                $tier = $booking['tier'] ?? 'Standard';
                                $date = $booking['date'] ?? 'N/A';
                                $time = $booking['time'] ?? 'N/A';
                                $guests = $booking['guest_count'] ?? 25;
                                $total = number_format((int)($booking['total'] ?? 4999));
                                $address = ($booking['address'] ?? '') . ', ' . ($booking['area'] ?? '') . ', ' . ($booking['city'] ?? 'Indore');
                                $notes = $booking['notes'] ?? 'None';
                            @endphp

                            <h2 style="font-size: 20px; font-weight: 800; color: #171719; margin: 0 0 8px 0;">
                                New Booking Request Received: #{{ $id }}
                            </h2>
                            <p style="font-size: 14px; line-height: 1.6; color: #4B5563; margin: 0 0 20px 0;">
                                A new customer booking request has been submitted and is waiting for your review and schedule confirmation.
                            </p>

                            <!-- Summary Card -->
                            <div style="background-color: #FAF8F5; border: 1px solid #EFECE6; border-radius: 12px; padding: 20px 22px; margin-bottom: 24px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size: 13.5px;">
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; width: 35%; font-weight: 600;">Customer:</td>
                                        <td style="padding: 5px 0; color: #1C1917; font-weight: 700;">{{ $name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; font-weight: 600;">Mobile:</td>
                                        <td style="padding: 5px 0; color: #1C1917; font-weight: 700;">
                                            <a href="tel:{{ $phone }}" style="color: #2563EB; text-decoration: none;">{{ $phone }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; font-weight: 600;">WhatsApp:</td>
                                        <td style="padding: 5px 0; color: #1C1917; font-weight: 700;">
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" style="color: #10B981; text-decoration: none;">{{ $wa }} &rarr;</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; font-weight: 600;">Email:</td>
                                        <td style="padding: 5px 0; color: #1C1917; font-weight: 600;">{{ $email }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; font-weight: 600;">Package:</td>
                                        <td style="padding: 5px 0; color: #1C1917; font-weight: 700;">{{ $package }} ({{ $tier }})</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; font-weight: 600;">Date & Time:</td>
                                        <td style="padding: 5px 0; color: #1C1917; font-weight: 700;">{{ $date }} • {{ $time }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; font-weight: 600;">Guests:</td>
                                        <td style="padding: 5px 0; color: #1C1917; font-weight: 600;">{{ $guests }} Guests</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; font-weight: 600; vertical-align: top;">Venue:</td>
                                        <td style="padding: 5px 0; color: #1C1917; font-weight: 600; line-height: 1.4;">{{ $address }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #78716C; font-weight: 600; vertical-align: top;">Notes:</td>
                                        <td style="padding: 5px 0; color: #4B5563; font-style: italic;">{{ $notes }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border-top: 1px dashed #E7E5E4; padding-top: 10px; margin-top: 6px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #171719; font-weight: 800; font-size: 15px;">Total Amount:</td>
                                        <td style="padding: 5px 0; color: #16A34A; font-weight: 900; font-size: 17px;">₹{{ $total }} (Offline)</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Action Buttons (PDF & WhatsApp & Dashboard) -->
                            @php
                                $pdfUrl = route('booking.success', ['id' => $id, 'download_pdf' => 1]);
                                $cleanWa = preg_replace('/[^0-9]/', '', $wa);
                                $waChatUrl = !empty($cleanWa) ? "https://wa.me/{$cleanWa}?text=" . urlencode("Hi {$name}, thank you for choosing Artizen! We received your booking request #{$id} for {$package}. We are reviewing availability and will coordinate setup.") : '#';
                            @endphp
                            <div style="text-align: center; margin-top: 26px;">
                                <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
                                    <tr>
                                        <td style="padding: 0 6px 10px 6px;">
                                            <a href="{{ $pdfUrl }}" style="display: inline-block; background-color: #171719; color: #ffffff; text-decoration: none; font-size: 13.5px; font-weight: 700; padding: 13px 26px; border-radius: 9999px; letter-spacing: 0.3px;">
                                                📄 Download Project Brief &amp; Invoice PDF &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                    @if(!empty($cleanWa))
                                    <tr>
                                        <td style="padding: 0 6px 10px 6px;">
                                            <a href="{{ $waChatUrl }}" style="display: inline-block; background-color: #25D366; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 700; padding: 11px 24px; border-radius: 9999px;">
                                                💬 Chat with Customer on WhatsApp
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="padding: 0 6px;">
                                            <a href="{{ route('admin.dashboard') }}" style="display: inline-block; background-color: #F3F4F6; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600; padding: 9px 20px; border-radius: 9999px;">
                                                Open Admin Dashboard
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #FAF8F5; border-top: 1px solid #EFECE6; padding: 18px 36px; text-align: center;">
                            <div style="font-size: 11px; color: #9CA3AF;">
                                ARTIZEN Admin Notification System &bull; Indore
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

