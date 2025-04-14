</div><!-- End of content-area -->
</div><!-- End of container mt-4 -->

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-copyright">
                <p>© <?php echo date('Y'); ?> Online Book Trading System</p>
            </div>
            
        </div>
    </div>
</footer>

<style>
    /* Footer Styles that match header gradient */
    .footer {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8367ff 100%);
        color: white;
        padding: 20px 0;
        margin-top: 30px;
        width: 100%;
        position: sticky;
        bottom: 0;
        z-index: 900;
    }
    
    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .footer-copyright {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .footer-links {
        display: flex;
        gap: 20px;
    }
    
    .footer-links a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        font-size: 0.9rem;
        transition: var(--transition);
    }
    
    .footer-links a:hover {
        color: white;
        text-decoration: underline;
    }
    
    /* Make sure the footer always stays at the bottom */
    html, body {
        height: 100%;
    }
    
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .container.mt-4 {
        flex: 1;
    }
    
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }
        
        .footer-links {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>

<script src="<?php echo $relativePath; ?>assets/js/script.js"></script>
</body>
</html>