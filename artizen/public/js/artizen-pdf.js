/**
 * ARTIZEN LUXURY EVENT CELEBRATIONS
 * Official Corporate Booking Invoice PDF Generator
 * Exact 1:1 match to reference design with official logo & pixel-perfect styling.
 * Guaranteed to fit on EXACTLY 1 SINGLE A4 PAGE with ZERO overflow.
 * Direct browser file download flow using standalone html2canvas + jsPDF.
 * 100% Zero Visual Leakage: Renders strictly offscreen and never appears on screen.
 */
(function(window) {
    'use strict';

    const ArtizenPDF = {
        /**
         * Preload and convert logo to data URL for synchronous, taint-free canvas rendering.
         */
        getLogoDataUrl: async function() {
            if (window.ARTIZEN_LOGO_BASE64) {
                return window.ARTIZEN_LOGO_BASE64;
            }
            return new Promise((resolve) => {
                const timer = setTimeout(() => resolve('/assets/images/logo/artizen.png'), 800);
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    clearTimeout(timer);
                    try {
                        const c = document.createElement('canvas');
                        c.width = img.naturalWidth || 120;
                        c.height = img.naturalHeight || 120;
                        const ctx = c.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        resolve(c.toDataURL('image/png'));
                    } catch (e) {
                        resolve('/assets/images/logo/artizen.png');
                    }
                };
                img.onerror = function() {
                    clearTimeout(timer);
                    resolve('/assets/images/logo/artizen.png');
                };
                img.src = '/assets/images/logo/artizen.png';
            });
        },

        /**
         * Generate and directly download the official booking PDF invoice.
         * @param {Object} booking Booking data object
         * @param {HTMLElement|null} triggerBtn Button element for UI loading state
         */
        download: async function(booking, triggerBtn) {
            const btn = triggerBtn || document.getElementById('download-pdf-btn');
            const origHtml = btn ? btn.innerHTML : 'Download PDF';
            if (btn) {
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Downloading PDF...';
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
            }

            const restoreBtn = () => {
                if (btn) {
                    btn.innerHTML = origHtml;
                    btn.disabled = false;
                    btn.style.pointerEvents = 'auto';
                }
            };

            const b = booking || {};
            const bookingId = String(b.id || b.booking_id || 'BK-8844');
            const filename = `Artizen-Invoice-${bookingId}.pdf`;

            // Clean up any stale offscreen containers if left behind
            const existingWrap = document.getElementById('artizen-pdf-offscreen-wrapper');
            if (existingWrap && existingWrap.parentNode) {
                existingWrap.parentNode.removeChild(existingWrap);
            }

            let wrapper = null;
            let host = null;

            try {
                // Verify required libraries
                if (typeof window.html2canvas === 'undefined') {
                    throw new Error('html2canvas library is not loaded');
                }
                const jsPDFClass = (window.jspdf && window.jspdf.jsPDF) || window.jsPDF;
                if (!jsPDFClass) {
                    throw new Error('jsPDF library is not loaded');
                }

                // Preload official logo as data URL for instant, taint-free drawing
                const logoUrl = window.ARTIZEN_LOGO_BASE64 || await this.getLogoDataUrl();

                // 1. Create completely offscreen wrapper:
                // Placed 25,000 pixels away from viewport - 100% IMPOSSIBLE to ever be visible on screen!
                wrapper = document.createElement('div');
                wrapper.id = 'artizen-pdf-offscreen-wrapper';
                wrapper.style.position = 'fixed';
                wrapper.style.left = '-25000px';
                wrapper.style.top = '-25000px';
                wrapper.style.width = '794px';
                wrapper.style.height = '1122px';
                wrapper.style.maxWidth = '794px';
                wrapper.style.maxHeight = '1122px';
                wrapper.style.overflow = 'hidden';
                wrapper.style.opacity = '1';
                wrapper.style.pointerEvents = 'none';
                wrapper.style.zIndex = '-999999';
                wrapper.setAttribute('aria-hidden', 'true');

                // 2. Create the exact invoice container
                host = document.createElement('div');
                host.id = 'artizen-official-invoice-print';
                host.style.width = '794px';
                host.style.minWidth = '794px';
                host.style.maxWidth = '794px';
                host.style.height = '1122px';
                host.style.minHeight = '1122px';
                host.style.maxHeight = '1122px';
                host.style.backgroundColor = '#FFFFFF';
                host.style.boxSizing = 'border-box';
                host.style.margin = '0';
                host.style.padding = '0';
                host.style.overflow = 'hidden';
                host.innerHTML = this.buildInvoiceHtml(b, logoUrl);

                wrapper.appendChild(host);
                document.body.appendChild(wrapper);

                // Buffer to ensure layout and fonts are settled
                await new Promise(resolve => setTimeout(resolve, 80));

                // 3. Render canvas via html2canvas with onclone coordinate reset
                const renderPromise = window.html2canvas(host, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#FFFFFF',
                    logging: false,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: 794,
                    windowHeight: 1122,
                    onclone: function(clonedDoc, clonedElem) {
                        const clonedW = clonedDoc.getElementById('artizen-pdf-offscreen-wrapper');
                        if (clonedW) {
                            clonedW.style.position = 'static';
                            clonedW.style.left = '0';
                            clonedW.style.top = '0';
                        }
                        clonedElem.style.position = 'static';
                        clonedElem.style.left = '0';
                        clonedElem.style.top = '0';
                        clonedElem.style.margin = '0';
                        clonedDoc.body.style.margin = '0';
                        clonedDoc.body.style.padding = '0';
                    }
                });

                // Safety timeout of 6 seconds
                const timeoutPromise = new Promise((_, reject) => {
                    setTimeout(() => reject(new Error('PDF generation timed out')), 6000);
                });

                const canvas = await Promise.race([renderPromise, timeoutPromise]);

                // 4. Convert canvas to high-quality JPEG
                const imgData = canvas.toDataURL('image/jpeg', 0.98);

                // 5. Generate A4 Portrait PDF (210mm x 297mm)
                const pdf = new jsPDFClass({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4',
                    compress: true
                });

                // Inject 1 single page image matching full A4 dimensions
                pdf.addImage(imgData, 'JPEG', 0, 0, 210, 297, undefined, 'FAST');

                // 6. Direct file download via Blob + Anchor for universal browser download
                try {
                    const blob = pdf.output('blob');
                    const blobUrl = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = blobUrl;
                    link.download = filename;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    setTimeout(() => {
                        if (link && link.parentNode) link.parentNode.removeChild(link);
                        URL.revokeObjectURL(blobUrl);
                    }, 2000);
                } catch (saveErr) {
                    // Fallback to native jsPDF save
                    pdf.save(filename);
                }

                if (btn) {
                    btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400 mr-1.5"></i> Downloaded!';
                    setTimeout(() => {
                        restoreBtn();
                    }, 2500);
                }
            } catch (err) {
                console.error('Artizen PDF generation error:', err);
                restoreBtn();
                // Graceful fallback to browser print if canvas rendering fails
                if (window.confirm('Direct PDF download encountered an issue. Would you like to open the official print dialog instead?')) {
                    window.print();
                }
            } finally {
                // Guaranteed cleanup: remove wrapper and host from DOM
                if (wrapper && wrapper.parentNode) {
                    wrapper.parentNode.removeChild(wrapper);
                }
                const lingeringWrap = document.getElementById('artizen-pdf-offscreen-wrapper');
                if (lingeringWrap && lingeringWrap.parentNode) {
                    lingeringWrap.parentNode.removeChild(lingeringWrap);
                }
                const lingeringHost = document.getElementById('artizen-official-invoice-print');
                if (lingeringHost && lingeringHost.parentNode) {
                    lingeringHost.parentNode.removeChild(lingeringHost);
                }
            }
        },

        /**
         * Helper to format dates to '04 Nov 2026' format (stripping any raw timestamps)
         */
        formatDate: function(dateStr) {
            if (!dateStr) return '04 Nov 2026';
            try {
                // If format has time like "04 Nov 2026, 05:59 PM", extract only the date portion
                const match = String(dateStr).match(/^(\d{1,2}\s+[A-Za-z]{3}\s+\d{4})/);
                if (match) {
                    return match[1];
                }
                const d = new Date(dateStr);
                if (!isNaN(d.getTime())) {
                    return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
                }
            } catch (e) {}
            return String(dateStr).split(',')[0].trim();
        },

        /**
         * Build the exact HTML layout matching reference image with official logo.
         * Guaranteed to fit on EXACTLY 1 SINGLE A4 PAGE with 100% dynamic data.
         * Zero SVG <text> elements to guarantee ultra-fast, hang-free canvas rendering.
         */
        buildInvoiceHtml: function(b, logoUrl) {
            const bookingId = String(b.id || b.booking_id || 'BK-8844');
            const custName = String(b.name || b.customer_name || 'Valued Customer');
            const custPhone = String(b.mobile || b.customer_mobile || b.phone || 'N/A');
            const custWa = String(b.whatsapp || b.customer_whatsapp || custPhone);
            const custEmail = String(b.email || b.customer_email || 'Not provided');
            const pkgName = String(b.package || b.package_name || 'Celebration Package');
            const tierName = String(b.tier || b.tier_name || 'Standard Setup');
            const catName = String(b.category || b.category_name || 'Event Setup');
            
            const rawEventDate = b.date || b.event_date || '2026-11-07';
            const formattedEventDate = this.formatDate(rawEventDate);
            
            // Clean time display to prevent double parentheses (( ... ))
            let rawTime = String(b.time || b.event_time || '18:00').trim();
            let cleanTime = rawTime;
            if (cleanTime.startsWith('(') && cleanTime.endsWith(')')) {
                cleanTime = cleanTime.slice(1, -1).trim();
            }
            const scheduleDisplay = `${formattedEventDate} &bull; ${cleanTime}`;

            const guests = String(b.guest_count || 25);
            
            const address = String(b.address || 'Indore, Madhya Pradesh');
            const area = String(b.area || '');
            const landmark = String(b.landmark || '');
            const pincode = String(b.pincode || '');
            const city = String(b.city || 'Indore');
            const state = String(b.state || 'Madhya Pradesh');

            const fullVenueParts = [
                address,
                landmark ? `Near ${landmark}` : '',
                area,
                `${city}, ${state}`,
                pincode
            ].filter(Boolean);
            const fullVenue = fullVenueParts.join(', ') || 'Indore, Madhya Pradesh';

            const basePrice = Number(b.package_price || b.price || b.total || 4999);
            const totalAmount = Number(b.total || b.total_amount || basePrice);
            const surcharge = Number(b.surcharge || b.surcharge_amount || (totalAmount > basePrice ? totalAmount - basePrice : 0));
            const rawStatus = String(b.status || 'Pending').toLowerCase().trim();
            
            // Clean booking date (strictly date only, no time like 05:59 PM)
            const rawCreated = b.created_at || new Date().toISOString();
            const formattedBookingDate = this.formatDate(rawCreated);

            const isConfirmed = /confirmed|approved|completed/i.test(rawStatus);
            const isCancelled = /cancel|reject/i.test(rawStatus);

            let statusLabel = isConfirmed ? 'Confirmed' : (isCancelled ? 'Cancelled' : 'Pending Review');
            
            let statusIconSvg = isConfirmed
                ? `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink: 0;"><path d="M20 6L9 17l-5-5"></path></svg>`
                : (isCancelled
                    ? `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink: 0;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`
                    : `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink: 0;"><path d="M5 22h14"></path><path d="M5 2h14"></path><path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"></path><path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"></path></svg>`);
            
            let statusCardBorder = isConfirmed ? '#10B981' : (isCancelled ? '#EF4444' : '#E5A93C');
            let statusSubColor = isConfirmed ? '#047857' : (isCancelled ? '#B91C1C' : '#92400E');

            // Format Currency
            const formatINR = (num) => '₹ ' + Number(num || 0).toLocaleString('en-IN');

            // Metadata Outline Line Icons
            const phoneSvg = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; flex-shrink: 0; min-width: 14px; margin-right:8px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>`;
            const waSvg = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; flex-shrink: 0; min-width: 14px; margin-right:8px;"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>`;
            const mailSvg = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; flex-shrink: 0; min-width: 14px; margin-right:8px;"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>`;
            const pinSvg = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; flex-shrink: 0; min-width: 14px; margin-right:8px;"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>`;
            const calSvg = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; flex-shrink: 0; min-width: 14px; margin-right:8px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>`;
            const usersSvg = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; flex-shrink: 0; min-width: 14px; margin-right:8px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`;

            // Addons rows
            const addons = Array.isArray(b.addons) ? b.addons : [];
            let addonsRowsHtml = '';
            addons.forEach((addon, idx) => {
                const aName = addon.name || addon.title || 'Add-on Service';
                const aPrice = Number(addon.price || 0);
                addonsRowsHtml += `
                    <div style="display: flex; align-items: center; padding: 10px 16px; border-bottom: 1px solid #F3F4F6; background-color: #FAFAFA; font-size: 12px;">
                        <div style="width: 36px; text-align: center; color: #6B7280; font-weight: 700;">${idx + 2}</div>
                        <div style="flex: 1; padding-left: 12px; text-align: left; font-weight: 700; color: #111827;">+ ${aName}</div>
                        <div style="width: 180px; text-align: left; color: #4B5563; font-size: 12px; font-weight: 500;">Custom Add-on</div>
                        <div style="width: 120px; text-align: right; font-weight: 800; font-size: 13.5px; color: #111827;">${formatINR(aPrice)}</div>
                    </div>
                `;
            });

            const resolvedLogo = logoUrl || '/assets/images/logo/artizen.png';

            return `
            <div style="width: 794px; height: 1122px; max-height: 1122px; background-color: #FFFFFF; padding: 34px 44px 28px 44px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #111827; line-height: 1.35; overflow: hidden;">
                
                <!-- Main Upper Body Content -->
                <div>
                    <!-- 1. Header (Official Logo on Left, Invoice Details on Right) -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <!-- Official Brand Logo -->
                        <div style="display: flex; align-items: center;">
                            <img src="${resolvedLogo}" alt="Artizen Logo" style="height: 78px; width: auto; object-fit: contain; flex-shrink: 0;">
                        </div>

                        <!-- Invoice Title & Meta -->
                        <div style="text-align: right; min-width: 260px;">
                            <div style="font-size: 26px; font-weight: 900; color: #111827; margin: 0 0 10px 0; letter-spacing: -0.5px; line-height: 1.25;">Booking Invoice</div>
                            <div style="font-size: 12.5px; color: #4B5563; margin-bottom: 5px; line-height: 1.4;">
                                Invoice No: <span style="font-weight: 800; color: #111827; margin-left: 4px;">${bookingId}</span>
                            </div>
                            <div style="font-size: 12.5px; color: #4B5563; line-height: 1.4;">
                                Booking Date: <span style="font-weight: 700; color: #111827; margin-left: 4px;">${formattedBookingDate}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subtle Full-Width Divider -->
                    <div style="border-top: 1px solid #E5E7EB; margin: 14px 0 16px 0;"></div>

                    <!-- 2. Middle 3-Column Section (Exact proportions) -->
                    <div style="display: flex; gap: 16px; align-items: stretch; font-size: 12.5px;">
                        
                        <!-- Col 1: Billed To -->
                        <div style="flex: 1.05; text-align: left; padding-right: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: #4B5563; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">Billed To</span>
                            <div style="font-size: 16.5px; font-weight: 900; color: #111827; margin-bottom: 8px; line-height: 1.3;">${custName}</div>
                            <div style="display: flex; flex-direction: column; gap: 6px; color: #1F2937; font-weight: 600; font-size: 12.5px;">
                                <div style="display: flex; align-items: center;">${phoneSvg} <span>+91 ${custPhone}</span></div>
                                <div style="display: flex; align-items: center;">${waSvg} <span>+91 ${custWa}</span></div>
                                <div style="display: flex; align-items: center; font-weight: 500; color: #374151;">${mailSvg} <span style="word-break: break-all;">${custEmail}</span></div>
                                <div style="display: flex; align-items: center;">${pinSvg} <span>${city}, ${state}</span></div>
                            </div>
                        </div>

                        <!-- Vertical Divider 1 -->
                        <div style="width: 1px; background-color: #E5E7EB; flex-shrink: 0;"></div>

                        <!-- Col 2: Event Details -->
                        <div style="flex: 1.35; text-align: left; padding: 0 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: #4B5563; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Event Details</span>
                            <div style="display: flex; flex-direction: column; gap: 6px; color: #1F2937; font-size: 12.5px;">
                                <div style="display: flex; align-items: center;">
                                    ${calSvg} <strong style="color: #111827; font-weight: 900; font-size: 13px;">${scheduleDisplay}</strong>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    ${usersSvg} <span>Guest Count: <strong style="color: #111827; font-weight: 800;">${guests} Guests</strong></span>
                                </div>
                                <div style="display: flex; align-items: flex-start; line-height: 1.45; margin-top: 1px; font-size: 12px;">
                                    ${pinSvg}
                                    <span><strong style="color: #111827; font-weight: 800;">Venue:</strong> ${fullVenue}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Vertical Divider 2 -->
                        <div style="width: 1px; background-color: #E5E7EB; flex-shrink: 0;"></div>

                        <!-- Col 3: Status Badge Card (Clean standalone border & background) -->
                        <div style="flex: 1.1; display: flex; align-items: center; justify-content: flex-end; padding-left: 4px;">
                            <div style="width: 100%; border: 1.5px solid ${statusCardBorder}; background-color: #FFFFFF; border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 14px; box-sizing: border-box;">
                                <div style="flex-shrink: 0;">${statusIconSvg}</div>
                                <div style="text-align: left;">
                                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; color: ${statusSubColor};">Booking Status</span>
                                    <span style="font-weight: 900; font-size: 16.5px; color: #111827; display: block; margin-top: 2px; white-space: nowrap;">${statusLabel}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 3. Table Section (Exact matching border, headers & rows) -->
                    <div style="margin-top: 18px; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden;">
                        <!-- Header Row -->
                        <div style="background-color: #F4F4F5; padding: 10px 16px; display: flex; align-items: center; font-size: 12px; font-weight: 900; color: #111827; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 1px solid #E5E7EB;">
                            <div style="width: 36px; text-align: center;">#</div>
                            <div style="flex: 1; padding-left: 12px; text-align: left;">Service &amp; Description</div>
                            <div style="width: 180px; text-align: left;">Tier / Specification</div>
                            <div style="width: 120px; text-align: right;">Amount (INR)</div>
                        </div>

                        <!-- Main Package Row -->
                        <div style="display: flex; align-items: center; padding: 12px 16px; border-bottom: 1px solid #F3F4F6;">
                            <div style="width: 36px; text-align: center; color: #4B5563; font-weight: 800; font-size: 12.5px;">1</div>
                            <div style="flex: 1; padding-left: 12px; text-align: left;">
                                <div style="font-weight: 900; font-size: 14.5px; color: #111827; line-height: 1.3;">${pkgName}</div>
                                <div style="font-size: 12px; color: #4B5563; margin-top: 3px; font-weight: 500;">Category: ${catName}</div>
                            </div>
                            <div style="width: 180px; text-align: left; color: #1F2937; font-weight: 600; font-size: 13px;">${tierName}</div>
                            <div style="width: 120px; text-align: right; font-weight: 900; font-size: 14.5px; color: #111827;">${formatINR(basePrice)}</div>
                        </div>

                        <!-- Add-on Rows (if any) -->
                        ${addonsRowsHtml}
                    </div>

                    <!-- 4. Totals Summary Box (Right-Aligned with explicit clearance) -->
                    <div style="margin-top: 12px; padding-top: 4px; display: flex; justify-content: flex-end;">
                        <div style="width: 340px; font-size: 13px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 6px; color: #374151; font-weight: 500; line-height: 1.4;">
                                <span>Base Package Subtotal</span>
                                <span style="font-weight: 700; color: #111827;">${formatINR(basePrice)}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 6px; color: #374151; font-weight: 500; line-height: 1.4;">
                                <span>Service &amp; Setup Coordination</span>
                                <span style="font-weight: 600; color: #374151;">Included</span>
                            </div>
                            ${surcharge > 0 ? `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 6px; color: #374151; font-weight: 500; line-height: 1.4;">
                                <span>Selected Add-ons Surcharge</span>
                                <span style="font-weight: 700; color: #111827;">${formatINR(surcharge)}</span>
                            </div>` : ''}

                            <!-- Highlighted Total Amount Strip matching reference image -->
                            <div style="background-color: #FFFFFF; border: 1.5px solid #E5A93C; border-radius: 10px; padding: 10px 16px; margin-top: 6px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 900; font-size: 15px; color: #111827;">Total Amount</span>
                                <span style="font-weight: 900; font-size: 22px; color: #9A3412;">${formatINR(totalAmount)}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 5. 100% Offline Payment Banner (Pure HTML circle badge - zero SVG text metrics bug) -->
                    <div style="margin-top: 14px; background-color: #FFFFFF; border: 1.5px solid #E5A93C; border-radius: 12px; padding: 12px 18px; display: flex; align-items: flex-start; gap: 14px; box-sizing: border-box;">
                        <div style="width: 22px; height: 22px; min-width: 22px; max-width: 22px; border-radius: 50%; border: 1.5px solid #E5A93C; background-color: #FFFFFF; color: #D97706; font-family: 'Times New Roman', Georgia, serif; font-size: 14px; font-weight: bold; font-style: italic; display: flex; align-items: center; justify-content: center; flex-shrink: 0; line-height: 1; margin-top: 1px;">i</div>
                        <div style="text-align: left;">
                            <div style="font-size: 13px; font-weight: 900; color: #111827; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.3px;">
                                100% Offline Payment Model &nbsp;|&nbsp; Zero Advance Mandate
                            </div>
                            <div style="font-size: 12px; line-height: 1.5; color: #1F2937; font-weight: 500;">
                                At Artizen, we believe in complete celebration perfection before payment. No online payment is collected on this portal. Please inspect your completed venue setup and settle the total amount directly with our event manager at your venue via Cash, UPI (Google Pay, PhonePe, Paytm), or Bank Transfer.
                            </div>
                        </div>
                    </div>

                    <!-- 6. Event Day Guidelines & Protocol (Pure HTML circle badges 1, 2, 3) -->
                    <div style="margin-top: 14px; text-align: left;">
                        <div style="display: flex; align-items: center; gap: 8px; padding-bottom: 6px; border-bottom: 1px solid #E5E7EB;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#111827" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; flex-shrink: 0;"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><line x1="8" y1="11" x2="16" y2="11"></line><line x1="8" y1="15" x2="12" y2="15"></line></svg>
                            <span style="font-size: 12.5px; font-weight: 900; color: #111827; text-transform: uppercase; letter-spacing: 0.4px;">Event Day Guidelines &amp; Protocol</span>
                        </div>
                        <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 7px; font-size: 12px; color: #1F2937;">
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <div style="width: 20px; height: 20px; min-width: 20px; max-width: 20px; border-radius: 50%; border: 1.3px solid #E5A93C; background-color: #FFFFFF; color: #92400E; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; line-height: 1; margin-top: 1px;">1</div>
                                <span style="line-height: 1.5;"><strong style="color: #111827; font-weight: 800;">Punctual Crew Arrival:</strong> Our setup artists will reach the venue 60–90 minutes prior to the booked event time.</span>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <div style="width: 20px; height: 20px; min-width: 20px; max-width: 20px; border-radius: 50%; border: 1.3px solid #E5A93C; background-color: #FFFFFF; color: #92400E; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; line-height: 1; margin-top: 1px;">2</div>
                                <span style="line-height: 1.5;"><strong style="color: #111827; font-weight: 800;">Venue Access &amp; Power:</strong> Ensure standard electrical power sockets and venue access permission are ready on arrival.</span>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <div style="width: 20px; height: 20px; min-width: 20px; max-width: 20px; border-radius: 50%; border: 1.3px solid #E5A93C; background-color: #FFFFFF; color: #92400E; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; line-height: 1; margin-top: 1px;">3</div>
                                <span style="line-height: 1.5;"><strong style="color: #111827; font-weight: 800;">Dedicated Coordination:</strong> For live updates, theme customization, or timing adjustments, connect with our concierge.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 7. Footer (Clean Single-Line Official Corporate Footer - Single Contact Line, Zero Duplication) -->
                <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #4B5563; width: 100%; box-sizing: border-box;">
                    <!-- Brand Logo on Left -->
                    <div style="display: flex; align-items: center; flex-shrink: 0;">
                        <img src="${resolvedLogo}" alt="Artizen Logo" style="height: 42px; width: auto; object-fit: contain; display: block;">
                    </div>

                    <!-- Clean Single-Line Contact Details (No duplicate phone numbers, zero line-wrap) -->
                    <div style="font-size: 10.5px; color: #4B5563; font-weight: 500; white-space: nowrap; text-align: right;">
                        <span>Call &amp; WhatsApp: <strong style="color: #111827; font-weight: 700;">+91 9131668156</strong></span>
                        <span style="color: #9CA3AF; margin: 0 8px;">&bull;</span>
                        <span>support@artizenevents.com</span>
                        <span style="color: #9CA3AF; margin: 0 8px;">&bull;</span>
                        <span>Indore, MP - 452001</span>
                        <span style="color: #9CA3AF; margin: 0 8px;">&bull;</span>
                        <span style="color: #111827; font-weight: 700;">www.artizens.in</span>
                    </div>
                </div>

            </div>
            `;
        }
    };

    window.ArtizenPDF = ArtizenPDF;
})(window);
