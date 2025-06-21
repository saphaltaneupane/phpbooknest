<?php
// Check if $relativePath is defined, otherwise set a default
if (!isset($relativePath)) {
    $relativePath = '';
}
?>
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <span class="footer-icon">📚</span>
                    <span class="footer-title">BookTrading</span>
                </div>
                <p class="footer-description">
                    Your trusted platform for buying, selling, and trading books online. 
                    Connect with fellow book lovers and discover your next great read.
                </p>
            </div>
            <div class="footer-section">
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $relativePath; ?>index.php">Home</a></li>
                    <?php if (isLoggedIn() && !isAdmin()): ?>
                        <li><a href="<?php echo $relativePath; ?>user/dashboard.php">Dashboard</a></li>
                        <li><a href="<?php echo $relativePath; ?>user/profile.php">Profile</a></li>
                    <?php endif; ?>
                    <?php if (!isLoggedIn()): ?>
                        <li><a href="<?php echo $relativePath; ?>login.php">Login</a></li>
                        <li><a href="<?php echo $relativePath; ?>register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-section">
                <h4 class="footer-heading">Support</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $relativePath; ?>contact.php">Contact Us</a></li>
                    <li><a href="<?php echo $relativePath; ?>help.php">Help Center</a></li>
                    <li><a href="<?php echo $relativePath; ?>privacy.php">Privacy Policy</a></li>
                    <li><a href="<?php echo $relativePath; ?>terms.php">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4 class="footer-heading">Follow the journey</h4>
                <div class="footer-social-links">
                    <a href="#" class="social-link" aria-label="Facebook">f</a>
                    <a href="#" class="social-link" aria-label="Twitter">t</a>
                    <a href="#" class="social-link" aria-label="Instagram">i</a>
                    <a href="#" class="social-link" aria-label="LinkedIn">in</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-divider"></div>
            <div class="footer-bottom-content">
                <p class="footer-copyright">
                    &copy; <?php echo date('Y'); ?> BookTrading. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
<style>
    /* Footer Styles */
    .footer {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f172a 100%);
        color: #e2e8f0;
        margin-top: auto;
        position: relative;
        overflow: hidden;
        width: 100%; /* changed from 100vw */
        left: 0;
        padding: 0;
        border: none;
    }

    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
        background-size: 400% 100%;
        animation: footerGradientMove 8s ease infinite;
    }

    @keyframes footerGradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .footer .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .footer-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 3rem;
        padding: 3rem 0 1.5rem 0;
        position: relative;
    }

    .footer-section {
        position: relative;
        min-width: 160px;
    }

    .footer-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.2rem;
    }

    .footer-icon {
        font-size: 2.2rem;
        filter: drop-shadow(0 2px 8px rgba(102, 126, 234, 0.3));
        animation: footerBookFloat 4s ease-in-out infinite;
    }

    @keyframes footerBookFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-4px) rotate(3deg); }
    }

    .footer-title {
        font-size: 1.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.5px;
    }

    .footer-description {
        color: #94a3b8;
        line-height: 1.7;
        font-size: 0.95rem;
        font-weight: 400;
        margin-bottom: 0;
    }

    .footer-heading {
        color: #f8fafc;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        letter-spacing: 0.5px;
    }

    .footer-heading::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 30px;
        height: 2px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.75rem;
    }

    .footer-links a {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: inline-block;
        padding: 0.25rem 0;
    }

    .footer-links a::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transition: width 0.3s ease;
    }

    .footer-links a:hover {
        color: #f8fafc;
        transform: translateX(5px);
    }

    .footer-links a:hover::before {
        width: 100%;
    }

    .footer-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(148, 163, 184, 0.2), transparent);
        margin: 2rem 0 1.5rem 0;
    }

    .footer-bottom-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .footer-copyright {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
        margin: 0;
    }

    .footer-social-links {
        display: flex;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .social-link {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .social-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        transform: translateY(-3px) scale(1.1);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    @media (max-width: 992px) {
        .footer-content {
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
            padding: 2.5rem 0 1.5rem 0;
        }
    }

    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 2rem 0 1rem 0;
            text-align: center;
        }

        .footer-logo {
            justify-content: center;
        }

        .footer-heading::after {
            display: none;
        }

        .footer-bottom-content {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
        }

        .footer-social-links {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .footer-content {
            padding: 1.5rem 0 0.5rem 0;
        }

        .footer-title {
            font-size: 1.3rem;
        }

        .footer-icon {
            font-size: 1.5rem;
        }
    }

    .footer-section {
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .footer-section:nth-child(1) { animation-delay: 0.1s; }
    .footer-section:nth-child(2) { animation-delay: 0.2s; }
    .footer-section:nth-child(3) { animation-delay: 0.3s; }
    .footer-section:nth-child(4) { animation-delay: 0.4s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .footer-section:hover .footer-heading {
        color: #ffffff;
        text-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
    }

    .footer::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -100px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.03) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
</style>
</body>
</html>