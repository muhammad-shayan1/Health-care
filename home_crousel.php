<style>
  @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap');

  :root {
    --cg-navy: #071e3d;
    --cg-blue: #1053a0;
    --cg-ocean: #0891b2;
    --cg-mint: #06d6a0;
    --cg-sky: #38d9e8;
    --cg-white: #ffffff;
    --cg-white70: rgba(255, 255, 255, 0.70);
    --cg-white15: rgba(255, 255, 255, 0.12);
    --cg-font-h: 'Cormorant Garamond', serif;
    --cg-font-b: 'Plus Jakarta Sans', sans-serif;
  }

  .cg-hero {
    position: relative;
    height: 650px;
    display: flex;
    align-items: center;
    overflow: hidden;
    font-family: var(--cg-font-b);
    background: var(--cg-navy);
    padding: 130px 0 80px;
  }

  .cg-hero__bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    background:
      radial-gradient(ellipse 80% 70% at 80% 50%, rgba(8, 145, 178, 0.28) 0%, transparent 65%),
      radial-gradient(ellipse 50% 60% at 10% 80%, rgba(16, 83, 160, 0.22) 0%, transparent 60%),
      radial-gradient(ellipse 40% 40% at 55% 10%, rgba(6, 214, 160, 0.10) 0%, transparent 55%),
      linear-gradient(145deg, #071e3d 0%, #0d2e5e 45%, #0a4a6e 100%);
  }

  .cg-hero__grid {
    position: absolute;
    inset: 0;
    z-index: 1;
    background-image:
      linear-gradient(rgba(255, 255, 255, 0.025) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px);
    background-size: 60px 60px;
    animation: cgGridShift 30s linear infinite;
  }

  @keyframes cgGridShift {
    from {
      background-position: 0 0;
    }

    to {
      background-position: 60px 60px;
    }
  }

  .cg-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(90px);
    pointer-events: none;
    z-index: 1;
  }

  .cg-orb--a {
    width: 600px;
    height: 600px;
    top: -150px;
    right: -100px;
    background: radial-gradient(circle, rgba(6, 214, 160, 0.18), transparent 70%);
    animation: cgOrbA 16s ease-in-out infinite alternate;
  }

  .cg-orb--b {
    width: 400px;
    height: 400px;
    bottom: -80px;
    left: -60px;
    background: radial-gradient(circle, rgba(56, 217, 232, 0.15), transparent 70%);
    animation: cgOrbB 20s ease-in-out infinite alternate;
  }

  .cg-orb--c {
    width: 250px;
    height: 250px;
    top: 40%;
    left: 45%;
    background: radial-gradient(circle, rgba(16, 83, 160, 0.20), transparent 70%);
    animation: cgOrbA 24s ease-in-out infinite alternate-reverse;
  }

  @keyframes cgOrbA {
    from {
      transform: translate(0, 0);
    }

    to {
      transform: translate(25px, 35px);
    }
  }

  @keyframes cgOrbB {
    from {
      transform: translate(0, 0);
    }

    to {
      transform: translate(-20px, -28px);
    }
  }

  .cg-rings {
    position: absolute;
    right: -60px;
    top: -60px;
    z-index: 1;
    pointer-events: none;
  }

  .cg-ring {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.055);
    animation: cgRingBreath 9s ease-in-out infinite alternate;
  }

  .cg-ring:nth-child(1) {
    width: 560px;
    height: 560px;
    top: 0;
    left: 0;
  }

  .cg-ring:nth-child(2) {
    width: 400px;
    height: 400px;
    top: 80px;
    left: 80px;
    animation-delay: -4s;
    border-color: rgba(56, 217, 232, 0.08);
  }

  .cg-ring:nth-child(3) {
    width: 250px;
    height: 250px;
    top: 155px;
    left: 155px;
    animation-delay: -7s;
    border-color: rgba(6, 214, 160, 0.10);
  }

  @keyframes cgRingBreath {
    from {
      transform: scale(1);
      opacity: 0.5;
    }

    to {
      transform: scale(1.05);
      opacity: 1;
    }
  }

  .cg-dot {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 2;
    opacity: 0;
    animation: cgDotRise linear infinite;
  }

  @keyframes cgDotRise {
    0% {
      transform: translateY(0) scale(1);
      opacity: 0;
    }

    12% {
      opacity: 0.55;
    }

    88% {
      opacity: 0.22;
    }

    100% {
      transform: translateY(-460px) scale(0.2);
      opacity: 0;
    }
  }

  .cg-hero__content {
    position: relative;
    z-index: 10;
    padding: 0 16px;
  }

  .cg-badge {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 7px 20px;
    border-radius: 40px;
    background: rgba(6, 214, 160, 0.10);
    border: 1px solid rgba(6, 214, 160, 0.30);
    color: #a7f3e2;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    margin-bottom: 30px;
    animation: cgSlideUp 0.7s ease 0.1s both;
  }

  .cg-badge__dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--cg-mint);
    box-shadow: 0 0 8px var(--cg-mint);
    animation: cgDotBlink 2s ease-in-out infinite;
  }

  @keyframes cgDotBlink {

    0%,
    100% {
      opacity: 1;
    }

    50% {
      opacity: 0.25;
    }
  }

  .cg-hero__title {
    font-family: var(--cg-font-h);
    font-size: clamp(2.6rem, 5.5vw, 4.2rem);
    font-weight: 700;
    line-height: 1.1;
    color: var(--cg-white);
    margin-bottom: 20px;
    animation: cgSlideUp 0.7s ease 0.22s both;
  }

  .cg-hero__title em {
    font-style: italic;
    background: linear-gradient(90deg, var(--cg-mint), var(--cg-sky));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .cg-hero__desc {
    font-size: 1rem;
    color: var(--cg-white70);
    line-height: 1.82;
    max-width: 470px;
    margin-bottom: 40px;
    font-weight: 300;
    animation: cgSlideUp 0.7s ease 0.35s both;
  }

  .cg-hero__btns {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 56px;
    animation: cgSlideUp 0.7s ease 0.48s both;
  }

  .cg-btn-main {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 14px 32px;
    border-radius: 50px;
    background: linear-gradient(135deg, #06d6a0, #0891b2);
    color: var(--cg-white) !important;
    text-decoration: none !important;
    font-family: var(--cg-font-b);
    font-size: 0.92rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    box-shadow: 0 6px 30px rgba(6, 214, 160, 0.38);
    transition: transform 0.22s, box-shadow 0.22s;
  }

  .cg-btn-main:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(6, 214, 160, 0.55);
  }

  .cg-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 13px 32px;
    border-radius: 50px;
    background: transparent;
    color: var(--cg-white) !important;
    text-decoration: none !important;
    font-family: var(--cg-font-b);
    font-size: 0.92rem;
    font-weight: 400;
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s, transform 0.2s;
  }

  .cg-btn-ghost:hover {
    border-color: var(--cg-sky);
    background: rgba(56, 217, 232, 0.07);
    transform: translateY(-3px);
  }

  .cg-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 0;
    animation: cgSlideUp 0.7s ease 0.62s both;
  }

  .cg-stat {
    padding-right: 30px;
    margin-right: 30px;
    border-right: 1px solid rgba(255, 255, 255, 0.12);
  }

  .cg-stat:last-child {
    border-right: none;
    margin-right: 0;
    padding-right: 0;
  }

  .cg-stat__num {
    font-family: var(--cg-font-h);
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--cg-white);
    line-height: 1;
  }

  .cg-stat__num .cg-sfx {
    background: linear-gradient(90deg, var(--cg-mint), var(--cg-sky));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.6rem;
  }

  .cg-stat__label {
    font-size: 0.67rem;
    letter-spacing: 0.13em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.42);
    font-weight: 600;
    margin-top: 5px;
  }

  .cg-visual {
    position: absolute;
    right: 3%;
    top: 50%;
    transform: translateY(-50%);
    z-index: 8;
    animation: cgFloat 8s ease-in-out infinite alternate;
  }

  @keyframes cgFloat {
    from {
      transform: translateY(-50%) translateY(0);
    }

    to {
      transform: translateY(-50%) translateY(-22px);
    }
  }

  .cg-visual__ring {
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.05), rgba(6, 214, 160, 0.08));
    border: 1.5px solid rgba(255, 255, 255, 0.09);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
  }

  .cg-visual__core {
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: linear-gradient(145deg, rgba(6, 214, 160, 0.14), rgba(8, 145, 178, 0.16));
    border: 1px solid rgba(255, 255, 255, 0.11);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
  }

  .cg-visual__icon {
    font-size: 72px;
    filter: drop-shadow(0 0 24px rgba(6, 214, 160, 0.55));
  }

  .cg-visual__tag {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.7rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    font-family: var(--cg-font-b);
    font-weight: 600;
  }

  .cg-chip {
    position: absolute;
    background: rgba(7, 30, 61, 0.78);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 16px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--cg-white);
    font-size: 0.76rem;
    font-family: var(--cg-font-b);
    white-space: nowrap;
    animation: cgChipGlow 3.5s ease-in-out infinite alternate;
  }

  @keyframes cgChipGlow {
    from {
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }

    to {
      box-shadow: 0 6px 28px rgba(6, 214, 160, 0.25);
    }
  }

  .cg-chip--top {
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
  }

  .cg-chip--right {
    right: -24px;
    top: 43%;
  }

  .cg-chip--bottom {
    bottom: 26px;
    left: -24px;
  }

  .cg-chip__led {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--cg-mint);
    box-shadow: 0 0 6px var(--cg-mint);
    flex-shrink: 0;
  }

  .cg-chip__led--pulse {
    background: #4ade80;
    box-shadow: 0 0 6px #4ade80;
  }

  @keyframes cgSlideUp {
    from {
      opacity: 0;
      transform: translateY(26px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 991px) {
    .cg-visual {
      display: none;
    }

    .cg-rings {
      display: none;
    }

    .cg-hero {
      padding: 80px 0 50px;
    }

    .cg-stat {
      padding-right: 20px;
      margin-right: 20px;
    }
  }

  @media (max-width: 575px) {
    .cg-hero {
      padding: 60px 0 40px;
    }

    .cg-hero__title {
      font-size: 2.2rem;
    }

    .cg-btn-main,
    .cg-btn-ghost {
      width: 100%;
      justify-content: center;
    }

    .cg-stat {
      padding-right: 16px;
      margin-right: 16px;
    }

    .cg-stat__num {
      font-size: 1.75rem;
    }
  }

  @media (max-width: 991px) {
    .cg-hero {
      padding: 80px 0 50px;
    }

    .cg-visual,
    .cg-rings {
      display: none;
    }

    .cg-stat {
      padding-right: 20px;
      margin-right: 20px;
    }
  }

  @media (max-width: 768px) {
    .cg-hero {
      height: 500px;
      padding: 70px 0 40px;
    }

    .cg-hero__title {
      font-size: 2.4rem;
    }

    .cg-hero__desc {
      font-size: 0.95rem;
    }

    .cg-stats {
      gap: 10px 0;
    }

    .cg-stat {
      padding-right: 16px;
      margin-right: 16px;
    }

    .cg-stat__num {
      font-size: 1.9rem;
    }
  }

  @media (max-width: 500px) {
    .cg-hero {
      height: 600px;
      padding: 60px 0 35px;
    }

    .cg-hero__title {
      font-size: 2rem;
    }

    .cg-badge {
      font-size: 0.65rem;
      padding: 6px 14px;
    }

    .cg-btn-main,
    .cg-btn-ghost {
      width: 100%;
      justify-content: center;
    }

    .cg-stat {
      padding-right: 12px;
      margin-right: 12px;
    }

    .cg-stat__num {
      font-size: 1.6rem;
    }

    .cg-stat__label {
      font-size: 0.6rem;
    }
  }
</style>


<!-- ====== HERO SECTION ====== -->
<section class="cg-hero">

  <div class="cg-hero__bg"></div>
  <div class="cg-hero__grid"></div>

  <div class="cg-orb cg-orb--a"></div>
  <div class="cg-orb cg-orb--b"></div>
  <div class="cg-orb cg-orb--c"></div>

  <div class="cg-rings">
    <div class="cg-ring"></div>
    <div class="cg-ring"></div>
    <div class="cg-ring"></div>
  </div>

  <div class="cg-visual">
    <div class="cg-visual__ring">
      <div class="cg-visual__core">
        <div class="cg-visual__icon">🏥</div>
        <div class="cg-visual__tag">Healthcare Platform</div>
      </div>
      <div class="cg-chip cg-chip--top">
        <div class="cg-chip__led cg-chip__led--pulse"></div>
        <span><?php
        $r = $connect->query('SELECT COUNT(*) AS total FROM doctors');
        echo $r->fetch_assoc()['total'];
        ?> Doctors Online</span>
      </div>
      <div class="cg-chip cg-chip--right">
        <div class="cg-chip__led"></div>
        <span>4.9 ★ Rating</span>
      </div>
      <div class="cg-chip cg-chip--bottom">
        <span>⚡</span>
        <span>Instant Booking</span>
      </div>
    </div>
  </div>

  <div class="container position-relative" style="z-index:2;">
    <div class="row align-items-center">
      <div class="col-lg-6 cg-hero__content">

        <div class="cg-badge">
          <div class="cg-badge__dot"></div>
          <i class="fas fa-shield-alt"></i> Trusted Healthcare Platform
        </div>

        <h1 class="cg-hero__title">
          Your Health, Our <em>Priority</em> &amp; Care
        </h1>

        <p class="cg-hero__desc">
          Book appointments with top specialists, track your medical history,
          and access quality healthcare – all in one place.
        </p>

        <div class="cg-hero__btns d-flex flex-wrap gap-3">
          <a href="search-doctor.php" class="cg-btn-main">
            <i class="fas fa-search"></i> Find a Doctor
          </a>
          <a href="register.php" class="cg-btn-ghost">
            <i class="fas fa-user-plus"></i> Get Started
          </a>
        </div>

        <div class="cg-stats">
          <div class="cg-stat">
            <div class="cg-stat__num" data-target="<?php
            $r = $connect->query('SELECT COUNT(*) AS total FROM doctors');
            echo $r->fetch_assoc()['total'];
            ?>" data-suffix="+">0</div>
            <div class="cg-stat__label">doctors</div>
          </div>
          <div class="cg-stat">
            <div class="cg-stat__num" data-target="<?php
            $r = $connect->query('SELECT COUNT(*) AS total FROM patients');
            echo $r->fetch_assoc()['total'];
            ?>" data-suffix="+">0</div>
            <div class="cg-stat__label">Patients Served</div>
          </div>
          <div class="cg-stat">
            <div class="cg-stat__num" data-target="<?php
            $r = $connect->query('SELECT COUNT(*) AS total FROM specialization');
            echo $r->fetch_assoc()['total'];
            ?>" data-suffix="+">0</div>
            <div class="cg-stat__label">Specializations</div>
          </div>
          <div class="cg-stat">
            <div class="cg-stat__num" data-target="<?php
            $r = $connect->query('SELECT COUNT(*) AS total FROM cities');
            echo $r->fetch_assoc()['total'];
            ?>" data-suffix="">0</div>
            <div class="cg-stat__label">Cities</div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
<!-- JS -->
<script>
  (function () {
    const hero = document.querySelector('.cg-hero');
    if (!hero) return;

    // Floating dots
    const dotColors = ['rgba(6,214,160,0.55)', 'rgba(56,217,232,0.45)', 'rgba(255,255,255,0.3)'];
    for (let i = 0; i < 28; i++) {
      const d = document.createElement('div');
      d.className = 'cg-dot';
      const s = Math.random() * 5 + 2;
      d.style.cssText = `width:${s}px;height:${s}px;left:${Math.random() * 100}%;bottom:${Math.random() * 35}%;background:${dotColors[i % 3]};animation-duration:${(Math.random() * 12 + 7).toFixed(1)}s;animation-delay:${(Math.random() * 14).toFixed(1)}s`;
      hero.appendChild(d);
    }

    // Counter animation
    const counters = document.querySelectorAll('.cg-stat__num[data-target]');
    let done = false;

    function runCounters() {
      counters.forEach(el => {
        const target = parseInt(el.dataset.target) || 0;
        const suffix = el.dataset.suffix || '';
        const dur = 1800, start = performance.now();
        (function tick(now) {
          const p = Math.min((now - start) / dur, 1);
          const e = 1 - Math.pow(1 - p, 3);
          el.innerHTML = `${Math.round(target * e)}<span class="cg-sfx">${suffix}</span>`;
          if (p < 1) requestAnimationFrame(tick);
        })(start);
      });
    }

    new IntersectionObserver(entries => {
      if (entries[0].isIntersecting && !done) { done = true; runCounters(); }
    }, { threshold: 0.3 }).observe(hero);
  })();
</script>