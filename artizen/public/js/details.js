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
    const data = (window.currentEventData && window.currentEventData.title) ? window.currentEventData : (db[eventId] || db[String(eventId)] || Object.values(db)[0]);

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

    // Thumbnails container
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
        extra_balloons: `<i class="fa-regular fa-lightbulb text-sm text-neutral-700 dark:text-neutral-300" aria-hidden="true"></i>`,
        led_numbers: `<i class="fa-solid fa-bolt text-sm text-neutral-700 dark:text-neutral-300" aria-hidden="true"></i>`,
        photographer: `<i class="fa-solid fa-camera text-sm text-neutral-700 dark:text-neutral-300" aria-hidden="true"></i>`,
        cake: `<i class="fa-solid fa-cake-candles text-sm text-neutral-700 dark:text-neutral-300" aria-hidden="true"></i>`,
        cold_pyro: `<i class="fa-solid fa-wand-magic-sparkles text-sm text-neutral-700 dark:text-neutral-300" aria-hidden="true"></i>`
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
        document.title = `${data.title || selectedTier.name} | ARTIZEN`;

        // Update breadcrumbs
        if (categoryBreadcrumb) categoryBreadcrumb.innerText = data.category || 'Packages';
        if (packageBreadcrumb) packageBreadcrumb.innerText = data.title || selectedTier.name;

        // Update main content dynamically based on selected tier
        if (titleEl) titleEl.innerText = data.title || selectedTier.name;
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

    // Create 4-5 image gallery dynamically using active setup images & Swiper carousel
    function renderGalleryAndThumbnails(activeImage) {
        const thumbContainer = document.getElementById('detail-thumbnails-container');
        const galleryGrid = document.getElementById('package-gallery-grid');
        const swiperWrapper = document.getElementById('detail-swiper-wrapper');

        // Collate images: active tier uploaded gallery first, active main image, then category general image
        const tierGallery = selectedTier.gallery || [];
        const rawList = [
            activeImage,
            ...(tierGallery.filter(Boolean)),
            data.image,
            ...(data.gallery || []),
            ...data.tiers.map(t => t.image)
        ].filter(Boolean);

        // Strictly deduplicate by normalized path so full URLs and relative paths never duplicate
        const seen = new Set();
        const galleryImages = [];
        rawList.forEach(img => {
            const norm = (img || '').replace(/^https?:\/\/[^\/]+/, '');
            if (norm && !seen.has(norm)) {
                seen.add(norm);
                galleryImages.push(img);
            }
        });
        window.currentGalleryImages = galleryImages;

        // 1. Populate Main Showcase Swiper Slides
        if (swiperWrapper) {
            swiperWrapper.innerHTML = '';
            galleryImages.forEach((imgSrc, index) => {
                const slide = document.createElement('div');
                slide.className = 'swiper-slide w-full h-full flex items-center justify-center bg-gray-50';
                slide.innerHTML = `<img src="${imgSrc}" alt="${selectedTier.name || 'Package'}" class="w-full h-full object-cover select-none pointer-events-none" ${index === 0 ? 'id="detail-image"' : ''}>`;
                swiperWrapper.appendChild(slide);
            });

            // Destroy previous instance of Swiper cleanly
            if (window.detailMainSwiper && typeof window.detailMainSwiper.destroy === 'function') {
                try {
                    window.detailMainSwiper.destroy(true, true);
                } catch(e) {}
            }

            // Initialize Swiper for drag / swipe left to right & right to left
            if (typeof Swiper !== 'undefined') {
                window.detailMainSwiper = new Swiper('.detail-main-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    grabCursor: true,
                    observer: true,
                    observeParents: true,
                    resistanceRatio: 0.85,
                    speed: 350,
                    pagination: {
                        el: '.detail-swiper-pagination',
                        clickable: true,
                        dynamicBullets: true,
                    },
                    on: {
                        slideChange: function () {
                            updateActiveThumbnail(this.activeIndex);
                        }
                    }
                });
            }
        }

        function updateActiveThumbnail(activeIndex) {
            if (!thumbContainer) return;
            const buttons = thumbContainer.querySelectorAll('button');
            buttons.forEach((btn, idx) => {
                if (idx === activeIndex) {
                    btn.className = 'thumbnail-btn w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border-2 shrink-0 cursor-pointer border-[#18181B] dark:border-white opacity-100';
                    btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
                } else {
                    btn.className = 'thumbnail-btn w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border-2 shrink-0 cursor-pointer border-[#E8E5DF] dark:border-white/10 opacity-60';
                }
            });
        }

        // 2. Render Thumbnails row (without arrow buttons)
        if (thumbContainer) {
            thumbContainer.innerHTML = '';
            if (galleryImages.length <= 1) {
                thumbContainer.style.display = 'none';
            } else {
                thumbContainer.style.display = 'flex';
                galleryImages.forEach((imgSrc, index) => {
                    const thumbBtn = document.createElement('button');
                    thumbBtn.type = 'button';
                    thumbBtn.className = `thumbnail-btn w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border-2 shrink-0 cursor-pointer ${index === 0 ? 'border-[#18181B] dark:border-white opacity-100' : 'border-[#E8E5DF] dark:border-white/10 opacity-60'}`;
                    thumbBtn.innerHTML = `<img src="${imgSrc}" class="w-full h-full object-cover select-none">`;

                    thumbBtn.addEventListener('click', () => {
                        if (window.detailMainSwiper && typeof window.detailMainSwiper.slideTo === 'function') {
                            window.detailMainSwiper.slideTo(index);
                        } else if (imgEl) {
                            imgEl.src = imgSrc;
                        }
                        updateActiveThumbnail(index);
                    });

                    thumbContainer.appendChild(thumbBtn);
                });
            }
        }

        // 3. Render static image grid (4 columns layout)
        if (galleryGrid) {
            galleryGrid.innerHTML = '';
            galleryImages.slice(0, 4).forEach(imgSrc => {
                const div = document.createElement('div');
                div.className = "aspect-[4/3] rounded-lg overflow-hidden border border-[#E8E5DF] dark:border-white/10 bg-white dark:bg-white/5 cursor-pointer";
                div.innerHTML = `<img src="${imgSrc}" class="w-full h-full object-cover">`;
                div.addEventListener('click', () => {
                    const idx = galleryImages.indexOf(imgSrc);
                    if (typeof openFullGalleryModal === 'function') {
                        openFullGalleryModal(idx !== -1 ? idx : imgSrc);
                    } else if (idx !== -1 && window.detailMainSwiper) {
                        window.detailMainSwiper.slideTo(idx);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
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
                    <div class="w-5 h-5 rounded-md border border-[#E5E7EB] dark:border-white/10 flex items-center justify-center shrink-0 bg-[#F4F4F5] dark:bg-white/5 text-[#18181B] dark:text-white">
                        <i class="fa-solid fa-check text-[10px]" aria-hidden="true"></i>
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
                item.className = 'flex items-start gap-2 py-1 text-xs text-[#222222] dark:text-[#E4E4E7] font-medium font-body leading-snug';
                item.innerHTML = `
                    <i class="fa-solid fa-check text-neutral-800 dark:text-neutral-200 text-xs shrink-0 mt-0.5" aria-hidden="true"></i>
                    <span class="line-clamp-2">${inc}</span>
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
            card.className = `py-3 border-b border-[#F0EFEB] dark:border-white/5 last:border-0 flex items-center justify-between gap-3`;

            const iconHtml = addon.faClass ? `<i class="${addon.faClass} text-neutral-700 dark:text-neutral-300 text-sm"></i>` : (iconSVGs[addon.icon] || '<i class="fa-solid fa-wand-magic-sparkles text-neutral-700 dark:text-neutral-300 text-sm"></i>');

            card.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#F4F4F5] dark:bg-white/5 text-neutral-700 dark:text-neutral-300 flex items-center justify-center shrink-0 border border-[#E5E7EB] dark:border-white/10">
                        ${iconHtml}
                    </div>
                    <span class="font-heading font-bold text-xs sm:text-sm text-[#171717] dark:text-white">${addon.name}</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="font-heading font-bold text-xs sm:text-sm text-[#171717] dark:text-white">+₹${addon.price.toLocaleString('en-IN')}</span>
                    <button id="addon-btn-${addon.id}" class="px-4 py-1.5 rounded-lg text-xs font-heading font-bold cursor-pointer ${isAdded ? 'bg-[#18181B] text-white border border-[#18181B]' : 'bg-white dark:bg-[#18181B] text-[#18181B] dark:text-white border border-[#D4D4D8] dark:border-white/20 hover:border-black'}">
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
            btnEl.className = 'px-4 py-1.5 rounded-lg text-xs font-heading font-bold cursor-pointer bg-white dark:bg-[#18181B] text-[#18181B] dark:text-white border border-[#D4D4D8] dark:border-white/20 hover:border-black';
        } else {
            selectedAddons.add(addonId);
            btnEl.innerText = 'Added';
            btnEl.className = 'px-4 py-1.5 rounded-lg text-xs font-heading font-bold cursor-pointer bg-[#18181B] text-white border border-[#18181B]';
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
        const selectedTier = (data && data.tiers && data.tiers[selectedTierIndex]) ? data.tiers[selectedTierIndex] : { name: 'Standard Setup', price: 4999 };
        const catTitle = (data && (data.category_name || data.category)) ? (data.category_name || data.category) : (data && data.title ? data.title : 'Event Setup');
        const pkgTitle = (data && data.title) ? data.title : 'Event Package';
        const pkgSlug = (data && data.slug) ? data.slug : '';
        const price = selectedTier.price || 4999;

        // Gather all currently selected add-ons
        const selectedAddonItems = [];
        if (typeof selectedAddons !== 'undefined' && selectedAddons && selectedAddons.size > 0) {
            selectedAddons.forEach(id => {
                const a = (typeof addonsData !== 'undefined' && addonsData) ? addonsData.find(item => item.id === id) : null;
                if (a) {
                    selectedAddonItems.push({
                        id: a.id,
                        name: a.name,
                        price: a.price
                    });
                }
            });
        }

        const bookingPayload = {
            slug: pkgSlug,
            package_name: pkgTitle,
            category: catTitle,
            tier_index: selectedTierIndex,
            tier_name: selectedTier.name,
            tier_price: price,
            addons: selectedAddonItems,
            image: selectedTier.image || data.image || '/assets/images/hero/1.jpg'
        };

        try {
            sessionStorage.setItem('artizen_checkout_pkg', JSON.stringify(bookingPayload));
        } catch(e) {}

        const addonIds = selectedAddonItems.map(a => a.id).join(',');
        const queryParams = new URLSearchParams({
            slug: pkgSlug,
            category: catTitle,
            package: pkgTitle,
            tier: selectedTierIndex,
            tier_name: selectedTier.name,
            price: price
        });
        if (addonIds) {
            queryParams.set('addons', addonIds);
        }

        // Direct transition to checkout with full state preserved
        window.location.href = `/booking?${queryParams.toString()}`;
    };

    // 6. Similar/Related packages are rendered server-side in details.blade.php matching home page card UI exactly
    function renderSimilarPackages() {
        return;
    }

    // Initial load executions
    renderDynamicConfig();
    renderAddons();
    renderSimilarPackages();
});
