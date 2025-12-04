/**
 * Visual Effects System - Enhanced Version
 * Lightweight particle effects for the entire panel
 * Supports: snow, bubbles, confetti, stars, hearts, leaves, rain, fireflies, sakura, diamonds, ribbons, sparkles
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
    var time = 0;

    // Size mappings - Enhanced with more options
    var sizeMappings = {
        tiny: { min: 1, max: 3 },
        small: { min: 2, max: 6 },
        medium: { min: 5, max: 12 },
        large: { min: 10, max: 20 },
        xlarge: { min: 15, max: 30 },
        mixed: { min: 2, max: 25 }
    };

    // Density mappings - Enhanced
    var densityMappings = {
        minimal: 15,
        light: 30,
        medium: 60,
        heavy: 100,
        extreme: 150,
        insane: 200
    };

    // Speed mappings - Enhanced
    var speedMappings = {
        very_slow: 0.3,
        slow: 0.5,
        normal: 1,
        fast: 1.5,
        very_fast: 2.5
    };

    // Color palettes for various effects
    var colorPalettes = {
        confetti: ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f7dc6f', '#bb8fce', '#82e0aa', '#ff9ff3', '#54a0ff', '#5f27cd', '#00d2d3'],
        sakura: ['#ffb7c5', '#ffc0cb', '#ff69b4', '#ffb6c1', '#ffa07a'],
        firefly: ['#ffff00', '#ffd700', '#ff8c00', '#adff2f', '#7fff00'],
        rainbow: ['#ff0000', '#ff7f00', '#ffff00', '#00ff00', '#0000ff', '#4b0082', '#9400d3'],
        autumn: ['#d35400', '#e74c3c', '#f39c12', '#c0392b', '#e67e22'],
        ocean: ['#3498db', '#2980b9', '#1abc9c', '#16a085', '#00cec9']
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
        var count = densityMappings[config.density] || 60;
        var sizeRange = sizeMappings[config.size] || sizeMappings.medium;
        
        particles = [];
        
        for (var i = 0; i < count; i++) {
            particles.push(createParticle(sizeRange, i));
        }
    }

    /**
     * Create a single particle with enhanced properties
     */
    function createParticle(sizeRange, index) {
        var size = Math.random() * (sizeRange.max - sizeRange.min) + sizeRange.min;
        var speedMultiplier = speedMappings[config.speed] || 1;
        
        // Select palette based on effect type
        var palette = colorPalettes.confetti;
        if (config.type === 'sakura') palette = colorPalettes.sakura;
        else if (config.type === 'fireflies') palette = colorPalettes.firefly;
        else if (config.type === 'leaves') palette = colorPalettes.autumn;
        else if (config.type === 'rain') palette = colorPalettes.ocean;
        
        var randomColor = palette[Math.floor(Math.random() * palette.length)];
        
        return {
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height - canvas.height,
            size: size,
            baseSize: size,
            speedY: (Math.random() * 1.5 + 0.5) * speedMultiplier,
            speedX: (Math.random() - 0.5) * 2 * speedMultiplier,
            opacity: Math.random() * 0.6 + 0.4,
            baseOpacity: Math.random() * 0.6 + 0.4,
            rotation: Math.random() * 360,
            rotationSpeed: (Math.random() - 0.5) * 3,
            color: config.color || '#ffffff',
            randomColor: randomColor,
            // Enhanced properties
            wobble: Math.random() * Math.PI * 2,
            wobbleSpeed: Math.random() * 0.1 + 0.02,
            wobbleAmount: Math.random() * 3 + 1,
            pulse: Math.random() * Math.PI * 2,
            pulseSpeed: Math.random() * 0.05 + 0.01,
            index: index,
            // Shape variations
            shapeVariant: Math.floor(Math.random() * 3),
            spikes: Math.floor(Math.random() * 3) + 4, // 4-6 spikes for stars
            ribbonWave: Math.random() * Math.PI * 2
        };
    }

    /**
     * Animation loop with time tracking
     */
    function animate() {
        time += 0.016; // Approximate 60fps
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        var sizeRange = sizeMappings[config.size] || sizeMappings.medium;
        
        for (var i = 0; i < particles.length; i++) {
            var p = particles[i];
            
            // Update wobble for organic movement
            p.wobble += p.wobbleSpeed;
            p.pulse += p.pulseSpeed;
            
            // Type-specific movement
            updateParticle(p);
            
            // Reset particle when it goes off screen
            if (p.y > canvas.height + p.size * 2) {
                resetParticle(p, sizeRange);
            }
            if (p.x > canvas.width + p.size * 2) {
                p.x = -p.size * 2;
            }
            if (p.x < -p.size * 2) {
                p.x = canvas.width + p.size * 2;
            }
            
            // Draw particle based on effect type
            drawParticle(p);
        }
        
        animationId = requestAnimationFrame(animate);
    }

    /**
     * Update particle position based on effect type
     */
    function updateParticle(p) {
        var wobbleX = Math.sin(p.wobble) * p.wobbleAmount;
        
        switch (config.type) {
            case 'fireflies':
                // Erratic floating movement
                p.x += Math.sin(p.wobble * 2) * 2 + p.speedX * 0.3;
                p.y += Math.cos(p.wobble * 1.5) * 1.5;
                p.opacity = p.baseOpacity * (0.5 + Math.sin(p.pulse * 3) * 0.5);
                break;
            case 'rain':
                // Fast straight down with slight angle
                p.y += p.speedY * 3;
                p.x += p.speedX * 0.2;
                break;
            case 'sakura':
            case 'leaves':
                // Gentle floating with swaying
                p.y += p.speedY * 0.7;
                p.x += wobbleX + p.speedX * 0.5;
                p.rotation += p.rotationSpeed * 0.5;
                break;
            case 'bubbles':
                // Float upward
                p.y -= p.speedY * 0.5;
                p.x += wobbleX * 0.5;
                if (p.y < -p.size * 2) {
                    p.y = canvas.height + p.size;
                }
                break;
            case 'sparkles':
                // Twinkle in place with slight drift
                p.x += p.speedX * 0.2;
                p.y += p.speedY * 0.3;
                p.size = p.baseSize * (0.5 + Math.sin(p.pulse * 4) * 0.5);
                p.opacity = p.baseOpacity * (0.3 + Math.sin(p.pulse * 5) * 0.7);
                break;
            case 'ribbons':
                // Flowing wave motion
                p.y += p.speedY;
                p.x += Math.sin(p.ribbonWave + time * 2) * 3;
                p.ribbonWave += 0.05;
                break;
            default:
                // Standard falling with wobble
                p.y += p.speedY;
                p.x += wobbleX * 0.3 + p.speedX * 0.3;
                p.rotation += p.rotationSpeed;
        }
    }

    /**
     * Reset particle to top of screen
     */
    function resetParticle(p, sizeRange) {
        p.y = -p.size * 2;
        p.x = Math.random() * canvas.width;
        p.size = Math.random() * (sizeRange.max - sizeRange.min) + sizeRange.min;
        p.baseSize = p.size;
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
            case 'rain':
                drawRain(p);
                break;
            case 'fireflies':
                drawFirefly(p);
                break;
            case 'sakura':
                drawSakura(p);
                break;
            case 'diamonds':
                drawDiamond(p);
                break;
            case 'ribbons':
                drawRibbon(p);
                break;
            case 'sparkles':
                drawSparkle(p);
                break;
            default:
                drawSnowflake(p);
        }
        
        ctx.restore();
    }

    /**
     * Draw enhanced snowflake with crystal pattern
     */
    function drawSnowflake(p) {
        var size = p.size;
        ctx.fillStyle = p.color;
        ctx.strokeStyle = p.color;
        ctx.lineWidth = size / 8;
        
        // Draw crystal snowflake pattern
        if (p.shapeVariant === 0) {
            // Simple circle with glow
            ctx.beginPath();
            ctx.arc(0, 0, size, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowColor = p.color;
            ctx.shadowBlur = size;
        } else {
            // Crystal pattern
            for (var i = 0; i < 6; i++) {
                ctx.save();
                ctx.rotate((i * Math.PI) / 3);
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.lineTo(0, -size);
                ctx.stroke();
                // Add branches
                ctx.beginPath();
                ctx.moveTo(0, -size * 0.5);
                ctx.lineTo(size * 0.3, -size * 0.7);
                ctx.moveTo(0, -size * 0.5);
                ctx.lineTo(-size * 0.3, -size * 0.7);
                ctx.stroke();
                ctx.restore();
            }
        }
    }

    /**
     * Draw enhanced bubble with shine and gradient
     */
    function drawBubble(p) {
        var size = p.size;
        
        // Create gradient for bubble
        var gradient = ctx.createRadialGradient(-size/3, -size/3, 0, 0, 0, size);
        gradient.addColorStop(0, 'rgba(255,255,255,0.8)');
        gradient.addColorStop(0.5, hexToRgba(p.color, 0.3));
        gradient.addColorStop(1, hexToRgba(p.color, 0.1));
        
        ctx.beginPath();
        ctx.arc(0, 0, size, 0, Math.PI * 2);
        ctx.fillStyle = gradient;
        ctx.fill();
        
        // Outer ring
        ctx.strokeStyle = hexToRgba(p.color, 0.5);
        ctx.lineWidth = 1.5;
        ctx.stroke();
        
        // Shine highlights
        ctx.beginPath();
        ctx.arc(-size/3, -size/3, size/4, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(255,255,255,0.7)';
        ctx.fill();
        
        ctx.beginPath();
        ctx.arc(size/4, size/4, size/6, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(255,255,255,0.3)';
        ctx.fill();
    }

    /**
     * Draw enhanced confetti with multiple shapes
     */
    function drawConfetti(p) {
        ctx.fillStyle = p.randomColor;
        
        switch (p.shapeVariant) {
            case 0:
                // Rectangle
                ctx.fillRect(-p.size/2, -p.size/4, p.size, p.size/2);
                break;
            case 1:
                // Circle
                ctx.beginPath();
                ctx.arc(0, 0, p.size/2, 0, Math.PI * 2);
                ctx.fill();
                break;
            case 2:
                // Triangle
                ctx.beginPath();
                ctx.moveTo(0, -p.size/2);
                ctx.lineTo(p.size/2, p.size/2);
                ctx.lineTo(-p.size/2, p.size/2);
                ctx.closePath();
                ctx.fill();
                break;
        }
    }

    /**
     * Draw enhanced star with variable spikes and glow
     */
    function drawStar(p) {
        var spikes = p.spikes;
        var outerRadius = p.size;
        var innerRadius = p.size / 2.5;
        
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
        
        // Add gradient fill
        var gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, outerRadius);
        gradient.addColorStop(0, '#ffffff');
        gradient.addColorStop(0.5, p.color);
        gradient.addColorStop(1, hexToRgba(p.color, 0.5));
        
        ctx.fillStyle = gradient;
        ctx.fill();
        
        // Add glow
        ctx.shadowColor = p.color;
        ctx.shadowBlur = p.size;
    }

    /**
     * Draw enhanced heart with gradient
     */
    function drawHeart(p) {
        var size = p.size;
        
        ctx.beginPath();
        ctx.moveTo(0, size * 0.3);
        ctx.bezierCurveTo(size * 0.5, -size * 0.3, size, size * 0.1, 0, size);
        ctx.bezierCurveTo(-size, size * 0.1, -size * 0.5, -size * 0.3, 0, size * 0.3);
        
        // Gradient fill
        var gradient = ctx.createRadialGradient(0, size * 0.3, 0, 0, size * 0.3, size);
        gradient.addColorStop(0, '#ff6b8a');
        gradient.addColorStop(1, p.color);
        
        ctx.fillStyle = gradient;
        ctx.fill();
        
        // Add shine
        ctx.beginPath();
        ctx.arc(-size * 0.3, -size * 0.1, size * 0.15, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(255,255,255,0.4)';
        ctx.fill();
    }

    /**
     * Draw enhanced autumn leaf
     */
    function drawLeaf(p) {
        var size = p.size;
        ctx.fillStyle = p.randomColor;
        ctx.strokeStyle = hexToRgba(p.randomColor, 0.8);
        ctx.lineWidth = 1;
        
        // Leaf shape
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.bezierCurveTo(size * 0.8, -size * 0.5, size * 0.6, size * 0.5, 0, size);
        ctx.bezierCurveTo(-size * 0.6, size * 0.5, -size * 0.8, -size * 0.5, 0, -size);
        ctx.fill();
        
        // Stem and veins
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.lineTo(0, size * 1.3);
        ctx.stroke();
        
        // Side veins
        for (var i = 1; i <= 3; i++) {
            var y = -size + (size * 1.5 * i / 4);
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(size * 0.4, y - size * 0.2);
            ctx.moveTo(0, y);
            ctx.lineTo(-size * 0.4, y - size * 0.2);
            ctx.stroke();
        }
    }

    /**
     * Draw rain drop
     */
    function drawRain(p) {
        var size = p.size;
        ctx.fillStyle = hexToRgba(p.color, 0.6);
        
        // Elongated drop shape
        ctx.beginPath();
        ctx.moveTo(0, -size * 2);
        ctx.bezierCurveTo(size * 0.3, -size, size * 0.3, 0, 0, size * 0.3);
        ctx.bezierCurveTo(-size * 0.3, 0, -size * 0.3, -size, 0, -size * 2);
        ctx.fill();
    }

    /**
     * Draw firefly with glowing effect
     */
    function drawFirefly(p) {
        var size = p.size;
        
        // Outer glow
        var gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, size * 3);
        gradient.addColorStop(0, p.randomColor);
        gradient.addColorStop(0.3, hexToRgba(p.randomColor, 0.5));
        gradient.addColorStop(1, 'rgba(0,0,0,0)');
        
        ctx.beginPath();
        ctx.arc(0, 0, size * 3, 0, Math.PI * 2);
        ctx.fillStyle = gradient;
        ctx.fill();
        
        // Core
        ctx.beginPath();
        ctx.arc(0, 0, size * 0.5, 0, Math.PI * 2);
        ctx.fillStyle = '#ffffff';
        ctx.fill();
    }

    /**
     * Draw sakura (cherry blossom) petal
     */
    function drawSakura(p) {
        var size = p.size;
        ctx.fillStyle = p.randomColor;
        
        // Petal shape
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.bezierCurveTo(size * 0.5, -size * 0.7, size * 0.5, size * 0.3, 0, size * 0.5);
        ctx.bezierCurveTo(-size * 0.5, size * 0.3, -size * 0.5, -size * 0.7, 0, -size);
        ctx.fill();
        
        // Add gradient overlay
        var gradient = ctx.createRadialGradient(0, -size * 0.3, 0, 0, 0, size);
        gradient.addColorStop(0, 'rgba(255,255,255,0.5)');
        gradient.addColorStop(1, 'rgba(255,255,255,0)');
        ctx.fillStyle = gradient;
        ctx.fill();
    }

    /**
     * Draw diamond shape
     */
    function drawDiamond(p) {
        var size = p.size;
        
        // Create faceted diamond
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.lineTo(size * 0.6, 0);
        ctx.lineTo(0, size);
        ctx.lineTo(-size * 0.6, 0);
        ctx.closePath();
        
        // Gradient fill for shine
        var gradient = ctx.createLinearGradient(-size, -size, size, size);
        gradient.addColorStop(0, '#ffffff');
        gradient.addColorStop(0.3, p.color);
        gradient.addColorStop(0.7, hexToRgba(p.color, 0.5));
        gradient.addColorStop(1, '#ffffff');
        
        ctx.fillStyle = gradient;
        ctx.fill();
        
        ctx.strokeStyle = hexToRgba(p.color, 0.8);
        ctx.lineWidth = 1;
        ctx.stroke();
        
        // Add facet lines
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.lineTo(0, size);
        ctx.moveTo(-size * 0.6, 0);
        ctx.lineTo(size * 0.6, 0);
        ctx.strokeStyle = 'rgba(255,255,255,0.3)';
        ctx.stroke();
    }

    /**
     * Draw ribbon/streamer
     */
    function drawRibbon(p) {
        var size = p.size;
        ctx.fillStyle = p.randomColor;
        ctx.strokeStyle = p.randomColor;
        ctx.lineWidth = size / 3;
        ctx.lineCap = 'round';
        
        // Wavy ribbon
        ctx.beginPath();
        ctx.moveTo(-size, -size);
        ctx.bezierCurveTo(
            -size * 0.5, -size * 0.5 + Math.sin(p.ribbonWave) * size * 0.3,
            size * 0.5, size * 0.5 + Math.cos(p.ribbonWave) * size * 0.3,
            size, size
        );
        ctx.stroke();
    }

    /**
     * Draw sparkle/twinkle
     */
    function drawSparkle(p) {
        var size = p.size;
        ctx.fillStyle = p.color;
        ctx.strokeStyle = p.color;
        ctx.lineWidth = size / 6;
        
        // Four-point sparkle
        ctx.beginPath();
        ctx.moveTo(0, -size);
        ctx.lineTo(0, size);
        ctx.moveTo(-size, 0);
        ctx.lineTo(size, 0);
        ctx.stroke();
        
        // Diagonal lines (smaller)
        ctx.lineWidth = size / 8;
        ctx.beginPath();
        ctx.moveTo(-size * 0.5, -size * 0.5);
        ctx.lineTo(size * 0.5, size * 0.5);
        ctx.moveTo(size * 0.5, -size * 0.5);
        ctx.lineTo(-size * 0.5, size * 0.5);
        ctx.stroke();
        
        // Center glow
        ctx.beginPath();
        ctx.arc(0, 0, size / 4, 0, Math.PI * 2);
        ctx.fillStyle = '#ffffff';
        ctx.fill();
    }

    /**
     * Helper: Convert hex to rgba
     */
    function hexToRgba(hex, alpha) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        if (result) {
            return 'rgba(' + parseInt(result[1], 16) + ',' + parseInt(result[2], 16) + ',' + parseInt(result[3], 16) + ',' + alpha + ')';
        }
        return 'rgba(255,255,255,' + alpha + ')';
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
