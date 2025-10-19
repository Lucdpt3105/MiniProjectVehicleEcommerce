    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-store"></i> Mini Shop</h5>
                    <p>Cửa hàng mô hình xe hơi classic chất lượng cao</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>
                        <i class="fas fa-phone"></i> 0123-456-789<br>
                        <i class="fas fa-envelope"></i> support@minishop.com<br>
                        <i class="fas fa-map-marker-alt"></i> Hà Nội, Việt Nam
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi</h5>
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-twitter fa-2x"></i></a>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <small>&copy; 2025 Mini Shop. All rights reserved.</small>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/mini_shop/assets/js/main.js"></script>
    
    <?php if (isset($extraJS)): ?>
        <?php echo $extraJS; ?>
    <?php endif; ?>
</body>
</html>
