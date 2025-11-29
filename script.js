// Mobile Menu
const navToggle = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');

if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });

    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
        });
    });
}

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Scroll Animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

document.querySelectorAll('.project-card, .trabajo-item, .about-grid, .contact-wrapper').forEach(el => {
    el.classList.add('fade-in');
    observer.observe(el);
});

// Trabajos Tabs Functionality
const tabButtons = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        const targetTab = button.getAttribute('data-tab');
        
        // Remove active class from all buttons and contents
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked button and corresponding content
        button.classList.add('active');
        document.getElementById(targetTab).classList.add('active');
        
        // 3D animation effect
        button.style.transform = 'perspective(1000px) rotateX(10deg) scale(0.95)';
        setTimeout(() => {
            button.style.transform = '';
        }, 200);
    });
});

// Navbar scroll effect - OPTIMIZED
const navbar = document.querySelector('.navbar');
let navbarTicking = false;

function updateNavbar() {
    if (window.scrollY > 100) {
        navbar.style.background = 'rgba(15, 23, 42, 0.95)';
        navbar.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.3)';
    } else {
        navbar.style.background = 'rgba(15, 23, 42, 0.8)';
        navbar.style.boxShadow = 'none';
    }
    navbarTicking = false;
}

if (navbar) {
    window.addEventListener('scroll', () => {
        if (!navbarTicking) {
            requestAnimationFrame(updateNavbar);
            navbarTicking = true;
        }
    }, { passive: true });
}

// Form Submission
const contactForm = document.querySelector('.contact-form');
if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const button = contactForm.querySelector('button');
        const originalText = button.textContent;
        button.textContent = 'Enviado ✓';
        button.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        
        setTimeout(() => {
            button.textContent = originalText;
            button.style.background = '';
            contactForm.reset();
        }, 2000);
    });
}

// Parallax effect - OPTIMIZED with throttle
let scrollTicking = false;

function updateParallax() {
    const scrolled = window.pageYOffset;
    const shapes = document.querySelectorAll('.shape');
    
    shapes.forEach((shape, index) => {
        const speed = (index + 1) * 0.3; // Reduced speed
        shape.style.transform = `translate3d(0, ${scrolled * speed}px, 0)`; // Use translate3d for GPU
    });
    
    scrollTicking = false;
}

window.addEventListener('scroll', () => {
    if (!scrollTicking) {
        requestAnimationFrame(updateParallax);
        scrollTicking = true;
    }
}, { passive: true });

// 3D Mouse Tracking - OPTIMIZED with throttle
let mouseX = 0;
let mouseY = 0;
let ticking = false;

function updateMouseEffects() {
    // Removed 3D parallax on hero title - using clean JC de Las Lomas style
    
    ticking = false;
}

document.addEventListener('mousemove', (e) => {
    mouseX = (e.clientX / window.innerWidth) * 100;
    mouseY = (e.clientY / window.innerHeight) * 100;
    
    if (!ticking) {
        requestAnimationFrame(updateMouseEffects);
        ticking = true;
    }
    
    // 3D effect on project cards - only on hover
    document.querySelectorAll('.project-card').forEach(card => {
        if (card.matches(':hover')) {
            const rect = card.getBoundingClientRect();
            const cardX = e.clientX - rect.left;
            const cardY = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((cardY - centerY) / centerY) * 10; // Reduced
            const rotateY = ((centerX - cardX) / centerX) * 10;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`;
        }
    });
});

// Add 3D particles on click - OPTIMIZED (reduced particles)
let particleCount = 0;
const MAX_PARTICLES = 50; // Limit total particles

document.addEventListener('click', (e) => {
    if (particleCount < MAX_PARTICLES) {
        const particles = 5; // Reduced from 30 to 5
        for (let i = 0; i < particles; i++) {
            create3DParticle(e.clientX, e.clientY);
            particleCount++;
        }
    }
});

function create3DParticle(x, y) {
    const particle = document.createElement('div');
    particle.style.position = 'fixed';
    particle.style.left = x + 'px';
    particle.style.top = y + 'px';
    const size = 5 + Math.random() * 8;
    particle.style.width = size + 'px';
    particle.style.height = size + 'px';
    const colors = ['#2563eb', '#059669', '#0891b2', '#3b82f6'];
    const color = colors[Math.floor(Math.random() * colors.length)];
    particle.style.background = color;
    particle.style.borderRadius = '50%';
    particle.style.pointerEvents = 'none';
    particle.style.zIndex = '9999';
    particle.style.boxShadow = `0 0 25px ${color}, 0 0 50px ${color}`;
    particle.style.transformStyle = 'preserve-3d';
    document.body.appendChild(particle);

    const angle = (Math.PI * 2 * Math.random());
    const velocity = 4 + Math.random() * 5;
    const vx = Math.cos(angle) * velocity;
    const vy = Math.sin(angle) * velocity;
    const vz = (Math.random() - 0.5) * 8;

    let posX = x;
    let posY = y;
    let posZ = 0;
    let opacity = 1;
    let rotationX = Math.random() * 360;
    let rotationY = Math.random() * 360;
    let rotationZ = Math.random() * 360;

    function animate() {
        posX += vx;
        posY += vy;
        posZ += vz;
        opacity -= 0.012;
        rotationX += 8;
        rotationY += 8;
        rotationZ += 8;

        const scale = 1 + (posZ / 150);
        const blur = Math.abs(posZ) * 0.3;

        particle.style.left = posX + 'px';
        particle.style.top = posY + 'px';
        particle.style.opacity = opacity;
        particle.style.transform = `perspective(2000px) translateZ(${posZ}px) rotateX(${rotationX}deg) rotateY(${rotationY}deg) rotateZ(${rotationZ}deg) scale(${scale})`;
        particle.style.filter = `blur(${blur}px)`;

        if (opacity > 0 && Math.abs(posZ) < 600) {
            requestAnimationFrame(animate);
        } else {
            particle.remove();
            particleCount = Math.max(0, particleCount - 1);
        }
    }

    animate();
}

// Simple hover effect on stat boxes - JC de Las Lomas style
// Removed complex 3D tilt effects for cleaner style

// Simple button hover - handled by CSS only (JC de Las Lomas style)

// Simple skill hover - handled by CSS only (JC de Las Lomas style)

// Initialize AOS
document.addEventListener('DOMContentLoaded', () => {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-out-cubic',
            offset: 100
        });
    }
    
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 100);
    
    // Add entrance animation to cards
    document.querySelectorAll('.project-card').forEach((card, index) => {
        setTimeout(() => {
            card.style.animation = `card-entrance 0.8s ease-out forwards`;
        }, index * 100);
    });
});

// Add card entrance animation
const style = document.createElement('style');
style.textContent = `
    @keyframes card-entrance {
        0% {
            opacity: 0;
            transform: perspective(1000px) rotateX(90deg) translateY(100px);
        }
        100% {
            opacity: 1;
            transform: perspective(1000px) rotateX(0deg) translateY(0);
        }
    }
`;
document.head.appendChild(style);
