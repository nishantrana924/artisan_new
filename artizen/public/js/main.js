// Main site JS file for theme, cart, mobile navigation and global event data

if (!window.eventDatabase || Object.keys(window.eventDatabase).length === 0) {
    window.eventDatabase = {
        1: {
            id: 1,
            title: "Adult Birthdays",
            desc: "Select your perfect birthday celebration setup from our tiered packages. We handle decoration, sound, lighting, and coordination.",
            image: "https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [
                "https://images.unsplash.com/photo-1513151233558-d860c5398176?q=80&w=800&auto=format&fit=crop",
                "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=800&auto=format&fit=crop",
                "https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=800&auto=format&fit=crop"
            ],
            tiers: [
                {
                    name: "Basic Birthday Package",
                    price: 4999,
                    desc: "Affordable and classic setup featuring a standard balloon backdrop arch, customized foil banner, cake table styling, and warm ambient LED spotlighting.",
                    image: "https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Balloon Backdrop & Foil Banner",
                        "Cake Table & Ambient Lights",
                        "PA Column Sound & Mic",
                        "Setup & Pack-up Service",
                        "Welcome Standee Decor"
                    ]
                },
                {
                    name: "Premium Birthday Package",
                    price: 9999,
                    desc: "Vibrant theme decoration with a customized backdrop panel, professional PA sound system, ambient spotlights, welcome standee, and 1 hour of DSLR photography coverage.",
                    image: "https://images.unsplash.com/photo-1513151233558-d860c5398176?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Theme Decoration Backdrop",
                        "PA Column Speaker & Welcome Standee",
                        "DSLR Photography (1 Hour)",
                        "LED Floor Lighting",
                        "Cake Table Premium Props"
                    ]
                },
                {
                    name: "Luxury Birthday Celebration",
                    price: 19999,
                    desc: "Ultimate club vibe at your home or venue. Includes concert-grade high-bass sound rigs, a live professional mixing DJ, smoke entrance effects, premium theme stage decor, and an energetic party host.",
                    image: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Premium Theme Stage Decor",
                        "Pro DJ Console & Concert Sound Rig",
                        "Event Host, DSLR shoot & Smoke Effects",
                        "Strobe & Disco Lights Rig",
                        "Red Carpet Entrance Setup"
                    ]
                },
                {
                    name: "Neon Glow Theme Birthday",
                    price: 12999,
                    desc: "Highly visual UV-reactive theme birthday setup. Featuring neon balloon arches, UV reactive backdrops, neon cake table styling, face paint artist, and colorful party LED strobe lights.",
                    image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Neon Backdrop & UV Ambient Lights",
                        "Neon Balloons & LED Happy Birthday Sign",
                        "Face Paint Artist & Glow Accessories",
                        "LED Spotlights & Strobes",
                        "Premium Neon Cake Table Props"
                    ]
                }
            ]
        },
        2: {
            id: 2,
            title: "House Party Rigs",
            desc: "High bass speaker columns, live DJs, and concert vibe lighting setups directly to your venue.",
            image: "https://images.unsplash.com/photo-1506157786151-b8491531f063?q=80&w=800&auto=format&fit=crop",
            tag: "live",
            gallery: [
                "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=800&auto=format&fit=crop",
                "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=800&auto=format&fit=crop"
            ],
            tiers: [
                {
                    name: "House Party Basic",
                    price: 5999,
                    desc: "Compact high-bass speaker column rigs with Bluetooth hookup, a professional sound console mixer, and ambient spot dance lights for a small gathering.",
                    image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "DJ Sound Console Mixer Setup",
                        "High-Bass PA Sound Columns",
                        "Live Mixing DJ (3 Hours)",
                        "Wireless Microphone for Speeches",
                        "Ambient Spot Dance Lights"
                    ]
                },
                {
                    name: "House Party Premium",
                    price: 11999,
                    desc: "Complete party vibes with a live professional DJ, dual active columns, sound-active strobe lighting, heavy fog machine, and a decorated entrance/backdrop setup.",
                    image: "https://images.unsplash.com/photo-1506157786151-b8491531f063?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Professional Live DJ (3 Hours)",
                        "Dual Column Speakers & Strobe Lights",
                        "Heavy Fog Machine & Decor Backdrop",
                        "Party LED Laser Show",
                        "2x Cordless Karaoke Mics"
                    ]
                },
                {
                    name: "House Party Elite",
                    price: 24999,
                    desc: "Massive club-level party production featuring dual high-power active subwoofers, heavy metal box light trussing, custom venue backdrops, a celebrity DJ, and a DSLR photographer.",
                    image: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Celebrity DJ & Double Bass Subwoofers",
                        "Truss Lighting Rig & Ambient Backdrop",
                        "DSLR Photographer & Event Host (3 Hrs)",
                        "Full Stage Fog & Pyro Effects",
                        "Premium VIP Entrance Carpeting"
                    ]
                },
                {
                    name: "Karaoke & Acoustic Setup",
                    price: 7999,
                    desc: "Warm, cozy interactive setup featuring professional dual wireless microphones, a digital lyrics display monitor, wireless sound console, and ambient fairy lights.",
                    image: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Wireless Karaoke Mics & lyrics screen",
                        "Professional PA sound with Echo effects",
                        "Acoustic warm background lights setup",
                        "Fairy Light Curtain Backdrop",
                        "Digital Song Catalogue App Link"
                    ]
                }
            ]
        },
        3: {
            id: 3,
            title: "Romantic Proposal Setups",
            desc: "Make it unforgettable with red rose pathways, glowing neon letters, candles, and acoustic violins.",
            image: "https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [
                "https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800&auto=format&fit=crop"
            ],
            tiers: [
                {
                    name: "Romantic Proposal Setup",
                    price: 6999,
                    desc: "Create a magical moment with a fresh red rose petals pathway, LED candlelit walkway, a custom glowing neon sign board, music hookups, and a live romantic violinist.",
                    image: "https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Rose Petals & LED Candle Walkway",
                        "Custom LED Board & Ambient Music",
                        "Live Romantic Violinist",
                        "Heart-shaped Balloon Arch",
                        "Photoshoot Backdrop Setup"
                    ]
                },
                {
                    name: "Luxury Proposal Setup",
                    price: 14999,
                    desc: "Breath-taking proposal setup featuring bespoke floral arches, giant glowing neon 'MARRY ME' letters, dry ice heavy smoke entrance, cold fire sparklers, and professional photography.",
                    image: "https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Giant \"MARRY ME\" Neon Letters",
                        "Live Violinist Duo & Photographer",
                        "Cold Sparklers entrance & Heavy Smoke",
                        "Bespoke Red Rose Floral Arches",
                        "DSLR Proposal Video Capture"
                    ]
                }
            ]
        },
        4: {
            id: 4,
            title: "Anniversary Setups",
            desc: "Elegant dining tables, fairy light canopies, flower panels, and live acoustic music for your milestone.",
            image: "https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [
                "https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=800&auto=format&fit=crop"
            ],
            tiers: [
                {
                    name: "Classic Anniversary Setup",
                    price: 7999,
                    desc: "Elegant romantic table styling for couples, detailed fairy lights canopy setup, ambient bluetooth column speaker, and a live acoustic singer performing for 2 hours.",
                    image: "https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Romantic Table & Backdrop Decor",
                        "Fairy Lights Canopy & Speaker Setup",
                        "Live Acoustic Singer (2 Hrs)",
                        "Fresh Rose Table Flower Vases",
                        "Custom Anniversary Name Banner"
                    ]
                },
                {
                    name: "Premium Anniversary Setup",
                    price: 15999,
                    desc: "Grand anniversary milestone setup featuring customized backdrop panels, premium flower panels, a live acoustic band (singer and guitarist), and full event DSLR photography.",
                    image: "https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Custom Backdrop & Flower paneling",
                        "Live Acoustic Band (Singer + Guitar)",
                        "DSLR Event Photographer (2 Hours)",
                        "Heart Balloon Backdrop Accents",
                        "Red Carpet Dining Area Entry"
                    ]
                }
            ]
        },
        5: {
            id: 5,
            title: "Acoustic Night",
            desc: "Live guitarist and acoustic setup for private terrace gatherings or indoor sessions.",
            image: "https://images.unsplash.com/photo-1498038432885-c6f3f1b912ee?q=80&w=800&auto=format&fit=crop",
            tag: "live",
            gallery: [
                "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=800&auto=format&fit=crop"
            ],
            tiers: [
                {
                    name: "Basic Acoustic Night",
                    price: 8999,
                    desc: "Relaxed live performance by a solo acoustic guitarist and singer for 2 hours. Includes a compact active column PA sound system and pro mixer.",
                    image: "https://images.unsplash.com/photo-1498038432885-c6f3f1b912ee?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Solo Singer-Guitarist (2 Hours)",
                        "Active Column PA Sound rig",
                        "Pro Sound Mixer & Strobe lights",
                        "Acoustic Ambient Warm Lights",
                        "Wireless Mic for Guest requests"
                    ]
                },
                {
                    name: "Premium Acoustic Duo",
                    price: 16999,
                    desc: "Full live music experience with a professional acoustic duo (singer and guitarist), dual column sound rig with subwoofers, stage lighting, and photographer coverage.",
                    image: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Acoustic Singer & Guitarist Duo",
                        "Double PA Sound columns + Mixer",
                        "Ambient fairy lights + Photographer",
                        "Stage Warm Spotlighting Setup",
                        "Wireless Request Mic & Stand"
                    ]
                }
            ]
        },
        6: {
            id: 6,
            title: "DJ Night",
            desc: "Professional live DJs with truss rigs, strobes, laser light shows, and high-power party sound.",
            image: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=800&auto=format&fit=crop",
            tag: "live",
            gallery: [],
            tiers: [
                {
                    name: "DJ Night Basic",
                    price: 5999,
                    desc: "High-energy party setup featuring a professional DJ controller, high-bass active party speakers, laser strobe light rig, and 3 hours of live mixing.",
                    image: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "DJ Console & Pro Controller Mixer",
                        "Active High-Bass Party Speakers",
                        "Laser Strobe Light Rig & Fog",
                        "3 Hours Live Mix Session",
                        "Wired Vocal MIC for Announcements"
                    ]
                },
                {
                    name: "DJ Night Premium",
                    price: 11999,
                    desc: "Elite dance party setup. Includes 4 hours of professional DJ mixing, dual high-power sound columns, active sound-reactive strobes, and heavy laser fog effects.",
                    image: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Pro DJ Console & 4 Hours Mixing",
                        "Dual Column Speakers & Strobe Lights",
                        "Heavy Fog Machine & Laser Show",
                        "Vocal MC Mic & DJ Stage booth",
                        "Red & Blue Dance Floor Strobes"
                    ]
                }
            ]
        },
        7: {
            id: 7,
            title: "Pre-Wedding Sangeet Setup",
            desc: "Sangeet stage backdrop, high-power sound systems, live dhol artists, and cinematic photography.",
            image: "https://images.unsplash.com/photo-1606800052052-a08af7148866?q=80&w=800&auto=format&fit=crop",
            tag: "live",
            gallery: [],
            tiers: [
                {
                    name: "Pre-Wedding Sangeet Setup",
                    price: 14999,
                    desc: "Grand Sangeet floral stage backdrop, high-power active PA sound system for family performances, traditional dhol artists, and cinematic event photography.",
                    image: "https://images.unsplash.com/photo-1606800052052-a08af7148866?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Traditional Floral Backdrop Stage",
                        "High-Power PA Sound System",
                        "Dhol Artists (1 Hr) & Photographer",
                        "Cordless Performance Mics (x2)",
                        "Stage Spotlight & LED par cans"
                    ]
                }
            ]
        },
        8: {
            id: 8,
            title: "Baby Shower Packages",
            desc: "Pink & blue themed organic balloon decorations, stage setups, lights, and baby shower photography.",
            image: "https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [],
            tiers: [
                {
                    name: "Basic Baby Shower",
                    price: 6999,
                    desc: "Vibrant pink & blue balloon arches, custom welcome board standee, themed cake table decor, and 2 hours of DSLR photography coverage.",
                    image: "https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Pink & Blue balloon arch backdrop",
                        "Welcome Board & Cake Table",
                        "DSLR Photography (2 Hours)",
                        "Baby Props & Theme Cutouts",
                        "Themed Cake Table Tablecloth"
                    ]
                },
                {
                    name: "Premium Baby Shower",
                    price: 13999,
                    desc: "Stunning custom themed stage backdrop, column sound system with cordless mic for games, DSLR photographer, and warm LED stage spotlighting.",
                    image: "https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Custom Theme Stage Backdrop",
                        "PA sound column with cordless mic",
                        "DSLR Photographer + LED Spotlights",
                        "Luxury Baby Shower Sofa Chair",
                        "Activity Props & Games Material"
                    ]
                }
            ]
        },
        9: {
            id: 9,
            title: "Small Office Event",
            desc: "Platform staging, corporate sound setups, rollup branding panels, and technical event coordination.",
            image: "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800&auto=format&fit=crop",
            tag: "live",
            gallery: [],
            tiers: [
                {
                    name: "Small Office Event",
                    price: 19999,
                    desc: "Professional corporate setup with an office platform stage (8x10ft), high-fidelity PA speakers, dual cordless mics, rollup branding panels, and an on-site coordinator.",
                    image: "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Platform Stage & Rollup Branding Panels",
                        "PA Column Speakers & Dual Mics",
                        "Technical On-site Event Coordinator",
                        "Speech Podium / Lectern",
                        "Ambient Stage LED spotlights"
                    ]
                }
            ]
        },
        10: {
            id: 10,
            title: "Wedding & Sangeet",
            desc: "Grand wedding decor, LED display walls, cinematic videography, and premium stage styling.",
            image: "https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [
                "https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800&auto=format&fit=crop"
            ],
            tiers: [
                {
                    name: "Classic Wedding Decor",
                    price: 49999,
                    desc: "Traditional royal stage setup (12x16ft) featuring elite floral backdrop panels, a couples royal sofa, spot lighting, and red carpet entry walkway.",
                    image: "https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Grand Wedding Stage (12x16ft)",
                        "Floral Backdrop Paneling & Spotlights",
                        "Couples Royal Sofa & Red Carpet Entry",
                        "Vase Accents & Stage Carpeting",
                        "Traditional Backdrop Brass lamps"
                    ]
                },
                {
                    name: "Premium Wedding Setup",
                    price: 99999,
                    desc: "Grand premium stage (12x20ft) with rich floral backdrops, decorated entrance & pathway, professional lighting rig, and dry ice heavy fog entry.",
                    image: "https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Premium Stage (12x20ft) & Backdrop",
                        "Decorated Entrance & Pathway",
                        "Pro Lighting Rig & Heavy Fog Machine",
                        "Floral Pillars & VIP Guest walkway",
                        "Warm LED par lights (x8)"
                    ]
                },
                {
                    name: "Royal Luxury Wedding",
                    price: 199999,
                    desc: "Ultimate wedding production with bespoke stage design, high-resolution LED display walls, complete venue lighting design, and full day photo + cinematic video shoot.",
                    image: "https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Bespoke Theme Stage + LED Screen",
                        "Complete Venue Lights + Stage Anchor",
                        "Full Day Photo & Cinematic Video shoot",
                        "Drone Coverage & Portrait shoot",
                        "VIP Entrance Walkway with Flowers"
                    ]
                }
            ]
        },
        11: {
            id: 11,
            title: "Traditional Haldi Setup",
            desc: "Traditional marigold decorations, Urli bowls, yellow backdrops, and gaddi seating.",
            image: "https://images.unsplash.com/photo-1595152772835-219674b2a8a6?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [],
            tiers: [
                {
                    name: "Traditional Haldi Setup",
                    price: 12999,
                    desc: "Bright traditional yellow backdrop, decorative brass Urli bowl for haldi ceremony, marigold curtains, and low gaddi seating for guests.",
                    image: "https://images.unsplash.com/photo-1595152772835-219674b2a8a6?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Yellow Traditional Backdrop",
                        "Decorative Brass Urli Bowl",
                        "Marigold curtains & Low Gaddi Seating",
                        "Haldi Ceremony Props & Platters",
                        "Traditional Umbrella backdrops"
                    ]
                }
            ]
        },
        12: {
            id: 12,
            title: "Classic Farewell",
            desc: "Memory string light photo boards, banner backdrops, sound rigs and cordless microphones.",
            image: "https://images.unsplash.com/photo-1549417229-aa67d3263c09?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [],
            tiers: [
                {
                    name: "Classic Farewell",
                    price: 8999,
                    desc: "Memory string light photo boards, custom farewell banner backdrops, professional speech sound rig, and cordless microphones.",
                    image: "https://images.unsplash.com/photo-1549417229-aa67d3263c09?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Goodbye Theme Banner Backdrop",
                        "Memory String Lights Photo Board",
                        "Sound Rig with Cordless speech Mic",
                        "Wish Jar & Message Cards Set",
                        "Selfie Frame Photo Prop"
                    ]
                }
            ]
        },
        14: {
            id: 14,
            title: "Kids Birthday Packages",
            desc: "Magical kids party decoration, organic balloon arches, cartoon backdrops, and play areas.",
            image: "https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [
                "https://images.unsplash.com/photo-1513151233558-d860c5398176?q=80&w=800&auto=format&fit=crop"
            ],
            tiers: [
                {
                    name: "Cartoon Theme Decor",
                    price: 5999,
                    desc: "Transform your party venue with a vibrant cartoon theme backdrop! This package includes a professionally styled organic balloon arch (120+ balloons), customized character standees matching your child's favorite themes, and a bright LED 'Happy Birthday' display board. Perfect for budget-friendly but visually stunning home or backyard setups.",
                    detailedDesc: "Transform your party venue with a vibrant cartoon theme backdrop! This package includes a professionally styled organic balloon arch (120+ balloons), customized character standees matching your child's favorite themes, and a bright LED 'Happy Birthday' display board. Perfect for budget-friendly but visually stunning home or backyard setups.",
                    image: "https://images.unsplash.com/photo-1513151233558-d860c5398176?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Cartoon Banner Backdrop",
                        "Organic Arch (120 Balloons)",
                        "Soft Play Zone Area",
                        "Themed Cake Table Tablecloth",
                        "LED Happy Birthday Board"
                    ]
                },
                {
                    name: "Kids Super Party Setup",
                    price: 14999,
                    desc: "Make your child's birthday unforgettable with our flagship Kids Super Party Setup. We set up a large deluxe character backdrop, dynamic balloon arch, and themed table setups. The package includes 3 hours of supervised soft play zone rental, a professional magician show (1 hour) to entertain the kids, and an energetic party host to manage games, cake cutting, and keep the vibe alive.",
                    detailedDesc: "Make your child's birthday unforgettable with our flagship Kids Super Party Setup. We set up a large deluxe character backdrop, dynamic balloon arch, and themed table setups. The package includes 3 hours of supervised soft play zone rental, a professional magician show (1 hour) to entertain the kids, and an energetic party host to manage games, cake cutting, and keep the vibe alive.",
                    image: "https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Deluxe Backdrop & Pillars Decor",
                        "Play Zone (3Hrs) & Magician (1Hr)",
                        "Party Host & Cupcake Stands",
                        "Welcome Arch Balloon Gate",
                        "Sound Speaker with Party Tracks"
                    ]
                }
            ]
        },
        15: {
            id: 15,
            title: "Cozy Private Lounge",
            desc: "Intimate low height wooden table layouts with rugs, fairy light curtains, and bluetooth column speakers.",
            image: "https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=800&auto=format&fit=crop",
            tag: "decor",
            gallery: [],
            tiers: [
                {
                    name: "Cozy Private Lounge",
                    price: 14999,
                    desc: "Low height wooden tables, rugs, warm fairy light curtains, and Bluetooth column sound.",
                    image: "https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "Warm Light Curtains & Florals",
                        "Low Seating Cushions & Rugs",
                        "Bluetooth Column PA Sound",
                        "Low Height Wooden Tables (x2)",
                        "Table Floral & Candle decor"
                    ]
                },
                {
                    name: "Teepee Tent Sleepover Setup",
                    price: 8999,
                    desc: "Beautiful teepee tents, cozy mattresses, fairy lights, custom name banners, and dreamcatchers.",
                    image: "https://images.unsplash.com/photo-1544816155-12df9643f363?q=80&w=800&auto=format&fit=crop",
                    inclusions: [
                        "4x Teepee Tents & Cozy Mattresses",
                        "Fairy Lights, Lanterns & Dreamcatchers",
                        "Board Games & Custom Activity Kits",
                        "Personalized Tent Name Banners",
                        "Plush Pillows & Cozy Blankets"

                    ]
                }
            ]
        }
    };
}

// 2. Navigation Handler (Redirect to detail page)
window.openEventLightbox = function (eventId, tierIndex) {
    window.location.href = `/event/${eventId}?tier=${tierIndex || 0}`;
};

// 3. Theme Management
window.toggleTheme = function () {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    if (newTheme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    localStorage.setItem('theme', newTheme);
    updateThemeToggleIcons();
};

function updateThemeToggleIcons() {
    const theme = document.documentElement.getAttribute('data-theme') || 'dark';
    const sunIcon = document.getElementById('theme-toggle-sun');
    const moonIcon = document.getElementById('theme-toggle-moon');
    if (theme === 'dark') {
        if (sunIcon) sunIcon.classList.remove('hidden');
        if (moonIcon) moonIcon.classList.add('hidden');
    } else {
        if (sunIcon) sunIcon.classList.add('hidden');
        if (moonIcon) moonIcon.classList.remove('hidden');
    }
}

// 4. Mobile Drawer Slide-in Navigation Menu
window.toggleMobileNav = function () {
    const mobileNav = document.getElementById('mobile-nav');
    const overlay = document.getElementById('mobile-nav-overlay');
    const waWidget = document.getElementById('whatsapp-floating-widget');
    if (!mobileNav) return;

    const isOpen = !mobileNav.classList.contains('translate-x-full');
    if (isOpen) {
        mobileNav.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');
        if (overlay) {
            overlay.classList.add('hidden', 'opacity-0', 'pointer-events-none');
            overlay.classList.remove('block', 'opacity-100');
        }
        if (waWidget) waWidget.classList.remove('hidden');
    } else {
        mobileNav.classList.remove('translate-x-full');
        document.body.classList.add('overflow-hidden');
        if (overlay) {
            overlay.classList.remove('hidden', 'pointer-events-none');
            setTimeout(() => overlay.classList.add('block', 'opacity-100'), 10);
        }
        if (waWidget) waWidget.classList.add('hidden');
    }
};

// 5. Shopping Cart Logic (Static client-side with LocalStorage)
let cart = JSON.parse(localStorage.getItem('cart')) || [];

window.addTicketToCart = function (eventId, name, price, date, location) {
    const item = {
        id: Date.now() + Math.random().toString(36).substr(2, 5),
        eventId,
        name,
        price,
        date: date || 'Not Selected',
        location: location || 'Not Selected'
    };
    cart.push(item);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();

    // Auto-open cart drawer
    const cartBody = document.getElementById('cart-body');
    const cartOverlay = document.getElementById('cart-overlay');
    if (cartBody) {
        cartBody.classList.remove('translate-x-full');
        if (cartOverlay) {
            cartOverlay.classList.remove('hidden');
            setTimeout(() => cartOverlay.classList.add('opacity-100'), 10);
        }
    }
};

window.toggleCartDrawer = function () {
    const cartBody = document.getElementById('cart-body');
    const cartOverlay = document.getElementById('cart-overlay');
    if (!cartBody) return;

    const isOpen = !cartBody.classList.contains('translate-x-full');
    if (isOpen) {
        cartBody.classList.add('translate-x-full');
        if (cartOverlay) {
            cartOverlay.classList.remove('opacity-100');
            setTimeout(() => cartOverlay.classList.add('hidden'), 300);
        }
    } else {
        cartBody.classList.remove('translate-x-full');
        if (cartOverlay) {
            cartOverlay.classList.remove('hidden');
            setTimeout(() => cartOverlay.classList.add('opacity-100'), 10);
        }
        renderCart();
    }
};

window.removeCartItem = function (itemId) {
    cart = cart.filter(item => item.id !== itemId);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
};

function renderCart() {
    const container = document.getElementById('cart-items-container');
    const emptyMessage = document.getElementById('empty-cart-message');
    const badge = document.getElementById('cart-badge');
    const badgeMobile = document.getElementById('cart-badge-mobile');
    const subtotalEl = document.getElementById('cart-subtotal');
    const taxEl = document.getElementById('cart-tax');
    const totalEl = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-button');

    // Update badges
    if (badge) badge.innerText = cart.length;
    if (badgeMobile) badgeMobile.innerText = cart.length;

    if (!container) return;

    container.innerHTML = '';

    if (cart.length === 0) {
        if (emptyMessage) emptyMessage.classList.remove('hidden');
        if (checkoutBtn) {
            checkoutBtn.disabled = true;
            checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        if (subtotalEl) subtotalEl.innerText = '₹0';
        if (taxEl) taxEl.innerText = '₹0';
        if (totalEl) totalEl.innerText = '₹0';
        return;
    }

    if (emptyMessage) emptyMessage.classList.add('hidden');
    if (checkoutBtn) {
        checkoutBtn.disabled = false;
        checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price;
        const itemEl = document.createElement('div');
        itemEl.className = 'flex gap-4 pb-4 border-b border-primary-border/40';
        itemEl.innerHTML = `
            <div class="flex-grow">
                <h4 class="font-heading font-bold text-xs uppercase text-main-text">${item.name}</h4>
                <p class="text-[10px] text-muted-text mt-1 uppercase">Date: ${item.date} | City: ${item.location}</p>
                <p class="text-xs font-heading font-semibold text-main-text mt-2">₹${item.price.toLocaleString('en-IN')}</p>
            </div>
            <button onclick="removeCartItem('${item.id}')" aria-label="Remove item" class="text-muted-text hover:text-red-500 transition-colors p-1 self-start cursor-pointer bg-transparent border-0">
                <i class="fa-regular fa-trash-can text-sm" aria-hidden="true"></i>
            </button>
        `;
        container.appendChild(itemEl);
    });

    const tax = Math.round(subtotal * 0.1);
    const total = subtotal + tax;

    if (subtotalEl) subtotalEl.innerText = `₹${subtotal.toLocaleString('en-IN')}`;
    if (taxEl) taxEl.innerText = `₹${tax.toLocaleString('en-IN')}`;
    if (totalEl) totalEl.innerText = `₹${total.toLocaleString('en-IN')}`;
}

window.openCheckoutModal = function () {
    const modal = document.getElementById('checkout-form-modal');
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.classList.remove('pointer-events-none');
            modal.querySelector('.transform')?.classList.remove('scale-95');
        }, 10);
    }
};

window.closeCheckoutModal = function () {
    const modal = document.getElementById('checkout-form-modal');
    if (modal) {
        modal.classList.add('opacity-0');
        modal.classList.add('pointer-events-none');
        modal.querySelector('.transform')?.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
};

window.triggerCheckout = function () {
    // Close cart drawer first
    const cartBody = document.getElementById('cart-body');
    if (cartBody) cartBody.classList.add('translate-x-full');
    const cartOverlay = document.getElementById('cart-overlay');
    if (cartOverlay) {
        cartOverlay.classList.remove('opacity-100');
        setTimeout(() => cartOverlay.classList.add('hidden'), 300);
    }

    // Open checkout modal
    window.openCheckoutModal();
};

window.submitBookingRequest = function (e) {
    e.preventDefault();

    const name = document.getElementById('checkout-name').value;
    const email = document.getElementById('checkout-email').value;
    const mobile = document.getElementById('checkout-mobile').value;
    const whatsapp = document.getElementById('checkout-whatsapp').value;
    const city = document.getElementById('checkout-city').value;
    const pincode = document.getElementById('checkout-pincode').value;
    const address = document.getElementById('checkout-address').value;
    const notes = document.getElementById('checkout-notes').value;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // POST cart to backend
    fetch('/booking/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            name,
            email,
            mobile,
            whatsapp,
            city,
            pincode,
            address,
            notes,
            items: JSON.stringify(cart)
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.closeCheckoutModal();
                cart = [];
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
                window.location.href = data.redirect || ('/booking/success?id=' + data.booking_id);
            } else {
                alert('Failed to submit booking request. Please check validation errors.');
            }
        });
};

window.closeSuccessModal = function () {
    const successModal = document.getElementById('success-modal');
    if (successModal) {
        successModal.classList.add('hidden');
        successModal.classList.remove('flex');
    }
};

// 6. Navigation Category Pilling
window.scrollToCategoryRow = function (id, e) {
    if (e && e.preventDefault) {
        e.preventDefault();
    }
    if (!id) return false;
    let targetId = id;
    let el = document.getElementById(targetId);

    if (!el && !targetId.startsWith('cat-')) {
        targetId = 'cat-' + id;
        el = document.getElementById(targetId);
    }

    if (!el) {
        // Normalize slug e.g. cat-house-party -> house-party
        const cleanKey = id.replace(/^pill-/, '').replace(/^cat-/, '').toLowerCase();

        // Search by selector or data attribute
        const allRows = document.querySelectorAll('.category-row-wrapper');
        for (let row of allRows) {
            const rowId = (row.id || '').toLowerCase();
            const altAttr = (row.getAttribute('data-cat-alt') || '').toLowerCase();
            if (rowId.includes(cleanKey) || altAttr.includes(cleanKey) || (rowId.length > 3 && cleanKey.includes(rowId.replace(/^cat-/, '')))) {
                el = row;
                targetId = row.id;
                break;
            }
        }
    }

    if (el) {
        const headerOffset = 90;
        const elementPosition = el.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });

        // Highlight corresponding navigation pill in sticky bar
        document.querySelectorAll('.category-nav-pill').forEach(pill => pill.classList.remove('active'));
        const activePill = document.getElementById('pill-' + id) || document.getElementById('pill-' + targetId);
        if (activePill) activePill.classList.add('active');
    }

    return false;
};

window.scrollToTop = function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

window.handleSubscribe = function (e) {
    e.preventDefault();
    alert('Subscribed successfully!');
    e.target.reset();
};

window.toggleFaq = function (index) {
    const container = document.getElementById(`faq-ans-${index}`);
    if (!container) return;

    const faqItem = container.closest('.faq-item');
    if (!faqItem) return;

    const isActive = faqItem.classList.contains('faq-active');

    // Close all other FAQs (accordion effect)
    const allItems = document.querySelectorAll('.faq-item');
    allItems.forEach((item) => {
        if (item !== faqItem) {
            item.classList.remove('faq-active');
            const ansContainer = item.querySelector('.faq-answer-container');
            if (ansContainer) {
                ansContainer.style.maxHeight = null;
            }
        }
    });

    if (isActive) {
        faqItem.classList.remove('faq-active');
        container.style.maxHeight = null;
    } else {
        faqItem.classList.add('faq-active');
        container.style.maxHeight = container.scrollHeight + 'px';
    }
};

// 7. Page Initial Boot
document.addEventListener('DOMContentLoaded', () => {
    updateThemeToggleIcons();
    renderCart();

    // Initialize Hero Slider if present
    const heroSwiperElement = document.querySelector('.hero-swiper');
    if (heroSwiperElement) {
        // Initialize Swiper
        if (typeof Swiper !== 'undefined') {
            new Swiper('.hero-swiper', {
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                speed: 1000
            });
        }
    }
});

// 8. Brand Credo / About Section Showcase Slider Logic
(function () {
    let currentCategory = 'acoustic';
    let currentImageIndex = 0;

    const categoryMapping = {
        'acoustic': { eventId: 5, badges: [{ text: 'Live Sound', type: 'gold' }, { text: 'Guitarist', type: 'white' }], tags: ['Unplugged', 'Private Terrace', 'Warm Fairy Lights'] },
        'birthday': { eventId: 1, badges: [{ text: 'Premium Decor', type: 'gold' }, { text: 'Fun Vibe', type: 'white' }], tags: ['Balloon Arch', 'Cake Table Styling', 'LED Board'] },
        'proposal': { eventId: 3, badges: [{ text: 'Romantic', type: 'gold' }, { text: 'Best Seller', type: 'white' }], tags: ['Rose Petals', 'Candlelit Pathway', 'Violinist'] },
        'party': { eventId: 2, badges: [{ text: 'High Bass', type: 'gold' }, { text: 'Club Vibe', type: 'white' }], tags: ['DJ Included', 'Fog Machine', 'Strobe Lights'] },
        'wedding': { eventId: 10, badges: [{ text: 'Grand Styling', type: 'gold' }, { text: 'Traditional', type: 'white' }], tags: ['Mandap Decor', 'Stage panelling', 'Royal Sofa'] },
        'corporate': { eventId: 9, badges: [{ text: 'AV Professional', type: 'gold' }, { text: 'Clean Look', type: 'white' }], tags: ['PA Column Speakers', 'Branding Panel', 'Technical Lead'] }
    };

    window.switchAboutShowcase = function (category, btnEl) {
        if (!categoryMapping[category]) return;
        currentCategory = category;
        currentImageIndex = 0;

        // Update active class on tab pills
        document.querySelectorAll('.showcase-tab-pill').forEach(btn => {
            btn.classList.remove('active');
        });
        if (btnEl) {
            btnEl.classList.add('active');
        } else {
            const activeBtn = document.getElementById(`about-tab-${category}`);
            if (activeBtn) activeBtn.classList.add('active');
        }

        // Load data from eventDatabase
        const mappingInfo = categoryMapping[category];
        const eventData = window.eventDatabase[mappingInfo.eventId];
        if (!eventData) return;

        // Build list of images: cover image + gallery images
        const imagesList = [eventData.image];
        if (eventData.gallery && eventData.gallery.length > 0) {
            imagesList.push(...eventData.gallery);
        }
        categoryMapping[category].images = imagesList;

        // Update details
        const titleEl = document.getElementById('about-showcase-title');
        if (titleEl) titleEl.innerText = eventData.title;

        // Find starting price (min tier price)
        let minPrice = Infinity;
        if (eventData.tiers && eventData.tiers.length > 0) {
            eventData.tiers.forEach(t => {
                if (t.price < minPrice) minPrice = t.price;
            });
        }
        if (minPrice === Infinity) minPrice = 8999; // fallback
        const priceEl = document.getElementById('about-showcase-price');
        if (priceEl) priceEl.innerText = `₹${minPrice.toLocaleString('en-IN')}`;

        const descEl = document.getElementById('about-showcase-desc');
        if (descEl) descEl.innerText = eventData.desc;

        // Update badges
        const badgesContainer = document.getElementById('showcase-badges-container');
        if (badgesContainer) {
            badgesContainer.innerHTML = mappingInfo.badges.map(badge => `
                <span class="showcase-badge-pill ${badge.type}">${badge.text}</span>
            `).join('');
        }

        // Update tags
        const tagsContainer = document.getElementById('about-showcase-tags');
        if (tagsContainer) {
            tagsContainer.innerHTML = mappingInfo.tags.map(tag => `
                <span class="showcase-tag-item">${tag}</span>
            `).join('');
        }

        // Update CTA button onclick handler
        const ctaBtn = document.getElementById('about-showcase-btn');
        if (ctaBtn) {
            ctaBtn.onclick = function () {
                window.openEventLightbox(mappingInfo.eventId, 0);
            };
        }

        // Render images slideshow
        renderShowcaseImage();
    };

    function renderShowcaseImage() {
        const mappingInfo = categoryMapping[currentCategory];
        const images = mappingInfo.images || [];
        if (images.length === 0) return;

        // Update main image src
        const mainImg = document.getElementById('about-showcase-img');
        if (mainImg) {
            mainImg.src = images[currentImageIndex];
            mainImg.alt = `${currentCategory} showcase image ${currentImageIndex + 1}`;
        }

        // Update thumbnails
        const thumbnailsList = document.getElementById('about-thumbnails-list');
        if (thumbnailsList) {
            thumbnailsList.innerHTML = images.map((imgSrc, idx) => `
                <button onclick="setAboutShowcaseImage(${idx})" class="thumbnail-img-btn ${idx === currentImageIndex ? 'active' : ''}" aria-label="Go to image ${idx + 1}">
                    <img src="${imgSrc}" alt="thumbnail ${idx + 1}">
                </button>
            `).join('');
        }
    }

    window.setAboutShowcaseImage = function (index) {
        const mappingInfo = categoryMapping[currentCategory];
        const images = mappingInfo.images || [];
        if (index >= 0 && index < images.length) {
            currentImageIndex = index;
            renderShowcaseImage();
        }
    };

    window.prevShowcaseImage = function () {
        const mappingInfo = categoryMapping[currentCategory];
        const images = mappingInfo.images || [];
        if (images.length === 0) return;
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        renderShowcaseImage();
    };

    window.nextShowcaseImage = function () {
        const mappingInfo = categoryMapping[currentCategory];
        const images = mappingInfo.images || [];
        if (images.length === 0) return;
        currentImageIndex = (currentImageIndex + 1) % images.length;
        renderShowcaseImage();
    };

    // Auto-initialize first tab & drag/wheel swipe handlers on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', () => {
        // Wait a small delay to make sure eventDatabase is loaded
        setTimeout(() => {
            window.switchAboutShowcase('acoustic');
        }, 150);

        // Enable smooth mouse-drag and wheel swipe on all .category-row-scroll containers
        const scrollRows = document.querySelectorAll('.category-row-scroll');
        scrollRows.forEach(row => {
            let isDown = false;
            let startX;
            let scrollLeft;

            row.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - row.offsetLeft;
                scrollLeft = row.scrollLeft;
            });

            row.addEventListener('mouseleave', () => { isDown = false; });
            row.addEventListener('mouseup', () => { isDown = false; });

            row.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - row.offsetLeft;
                const walk = (x - startX) * 2;
                row.scrollLeft = scrollLeft - walk;
            });

            row.addEventListener('wheel', (e) => {
                if (e.deltaY !== 0 && row.scrollWidth > row.clientWidth) {
                    e.preventDefault();
                    row.scrollLeft += e.deltaY * 1.5;
                }
            }, { passive: false });
        });
    });
})();

// ============================================================
// Interactive Product Card Cursor Hover Image Scrubber
// ============================================================
window.initCardImageScrubbers = function () {
    const containers = document.querySelectorAll('.experience-card-img-container[data-card-images]');
    containers.forEach(container => {
        if (container._hasAutoMotion) return;
        container._hasAutoMotion = true;

        let rawData = container.getAttribute('data-card-images');
        if (!rawData) return;
        let images = [];
        try {
            images = JSON.parse(rawData);
        } catch (e) {
            images = rawData.split(',').map(s => s.trim()).filter(Boolean);
        }
        images = images.filter(Boolean);
        if (!images || images.length <= 1) return;

        let primaryImg = container.querySelector('.primary-img') || container.querySelector('img');
        let secondaryImg = container.querySelector('.secondary-img');

        if (!primaryImg) {
            primaryImg = container.querySelector('img');
            if (primaryImg) primaryImg.classList.add('primary-img');
        }

        if (!secondaryImg && primaryImg) {
            secondaryImg = document.createElement('img');
            secondaryImg.className = 'experience-card-img secondary-img';
            secondaryImg.src = images[1] || images[0];
            secondaryImg.alt = primaryImg.alt || '';
            secondaryImg.loading = 'lazy';
            container.insertBefore(secondaryImg, primaryImg.nextSibling);
        }

        const dots = container.querySelectorAll('.card-img-dot');
        const initialPrimarySrc = primaryImg ? primaryImg.src : images[0];

        let activeIdx = 0;
        let motionTimer = null;
        let isSecondaryVisible = false;

        const cardParent = container.closest('.experience-card, .group, [class*="card"]') || container;

        function updateDots(idx) {
            dots.forEach((d, i) => {
                if (i === idx) d.classList.add('active');
                else d.classList.remove('active');
            });
        }

        function transitionTo(idx) {
            if (!images[idx]) return;
            activeIdx = idx;
            updateDots(activeIdx);

            container.classList.add('auto-motion-active');

            if (!isSecondaryVisible) {
                // Secondary layer fades in with the new image
                if (secondaryImg) {
                    secondaryImg.src = images[activeIdx];
                    secondaryImg.classList.add('img-in-motion');
                    secondaryImg.style.opacity = '1';
                }
                if (primaryImg) {
                    primaryImg.classList.remove('img-in-motion');
                }
                isSecondaryVisible = true;
            } else {
                // Primary layer updates underneath and secondary fades out
                if (primaryImg) {
                    primaryImg.src = images[activeIdx];
                    primaryImg.classList.add('img-in-motion');
                    primaryImg.style.opacity = '1';
                }
                if (secondaryImg) {
                    secondaryImg.style.opacity = '0';
                    secondaryImg.classList.remove('img-in-motion');
                }
                isSecondaryVisible = false;
            }
        }

        function startAutoMotion() {
            stopAutoMotion();
            container.classList.add('auto-motion-active');

            // Quick first transition after 400ms, then regular cadence every 1300ms
            motionTimer = setTimeout(function tick() {
                const nextIdx = (activeIdx + 1) % images.length;
                transitionTo(nextIdx);
                motionTimer = setTimeout(tick, 1300);
            }, 400);
        }

        function stopAutoMotion() {
            if (motionTimer) {
                clearTimeout(motionTimer);
                motionTimer = null;
            }
        }

        function resetCard() {
            stopAutoMotion();
            activeIdx = 0;
            updateDots(0);
            container.classList.remove('auto-motion-active');

            if (secondaryImg) {
                secondaryImg.style.opacity = '0';
                secondaryImg.classList.remove('img-in-motion');
            }
            if (primaryImg) {
                primaryImg.src = initialPrimarySrc;
                primaryImg.style.opacity = '1';
                primaryImg.classList.remove('img-in-motion');
            }
            isSecondaryVisible = false;
        }

        // Automatic motion as soon as cursor hovers anywhere on the card
        cardParent.addEventListener('mouseenter', () => {
            startAutoMotion();
        });

        cardParent.addEventListener('mouseleave', () => {
            resetCard();
        });

        // Scrubbing capability on direct container movement
        let scrubDebounce = null;
        container.addEventListener('mousemove', (e) => {
            stopAutoMotion();
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const pct = Math.max(0, Math.min(0.999, x / rect.width));
            const newIndex = Math.floor(pct * images.length);

            if (newIndex !== activeIdx && images[newIndex]) {
                transitionTo(newIndex);
            }

            // Resume automatic motion after user pauses cursor
            if (scrubDebounce) clearTimeout(scrubDebounce);
            scrubDebounce = setTimeout(() => {
                startAutoMotion();
            }, 1000);
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    window.initCardImageScrubbers();
    if (window.initLenisSmoothScroll) window.initLenisSmoothScroll();
    if (window.initMegaMenuAnimations) window.initMegaMenuAnimations();
});

// ==========================================
// LENIS SMOOTH SCROLL INITIALIZATION
// ==========================================
window.initLenisSmoothScroll = function () {
    if (typeof Lenis === 'undefined') return;
    try {
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            orientation: 'vertical',
            gestureOrientation: 'vertical',
            smoothWheel: true,
            wheelMultiplier: 1.0,
            touchMultiplier: 1.5,
        });
        window.lenis = lenis;

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // Override scrollToTop with Lenis
        window.scrollToTop = function () {
            if (window.lenis) {
                window.lenis.scrollTo(0, { duration: 1.2 });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        };
    } catch (e) {
        console.warn('Lenis scroll init notice:', e);
    }
};

// ==========================================
// GSAP BUTTER-SMOOTH MEGA MENU CONTROLLER
// (Hover-Intent Protection + Top-to-Bottom Curtain Open & Close)
// ==========================================
window.initMegaMenuAnimations = function () {
    if (typeof gsap === 'undefined') return;

    const navItems = document.querySelectorAll('.mega-nav-item');
    if (!navItems.length) return;

    let activeItem = null;
    let openTimer = null;
    let closeTimer = null;

    // Helper: Close a dropdown with bottom-to-top curtain collapse
    function closeDropdown(item, instant = false) {
        if (!item) return;
        const panel = item.querySelector('.mega-dropdown-panel');
        const card = item.querySelector('.mega-dropdown-card');
        if (!panel || !card) return;

        panel.style.pointerEvents = 'none';
        gsap.killTweensOf(card);

        if (instant) {
            gsap.set(card, { clipPath: 'inset(0% 0% 100% 0%)', opacity: 0, y: -8 });
            panel.style.visibility = 'hidden';
            if (activeItem === item) activeItem = null;
        } else {
            gsap.to(card, {
                clipPath: 'inset(0% 0% 100% 0%)',
                opacity: 0,
                y: -8,
                duration: 0.2,
                ease: 'power2.in',
                onComplete: () => {
                    panel.style.visibility = 'hidden';
                    if (activeItem === item) activeItem = null;
                }
            });
        }
    }

    // Helper: Open a dropdown with top-to-bottom curtain reveal
    function openDropdown(item) {
        if (!item) return;
        const panel = item.querySelector('.mega-dropdown-panel');
        const card = item.querySelector('.mega-dropdown-card');
        if (!panel || !card) return;

        // Cancel any pending close or open timers
        if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = null;
        }
        if (openTimer) {
            clearTimeout(openTimer);
            openTimer = null;
        }

        // If another item was open, close it instantly without lag
        if (activeItem && activeItem !== item) {
            closeDropdown(activeItem, true);
        }

        activeItem = item;

        gsap.killTweensOf(card);
        panel.style.visibility = 'visible';
        panel.style.pointerEvents = 'auto';

        // Silky smooth curtain reveal from top to bottom
        gsap.fromTo(card,
            {
                clipPath: 'inset(0% 0% 100% 0%)',
                opacity: 0,
                y: -8
            },
            {
                clipPath: 'inset(0% 0% 0% 0%)',
                opacity: 1,
                y: 0,
                duration: 0.32,
                ease: 'power2.out',
                overwrite: 'auto'
            }
        );
    }

    // Schedule closing with a brief grace window (160ms) for moving cursor into dropdown
    function scheduleClose() {
        if (openTimer) {
            clearTimeout(openTimer);
            openTimer = null;
        }
        if (closeTimer) clearTimeout(closeTimer);
        closeTimer = setTimeout(() => {
            if (activeItem) {
                const closingTarget = activeItem;
                activeItem = null;
                closeDropdown(closingTarget, false);
            }
        }, 160);
    }

    // Bind event listeners with Hover-Intent threshold
    navItems.forEach(item => {
        const link = item.querySelector(':scope > a');
        const panel = item.querySelector('.mega-dropdown-panel');
        const card = item.querySelector('.mega-dropdown-card');
        if (!link || !panel || !card) return;

        // 1. Mouse enters nav link: require intentional hover (180ms delay) if no menu is open, or switch immediately if already exploring menus
        link.addEventListener('mouseenter', () => {
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }

            if (activeItem) {
                // User is already active in menu navigation: switch fast
                openDropdown(item);
            } else {
                // User is moving cursor from page: wait 180ms to confirm intention and avoid accidental triggers
                if (openTimer) clearTimeout(openTimer);
                openTimer = setTimeout(() => {
                    openDropdown(item);
                }, 180);
            }
        });

        // 2. Mouse leaves nav item container: cancel pending open and schedule close
        item.addEventListener('mouseleave', () => {
            if (openTimer) {
                clearTimeout(openTimer);
                openTimer = null;
            }
            if (activeItem) {
                scheduleClose();
            }
        });

        // 3. Hovering the dropdown panel keeps it open
        panel.addEventListener('mouseenter', () => {
            if (activeItem === item) {
                if (closeTimer) {
                    clearTimeout(closeTimer);
                    closeTimer = null;
                }
            }
        });

        // 4. Leaving dropdown panel schedules close
        panel.addEventListener('mouseleave', () => {
            if (activeItem === item) {
                scheduleClose();
            }
        });
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (openTimer) {
                clearTimeout(openTimer);
                openTimer = null;
            }
            if (activeItem) {
                closeDropdown(activeItem, true);
                activeItem = null;
            }
        }
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (activeItem && !activeItem.contains(e.target)) {
            if (openTimer) {
                clearTimeout(openTimer);
                openTimer = null;
            }
            closeDropdown(activeItem, true);
            activeItem = null;
        }
    });
};

