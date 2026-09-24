/**
 * PowerFlow Electric - Custom JavaScript
 * Enhanced interactions and animations
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize all components
    initScrollAnimations();
    initFormEnhancements();
    initNavigationEffects();
    initCounterAnimations();
    initParallaxEffects();

    /**
     * Scroll-triggered animations
     */
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');

                    // Stagger animation for multiple items
                    const siblings = entry.target.parentElement.children;

                    Array.from(siblings).forEach(function (sibling, index) {
                        if (sibling.classList.contains('feature-item')) {
                            setTimeout(function () {
                                sibling.style.opacity = '1';
                                sibling.style.transform = 'translateY(0)';
                            }, index * 100);
                        }
                    });
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document
            .querySelectorAll('.feature-item, .card, .timeline-item')
            .forEach(function (element) {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition =
                    'opacity 0.6s ease, transform 0.6s ease';

                observer.observe(element);
            });
    }

    /**
     * Form enhancements
     */
    function initFormEnhancements() {
        // Floating labels effect
        const formInputs = document.querySelectorAll(
            '.form-control, .form-select'
        );

        formInputs.forEach(function (input) {
            input.addEventListener('focus', function () {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function () {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Real-time validation feedback
        const emailInputs = document.querySelectorAll(
            'input[type="email"]'
        );

        emailInputs.forEach(function (input) {
            input.addEventListener('input', function () {
                const isValid = this.checkValidity();

                this.classList.toggle(
                    'is-valid',
                    isValid && this.value.length > 0
                );

                this.classList.toggle(
                    'is-invalid',
                    !isValid && this.value.length > 0
                );
            });
        });

        // Password strength indicator
        const passwordInputs = document.querySelectorAll(
            'input[type="password"]'
        );

        passwordInputs.forEach(function (input) {
            if (input.name === 'password') {
                input.addEventListener('input', function () {
                    const strength = calculatePasswordStrength(
                        this.value
                    );

                    updatePasswordStrengthIndicator(
                        this,
                        strength
                    );
                });
            }
        });
    }

    /**
     * Navigation effects
     */
    function initNavigationEffects() {
        const navbar = document.querySelector('.navbar');

        // Stop if navbar does not exist
        if (!navbar) {
            return;
        }

        let lastScrollTop = 0;

        window.addEventListener('scroll', function () {
            const scrollTop =
                window.pageYOffset ||
                document.documentElement.scrollTop;

            // Add/remove scrolled class
            if (scrollTop > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Hide/show navbar on scroll
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }

            lastScrollTop = scrollTop;
        });

        // Smooth scroll for anchor links
        document
            .querySelectorAll('a[href^="#"]')
            .forEach(function (anchor) {
                anchor.addEventListener('click', function (event) {
                    const targetSelector =
                        this.getAttribute('href');

                    // Ignore empty "#" links
                    if (
                        !targetSelector ||
                        targetSelector === '#'
                    ) {
                        return;
                    }

                    const target =
                        document.querySelector(targetSelector);

                    if (target) {
                        event.preventDefault();

                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
    }

    /**
     * Counter animations for statistics
     */
    function initCounterAnimations() {
        const counters =
            document.querySelectorAll('.stat-item h2');

        if (!counters.length) {
            return;
        }

        const counterObserver = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        const counter = entry.target;

                        const target = parseInt(
                            counter.textContent.replace(/\D/g, ''),
                            10
                        );

                        const suffix =
                            counter.textContent.replace(/\d/g, '');

                        animateCounter(
                            counter,
                            0,
                            target,
                            suffix,
                            2000
                        );

                        counterObserver.unobserve(counter);
                    }
                });
            }
        );

        counters.forEach(function (counter) {
            counterObserver.observe(counter);
        });
    }

    /**
     * Parallax effects
     */
    function initParallaxEffects() {
        const parallaxElements =
            document.querySelectorAll('.hero-section');

        if (!parallaxElements.length) {
            return;
        }

        window.addEventListener('scroll', function () {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;

            parallaxElements.forEach(function (element) {
                element.style.transform =
                    `translateY(${rate}px)`;
            });
        });
    }

    /**
     * Calculate password strength
     */
    function calculatePasswordStrength(password) {
        let strength = 0;

        if (password.length >= 8) {
            strength++;
        }

        if (/[a-z]/.test(password)) {
            strength++;
        }

        if (/[A-Z]/.test(password)) {
            strength++;
        }

        if (/[0-9]/.test(password)) {
            strength++;
        }

        if (/[^A-Za-z0-9]/.test(password)) {
            strength++;
        }

        return strength;
    }

    /**
     * Update password strength indicator
     */
    function updatePasswordStrengthIndicator(input, strength) {
        let indicator =
            input.parentElement.querySelector(
                '.password-strength'
            );

        if (!indicator) {
            indicator = document.createElement('div');
            indicator.className = 'password-strength mt-1';

            input.parentElement.appendChild(indicator);
        }

        const strengthLevels = [
            'Very Weak',
            'Weak',
            'Fair',
            'Good',
            'Strong'
        ];

        const strengthColors = [
            '#dc3545',
            '#fd7e14',
            '#ffc107',
            '#20c997',
            '#28a745'
        ];

        if (input.value.length === 0) {
            indicator.style.display = 'none';
            return;
        }

        indicator.style.display = 'block';

        const percentage = (strength / 5) * 100;
        const color =
            strengthColors[strength - 1] ||
            strengthColors[0];

        const level =
            strengthLevels[strength - 1] ||
            strengthLevels[0];

        indicator.innerHTML = `
            <div class="progress" style="height: 4px;">
                <div
                    class="progress-bar"
                    style="
                        width: ${percentage}%;
                        background-color: ${color};
                    "
                ></div>
            </div>

            <small class="text-muted">
                Password strength: ${level}
            </small>
        `;
    }

    /**
     * Animate statistics counter
     */
    function animateCounter(
        element,
        start,
        end,
        suffix,
        duration
    ) {
        const startTime = performance.now();

        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;

            const progress = Math.min(
                elapsed / duration,
                1
            );

            const current = Math.floor(
                start +
                (end - start) *
                    easeOutQuart(progress)
            );

            element.textContent = current + suffix;

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }

        requestAnimationFrame(updateCounter);
    }

    /**
     * Easing function
     */
    function easeOutQuart(t) {
        return 1 - (--t) * t * t * t;
    }

    /**
     * Loading states for forms
     */
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitBtn =
                this.querySelector(
                    'button[type="submit"]'
                );

            if (submitBtn && !submitBtn.disabled) {
                const originalText =
                    submitBtn.innerHTML;

                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                submitBtn.disabled = true;

                // Re-enable after 5 seconds as fallback
                setTimeout(function () {
                    submitBtn.innerHTML =
                        originalText;

                    submitBtn.disabled = false;
                }, 5000);
            }
        });
    });

    /**
     * Enhanced Bootstrap tooltips
     */
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll(
            '[data-bs-toggle="tooltip"]'
        )
    );

    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(
            tooltipTriggerEl
        );
    });

    /**
     * Back to top button
     */
    const backToTopBtn =
        document.createElement('button');

    backToTopBtn.innerHTML =
        '<i class="fas fa-chevron-up"></i>';

    backToTopBtn.className =
        'btn btn-primary position-fixed';

    backToTopBtn.setAttribute(
        'type',
        'button'
    );

    backToTopBtn.setAttribute(
        'aria-label',
        'Back to top'
    );

    backToTopBtn.style.cssText = `
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    `;

    document.body.appendChild(backToTopBtn);

    window.addEventListener('scroll', function () {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });

    backToTopBtn.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    /**
     * Console welcome message
     */
    console.log(
        '%cPowerFlow Electric',
        'color: #1e40af; font-size: 24px; font-weight: bold;'
    );

    console.log(
        '%cWebsite powered by CodeIgniter 4',
        'color: #f59e0b; font-size: 14px;'
    );
});

/**
 * Service Worker registration
 * Reserved for future PWA functionality.
 *
 * Electric Company CMV
 * Prepared by: John Benedic R. Enriquez
 */

if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        // Uncomment when sw.js is available.
        // navigator.serviceWorker.register('/sw.js');
    });
}
