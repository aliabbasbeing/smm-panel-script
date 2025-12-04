/**
 * Visual Effects System
 * Lightweight particle effects for the entire panel
 * Supports: snow, bubbles, confetti, stars, hearts, leaves
 * 
 * This script creates a canvas overlay that renders visual effects
 * without affecting any existing UI or click events.
 */

(function() {
    'use strict';

    // Check if visual effects are enabled
    if (typeof VISUAL_EFFECTS_CONFIG === 'undefined' || !VISUAL_EFFECTS_CONFIG.enabled) {
        return;
    }

    var config = VISUAL_EFFECTS_CONFIG;
    var canvas, ctx;
    var particles = [];
    var animationId = null;

    // Size mappings
    var sizeMappings = {
        small: { min: 2, max: 5 },
        medium: { min: 4, max: 10 },
        large: { min: 8, max: 16 },
        mixed: { min: 2, max: 16 }
    };

    // Density mappings
    var densityMappings = {
        light: 25,
        medium: 50,
        heavy: 100,
        extreme: 150
    };

    // Speed mappings
    var speedMappings = {
        slow: 0.5,
        normal: 1,
        fast: 2
    };

    /**
     * Initialize the visual effects system
     */
    function init() {
        createCanvas();
        createParticles();
        animate();
        
        // Handle window resize
        window.addEventListener('resize', function() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    }

    /**
     * Create the canvas overlay
     */
    function createCanvas() {
        canvas = document.createElement('canvas');
        canvas.id = 'visual-effects-canvas';
        canvas.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9998;';
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        document.body.appendChild(canvas);
        ctx = canvas.getContext('2d');
    }

    /**
     * Create particles based on configuration
     */
    function createParticles() {
        var count = densityMappings[config.density] || 50;
        var sizeRange = sizeMappings[config.size] || sizeMappings.medium;
        
        particles = [];
        
        for (var i = 0; i < count; i++) {
            particles.push(createParticle(sizeRange));
        }
    }

    /**
     * Create a single particle
     */
    function createParticle(sizeRange) {
        var size = Math.random() * (sizeRange.max - sizeRange.min) + sizeRange.min;
        
        // Assign stable random color for confetti effect
        var confettiColors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f7dc6f', '#bb8fce', '#82e0aa'];
        var confettiColor = confettiColors[Math.floor(Math.random() * confettiColors.length)];
        
        return {
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height - canvas.height,
            size: size,
            speedY: (Math.random() * 1 + 0.5) * (speedMappings[config.speed] || 1),
            speedX: (Math.random() - 0.5) * 2,
            opacity: Math.random() * 0.5 + 0.5,
            rotation: Math.random() * 360,
            rotationSpeed: (Math.random() - 0.5) * 2,
            color: config.color || '#ffffff',
            confettiColor: confettiColor
        };
    }

    /**
     * Animation loop
     */
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        var sizeRange = sizeMappings[config.size] || sizeMappings.medium;
        
        for (var i = 0; i < particles.length; i++) {
            var p = particles[i];
            
            // Update position
            p.y += p.speedY;
            p.x += p.speedX;
            p.rotation += p.rotationSpeed;
            
            // Reset particle when it goes off screen
            if (p.y > canvas.height + p.size) {
                p.y = -p.size;
                p.x = Math.random() * canvas.width;
            }
            if (p.x > canvas.width + p.size) {
                p.x = -p.size;
            }
            if (p.x < -p.size) {
                p.x = canvas.width + p.size;
            }
            
            // Draw particle based on effect type
            drawParticle(p);
        }
        
        animationId = requestAnimationFrame(animate);
    }

    /**
     * Draw a particle based on effect type
     */
    function drawParticle(p) {
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate(p.rotation * Math.PI / 180);
        ctx.globalAlpha = p.opacity;
        
        switch (config.type) {
            case 'snow':
                drawSnowflake(p);
                break;
            case 'bubbles':
                drawBubble(p);
                break;
            case 'confetti':
                drawConfetti(p);
                break;
            case 'stars':
                drawStar(p);
                break;
            case 'hearts':
                drawHeart(p);
                break;
            case 'leaves':
                drawLeaf(p);
                break;
            default:
                drawSnowflake(p);
        }
        
        ctx.restore();
    }

    /**
     * Draw snowflake
     */
    function drawSnowflake(p) {
        ctx.beginPath();
        ctx.arc(0, 0, p.size, 0, Math.PI * 2);
        ctx.fillStyle = p.color;
        ctx.fill();
        
        // Add a subtle glow
        ctx.shadowColor = p.color;
        ctx.shadowBlur = p.size / 2;
    }

    /**
     * Draw bubble
     */
    function drawBubble(p) {
        ctx.beginPath();
        ctx.arc(0, 0, p.size, 0, Math.PI * 2);
        ctx.strokeStyle = p.color;
        ctx.lineWidth = 1;
        ctx.stroke();
        
        // Add highlight
        ctx.beginPath();
        ctx.arc(-p.size/3, -p.size/3, p.size/4, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(255,255,255,0.5)';
        ctx.fill();
    }

    /**
     * Draw confetti
     */
    function drawConfetti(p) {
        // Use the stable confetti color assigned during particle creation
        ctx.fillStyle = p.confettiColor;
        ctx.fillRect(-p.size/2, -p.size/4, p.size, p.size/2);
    }

    /**
     * Draw star
     */
    function drawStar(p) {
        var spikes = 5;
        var outerRadius = p.size;
        var innerRadius = p.size / 2;
        
        ctx.beginPath();
        for (var i = 0; i < spikes * 2; i++) {
            var radius = i % 2 === 0 ? outerRadius : innerRadius;
            var angle = (i * Math.PI) / spikes - Math.PI / 2;
            var x = Math.cos(angle) * radius;
            var y = Math.sin(angle) * radius;
            
            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.closePath();
        ctx.fillStyle = p.color;
        ctx.fill();
    }

    /**
     * Draw heart
     */
    function drawHeart(p) {
        var size = p.size;
        ctx.beginPath();
        ctx.moveTo(0, size / 4);
        ctx.bezierCurveTo(size / 2, -size / 2, size, size / 4, 0, size);
        ctx.bezierCurveTo(-size, size / 4, -size / 2, -size / 2, 0, size / 4);
        ctx.fillStyle = p.color;
        ctx.fill();
    }

    /**
     * Draw leaf
     */
    function drawLeaf(p) {
        var size = p.size;
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.bezierCurveTo(size/2, -size/2, size/2, size/2, 0, size);
        ctx.bezierCurveTo(-size/2, size/2, -size/2, -size/2, 0, -size);
        ctx.fillStyle = p.color;
        ctx.fill();
        
        // Add stem
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.lineTo(0, size);
        ctx.strokeStyle = p.color;
        ctx.lineWidth = 1;
        ctx.stroke();
    }

    /**
     * Destroy the visual effects system
     */
    function destroy() {
        if (animationId) {
            cancelAnimationFrame(animationId);
        }
        if (canvas && canvas.parentNode) {
            canvas.parentNode.removeChild(canvas);
        }
        particles = [];
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose destroy method globally
    window.destroyVisualEffects = destroy;

})();
