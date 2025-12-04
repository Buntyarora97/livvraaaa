// ==========================================
// LIVVRA - Main JavaScript
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initPreloader();
    initHeader();
    initMobileMenu();
    initScrollAnimations();
    initProductCards();
    initQuantityControls();
    initBackToTop();
    initSearchBox();
    initParallax();
    init3DEffects();
    initHeroCarousel();
});

// Preloader
function initPreloader() {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        // Hide preloader after a short delay or when page loads
        setTimeout(function() {
            preloader.classList.add('hidden');
        }, 1500);
        
        // Also hide on window load as fallback
        window.addEventListener('load', function() {
            preloader.classList.add('hidden');
        });
    }
}

// Header Scroll Effect
function initHeader() {
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }
}

// Mobile Menu
function initMobileMenu() {
    const menuBtn = document.getElementById('mobileMenuBtn');
    const navMenu = document.getElementById('navMenu');
    
    if (menuBtn && navMenu) {
        menuBtn.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            menuBtn.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !menuBtn.contains(e.target)) {
                navMenu.classList.remove('active');
                menuBtn.classList.remove('active');
            }
        });
    }
}

// Scroll Reveal Animations
function initScrollAnimations() {
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
    
    const revealOnScroll = function() {
        revealElements.forEach(function(element) {
            const windowHeight = window.innerHeight;
            const elementTop = element.getBoundingClientRect().top;
            const revealPoint = 150;
            
            if (elementTop < windowHeight - revealPoint) {
                element.classList.add('active');
            }
        });
    };
    
    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); // Check on load
}

// Product Cards Hover Effects
function initProductCards() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Quantity Controls
function initQuantityControls() {
    const quantityControls = document.querySelectorAll('.quantity-controls');
    
    quantityControls.forEach(function(control) {
        const minusBtn = control.querySelector('.minus-btn');
        const plusBtn = control.querySelector('.plus-btn');
        const input = control.querySelector('input');
        
        if (minusBtn && plusBtn && input) {
            minusBtn.addEventListener('click', function() {
                let value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                    updateCartQuantity(input);
                }
            });
            
            plusBtn.addEventListener('click', function() {
                let value = parseInt(input.value);
                input.value = value + 1;
                updateCartQuantity(input);
            });
            
            input.addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
                updateCartQuantity(this);
            });
        }
    });
}

// Update Cart Quantity
function updateCartQuantity(input) {
    const productId = input.dataset.productId;
    const quantity = input.value;
    
    if (productId) {
        fetch('ajax/update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartDisplay(data);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Update Cart Display
function updateCartDisplay(data) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount && data.cartCount !== undefined) {
        cartCount.textContent = data.cartCount;
    }
    
    // Update totals if on cart page
    const subtotalEl = document.querySelector('.cart-subtotal');
    const totalEl = document.querySelector('.cart-total');
    
    if (subtotalEl && data.subtotal !== undefined) {
        subtotalEl.textContent = '₹' + data.subtotal;
    }
    
    if (totalEl && data.total !== undefined) {
        totalEl.textContent = '₹' + data.total;
    }
}

// Back to Top Button
function initBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 500) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Search Box
function initSearchBox() {
    const searchForm = document.querySelector('.search-box');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            const query = searchInput.value.trim();
            
            if (!query) {
                e.preventDefault();
                return false;
            }
        });
    }
}

// Parallax Effect
function initParallax() {
    const parallaxElements = document.querySelectorAll('[data-parallax]');
    
    window.addEventListener('scroll', function() {
        parallaxElements.forEach(function(element) {
            const speed = element.dataset.parallax || 0.5;
            const yPos = -(window.scrollY * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    });
}

// 3D Card Effects
function init3DEffects() {
    const cards = document.querySelectorAll('.card-3d');
    
    cards.forEach(function(card) {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
        });
    });
}

// Add to Cart Function
function addToCart(productId) {
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            updateCartCount(data.cart_count);
            showNotification(data.message || 'Product added to cart!', 'success');
        } else {
            showNotification(data.message || 'Error adding product to cart', 'error');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showNotification('Error adding product to cart', 'error');
    });
}

// Update Cart Count Display
function updateCartCount(count) {
    var cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.transform = 'scale(1.5)';
        setTimeout(function() {
            cartCount.style.transform = 'scale(1)';
        }, 300);
    }
}

// Remove from Cart
function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch('ajax/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Show Notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} notification-slide`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideInRight 0.5s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(function() {
        notification.style.animation = 'fadeOut 0.5s ease forwards';
        setTimeout(function() {
            notification.remove();
        }, 500);
    }, 3000);
}

// Counter Animation
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = Math.ceil(start);
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target;
        }
    }
    
    updateCounter();
}

// Initialize counters on scroll
function initCounters() {
    const counters = document.querySelectorAll('.counter');
    let triggered = false;
    
    window.addEventListener('scroll', function() {
        if (!triggered) {
            counters.forEach(function(counter) {
                const rect = counter.getBoundingClientRect();
                if (rect.top < window.innerHeight) {
                    const target = parseInt(counter.dataset.target);
                    animateCounter(counter, target);
                    triggered = true;
                }
            });
        }
    });
}

// Image Lazy Loading
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(function(img) {
        imageObserver.observe(img);
    });
}

// Form Validation
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(function(input) {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
        
        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                input.classList.add('error');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

// Smooth Scroll for Anchor Links
document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
    anchor.addEventListener('click', function(e) {
        var href = this.getAttribute('href');
        if (href && href !== '#' && href.length > 1) {
            e.preventDefault();
            var target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// Scroll Progress Bar
function initScrollProgress() {
    const progressBar = document.createElement('div');
    progressBar.className = 'scroll-progress';
    document.body.appendChild(progressBar);
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;
        progressBar.style.width = scrollPercent + '%';
    });
}

// Initialize scroll progress
initScrollProgress();

// Hero Carousel
function initHeroCarousel() {
    const slides = [
        {
            badge: '<i class="fas fa-leaf"></i> 100% Natural & Ayurvedic',
            title: 'Pure Herbal Products<br><span>Live Better, Live Strong</span>',
            subtitle: 'Experience the power of Ayurveda with LIVVRA\'s premium range of natural health supplements and wellness products by Dr Tridosha Herbotech.',
            price: '₹299',
            discount: 'Get 20% OFF on your first order | Use Code: <strong>LIVVRA20</strong>',
            image: 'assets/images/products/hero-product.jpg'
        },
        {
            badge: '<i class="fas fa-spa"></i> Ayurvedic Excellence',
            title: 'Premium Wellness<br><span>Natural & Effective</span>',
            subtitle: 'Discover the ancient wisdom of Ayurveda combined with modern science for optimal health and vitality.',
            price: '₹399',
            discount: 'Special Offer: <strong>Buy 2 Get 1 FREE</strong>',
            image: 'assets/images/products/ashwagandha.jpg'
        },
        {
            badge: '<i class="fas fa-heart"></i> Health First',
            title: 'Boost Immunity<br><span>Stay Strong & Healthy</span>',
            subtitle: 'Strengthen your immune system naturally with our scientifically formulated herbal supplements.',
            price: '₹349',
            discount: 'Limited Time: <strong>Free Shipping</strong> on all orders',
            image: 'assets/images/products/immunity-booster.jpg'
        },
        {
            badge: '<i class="fas fa-bolt"></i> Energy Boost',
            title: 'Power Up Daily<br><span>Natural Energy</span>',
            subtitle: 'Get sustained energy throughout the day with our premium Shilajit and herbal energy formulations.',
            price: '₹449',
            discount: 'New Launch: <strong>15% OFF</strong> this week only',
            image: 'assets/images/products/shilajit-gold.jpg'
        }
    ];

    let currentSlide = 0;
    const heroContent = document.querySelector('.hero-content');
    const heroImage = document.querySelector('.hero-product-img img');
    const dots = document.querySelectorAll('.hero-slider-dots .dot');

    if (!heroContent || !heroImage || !dots.length) return;

    function updateSlide(index) {
        const slide = slides[index];
        
        // Add fade out effect
        heroContent.style.opacity = '0';
        heroImage.style.opacity = '0';
        
        setTimeout(() => {
            // Update content
            document.querySelector('.hero-badge').innerHTML = slide.badge;
            document.querySelector('.hero-title').innerHTML = slide.title;
            document.querySelector('.hero-subtitle').textContent = slide.subtitle;
            document.querySelector('.price-tag').innerHTML = '<span>Starting from</span> ' + slide.price;
            document.querySelector('.hero-discount').innerHTML = slide.discount;
            heroImage.src = slide.image;
            
            // Update dots
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
            
            // Fade in
            heroContent.style.opacity = '1';
            heroImage.style.opacity = '1';
        }, 300);
    }

    // Auto slide
    setInterval(() => {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlide(currentSlide);
    }, 5000);

    // Manual slide control
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            updateSlide(currentSlide);
        });
    });
}
