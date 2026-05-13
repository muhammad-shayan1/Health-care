<?php
include 'includes/head.php';
?>
<?php
$news = [
    [
        'id' => 1,
        'title' => 'Revolutionary Heart Treatment Now Available in Pakistan',
        'summary' => 'A groundbreaking minimally invasive cardiac procedure is now available at leading hospitals across the country.',
        'category' => 'Cardiology',
        'image' => 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=800&q=80',
        'author' => 'Dr. Ahmed Ali',
        'published_at' => '2025-02-15'
    ],
    [
        'id' => 2,
        'title' => 'COVID-19 Booster Guidelines Updated for 2025',
        'summary' => 'Health authorities have released updated recommendations for COVID-19 booster shots targeting new variants.',
        'category' => 'General Health',
        'image' => 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?w=800&q=80',
        'author' => 'CARE Group',
        'published_at' => '2025-02-10'
    ],
    [
        'id' => 3,
        'title' => 'New Diabetes Management Guidelines Released',
        'summary' => 'Continuous glucose monitoring now recommended for all Type 1 patients under the latest clinical guidelines.',
        'category' => 'Endocrinology',
        'image' => 'https://images.unsplash.com/photo-1631815589968-fdb09a223b1e?w=800&q=80',
        'author' => 'Dr. Sara Khan',
        'published_at' => '2025-02-05'
    ],
    [
        'id' => 4,
        'title' => 'Mental Health Awareness Month Initiatives',
        'summary' => 'CARE Group joins global efforts to raise mental health awareness with free counseling sessions available.',
        'category' => 'Mental Health',
        'image' => 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=800&q=80',
        'author' => 'CARE Group',
        'published_at' => '2025-01-28'
    ],
];
?>
<!-- ===== NEWS HERO CAROUSEL ===== -->
<section class="news-hero-section">
  <!-- Bootstrap Carousel -->
  <div id="newsHeroCarousel" class="carousel slide carousel-fade news-carousel" data-bs-ride="carousel" data-bs-interval="4500">
    <!-- Slides -->
    <div class="carousel-inner news-carousel-inner">
      <?php foreach ($news as $i => $item): ?>
      <div class="carousel-item news-slide <?= $i === 0 ? 'active' : '' ?>">

        <!-- Background Image -->
        <div class="slide-bg"
          style="background-image: url('<?= htmlspecialchars($item['image'] ?? '') ?>')">
          <div class="slide-overlay"></div>
        </div>

        <!-- Content -->
        <div class="container h-100">
          <div class="row h-100 align-items-end">
            <div class="col-lg-8 pb-5">
              <div class="slide-content">
                <span class="slide-category"><?= htmlspecialchars($item['category'] ?? 'Health') ?></span>
                <h2 class="slide-title"><?= htmlspecialchars($item['title']) ?></h2>
                <p class="slide-desc"><?= htmlspecialchars($item['summary'] ?? '') ?></p>
                <div class="slide-meta">
                  <span><i class="fas fa-user-md me-1"></i><?= htmlspecialchars($item['author'] ?? '') ?></span>
                  <span class="ms-3"><i class="fas fa-calendar me-1"></i><?= date('M d, Y', strtotime($item['published_at'])) ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <!-- Thumbnail Strip -->
  <div class="news-thumb-strip">
    <div class="container">
      <div class="thumb-row">
        <?php foreach ($news as $i => $item): ?>
        <div class="thumb-item <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"
          onclick="goToSlide(<?= $i ?>)">
          <div class="thumb-img" style="background-image:url('<?= htmlspecialchars($item['image'] ?? '') ?>')"></div>
          <div class="thumb-info">
            <span class="thumb-cat"><?= htmlspecialchars($item['category'] ?? '') ?></span>
            <p class="thumb-title"><?= htmlspecialchars(mb_strimwidth($item['title'], 0, 55, '…')) ?></p>
          </div>
          <div class="thumb-progress"></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<script>
(function() {
  const carousel = document.getElementById('newsHeroCarousel');
  if (!carousel) return;
  const thumbs   = document.querySelectorAll('.thumb-item');
  const bsCarousel = bootstrap.Carousel.getOrCreateInstance(carousel);

  function setActive(idx) {
    thumbs.forEach((t, i) => {
      t.classList.toggle('active', i === idx);
      // restart progress animation by re-adding element
      const prog = t.querySelector('.thumb-progress');
      if (prog) {
        prog.style.animation = 'none';
        void prog.offsetWidth; // reflow
        if (i === idx) prog.style.animation = '';
      }
    });
  }

  carousel.addEventListener('slid.bs.carousel', function(e) {
    setActive(e.to);
  });
})();

function goToSlide(idx) {
  const bsCarousel = bootstrap.Carousel.getOrCreateInstance(
    document.getElementById('newsHeroCarousel')
  );
  bsCarousel.to(idx);
}
</script>
<?php include 'includes/scripts.php'; ?>