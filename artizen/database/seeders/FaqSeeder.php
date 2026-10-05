<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Services\JsonStorageService;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds for Artizen FAQs.
     */
    public function run(): void
    {
        $faqs = [
            // Homepage Featured FAQs (sort_order 1 to 6)
            [
                'id'               => 4,
                'question'         => 'How do I book an event package on Artizen?',
                'answer'           => 'Browse our curated celebration packages (Birthdays, Proposals, Sound Rigs, Weddings, and Baby Showers). Choose your preferred setup, select your desired date and time, fill in your venue address in Indore, and submit your booking request. Our dedicated event coordinator will contact you promptly to confirm all details.',
                'category'         => 'booking',
                'is_active'        => true,
                'show_on_homepage' => true,
                'sort_order'       => 1,
            ],
            [
                'id'               => 7,
                'question'         => 'Do I need to pay online while submitting a booking?',
                'answer'           => 'No online payment is required during checkout! Artizen operates on a 100% safe offline payment model. You submit a booking inquiry with zero advance payment. Once our coordinator verifies venue availability and timings, payment is handled securely offline (UPI, bank transfer, or cash) after confirmation.',
                'category'         => 'payments',
                'is_active'        => true,
                'show_on_homepage' => true,
                'sort_order'       => 2,
            ],
            [
                'id'               => 10,
                'question'         => 'How early does the setup team arrive before the celebration?',
                'answer'           => 'Our professional decorators and sound technicians arrive 2 to 3 hours prior to your scheduled party start time. We ensure all balloon arches, neon backdrops, sound systems, and lighting are completely installed and sound-checked before your guests arrive.',
                'category'         => 'setup',
                'is_active'        => true,
                'show_on_homepage' => true,
                'sort_order'       => 3,
            ],
            [
                'id'               => 11,
                'question'         => 'Which areas in Indore do you deliver and set up?',
                'answer'           => 'We provide doorstep delivery and on-site setup across all major localities in Indore, including Vijay Nagar, Palasia, Saket Nagar, Bhawarkua, Super Corridor, AB Road, Annapurna Road, Rau, Bypass Road, Scheme 54, Scheme 78, Nipania, Mahalaxmi Nagar, and surrounding venues.',
                'category'         => 'setup',
                'is_active'        => true,
                'show_on_homepage' => true,
                'sort_order'       => 4,
            ],
            [
                'id'               => 13,
                'question'         => 'Can I customize my event package (theme, colors, sound)?',
                'answer'           => 'Yes, absolutely! All Artizen packages are fully customizable. You can request custom balloon shades, specific neon sign lettering, personalized backdrops, extra high-bass sound speakers, or live acoustic artists when speaking with your assigned event manager.',
                'category'         => 'customization',
                'is_active'        => true,
                'show_on_homepage' => true,
                'sort_order'       => 5,
            ],
            [
                'id'               => 15,
                'question'         => 'What is your cancellation and rescheduling policy?',
                'answer'           => 'We offer flexible rescheduling free of charge up to 48 hours before the event setup. For cancellations made at least 24 to 48 hours in advance, any deposit is 100% refunded with zero cancellation penalties.',
                'category'         => 'policy',
                'is_active'        => true,
                'show_on_homepage' => true,
                'sort_order'       => 6,
            ],

            // Dedicated Help Center & FAQ Page Items (sort_order 7 to 16)
            [
                'id'               => 5,
                'question'         => 'How much in advance should I book my event?',
                'answer'           => 'We recommend booking at least 2 to 4 days in advance to guarantee your preferred time slot and theme availability. However, we also cater to same-day or last-minute urgent setup requests in Indore depending on team availability.',
                'category'         => 'booking',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 7,
            ],
            [
                'id'               => 6,
                'question'         => 'Can I track the status of my booking?',
                'answer'           => 'Yes! Every booking receives a unique Booking ID (e.g. ART-XXXX). You can track your booking status directly on our website using the Track Booking tool or receive live updates via WhatsApp.',
                'category'         => 'booking',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 8,
            ],
            [
                'id'               => 8,
                'question'         => 'When is the payment collected?',
                'answer'           => 'Payment is collected offline after our manager confirms your booking details and setup schedule. You can inspect the decor and sound equipment at your venue before finalizing settlement.',
                'category'         => 'payments',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 9,
            ],
            [
                'id'               => 9,
                'question'         => 'Are there any hidden delivery or convenience charges in Indore?',
                'answer'           => 'No hidden charges. The price quoted on the package includes standard setup, equipment, and delivery within city municipal limits. For venues outside Indore bypass, minimal transport charges will be communicated transparently in advance.',
                'category'         => 'payments',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 10,
            ],
            [
                'id'               => 12,
                'question'         => 'Who handles teardown and cleanup after the event?',
                'answer'           => 'Our crew returns after your party or the following morning (as scheduled with you) to carefully dismantle the decor, pack rental audio equipment, and leave your venue clean.',
                'category'         => 'setup',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 11,
            ],
            [
                'id'               => 14,
                'question'         => 'Can I provide my own theme design or reference photo?',
                'answer'           => 'Yes! You can share your reference photos from Pinterest or Instagram with our coordinator on WhatsApp, and our creative design team will tailor a bespoke setup to match your vision.',
                'category'         => 'customization',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 12,
            ],
            [
                'id'               => 16,
                'question'         => 'What happens in case of outdoor rain or bad weather?',
                'answer'           => 'For outdoor lawn or terrace setups, our team works with you to shift decorations into a sheltered area or reschedule timing at zero extra convenience fee.',
                'category'         => 'policy',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 13,
            ],
            [
                'id'               => 1,
                'question'         => 'How fast is setup completed in Indore?',
                'answer'           => 'Most birthday and proposal setups take 3-4 hours to assemble. Our team coordinates directly with the venue contact.',
                'category'         => 'general',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 14,
            ],
            [
                'id'               => 2,
                'question'         => 'How is payment collected?',
                'answer'           => 'There is no online payment required on booking. The Indore coordinator contacts the customer on WhatsApp to confirm details, and 50% advance is collected offline via UPI.',
                'category'         => 'general',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 15,
            ],
            [
                'id'               => 3,
                'question'         => 'Can I cancel a confirmed booking?',
                'answer'           => 'Yes, you can cancel up to 24 hours before the event with a full refund of any deposits.',
                'category'         => 'general',
                'is_active'        => true,
                'show_on_homepage' => false,
                'sort_order'       => 16,
            ],
        ];

        foreach ($faqs as $item) {
            Faq::updateOrCreate(
                ['id' => $item['id']],
                [
                    'question'         => $item['question'],
                    'answer'           => $item['answer'],
                    'category'         => $item['category'],
                    'is_active'        => $item['is_active'],
                    'show_on_homepage' => $item['show_on_homepage'],
                    'sort_order'       => $item['sort_order'],
                ]
            );
        }

        // Keep JSON sync updated
        $allFaqs = Faq::ordered()->get()->map(function ($f) {
            return [
                'id'               => $f->id,
                'q'                => $f->question,
                'a'                => $f->answer,
                'category'         => $f->category,
                'active'           => (bool) $f->is_active,
                'show_on_home'     => (bool) $f->show_on_homepage,
                'show_on_homepage' => (bool) $f->show_on_homepage,
                'sort_order'       => (int) $f->sort_order,
                'display_order'    => (int) $f->sort_order,
            ];
        })->toArray();

        JsonStorageService::write('faqs.json', $allFaqs);
    }
}
