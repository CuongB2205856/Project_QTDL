<?php
// app/views/studentpanel/student_footer.php
?>
</main>

<footer class="bg-white border-top mt-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                    <img src="/image/CTU_logo.png" alt="CTU Logo" style="max-height: 40px;" class="me-3">
                    <div>
                        <h6 class="mb-0 text-primary">Ký túc xá Đại học Cần Thơ</h6>
                        <small class="text-muted">Can Tho University Dormitory</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-1 text-muted">
                    <i class="bi bi-envelope me-1"></i>
                    <a href="mailto:ktx@ctu.edu.vn" class="text-decoration-none text-muted">ktx@ctu.edu.vn</a>
                </p>
                <p class="mb-0 text-muted">
                    <i class="bi bi-telephone me-1"></i>
                    <a href="tel:+842923832663" class="text-decoration-none text-muted">(0292) 383 2663</a>
                </p>
            </div>
        </div>
        <hr class="my-3">
        <div class="text-center">
            <p class="mb-0 text-muted small">
                &copy; <?php echo date('Y'); ?> Ký túc xá Đại học Cần Thơ. 
                <span class="mx-2">|</span>
                Phát triển bởi <strong>Phòng Công nghệ thông tin</strong>
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
        crossorigin="anonymous"></script>

<!-- Smooth Scroll to Top Button -->
<button onclick="scrollToTop()" id="scrollTopBtn" 
        class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-4 shadow" 
        style="width: 50px; height: 50px; display: none; z-index: 1000;">
    <i class="bi bi-arrow-up"></i>
</button>

<script>
// Show/Hide Scroll to Top Button
window.onscroll = function() {
    const scrollBtn = document.getElementById('scrollTopBtn');
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        scrollBtn.style.display = 'block';
    } else {
        scrollBtn.style.display = 'none';
    }
};

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add fade-in animation to cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

</body>
</html>