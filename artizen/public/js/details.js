// Controller script for minimal, clean e-commerce event-details.html

document.addEventListener('DOMContentLoaded', () => {
    // Only run on event details pages — guard prevents title/DOM clobbering on other pages
    if (!document.getElementById('detail-image') && !window.location.pathname.match(/^\/event\//)) return;

    // 1. Parse URL Parameters
    const urlParams = new URLSearchParams(window.location.search);
    const paramId = urlParams.get('id');
    const eventId = paramId ? (parseInt(paramId) || paramId) : (window.currentMatchedId || 1);
    let selectedTierIndex = urlParams.has('tier') ? parseInt(urlParams.get('tier')) : (window.currentMatchedTierIdx || 0);

    // 2. Fetch Event Data safely
    const db = (typeof eventDatabase !== 'undefined' && eventDatabase && Object.keys(eventDatabase).length > 0) ? eventDatabase : (window.eventDatabase || {});
    const data = db[eventId] || db[String(eventId)] || Object.values(db)[0];

    if (!data) {
        document.body.innerHTML = `
            <div class="h-screen w-screen flex flex-col items-center justify-center bg-white text-black font-body">
                <h1 class="text-3xl font-heading font-bold mb-4 uppercase tracking-wider text-black">404 - Event Not Found</h1>
                <a href="/events" class="border border-black px-6 py-3 text-xs uppercase tracking-widest text-white bg-black hover:bg-white hover:text-black transition-colors rounded-lg font-heading font-bold">Go Back to Packages</a>
            </div>
        `;
        return;
    }

    let selectedTier = (data.tiers && data.tiers[selectedTierIndex]) ? data.tiers[selectedTierIndex] : (data.tiers ? data.tiers[0] : data);

    // 3. Find Page Elements
    const imgEl = document.getElementById('detail-image');
    const tagEl = document.getElementById('detail-tag');
    const titleEl = document.getElementById('detail-title');
    const descEl = document.getElementById('detail-desc');
    const aboutDescEl = document.getElementById('detail-about-desc');
    const categoryBreadcrumb = document.getElementById('breadcrumb-category');
    const packageBreadcrumb = document.getElementById('breadcrumb-package');

    // Select selectors
    const tierSelect = document.getElementById('details-tier-select');
    const dateSelector = document.getElementById('details-date-selector');
    const locationSelector = document.getElementById('details-location-selector');
    const guestsSelector = document.getElementById('details-guests-selector');

    // Summary values
    const startingPriceEl = document.getElementById('detail-starting-price');
    const durationEl = document.getElementById('detail-duration');
    const idealForEl = document.getElementById('detail-ideal-for');
    const setupLocationEl = document.getElementById('detail-setup-location');
    const cardPriceEl = document.getElementById('booking-card-price');
    const mobileStickyPriceEl = document.getElementById('mobile-sticky-price');

    // Thumbnails scroll buttons
    const thumbPrev = document.getElementById('thumb-prev');
    const thumbNext = document.getElementById('thumb-next');
    const thumbnailsContainer = document.getElementById('detail-thumbnails-container');

    // Add-ons scroll buttons and container
    const addonPrev = document.getElementById('addon-prev');
    const addonNext = document.getElementById('addon-next');
    const addonsContainer = document.getElementById('addons-container');

    // Similar Packages scroll buttons and container
    const similarPrev = document.getElementById('similar-prev');
    const similarNext = document.getElementById('similar-next');
    const similarContainer = document.getElementById('similar-packages-grid');

    // State management for Add-ons (aligned to mockup)
    const addonsData = [
        { id: 'extra_balloons', name: 'Extra Balloons', price: 499, icon: 'extra_balloons' },
        { id: 'led_numbers', name: 'LED Numbers', price: 799, icon: 'led_numbers' },
        { id: 'photographer', name: 'Photographer', price: 1999, icon: 'photographer' },
        { id: 'cake', name: 'Cake', price: 999, icon: 'cake' },
        { id: 'cold_pyro', name: 'Cold Pyro Entry', price: 2999, icon: 'cold_pyro' }
    ];
    let selectedAddons = new Set();

    // Custom icons for add-ons to look premium (Font Awesome 6)
    const iconSVGs = {
        extra_balloons: `<i class="fa-regular fa-lightbulb text-sm text-[#FFD600]" aria-hidden="true"></i>`,
        led_numbers: `<i class="fa-solid fa-bolt text-sm text-[#FFD600]" aria-hidden="true"></i>`,
        photographer: `<i class="fa-solid fa-camera text-sm text-[#FFD600]" aria-hidden="true"></i>`,
        cake: `<i class="fa-solid fa-cake-candles text-sm text-[#FFD600]" aria-hidden="true"></i>`,
        cold_pyro: `<i class="fa-solid fa-wand-magic-sparkles text-sm text-[#FFD600]" aria-hidden="true"></i>`
    };

    // Populate Tier Dropdown once
    if (tierSelect) {
        tierSelect.innerHTML = '';
        data.tiers.forEach((tier, index) => {
            const opt = document.createElement('option');
            opt.value = index;
            opt.innerText = `${tier.name} (₹${tier.price.toLocaleString('en-IN')})`;
            tierSelect.appendChild(opt);
        });
        tierSelect.value = selectedTierIndex;
        tierSelect.addEventListener('change', (e) => {
            selectedTierIndex = parseInt(e.target.value);
            renderDynamicConfig();
        });
    }

    // Set Date Selector to today by default
    if (dateSelector) {
        dateSelector.value = new Date().toISOString().split('T')[0];
    }

    if (locationSelector) {
        locationSelector.addEventListener('change', () => {
            updateLivePrice();
        });
    }

    // Wire Thumbnail Scroll Actions
    if (thumbPrev && thumbNext && thumbnailsContainer) {
        thumbPrev.addEventListener('click', () => {
            const isMobile = window.innerWidth < 640;
            if (isMobile) {
                thumbnailsContainer.scrollBy({ left: -80, behavior: 'smooth' });
            } else {
                thumbnailsContainer.scrollBy({ top: -80, behavior: 'smooth' });
            }
        });
        thumbNext.addEventListener('click', () => {
            const isMobile = window.innerWidth < 640;
            if (isMobile) {
                thumbnailsContainer.scrollBy({ left: 80, behavior: 'smooth' });
            } else {
                thumbnailsContainer.scrollBy({ top: 80, behavior: 'smooth' });
            }
        });
    }

    // Wire Similar Packages Scroll Actions
    if (similarPrev && similarNext && similarContainer) {
        similarPrev.addEventListener('click', () => {
            similarContainer.scrollBy({ left: -340, behavior: 'smooth' });
        });
        similarNext.addEventListener('click', () => {
            similarContainer.scrollBy({ left: 340, behavior: 'smooth' });
        });
    }

    // Function to show/hide scroll buttons with smooth transitions
    function updateScrollButtonsOpacity(container, prevBtn, nextBtn) {
        if (!container) return;
        const scrollLeft = container.scrollLeft;
        const scrollWidth = container.scrollWidth;
        const clientWidth = container.clientWidth;

        if (prevBtn) {
            if (scrollLeft > 10) {
                prevBtn.classList.remove('opacity-0', 'pointer-events-none');
                prevBtn.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                prevBtn.classList.remove('opacity-100', 'pointer-events-auto');
                prevBtn.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        if (nextBtn) {
            if (scrollWidth > clientWidth && scrollLeft + clientWidth < scrollWidth - 10) {
                nextBtn.classList.remove('opacity-0', 'pointer-events-none');
                nextBtn.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                nextBtn.classList.remove('opacity-100', 'pointer-events-auto');
                nextBtn.classList.add('opacity-0', 'pointer-events-none');
            }
        }
    }

    // Wire Add-ons Scroll Actions
    if (addonsContainer) {
        if (addonNext) {
            addonNext.addEventListener('click', () => {
                addonsContainer.scrollBy({ left: 300, behavior: 'smooth' });
            });
        }
        if (addonPrev) {
            addonPrev.addEventListener('click', () => {
                addonsContainer.scrollBy({ left: -300, behavior: 'smooth' });
            });
        }
        addonsContainer.addEventListener('scroll', () => {
            updateScrollButtonsOpacity(addonsContainer, addonPrev, addonNext);
        });
    }

    // Wire Similar Packages Scroll Actions
    if (similarContainer) {
        if (similarNext) {
            similarNext.addEventListener('click', () => {
                similarContainer.scrollBy({ left: 320, behavior: 'smooth' });
            });
        }
        if (similarPrev) {
            similarPrev.addEventListener('click', () => {
                similarContainer.scrollBy({ left: -320, behavior: 'smooth' });
            });
        }
        similarContainer.addEventListener('scroll', () => {
            updateScrollButtonsOpacity(similarContainer, similarPrev, similarNext);
        });
    }

    // Update on window resize (as scrollWidth/clientWidth might change)
    window.addEventListener('resize', () => {
        updateScrollButtonsOpacity(addonsContainer, addonPrev, addonNext);
        updateScrollButtonsOpacity(similarContainer, similarPrev, similarNext);
    });

    // 4. Render Layout & Dynamic Tier Selection Elements
    function renderDynamicConfig() {
        if (!data || !data.tiers) return;
        selectedTier = data.tiers[selectedTierIndex] || data.tiers[0];

        // Update browser page title
        document.title = `${selectedTier.name} | ARTIZEN`;

        // Update breadcrumbs
        if (categoryBreadcrumb) categoryBreadcrumb.innerText = data.title;
        if (packageBreadcrumb) packageBreadcrumb.innerText = selectedTier.name;

        // Update main content dynamically based on selected tier
        if (titleEl) titleEl.innerText = selectedTier.name;
        if (descEl) descEl.innerText = selectedTier.short_desc || selectedTier.desc || data.desc;
        if (aboutDescEl) {
            let detailsText = selectedTier.detailed_desc || selectedTier.detailedDesc || selectedTier.desc || data.detailedDesc;
            if (!detailsText) {
                const inclusionsStr = (selectedTier.inclusions || []).join(', ');
                detailsText = `Experience a flawless celebration with our premium ${selectedTier.name}. This curated package comes fully equipped with ${inclusionsStr || 'everything required for a stunning presentation'}. Our professional team handles the complete setup, alignment, and final execution, ensuring that every element matches the highest standards. We manage the logistics and pack-up, leaving you completely hassle-free to enjoy the event.`;
            }
            if (detailsText.includes('<') && detailsText.includes('>')) {
                aboutDescEl.innerHTML = detailsText;
            } else {
                aboutDescEl.innerText = detailsText;
            }
        }
        if (imgEl) imgEl.src = selectedTier.image || data.image;

        // Update tag dynamically
        if (tagEl) {
            tagEl.innerText = selectedTier.badge || (selectedTierIndex === 0 ? 'BASIC PACKAGE' : selectedTierIndex === 1 ? 'PREMIUM PACKAGE' : 'LUXURY PACKAGE');
        }

        // Update starting specifications
        if (startingPriceEl) {
            if (selectedTier.original_price && selectedTier.original_price > selectedTier.price) {
                startingPriceEl.innerHTML = `<span class="line-through text-gray-400 font-normal text-xs mr-1">₹${selectedTier.original_price.toLocaleString('en-IN')}</span>₹${selectedTier.price.toLocaleString('en-IN')}`;
            } else {
                startingPriceEl.innerText = `₹${selectedTier.price.toLocaleString('en-IN')}`;
            }
        }

        const specs = selectedTier.specifications || {};

        if (durationEl) durationEl.innerText = specs.duration || ((data.tag || "").toLowerCase() === 'decor' ? '3-4 Hours' : '4-5 Hours');

        if (idealForEl) {
            idealForEl.innerText = specs.guest_capacity || (selectedTierIndex === 0 ? 'Upto 50 Guests' : selectedTierIndex === 1 ? '50-100 Guests' : '100+ Guests');
        }

        if (setupLocationEl) {
            setupLocationEl.innerText = specs.location_type || ((data.tag || "").toLowerCase() === 'live' ? 'Indoor Only' : 'Indoor & Outdoor');
        }

        // Update dropdown state
        if (tierSelect) {
            tierSelect.value = selectedTierIndex;
        }

        // Update dynamic gallery images list & main showcase photo thumbnails
        renderGalleryAndThumbnails(selectedTier.image || data.image);

        // Render Inclusions checklist
        renderInclusions(selectedTier.inclusions);

        // Update price display including add-ons
        updateLivePrice();
    }

    // Create 4-5 image gallery dynamically using active setup images
    function renderGalleryAndThumbnails(activeImage) {
        const thumbContainer = document.getElementById('detail-thumbnails-container');
        const galleryGrid = document.getElementById('package-gallery-grid');

        // Collate images: active tier uploaded gallery first, active main image, then category general image
        const tierGallery = selectedTier.gallery || [];
        const galleryImages = [
            activeImage,
            ...(tierGallery.filter(Boolean)),
            data.image,
            ...(data.gallery || []),
            ...data.tiers.map(t => t.image)
        ].filter(Boolean).filter((img, pos, self) => self.indexOf(img) === pos).slice(0, 10);

        // 1. Render Thumbnails row
        if (thumbContainer) {
            thumbContainer.innerHTML = '';
            galleryImages.forEach((imgSrc, index) => {
                const thumbBtn = document.createElement('button');
                thumbBtn.className = `thumbnail-btn w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border-2 transition-all shrink-0 cursor-pointer ${index === 0 ? 'border-[#FFD600] ring-2 ring-[#FFD600]/20' : 'border-[#E8E5DF] hover:border-gray-400'}`;
                thumbBtn.innerHTML = `<img src="${imgSrc}" class="w-full h-full object-cover">`;

                thumbBtn.addEventListener('click', () => {
                    // Update main image src
                    if (imgEl) imgEl.src = imgSrc;
                    // Reset borders on thumbnails
                    thumbContainer.querySelectorAll('button').forEach(btn => {
                        btn.className = 'thumbnail-btn w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border-2 transition-all shrink-0 cursor-pointer border-[#E8E5DF] hover:border-gray-400';
                    });
                    thumbBtn.className = 'thumbnail-btn w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border-2 transition-all shrink-0 cursor-pointer border-[#FFD600] ring-2 ring-[#FFD600]/20';
                });

                thumbContainer.appendChild(thumbBtn);
            });
        }

        // 2. Render static image grid (4 columns layout)
        if (galleryGrid) {
            galleryGrid.innerHTML = '';
            galleryImages.slice(0, 4).forEach(imgSrc => {
                const div = document.createElement('div');
                div.className = "aspect-[4/3] rounded-lg overflow-hidden border border-[#E8E5DF] dark:border-white/10 bg-white dark:bg-white/5 cursor-pointer group";
                div.innerHTML = `<img src="${imgSrc}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">`;
                div.addEventListener('click', () => {
                    if (imgEl) imgEl.src = imgSrc;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                galleryGrid.appendChild(div);
            });
        }
    }

    // Render Checklist with checkmark icons inside card
    function renderInclusions(inclusions) {
        const container = document.getElementById('full-inclusions-checklist');
        const metaInclusionsContainer = document.getElementById('meta-inclusions-list');

        // If the database has less than 8 inclusions, augment them with standard deliverables so the UI looks complete
        let finalInclusions = Array.isArray(inclusions) ? [...inclusions] : [];
        const defaultExtras = [
            "Setup & Dismantle",
            "On-time Arrival",
            "Dedicated Assistant",
            "Fairy Lights Canopy"
        ];
        while (finalInclusions.length < 8 && defaultExtras.length > 0) {
            const nextExtra = defaultExtras.shift();
            if (!finalInclusions.includes(nextExtra)) {
                finalInclusions.push(nextExtra);
            }
        }

        if (container) {
            container.innerHTML = '';
            finalInclusions.slice(0, 8).forEach(inc => {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-2.5 py-1 bg-transparent';
                item.innerHTML = `
                    <div class="w-5 h-5 rounded-full border border-[#FFD600]/40 flex items-center justify-center shrink-0 bg-[#FFD600]/10">
                        <i class="fa-solid fa-check text-[10px] text-[#171719] dark:text-[#FFD600]" aria-hidden="true"></i>
                    </div>
                    <span class="text-xs text-[#333333] dark:text-[#D4D4D8] font-medium font-body leading-tight">${inc}</span>
                `;
                container.appendChild(item);
            });
        }

        if (metaInclusionsContainer) {
            metaInclusionsContainer.innerHTML = '';
            finalInclusions.slice(0, 6).forEach(inc => {
                const item = document.createElement('li');
                item.className = 'flex items-center gap-2 py-0.5 text-xs text-[#333333] dark:text-[#D4D4D8] font-medium font-body';
                item.innerHTML = `
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FFD600] shrink-0"></span>
                    <span class="truncate">${inc}</span>
                `;
                metaInclusionsContainer.appendChild(item);
            });
        }
    }

    // Render Optional Add-ons horizontal rows (clean list layout)
    function renderAddons() {
        const container = document.getElementById('addons-container');
        if (!container) return;

        const selectedTier = data.tiers[selectedTierIndex] || data.tiers[0];
        let currentAddons = addonsData;
        if (selectedTier.addons && selectedTier.addons.length > 0) {
            currentAddons = selectedTier.addons.map((a, idx) => ({
                id: `custom_addon_${idx}`,
                name: a.title,
                price: parseInt(a.price) || 0,
                icon: a.icon || 'pyro',
                faClass: a.icon && a.icon.startsWith('fa-') ? a.icon : null,
                desc: a.desc || ''
            }));
            addonsData = currentAddons;
        }

        container.innerHTML = '';
        currentAddons.forEach(addon => {
            const card = document.createElement('div');
            const isAdded = selectedAddons.has(addon.id);
            card.className = `py-3 border-b border-[#F0EFEB] last:border-0 flex items-center justify-between gap-3 transition-colors`;

            const iconHtml = addon.faClass ? `<i class="${addon.faClass} text-[#FFD600] text-sm"></i>` : (iconSVGs[addon.icon] || '<i class="fa-solid fa-wand-magic-sparkles text-[#FFD600] text-sm"></i>');

            card.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#FFD600]/10 text-[#171719] dark:text-[#FFD600] flex items-center justify-center shrink-0 border border-[#FFD600]/30">
                        ${iconHtml}
                    </div>
                    <span class="font-heading font-bold text-xs sm:text-sm text-[#171717] dark:text-white">${addon.name}</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="font-heading font-bold text-xs sm:text-sm text-[#171717] dark:text-white">+₹${addon.price.toLocaleString('en-IN')}</span>
                    <button id="addon-btn-${addon.id}" class="px-4 py-1.5 rounded-lg text-xs font-heading font-bold transition-all cursor-pointer ${isAdded ? 'bg-[#FFD600] text-[#171719] border border-[#FFD600]' : 'bg-white dark:bg-[#202024] text-[#171719] dark:text-white border border-[#E8E5DF] dark:border-white/10 hover:border-[#FFD600] hover:text-[#171719]'}">
                        ${isAdded ? 'Added' : 'Add'}
                    </button>
                </div>
            `;

            const btn = card.querySelector('button');
            btn.addEventListener('click', () => toggleAddon(addon.id, btn, card));
            container.appendChild(card);
        });
    }

    function toggleAddon(addonId, btnEl, cardEl) {
        if (selectedAddons.has(addonId)) {
            selectedAddons.delete(addonId);
            btnEl.innerText = 'Add';
            btnEl.className = 'px-4 py-1.5 rounded-lg text-xs font-heading font-bold transition-all cursor-pointer bg-white dark:bg-[#202024] text-[#171719] dark:text-white border border-[#E8E5DF] dark:border-white/10 hover:border-[#FFD600] hover:text-[#171719]';
        } else {
            selectedAddons.add(addonId);
            btnEl.innerText = 'Added';
            btnEl.className = 'px-4 py-1.5 rounded-lg text-xs font-heading font-bold transition-all cursor-pointer bg-[#FFD600] text-[#171719] border border-[#FFD600]';
        }

        updateLivePrice();
    }

        updateLivePrice();
    }

    const locationSurcharges = {
        "Vijay Nagar, Indore": 0,
        "Palasia, Indore": 0,
        "Scheme 54 / Saket, Indore": 0,
        "Rajwada, Indore": 299,
        "Nipania, Indore": 499,
        "Bypass Road, Indore": 799,
        "Silicon City / Rau, Indore": 999
    };

    function updateLivePrice() {
        const selectedTier = data.tiers[selectedTierIndex] || data.tiers[0];
        const basePrice = selectedTier.price;
        let addonsPrice = 0;

        selectedAddons.forEach(addonId => {
            const addon = addonsData.find(a => a.id === addonId);
            if (addon) addonsPrice += addon.price;
        });

        const locationVal = locationSelector ? locationSelector.value : "";
        const travelSurcharge = locationSurcharges[locationVal] || 0;
        const totalPrice = basePrice + addonsPrice + travelSurcharge;

        // Update Breakdown Elements
        const breakdownContainer = document.getElementById('price-breakdown-container');
        const breakdownBase = document.getElementById('breakdown-base');
        const breakdownAddons = document.getElementById('breakdown-addons');
        const breakdownAddonsRow = document.getElementById('breakdown-addons-row');
        const breakdownTravel = document.getElementById('breakdown-travel');
        const breakdownTravelRow = document.getElementById('breakdown-travel-row');

        if (breakdownContainer) {
            if (addonsPrice > 0 || travelSurcharge > 0) {
                breakdownContainer.classList.remove('hidden');
            } else {
                breakdownContainer.classList.add('hidden');
            }
        }

        if (breakdownBase) {
            breakdownBase.innerText = `₹${basePrice.toLocaleString('en-IN')}`;
        }

        if (breakdownAddons) {
            breakdownAddons.innerText = `₹${addonsPrice.toLocaleString('en-IN')}`;
            if (breakdownAddonsRow) {
                if (addonsPrice > 0) {
                    breakdownAddonsRow.classList.remove('hidden');
                } else {
                    breakdownAddonsRow.classList.add('hidden');
                }
            }
        }

        if (breakdownTravel) {
            breakdownTravel.innerText = `₹${travelSurcharge.toLocaleString('en-IN')}`;
            if (breakdownTravelRow) {
                if (travelSurcharge > 0) {
                    breakdownTravelRow.classList.remove('hidden');
                } else {
                    breakdownTravelRow.classList.add('hidden');
                }
            }
        }

        if (cardPriceEl) {
            cardPriceEl.innerText = `₹${totalPrice.toLocaleString('en-IN')}`;
        }
        if (mobileStickyPriceEl) {
            mobileStickyPriceEl.innerText = `₹${totalPrice.toLocaleString('en-IN')}`;
        }
    }

    // 5. Action Handlers bound to page
    window.enquireOnWhatsAppFromDetails = function () {
        const selectedTier = data.tiers[selectedTierIndex];
        const date = dateSelector ? dateSelector.value : "Not Selected";
        const location = locationSelector ? locationSelector.value : "Not Selected";
        const addressEl = document.getElementById('details-address-input');
        const address = addressEl ? addressEl.value : "Not Selected";
        const guests = guestsSelector ? guestsSelector.value : "Not Selected";

        let addonsText = '';
        if (selectedAddons.size > 0) {
            addonsText = '\n- Add-ons: ' + Array.from(selectedAddons).map(id => {
                const a = addonsData.find(addon => addon.id === id);
                return `${a.name} (₹${a.price})`;
            }).join(', ');
        }

        const travelSurcharge = locationSurcharges[location] || 0;
        let travelText = '';
        if (travelSurcharge > 0) {
            travelText = ` (+₹${travelSurcharge} Setup Fee)`;
        }

        const totalPrice = selectedTier.price +
            Array.from(selectedAddons).reduce((sum, id) => sum + addonsData.find(a => a.id === id).price, 0) +
            travelSurcharge;

        const text = `Hi Artizen, I want to book the "${data.title}" package.\n- Tier: ${selectedTier.name} (₹${selectedTier.price.toLocaleString('en-IN')})${addonsText}\n- Setup Area: ${location}${travelText}\n- Venue Address: ${address}\n- Date: ${date}\n- Expected Guests: ${guests}\n- Total Price: ₹${totalPrice.toLocaleString('en-IN')}\n\nPlease confirm availability and details.`;
        const encodedText = encodeURIComponent(text);
        const url = `https://wa.me/919131668156?text=${encodedText}`;
        window.open(url, '_blank');
    };

    window.bookPackageNow = function () {
        const selectedTier = (data && data.tiers && data.tiers[selectedTierIndex]) ? data.tiers[selectedTierIndex] : { name: 'Setup Package', price: 4999 };
        const catTitle = (data && data.title) ? data.title : 'Event Setup';
        const fullPkgName = `${catTitle} - ${selectedTier.name}`;
        const price = selectedTier.price || 4999;

        // Redirect directly to canonical Laravel /booking route with pre-filled package parameters
        window.location.href = `/booking?category=${encodeURIComponent(catTitle)}&package=${encodeURIComponent(fullPkgName)}&price=${price}`;
    };

    // 6. Similar/Related packages population (Exact Home Page Experience Card Design & Content)
    function renderSimilarPackages() {
        const container = document.getElementById('similar-packages-grid');
        if (!container) return;

        container.innerHTML = '';

        // Check current event ID
        const currentIdNum = parseInt(eventId) || (window.currentMatchedId ? parseInt(window.currentMatchedId) : 1);
        let similarList = [];
        if (currentIdNum === 1) {
            similarList = [
                {
                    id: 1,
                    tierIndex: 1,
                    categoryName: "Adult Birthdays",
                    title: "Standard Birthday Package",
                    price: 7999,
                    badge: "Best Seller",
                    badgeClass: "gold",
                    image: "https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=600&auto=format&fit=crop",
                    secondaryImage: "https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?q=80&w=600&auto=format&fit=crop",
                    images: [
                        "https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=600&auto=format&fit=crop",
                        "https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?q=80&w=600&auto=format&fit=crop"
                    ],
                    desc: "Premium balloon styling, LED numbers, customized backdrop arch, and dedicated party coordinator.",
                    rating: "4.9"
                },
                {
                    id: 1,
                    tierIndex: 2,
                    categoryName: "Adult Birthdays",
                    title: "Premium Luxury Birthday",
                    price: 14999,
                    badge: "Luxury Tier",
                    badgeClass: "gold",
                    image: "https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?q=80&w=600&auto=format&fit=crop",
                    secondaryImage: "https://images.unsplash.com/photo-1513151233558-d860c5398176?q=80&w=600&auto=format&fit=crop",
                    images: [
                        "https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?q=80&w=600&auto=format&fit=crop",
                        "https://images.unsplash.com/photo-1513151233558-d860c5398176?q=80&w=600&auto=format&fit=crop"
                    ],
                    desc: "Grand sequin shimmer backdrop, personalized neon sign, cold pyro entry, and complete sound setup.",
                    rating: "5.0"
                },
                {
                    id: 4,
                    tierIndex: 0,
                    categoryName: "Kids Birthday",
                    title: "Kids Pastel Birthday Setup",
                    price: 2999,
                    badge: "Essential Setup",
                    badgeClass: "",
                    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=600&auto=format&fit=crop",
                    secondaryImage: "https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=600&auto=format&fit=crop",
                    images: [
                        "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=600&auto=format&fit=crop",
                        "https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=600&auto=format&fit=crop"
                    ],
                    desc: "Themed kids backdrop, soft play zone, organic pastel balloon arch, and kids party music.",
                    rating: "4.8"
                },
                {
                    id: 2,
                    tierIndex: 0,
                    categoryName: "House Party",
                    title: "House Party Acoustic Rig",
                    price: 5999,
                    badge: "Weekend Special",
                    badgeClass: "",
                    image: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=600&auto=format&fit=crop",
                    secondaryImage: "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=600&auto=format&fit=crop",
                    images: [
                        "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=600&auto=format&fit=crop",
                        "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=600&auto=format&fit=crop"
                    ],
                    desc: "High-bass sound console, ambient disco party lights, mic setup, and DJ live mixing.",
                    rating: "4.9"
                }
            ];
        } else {
            const allEvents = Object.values(db);
            const otherEvents = allEvents.filter(e => parseInt(e.id) !== currentIdNum);
            const recommendations = otherEvents.slice(0, 4);

            recommendations.forEach(evt => {
                const defaultTier = (evt.tiers && evt.tiers[0]) ? evt.tiers[0] : { name: evt.title, price: 0, desc: evt.desc };
                const bText = defaultTier.badge || 'Essential Setup';
                const isGold = bText.toLowerCase().includes('seller') || bText.toLowerCase().includes('luxury');
                const primaryImage = defaultTier.image || evt.image || 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=600&auto=format&fit=crop';
                const nextTier = (evt.tiers && evt.tiers[1]) ? evt.tiers[1] : {};
                const gallery = evt.gallery || [];
                const secImg = (gallery[0] && gallery[0] !== primaryImage) 
                    ? gallery[0] 
                    : (nextTier.image && nextTier.image !== primaryImage ? nextTier.image : (gallery[1] || primaryImage));
                const allImgs = Array.from(new Set([primaryImage, secImg, ...(gallery.slice(0, 3)), ...(evt.tiers ? evt.tiers.map(t => t.image) : [])])).filter(Boolean);

                similarList.push({
                    id: evt.id,
                    tierIndex: 0,
                    categoryName: evt.title,
                    title: defaultTier.name || evt.title,
                    price: defaultTier.price || 4999,
                    badge: bText,
                    badgeClass: isGold ? 'gold' : '',
                    image: primaryImage,
                    secondaryImage: secImg,
                    images: allImgs,
                    desc: defaultTier.desc || evt.desc || 'Complete celebration setup with verified team and on-site coordination.',
                    rating: "4.9"
                });
            });
        }

        similarList.forEach(pkg => {
            const card = document.createElement('div');
            card.className = "experience-card cursor-pointer";
            card.onclick = () => {
                window.location.href = `/event/${pkg.id}?tier=${pkg.tierIndex || 0}`;
            };

            const allImgs = (pkg.images && pkg.images.length > 0) ? pkg.images : [pkg.image, pkg.secondaryImage].filter(Boolean);
            const secImgHtml = (pkg.secondaryImage && pkg.secondaryImage !== pkg.image)
                ? `<img src="${pkg.secondaryImage}" alt="${pkg.title}" class="experience-card-img secondary-img" loading="lazy">`
                : '';
            const indicatorsHtml = (allImgs.length > 1)
                ? `<div class="card-img-indicators">
                    ${allImgs.map((_, idx) => `<span class="card-img-dot ${idx === 0 ? 'active' : ''}"></span>`).join('')}
                   </div>`
                : '';

            card.innerHTML = `
                <div class="flex flex-col flex-grow">
                    <div class="experience-card-img-container" data-card-images='${JSON.stringify(allImgs)}'>
                        <img src="${pkg.image}" alt="${pkg.title}" class="experience-card-img primary-img" loading="lazy">
                        ${secImgHtml}
                        <div class="experience-card-badge ${pkg.badgeClass}">${pkg.badge}</div>
                        ${indicatorsHtml}
                    </div>
                    <div class="experience-card-header">
                        <h3 class="experience-card-title">${pkg.title}</h3>
                        <div class="experience-card-rating flex items-center gap-1">
                            <i class="fa-solid fa-star text-gold text-xs" aria-hidden="true"></i>
                            <span>${pkg.rating || '4.9'}</span>
                        </div>
                    </div>
                    <p class="experience-card-desc">${pkg.desc}</p>
                </div>
                <div class="experience-card-footer mt-auto">
                    <div class="experience-card-price-stack">
                        <div class="experience-card-price">From <span>₹${Number(pkg.price).toLocaleString('en-IN')}</span></div>
                    </div>
                    <span class="experience-card-btn">
                        Book Now
                        <i class="fa-solid fa-chevron-right text-[10px] experience-card-arrow" aria-hidden="true"></i>
                    </span>
                </div>
            `;

            container.appendChild(card);
        });

        // Initialize scrubber and hover event listeners for newly added cards
        if (typeof window.initCardImageScrubbers === 'function') {
            window.initCardImageScrubbers();
        }

        setTimeout(() => updateScrollButtonsOpacity(similarContainer, similarPrev, similarNext), 100);
    }

    // Initial load executions
    renderDynamicConfig();
    renderAddons();
    renderSimilarPackages();
});
