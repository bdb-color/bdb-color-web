<footer class="bg-light border-top pt-5 pb-4 mt-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <h5>CV BERKAH DOA BUNDA</h5>
        <p class="mb-1">Jl. Diklat Pemda  KP. Badodon</p>
        <p class="mb-1">RT.01 RW.15</p>
        <p class="mb-1">📞 0811-1185-2220</p>
        <p class="mb-1">✉️ bdbfloor@gmail.com</p>
        <div class="mt-3">
          <a href="https://wa.me/6281111852220" class="me-3 text-dark"><i class="bi bi-whatsapp fs-5"></i></a>
          <a href="https://instagram.com/cv_berkah_doa_bunda" class="me-3 text-dark"><i class="bi bi-instagram fs-5"></i></a>
          <a href="https://tiktok.com/@bdb_floor" class="me-3 text-dark"><i class="bi bi-tiktok fs-5"></i></a>
          <a href="https://facebook.com/Bdbfloor Industrial Coating" class="me-3 text-dark" target="_blank">
            <i class="bi bi-facebook fs-5"></i>
          </a>
        </div>
      </div>

      <div class="col-md-4">
        <h5>Tentang Kami</h5>
        <p style="text-align: justify;">BDB Color adalah aplikator epoxy lantai profesional yang berpengalaman dalam menangani berbagai jenis pekerjaan flooring, baik di area industri, komersial, maupun hunian pribadi. Kami menghadirkan solusi terbaik untuk setiap tantangan lantai dengan hasil yang rapi, kuat, dan tahan lama. Selain epoxy coating, kami juga menyediakan layanan pengecatan tangki dan pipa (protective coating), pengecatan lapangan, serta waterproofing baik menggunakan membran bakar maupun sistem coating. Tak hanya sebagai penyedia jasa, kami juga memproduksi berbagai jenis cat dengan merek sendiri seperti BDB FLOOR (epoxy lantai), MULTIZINC (zinc chromate), cat synthetic, serta cat dekoratif untuk tembok. Dengan menjaga kualitas produk dan layanan secara konsisten, kami berkomitmen memberikan hasil maksimal demi kepuasan setiap klien kami.</p>
      </div>

      <div class="col-md-4">
        <h5>Lokasi Kami</h5>
        <div class="ratio ratio-4x3 rounded shadow-sm">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.047521393961!2d106.5766881!3d-6.2574705999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fdb0d3b9c1c9%3A0x59198a5b7af88dd1!2sCV.%20BERKAH%20DOA%20BUNDA%20%5B%20GUDANG%20EPOXY%20DAN%20JASA%20EPOXY%20LANTAI%20TANGERANG%20%5D!5e0!3m2!1sid!2sid!4v1751289388283!5m2!1sid!2sid" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>

    <hr class="my-4">
    <div class="text-center text-muted small">
      &copy; <?= date('Y') ?> CV BERKAH DOA BUNDA. All rights reserved.
    </div>
  </div>
</footer>

<!-- Scroll to Top -->
<button onclick="window.scrollTo({top: 0, behavior: 'smooth'});" class="btn btn-primary rounded-circle position-fixed"
        style="bottom: 20px; left: 20px; z-index: 1000; display: none;" id="scrollTopBtn">
  <i class="bi bi-arrow-up"></i>
</button>

<script>
  const scrollBtn = document.getElementById('scrollTopBtn');
  window.onscroll = () => {
    scrollBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
  };
</script>

<!-- Bootstrap Bundle JS (diperlukan untuk alert dan komponen interaktif) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-lZN37f5QGtY1tq9V1VfRZITRbwbI7Wj5ilD+sHYgF4r7HWDY0qQu1jPA2x65KgfD"
  crossorigin="anonymous"></script>
</body>
</html>
