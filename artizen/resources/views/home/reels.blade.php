

<section id="reels" class="py-24 relative scroll-mt-20 md:scroll-mt-24">
        <!-- Subtle gold glow backing -->
        <div class="reels-glow-bg"></div>

        <div class="reels-container w-full px-4 md:px-12">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span
                    class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-bold text-gold uppercase tracking-wider mb-4">
                    MOMENTS THAT MATTER
                </span>
                <h2
                    class="font-heading font-extrabold text-3xl md:text-5xl lg:text-6xl tracking-tight text-black mb-4 dark:text-white uppercase">
                    Reels That Bring <span class="reels-gradient-text">Celebrations</span> to Life
                </h2>
                <p class="font-body text-gray-500 text-sm md:text-base max-w-xl mx-auto tracking-normal">
                    Real events. Real vibes. Real celebrations by Artizen.
                </p>
            </div>

            <!-- Category Pills Navigation -->
            <div class="reels-pills-nav">
                <button class="reels-pill active" data-category="all">
                    <i class="fa-solid fa-play text-xs" aria-hidden="true"></i>
                    <span>All Reels</span>
                </button>
                <button class="reels-pill" data-category="birthday">
                    <i class="fa-solid fa-cake-candles text-xs" aria-hidden="true"></i>
                    <span>Birthday</span>
                </button>
                <button class="reels-pill" data-category="proposal">
                    <i class="fa-regular fa-heart text-xs" aria-hidden="true"></i>
                    <span>Proposal</span>
                </button>
                <button class="reels-pill" data-category="wedding">
                    <i class="fa-solid fa-ring text-xs" aria-hidden="true"></i>
                    <span>Wedding</span>
                </button>
                <button class="reels-pill" data-category="house-party">
                    <i class="fa-solid fa-champagne-glasses text-xs" aria-hidden="true"></i>
                    <span>House Party</span>
                </button>
                <button class="reels-pill" data-category="dj-night">
                    <i class="fa-solid fa-music text-xs" aria-hidden="true"></i>
                    <span>DJ Night</span>
                </button>
                <button class="reels-pill" data-category="corporate">
                    <i class="fa-solid fa-briefcase text-xs" aria-hidden="true"></i>
                    <span>Corporate</span>
                </button>
                <button class="reels-pill" data-category="more">
                    <i class="fa-solid fa-ellipsis text-xs" aria-hidden="true"></i>
                    <span>More</span>
                </button>
            </div>

            <!-- Reels Slider Container -->
            <div class="reels-swiper-container">
                <div class="reels-swiper swiper">
                    <div class="swiper-wrapper">
                        <!-- Swiper slides injected dynamically from js/reels.js -->
                    </div>
                </div>
                <!-- Controls -->
                <div class="reels-prev flex items-center justify-center" aria-label="Previous Slide">
                    <i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i>
                </div>
                <div class="reels-next flex items-center justify-center" aria-label="Next Slide">
                    <i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i>
                </div>
            </div>

            <!-- Instagram Community Strip Section -->
            <div class="instagram-community-strip">
                <div class="insta-strip-left">
                    <div class="insta-logo-box flex items-center justify-center" aria-hidden="true">
                        <i class="fa-brands fa-instagram text-2xl text-white" aria-hidden="true"></i>
                    </div>
                    <div class="insta-strip-text">
                        <h4>Follow us on Instagram</h4>
                        <p>@vibeify.events</p>
                        <span class="desc">Daily doses of celebration inspiration ✨</span>
                    </div>
                </div>

                <!-- Avatar stack preview -->
                <div class="insta-avatar-stack" aria-label="Recent reels screenshots">
                    <img class="insta-avatar"
                        src="https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=150&auto=format&fit=crop"
                        alt="Thumbnail 1">
                    <img class="insta-avatar"
                        src="https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=150&auto=format&fit=crop"
                        alt="Thumbnail 2">
                    <img class="insta-avatar"
                        src="https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=150&auto=format&fit=crop"
                        alt="Thumbnail 3">
                    <img class="insta-avatar"
                        src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=150&auto=format&fit=crop"
                        alt="Thumbnail 4">
                    <img class="insta-avatar"
                        src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=150&auto=format&fit=crop"
                        alt="Thumbnail 5">
                    <div class="insta-avatar-more">+12</div>
                </div>

                <!-- Right button CTA -->
                <a href="https://www.instagram.com/vibifyeventsavenues?igsh=MThleThwMXpycmhoeg%3D%3D" target="_blank"
                    rel="noopener noreferrer" class="insta-follow-btn flex items-center gap-2">
                    <span>Follow & Get Inspired</span>
                    <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                </a>
            </div>

            <!-- Bottom CTA Text Link -->
            <div class="insta-bottom-cta">
                <a href="https://www.instagram.com/vibifyeventsavenues?igsh=MThleThwMXpycmhoeg%3D%3D" target="_blank"
                    rel="noopener noreferrer" class="insta-bottom-cta-link flex items-center justify-center gap-1.5">
                    <i class="fa-brands fa-instagram text-base mr-1 inline text-gold" aria-hidden="true"></i>
                    <span>Watch More Reels on Instagram</span>
                </a>
            </div>

        </div>
    </section>

    <!-- Glassmorphic Video Playback Modal -->
    <div id="reels-video-modal" class="reels-video-modal" role="dialog" aria-modal="true"
        aria-label="Reel Video Player">
        <button class="reels-modal-close flex items-center justify-center" aria-label="Close Video">
            <i class="fa-solid fa-xmark text-lg text-white" aria-hidden="true"></i>
        </button>
        <div class="reels-modal-content">
            <video playsinline loop controls muted></video>
            <!-- Floating audio state button overlay inside player -->
            <button class="reels-modal-audio-btn flex items-center justify-center" aria-label="Toggle Sound">
                <!-- Starts as muted icon -->
                <i class="fa-solid fa-volume-xmark text-base text-white" aria-hidden="true"></i>
            </button>
        </div>
    </div>




    <!-- Speed Advertisement Banner Section -->
    