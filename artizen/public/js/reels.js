/* ----------------------------------------------------
   ARTIZEN REELS GALLERY LOGIC
   Modular controller for dynamic filtering and video modal
   ---------------------------------------------------- */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Reel Data Source
    const reelsData = [
        {
            id: 1,
            category: 'birthday',
            badge: 'Most Viewed',
            badgeIcon: `<i class="fa-solid fa-fire text-xs" aria-hidden="true"></i>`,
            views: '52.4K',
            title: 'Birthday Celebration',
            location: 'Indore',
            duration: '45 Seconds',
            desc: 'Stunning premium neon "Better Together" garden decor and warm ambient seating setup.',
            image: 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=600&auto=format&fit=crop&ar=9:16',
            video: 'https://assets.mixkit.co/videos/preview/mixkit-party-crowd-with-neon-lights-and-dj-41712-large.mp4'
        },
        {
            id: 2,
            category: 'proposal',
            badge: 'Trending',
            badgeIcon: `<i class="fa-solid fa-arrow-trend-up text-xs" aria-hidden="true"></i>`,
            views: '38.7K',
            title: 'Romantic Proposal',
            location: 'Mumbai',
            duration: '60 Seconds',
            desc: 'Grand "MARRY ME" glowing led sign, candles pathway, and red floral archway.',
            image: 'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=600&auto=format&fit=crop&ar=9:16',
            video: 'https://assets.mixkit.co/videos/preview/mixkit-sparkler-candle-on-a-birthday-cake-34429-large.mp4'
        },
        {
            id: 3,
            category: 'wedding',
            badge: "Editor's Pick",
            badgeIcon: `<i class="fa-solid fa-award text-xs" aria-hidden="true"></i>`,
            views: '91.2K',
            title: 'Luxury Wedding Mandap',
            location: 'Delhi',
            duration: '90 Seconds',
            desc: 'Royal dome flower installation, warm chandeliers, and majestic traditional sangeet stage.',
            image: 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=600&auto=format&fit=crop&ar=9:16',
            video: 'https://assets.mixkit.co/videos/preview/mixkit-bride-and-groom-having-their-first-dance-34320-large.mp4'
        },
        {
            id: 4,
            category: 'dj-night',
            badge: 'Popular',
            badgeIcon: `<i class="fa-regular fa-heart text-xs" aria-hidden="true"></i>`,
            views: '47.1K',
            title: 'Ultimate House DJ Party',
            location: 'Pune',
            duration: '30 Seconds',
            desc: 'High-bass column audio rig, intelligent strobe lights, lasers, and club fog atmosphere.',
            image: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=600&auto=format&fit=crop&ar=9:16',
            video: 'https://assets.mixkit.co/videos/preview/mixkit-party-crowd-with-neon-lights-and-dj-41712-large.mp4'
        },
        {
            id: 5,
            category: 'corporate',
            badge: 'Recently Added',
            badgeIcon: `<i class="fa-regular fa-clock text-xs" aria-hidden="true"></i>`,
            views: '29.3K',
            title: 'Corporate Awards Night',
            location: 'Bengaluru',
            duration: '120 Seconds',
            desc: 'Premium staging platform, custom brand rollup backdrops, LED display screens, and MC anchoring.',
            image: 'https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=600&auto=format&fit=crop&ar=9:16',
            video: 'https://assets.mixkit.co/videos/preview/mixkit-crowd-at-a-concert-jumping-with-their-hands-up-41713-large.mp4'
        },
        {
            id: 6,
            category: 'house-party',
            badge: 'Premium',
            badgeIcon: `<i class="fa-solid fa-crown text-xs" aria-hidden="true"></i>`,
            views: '18.5K',
            title: 'Cozy Terrace Live Acoustic',
            location: 'Goa',
            duration: '45 Seconds',
            desc: 'Intimate seating layout with warm fairy lights and live singer acoustic set.',
            image: 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?q=80&w=600&auto=format&fit=crop&ar=9:16',
            video: 'https://assets.mixkit.co/videos/preview/mixkit-crowd-at-a-concert-jumping-with-their-hands-up-41713-large.mp4'
        },
        {
            id: 7,
            category: 'birthday',
            badge: 'Trending',
            badgeIcon: `<i class="fa-solid fa-arrow-trend-up text-xs" aria-hidden="true"></i>`,
            views: '24.1K',
            title: 'Kids Pastel Birthday Balloon',
            location: 'Jaipur',
            duration: '40 Seconds',
            desc: 'Organic balloon garland arch and cute customized cartoon backdrop setups.',
            image: 'https://images.unsplash.com/photo-1519225495810-7512c696505a?q=80&w=600&auto=format&fit=crop&ar=9:16',
            video: 'https://assets.mixkit.co/videos/preview/mixkit-sparkler-candle-on-a-birthday-cake-34429-large.mp4'
        }
    ];

    // 2. DOM Elements
    const wrapper = document.querySelector('.reels-swiper .swiper-wrapper');
    const pills = document.querySelectorAll('.reels-pill');
    const modal = document.querySelector('#reels-video-modal');
    const modalContent = modal ? modal.querySelector('.reels-modal-content') : null;
    const modalVideo = modal ? modal.querySelector('video') : null;
    const modalClose = modal ? modal.querySelector('.reels-modal-close') : null;
    const audioBtn = modal ? modal.querySelector('.reels-modal-audio-btn') : null;

    let reelsSwiper = null;

    // 3. Render Cards Helper
    function renderReels(filterCategory) {
        if (!wrapper) return;

        // Clear existing slides
        wrapper.innerHTML = '';

        // Filter data
        const filtered = filterCategory === 'all' 
            ? reelsData 
            : reelsData.filter(item => item.category === filterCategory);

        filtered.forEach(reel => {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';

            slide.innerHTML = `
                <div class="reels-card" data-video-url="${reel.video}">
                    <img data-src="${reel.image}" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 9 16'></svg>" class="lazyload-reel" alt="${reel.title}">
                    <div class="reels-card-overlay"></div>
                    
                    <div class="reel-top-badge">
                        <span>${reel.badge}</span>
                    </div>

                    <div class="reel-play-btn-wrapper">
                        <button class="reel-play-btn flex items-center justify-center" aria-label="Play Reel">
                            <i class="fa-solid fa-play text-sm ml-0.5 text-white" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="reel-details-box">
                        <div class="reel-details-title">${reel.title}</div>
                        <div class="reel-details-views">${reel.views} Views</div>
                    </div>
                </div>
            `;
            wrapper.appendChild(slide);
        });

        // Initialize Lazy Loading
        lazyLoadImages();

        // Bind Play Clicks to the new cards
        bindPlayClicks();
    }

    // 4. Lazy Loading Images
    function lazyLoadImages() {
        const images = document.querySelectorAll('.lazyload-reel');
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, self) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.getAttribute('data-src');
                        img.onload = () => img.classList.add('loaded');
                        self.unobserve(img);
                    }
                });
            });
            images.forEach(img => observer.observe(img));
        } else {
            images.forEach(img => {
                img.src = img.getAttribute('data-src');
            });
        }
    }

    // Swiper Carousel Initialization
    function initSwiperInstance() {
        if (swiperInstance) {
            swiperInstance.destroy(true, true);
        }

        swiperInstance = new Swiper('.reels-swiper', {
            slidesPerView: 'auto',
            spaceBetween: 16,
            centeredSlides: false,
            grabCursor: true,
            freeMode: false,
            navigation: {
                nextEl: '.reels-next',
                prevEl: '.reels-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: 1.25,
                    spaceBetween: 12,
                },
                480: {
                    slidesPerView: 1.8,
                    spaceBetween: 14,
                },
                768: {
                    slidesPerView: 2.5,
                    spaceBetween: 16,
                },
                1024: {
                    slidesPerView: 3.5,
                    spaceBetween: 20,
                },
                1280: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                }
            }
        });
    }

    // 6. Category Pill Events
    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            const category = pill.getAttribute('data-category');
            if (!category) return;

            // Update active styling
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');

            // Render filtered reels
            renderReels(category);

            // Re-init Swiper to calculate slides
            initSwiperInstance();
        });
    });

    // 5. Card Click -> Open Video Lightbox
    function bindPlayClicks() {
        const cards = document.querySelectorAll('.reels-card');
        cards.forEach(card => {
            card.addEventListener('click', (e) => {
                const videoUrl = card.getAttribute('data-video-url');
                if (videoUrl) {
                    openReelModal(videoUrl);
                }
            });
        });
    }

    function openReelModal(videoUrl) {
        if (!modal || !modalVideo) return;
        modalVideo.src = videoUrl;
        modalVideo.currentTime = 0;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Lock background scrolling
        modalVideo.play().catch(err => {
            console.log('Autoplay blocked, user gesture handled');
        });
    }

    function closeReelModal() {
        if (!modal || !modalVideo) return;
        modal.classList.remove('active');
        modalVideo.pause();
        modalVideo.src = '';
        document.body.style.overflow = ''; // Unlock background scrolling
    }

    // 6. Modal Close Triggers
    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', closeReelModal);
    }

    if (modal) {
        modal.addEventListener('click', (e) => {
            // Close when clicked on outside backdrop area
            if (e.target === modal) {
                closeReelModal();
            }
        });
    }

    // Keyboard ESC key close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeReelModal();
        }
    });

    // Audio Toggle
    if (audioBtn) {
        audioBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (!modalVideo) return;
            if (modalVideo.muted) {
                modalVideo.muted = false;
                audioBtn.innerHTML = `
                    <i class="fa-solid fa-volume-high text-base text-white" aria-hidden="true"></i>
                `;
            } else {
                modalVideo.muted = true;
                audioBtn.innerHTML = `
                    <i class="fa-solid fa-volume-xmark text-base text-white" aria-hidden="true"></i>
                `;
            }
        });
    }

    // 8. Bootstrap Initial Rendering
    if (wrapper) {
        renderReels('all');
        initSwiperInstance();
    }
});
