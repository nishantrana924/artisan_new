<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JsonStorageService;

class LegalController extends Controller
{
    /**
     * Get default legal page content.
     */
    public function getDefaultLegalPages(): array
    {
        return [
            'privacy' => [
                'title'    => 'Privacy Policy',
                'subtitle' => 'Last updated: October 2, 2026 • ARTIZEN Event Booking Platform (Indore, MP)',
                'content'  => '<h2>1. Information We Collect</h2>
<p>When you submit a booking request on ARTIZEN, we collect the following personal information: full name, mobile number, WhatsApp number, email address, event details, and venue address. This information is used solely to process your event booking enquiry.</p>

<h2>2. How We Use Your Information</h2>
<p>We use your information to contact you regarding your booking request, confirm event arrangements, and provide customer support. We do not share your personal information with third parties without your consent, except as required by law.</p>

<h2>3. Data Storage</h2>
<p>Your data is stored securely on our servers. We implement industry-standard security measures to protect your personal information from unauthorized access, use, or disclosure.</p>

<h2>4. Cookies</h2>
<p>Our website may use cookies to improve your browsing experience. You can choose to disable cookies through your browser settings. Disabling cookies may affect some functionality of the website.</p>

<h2>5. Third-Party Links</h2>
<p>Our website may contain links to third-party websites. We are not responsible for the privacy practices of those websites. We encourage you to read their privacy policies before providing any personal information.</p>

<h2>6. Your Rights</h2>
<p>You have the right to request access to the personal information we hold about you, to request corrections, and to request deletion of your data. Please contact us at support@artizen.events to exercise these rights.</p>

<h2>7. Contact Us</h2>
<p>If you have any questions about this Privacy Policy, please contact us at support@artizen.events or call us at +91 91316 68156.</p>',
            ],
            'terms' => [
                'title'    => 'Terms & Conditions',
                'subtitle' => 'Effective Date: October 2, 2026 • ARTIZEN Event Booking Platform (Indore, MP)',
                'content'  => '<h2>1. Acceptance of Terms</h2>
<p>By accessing and using the ARTIZEN platform, you agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, please do not use our services.</p>

<h2>2. Booking Process</h2>
<p>All bookings made through ARTIZEN are subject to availability and confirmation by our team. Submitting a booking request does not guarantee a confirmed booking until our team contacts you and confirms the arrangement.</p>

<h2>3. Payment Terms</h2>
<p>ARTIZEN does not process online payments. All payments are collected offline after booking confirmation. Our team will provide you with payment details (Cash, UPI, NEFT/RTGS) upon confirming your booking.</p>

<h2>4. Event Setup Standards</h2>
<p>ARTIZEN strives to deliver event setups as described in our packages. Minor variations in decor elements may occur due to availability of materials. We will always maintain the quality and theme as agreed.</p>

<h2>5. Customer Responsibilities</h2>
<p>Customers are responsible for ensuring that the venue is accessible to our team for setup and teardown. Any damage to our equipment caused by customers or venue issues will be charged accordingly.</p>

<h2>6. Governing Law</h2>
<p>These Terms and Conditions are governed by the laws of Madhya Pradesh, India. Any disputes shall be subject to the exclusive jurisdiction of the courts in Indore, MP.</p>

<h2>7. Changes to Terms</h2>
<p>ARTIZEN reserves the right to update these Terms and Conditions at any time. Continued use of our platform after changes constitutes acceptance of the revised terms.</p>',
            ],
            'cancellation' => [
                'title'    => 'Refund & Cancellation Policy',
                'subtitle' => 'Effective Date: October 2, 2026 • ARTIZEN Event Booking Platform (Indore, MP)',
                'content'  => '<h2>1. Cancellation by Customer</h2>
<p>If you need to cancel your booking, please contact us at least 48 hours before the scheduled event date. Cancellations made within this timeframe will be processed without any penalty, as ARTIZEN operates on a zero-advance model.</p>

<h2>2. Zero-Advance Model</h2>
<p>ARTIZEN does not charge any advance payment at the time of booking. Payment is collected only after the event setup is completed to your satisfaction. This means there is no amount to be refunded in most cases.</p>

<h2>3. Last-Minute Cancellations</h2>
<p>Cancellations made less than 24 hours before the event may incur a nominal setup cost charge to cover material and labor costs already invested. Our team will communicate any applicable charges before confirming.</p>

<h2>4. Weather & Force Majeure</h2>
<p>In case of extreme weather conditions, natural calamities, or other force majeure events that prevent setup execution, we will work with you to reschedule the event at no additional cost.</p>

<h2>5. Rescheduling Policy</h2>
<p>You may request to reschedule your event up to 24 hours before the original event date, subject to availability. Rescheduling is provided as a courtesy and is not guaranteed during peak seasons.</p>

<h2>6. Contact for Cancellations</h2>
<p>To cancel or reschedule, please contact us immediately at +91 91316 68156 (WhatsApp/Call) or email support@artizen.events. Verbal cancellations over the phone must be followed up with a written confirmation via WhatsApp or email.</p>',
            ],
            'booking_policy' => [
                'title'    => 'Booking & Payment Policy',
                'subtitle' => 'Effective Date: October 2, 2026 • ARTIZEN Event Booking Platform (Indore, MP)',
                'content'  => '<h2>1. How Booking Works</h2>
<p>To book an event setup with ARTIZEN, simply select your desired package, fill out the booking form with your event details, and submit your request. Our team will contact you within 24 hours to confirm availability and discuss your requirements.</p>

<h2>2. Booking Confirmation</h2>
<p>A booking is only considered confirmed after our team has contacted you and provided an official confirmation. Please do not make any venue arrangements based solely on a submitted booking form.</p>

<h2>3. Payment Methods</h2>
<p>ARTIZEN accepts the following offline payment methods upon event completion:</p>
<ul>
<li>Cash payment at the event venue</li>
<li>UPI transfers (Google Pay, PhonePe, Paytm)</li>
<li>NEFT/RTGS bank transfers</li>
</ul>
<p>No online payment gateway is used. All transactions are handled directly between the customer and our team.</p>

<h2>4. No Advance Payment Required</h2>
<p>We operate on a trust-based, zero-advance model. You do not need to pay anything upfront. Payment is collected only after the setup is installed and you are satisfied with the arrangement.</p>

<h2>5. Delivery Zones</h2>
<p>We currently serve Indore, Madhya Pradesh and nearby areas within a 30 km radius. Additional travel charges may apply for locations outside Indore city limits. Please confirm with our team when booking.</p>

<h2>6. Booking Modifications</h2>
<p>If you need to modify your booking (change of date, venue, or package), please contact us at least 48 hours in advance. Modifications are subject to availability and may be subject to price adjustments based on the revised package.</p>',
            ],
        ];
    }

    /**
     * Display Privacy Policy page.
     */
    public function privacyPolicy()
    {
        $defaultPages = $this->getDefaultLegalPages();
        $storedPages  = JsonStorageService::read('legal_pages.json', []);
        $page         = array_merge($defaultPages['privacy'], $storedPages['privacy'] ?? []);

        return view('legal.privacy', compact('page'));
    }

    /**
     * Display Terms & Conditions page.
     */
    public function termsConditions()
    {
        $defaultPages = $this->getDefaultLegalPages();
        $storedPages  = JsonStorageService::read('legal_pages.json', []);
        $page         = array_merge($defaultPages['terms'], $storedPages['terms'] ?? []);

        return view('legal.terms', compact('page'));
    }

    /**
     * Display Refund & Cancellation Policy page.
     */
    public function cancellationPolicy()
    {
        $defaultPages = $this->getDefaultLegalPages();
        $storedPages  = JsonStorageService::read('legal_pages.json', []);
        $page         = array_merge($defaultPages['cancellation'], $storedPages['cancellation'] ?? []);

        return view('legal.cancellation', compact('page'));
    }

    /**
     * Display Booking & Payment Policy page.
     */
    public function bookingPolicy()
    {
        $defaultPages = $this->getDefaultLegalPages();
        $storedPages  = JsonStorageService::read('legal_pages.json', []);
        $page         = array_merge($defaultPages['booking_policy'], $storedPages['booking_policy'] ?? []);

        return view('legal.booking-policy', compact('page'));
    }
}
