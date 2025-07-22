    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-nav">
                <ul class="footer-nav-list">
                    <li><a href="/" class="footer-nav-link">Home</a></li>
                    <li><a href="/about.php" class="footer-nav-link">About Us</a></li>
                    <li><a href="/categories.php" class="footer-nav-link">Categories</a></li>
                    <li><a href="/countries.php" class="footer-nav-link">Countries</a></li>
                    <li><a href="/contact.php" class="footer-nav-link">Contact</a></li>
                </ul>
            </div>
            <div class="footer-alphabet">
                <?php echo get_alphabet_links(); ?>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="/assets/js/main.js"></script>
</body>
</html> 