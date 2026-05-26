<?php include "header.php"; ?>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pinterest Newsroom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,800;1,9..40,400&display=swap" rel="stylesheet" />
    <style>
      :root {
        --red: #e60023;
        --red-dark: #ad081b;
        --bg: #f8f7f5;
        --white: #fff;
        --text: #111;
        --muted: #6b6b6b;
        --border: #e8e8e8;
        --nav-h: 64px;
      }
      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
      html { scroll-behavior: smooth; }
      body {
        font-family: "DM Sans", sans-serif;
        background: var(--white);
        color: var(--text);
        overflow-x: hidden;
      }

      /* ── NAVBAR ── */
      .nr-nav {
        position: sticky;
        top: 0;
        z-index: 100;
        height: var(--nav-h);
        background: var(--white);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        padding: 0 40px;
        gap: 32px;
      }
      .nr-nav .brand {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: var(--text);
        font-weight: 700;
        font-size: 1.05rem;
        flex-shrink: 0;
      }
      .nr-nav .brand svg { width: 28px; height: 28px; fill: var(--text); }
      .nr-nav .nav-links {
        display: flex;
        gap: 4px;
        margin-left: auto;
        align-items: center;
      }
      .nr-nav .nav-links a {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text);
        text-decoration: none;
        padding: 8px 14px;
        border-radius: 20px;
        transition: background 0.15s;
        white-space: nowrap;
      }
      .nr-nav .nav-links a:hover { background: var(--bg); }
      .nr-nav .nav-search {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: var(--text);
        transition: background 0.15s;
      }
      .nr-nav .nav-search:hover { background: var(--bg); }

      /* ── FEATURED CAROUSEL ── */
      .featured-section {
        padding: 40px 0 20px;
        overflow: hidden;
      }
      .featured-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text);
        letter-spacing: 0.02em;
        padding: 0 48px;
        margin-bottom: 20px;
      }
      .featured-track-wrap {
        position: relative;
        overflow: hidden;
      }
      .featured-track {
        display: flex;
        transition: transform 0.45s cubic-bezier(0.4,0,0.2,1);
        will-change: transform;
      }
      .featured-slide {
        min-width: 100%;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 0;
        padding: 0 48px;
      }
      .slide-text {
        background: var(--white);
        border-radius: 20px;
        padding: 48px 40px;
        max-width: 420px;
      }
      .slide-text h2 {
        font-size: clamp(1.6rem, 2.5vw, 2.2rem);
        font-weight: 800;
        line-height: 1.18;
        letter-spacing: -0.01em;
        margin-bottom: 32px;
      }
      .slide-text .slide-date {
        font-size: 0.85rem;
        color: var(--muted);
      }
      .slide-collage {
        width: clamp(340px, 45vw, 700px);
        aspect-ratio: 1 / 0.72;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
      }
      .collage-grid {
        display: grid;
        width: 100%;
        height: 100%;
        gap: 4px;
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: repeat(3, 1fr);
      }
      .collage-grid .ci {
        background: #ddd;
        overflow: hidden;
        border-radius: 6px;
      }
      .collage-grid .ci img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
      }
      /* Save badge */
      .save-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--red);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        pointer-events: none;
      }
      /* Arrow button */
      .featured-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 60px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--white);
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.1rem;
        color: var(--text);
        z-index: 10;
        transition: box-shadow 0.15s;
      }
      .featured-arrow:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.15); }

      /* ── FILTERS ── */
      .filters-row {
        display: flex;
        gap: 8px;
        padding: 32px 48px 24px;
        flex-wrap: wrap;
        align-items: center;
      }
      .filter-btn {
        border: none;
        background: transparent;
        font-family: "DM Sans", sans-serif;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text);
        padding: 8px 18px;
        border-radius: 20px;
        cursor: pointer;
        transition: background 0.15s;
      }
      .filter-btn:hover { background: var(--bg); }
      .filter-btn.active {
        background: var(--text);
        color: var(--white);
      }

      /* ── NEWS GRID ── */
      .news-section { padding: 0 48px 60px; }
      .news-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px 24px;
      }
      .news-card {
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
      }
      .news-card:hover { text-decoration: none; color: inherit; }
      .news-card .card-img {
        width: 100%;
        aspect-ratio: 4/3;
        border-radius: 16px;
        overflow: hidden;
        background: var(--bg);
        margin-bottom: 16px;
        position: relative;
      }
      .news-card .card-img img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.35s ease;
      }
      .news-card:hover .card-img img { transform: scale(1.04); }
      .news-card .card-img .collage-mini {
        display: grid;
        width: 100%;
        height: 100%;
        gap: 3px;
      }
      .news-card .card-img .collage-mini .ci { background: #ddd; overflow: hidden; }
      .news-card .card-img .collage-mini .ci img { width:100%; height:100%; object-fit:cover; display:block; }
      .card-grid-3x3 { grid-template-columns: repeat(3,1fr); grid-template-rows: repeat(3,1fr); }
      .card-grid-3x2 { grid-template-columns: repeat(3,1fr); grid-template-rows: repeat(2,1fr); }

      .card-save-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--red);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
      }
      .card-plain-img { width:100%; height:100%; object-fit:cover; display:block; }

      .news-card h3 {
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 10px;
        color: var(--text);
        transition: text-decoration 0.15s;
      }
      .news-card:hover h3 { text-decoration: underline; }
      .card-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
      }
      .card-date {
        font-size: 0.8rem;
        color: var(--muted);
      }
      .card-tag {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--muted);
        background: var(--bg);
        padding: 3px 10px;
        border-radius: 20px;
      }

      /* ── LOAD MORE ── */
      .load-more-wrap {
        text-align: center;
        padding: 16px 0 40px;
      }
      .btn-load {
        border: 2px solid var(--text);
        background: transparent;
        font-family: "DM Sans", sans-serif;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text);
        padding: 12px 32px;
        border-radius: 30px;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
      }
      .btn-load:hover { background: var(--text); color: var(--white); }

      /* ── CEO SECTION ── */
      .ceo-section {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 60px;
        padding: 80px 48px;
        border-top: 1px solid var(--border);
      }
      .ceo-photo {
        width: 420px;
        aspect-ratio: 3/4;
        border-radius: 20px;
        overflow: hidden;
        background: var(--bg);
        flex-shrink: 0;
      }
      .ceo-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
      .ceo-text h2 {
        font-size: clamp(2rem, 3vw, 2.8rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 20px;
      }
      .ceo-text p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--muted);
        max-width: 540px;
        margin-bottom: 28px;
      }
      .btn-outline-pill {
        border: 2px solid var(--text);
        background: transparent;
        font-family: "DM Sans", sans-serif;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text);
        padding: 10px 26px;
        border-radius: 30px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background 0.15s, color 0.15s;
      }
      .btn-outline-pill:hover { background: var(--text); color: var(--white); }

      /* ── MORE SECTION ── */
      .more-section {
        border-top: 1px solid var(--border);
        padding: 80px 48px;
        text-align: center;
      }
      .more-section h2 {
        font-size: clamp(2rem, 3.5vw, 3rem);
        font-weight: 800;
        margin-bottom: 48px;
      }
      .more-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
      }
      .more-card {
        background: var(--bg);
        border-radius: 20px;
        aspect-ratio: 3/2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        color: var(--text);
        transition: background 0.15s;
      }
      .more-card:hover { background: #eee; }
      .more-card i { font-size: 1.6rem; }
      .more-card span {
        font-size: 0.95rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
      }

      /* ── FOOTER ── */
      .nr-footer {
        border-top: 1px solid var(--border);
        padding: 60px 48px 40px;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 60px;
      }
      .footer-logo {
        font-family: "DM Sans", sans-serif;
        font-size: 1.6rem;
        font-weight: 800;
        font-style: italic;
        color: var(--text);
        letter-spacing: -0.02em;
      }
      .footer-logo i { color: var(--red); }
      .lang-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--border);
        background: transparent;
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 0.82rem;
        cursor: pointer;
        margin-top: 16px;
        font-family: "DM Sans", sans-serif;
        color: var(--text);
      }
      .footer-cols {
        display: grid;
        grid-template-columns: repeat(3, auto);
        gap: 40px;
        justify-content: end;
      }
      .footer-col h6 {
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 16px;
        color: var(--text);
      }
      .footer-col a {
        display: block;
        font-size: 0.85rem;
        color: var(--text);
        text-decoration: none;
        margin-bottom: 10px;
        transition: color 0.15s;
      }
      .footer-col a:hover { color: var(--red); }
      .footer-bottom {
        border-top: 1px solid var(--border);
        padding: 20px 48px;
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        align-items: center;
      }
      .footer-bottom a, .footer-bottom span {
        font-size: 0.78rem;
        color: var(--muted);
        text-decoration: none;
      }
      .footer-bottom a:hover { text-decoration: underline; }

      /* Collage color swatches for placeholder blocks */
      .c1 { background: #c8a898; }
      .c2 { background: #b8c4b0; }
      .c3 { background: #d4b8a0; }
      .c4 { background: #a8b4c0; }
      .c5 { background: #c0b8d0; }
      .c6 { background: #d8c0b0; }
      .c7 { background: #b0c8c0; }
      .c8 { background: #c8c0a8; }
      .c9 { background: #e8d0c0; }

      /* hidden filtered cards */
      .news-card[data-cat].hidden { display: none; }

      @media (max-width: 1024px) {
        .featured-slide { grid-template-columns: 1fr; padding: 0 24px; }
        .news-grid { grid-template-columns: repeat(2,1fr); }
        .more-cards { grid-template-columns: repeat(3,1fr); }
        .ceo-section { grid-template-columns: 1fr; }
        .ceo-photo { width: 100%; max-width: 400px; aspect-ratio: 4/3; }
      }
      @media (max-width: 640px) {
        .nr-nav { padding: 0 16px; gap: 12px; }
        .nr-nav .nav-links { gap: 0; }
        .nr-nav .nav-links a { padding: 8px 8px; font-size: 0.82rem; }
        .featured-label, .filters-row, .news-section, .ceo-section, .more-section, .nr-footer, .footer-bottom { padding-left: 16px; padding-right: 16px; }
        .news-grid { grid-template-columns: 1fr; }
        .more-cards { grid-template-columns: 1fr; }
        .footer-cols { grid-template-columns: repeat(2,auto); }
        .nr-footer { grid-template-columns: 1fr; gap: 32px; }
      }
    </style>
  </head>
  <body>

    <!-- NAVBAR -->
    <nav class="nr-nav">
      <a href="news.php" class="brand">
        <!-- Pinterest P logo -->
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
        </svg>
        Newsroom
      </a>
      <div class="nav-links">
        <a href="#">Company</a>
        <a href="#">Impact</a>
        <a href="#">Press assets</a>
        <a href="#">Contact</a>
        <button class="nav-search" aria-label="Search"><i class="bi bi-search"></i></button>
      </div>
    </nav>

    <!-- FEATURED NEWS CAROUSEL -->
    <section class="featured-section">
      <p class="featured-label">Featured news</p>
      <div class="featured-track-wrap">
        <div class="featured-track" id="featuredTrack">

          <!-- Slide 1: Wedding Trends -->
          <div class="featured-slide">
            <div class="slide-text">
              <h2>Pinterest Wedding Trends Report 2026: Maximum romance, modern individuality</h2>
              <p class="slide-date">28 April 2026</p>
            </div>
            <div class="slide-collage">
              <div class="collage-grid" style="grid-template-columns:repeat(5,1fr);grid-template-rows:repeat(3,1fr);">
                <div class="ci c1"></div>
                <div class="ci c2"></div>
                <div class="ci c3" style="grid-column:span 2;"></div>
                <div class="ci c4"></div>
                <div class="ci c5" style="grid-row:span 2;"></div>
                <div class="ci c6"></div>
                <div class="ci c7" style="position:relative;"><span class="save-badge">Save</span></div>
                <div class="ci c8"></div>
                <div class="ci c9" style="grid-row:span 2;"></div>
                <div class="ci c1"></div>
                <div class="ci c3"></div>
                <div class="ci c5"></div>
                <div class="ci c2"></div>
              </div>
            </div>
            <div></div><!-- spacer -->
          </div>

          <!-- Slide 2: Spring Trend -->
          <div class="featured-slide">
            <div></div><!-- spacer -->
            <div class="slide-collage">
              <div class="collage-grid" style="grid-template-columns:repeat(5,1fr);grid-template-rows:repeat(3,1fr);">
                <div class="ci c7"></div>
                <div class="ci c2" style="grid-column:span 2;"></div>
                <div class="ci c3"></div>
                <div class="ci c4"></div>
                <div class="ci c5"></div>
                <div class="ci c8" style="position:relative;"><span class="save-badge">Save</span></div>
                <div class="ci c1"></div>
                <div class="ci c6"></div>
                <div class="ci c9"></div>
                <div class="ci c3"></div>
                <div class="ci c2"></div>
                <div class="ci c7"></div>
                <div class="ci c4"></div>
                <div class="ci c5"></div>
              </div>
            </div>
            <div class="slide-text">
              <h2>Pinterest Spring Trend Report 2026: Personalisation is in bloom</h2>
              <p class="slide-date">17 March 2026</p>
            </div>
          </div>

        </div>
        <!-- Next Arrow -->
        <button class="featured-arrow" id="featuredArrow" onclick="advanceSlide()" aria-label="Next slide">
          <i class="bi bi-arrow-right"></i>
        </button>
      </div>
    </section>

    <!-- FILTER TABS -->
    <div class="filters-row">
      <button class="filter-btn active" data-filter="all" onclick="filterNews(this,'all')">All</button>
      <button class="filter-btn" data-filter="ads" onclick="filterNews(this,'ads')">Ads</button>
      <button class="filter-btn" data-filter="company" onclick="filterNews(this,'company')">Company</button>
      <button class="filter-btn" data-filter="creators" onclick="filterNews(this,'creators')">Creators</button>
      <button class="filter-btn" data-filter="partnerships" onclick="filterNews(this,'partnerships')">Partnerships</button>
      <button class="filter-btn" data-filter="product" onclick="filterNews(this,'product')">Product</button>
      <button class="filter-btn" data-filter="trust" onclick="filterNews(this,'trust')">Trust and safety</button>
      <button class="filter-btn" data-filter="trends" onclick="filterNews(this,'trends')">Trends</button>
    </div>

    <!-- NEWS GRID -->
    <section class="news-section">
      <div class="news-grid" id="newsGrid">

        <!-- Card 1 -->
        <a class="news-card" href="#" data-cat="ads company">
          <div class="card-img">
            <div style="width:100%;height:100%;background:linear-gradient(135deg,#d4c8b8 0%,#b8c4a8 100%);display:flex;align-items:center;justify-content:center;">
              <div style="width:80%;height:80%;background:rgba(255,255,255,0.3);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:3rem;opacity:0.5;">🌸</div>
            </div>
          </div>
          <h3>Pinterest's latest campaign wants you to live your life, not just scroll it.</h3>
          <div class="card-meta">
            <span class="card-date">1 May 2026</span>
            <span class="card-tag">Ads</span>
            <span class="card-tag">Company</span>
          </div>
        </a>

        <!-- Card 2 – collage -->
        <a class="news-card" href="#" data-cat="trends">
          <div class="card-img">
            <div class="collage-mini card-grid-3x3" style="padding:3px;border-radius:16px;background:#111;">
              <div class="ci c1"></div><div class="ci c3"></div><div class="ci c4"></div>
              <div class="ci c5"></div><div class="ci c7" style="position:relative;"><span class="card-save-badge">Save</span></div><div class="ci c8"></div>
              <div class="ci c2"></div><div class="ci c6"></div><div class="ci c9"></div>
            </div>
          </div>
          <h3>Pinterest Wedding Trends Report 2026: Maximum romance, modern individuality</h3>
          <div class="card-meta">
            <span class="card-date">28 April 2026</span>
            <span class="card-tag">Trends</span>
          </div>
        </a>

        <!-- Card 3 -->
        <a class="news-card" href="#" data-cat="creators company">
          <div class="card-img">
            <div style="width:100%;height:100%;background:linear-gradient(135deg,#f0e8d8,#e0d0c0);display:flex;flex-direction:column;align-items:flex-start;padding:16px;gap:8px;">
              <div style="background:#fff;border-radius:30px;padding:10px 20px;font-size:0.85rem;font-weight:700;display:flex;align-items:center;gap:8px;box-shadow:0 2px 12px rgba(0,0,0,0.1);">
                <span style="color:#e60023;font-weight:900;font-size:1rem;">P</span>
                Bring My <span style="color:#e60023;">Pinterest</span> to Life
              </div>
            </div>
          </div>
          <h3>With a new shoppable streaming series, people bring their Pinterest boards to life</h3>
          <div class="card-meta">
            <span class="card-date">23 March 2026</span>
            <span class="card-tag">Creators</span>
            <span class="card-tag">Company</span>
          </div>
        </a>

        <!-- Card 4 – collage spring -->
        <a class="news-card" href="#" data-cat="trends">
          <div class="card-img">
            <div class="collage-mini card-grid-3x3" style="padding:3px;border-radius:16px;background:#f0eeeb;">
              <div class="ci c7"></div><div class="ci c2"></div><div class="ci c3"></div>
              <div class="ci c5"></div><div class="ci c8" style="position:relative;"><span class="card-save-badge">Save</span></div><div class="ci c1"></div>
              <div class="ci c4"></div><div class="ci c6"></div><div class="ci c9"></div>
            </div>
          </div>
          <h3>Pinterest Spring Trend Report 2026: Personalisation is in bloom</h3>
          <div class="card-meta">
            <span class="card-date">17 March 2026</span>
            <span class="card-tag">Trends</span>
          </div>
        </a>

        <!-- Card 5 – collage parenting -->
        <a class="news-card" href="#" data-cat="trends">
          <div class="card-img">
            <div class="collage-mini card-grid-3x3" style="padding:3px;border-radius:16px;background:#f5f0e8;">
              <div class="ci c3"></div><div class="ci c8"></div><div class="ci c4"></div>
              <div class="ci c1"></div><div class="ci c7" style="position:relative;"><span class="card-save-badge">Save</span></div><div class="ci c2"></div>
              <div class="ci c9"></div><div class="ci c5"></div><div class="ci c6"></div>
            </div>
          </div>
          <h3>Pinterest Parenting Trend Report 2026: Raising screen-smart kids who seek real-world adventure</h3>
          <div class="card-meta">
            <span class="card-date">24 February 2026</span>
            <span class="card-tag">Trends</span>
          </div>
        </a>

        <!-- Card 6 – Pinterest Palette -->
        <a class="news-card" href="#" data-cat="trends">
          <div class="card-img">
            <div style="width:100%;height:100%;background:#f8f6f0;display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:16px;align-items:center;">
              <div>
                <div style="color:#e60023;font-size:1.1rem;font-weight:900;margin-bottom:4px;">P</div>
                <div style="font-size:0.9rem;font-weight:800;line-height:1.2;">Pinterest<br>Palette<br>2026</div>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;">
                <div style="background:#4a90d9;border-radius:50%;aspect-ratio:1;"></div>
                <div style="background:#e07b39;border-radius:50%;aspect-ratio:1;"></div>
                <div style="background:#2d5a27;border-radius:50%;aspect-ratio:1;"></div>
                <div style="background:#8b1a1a;border-radius:50%;aspect-ratio:1;"></div>
              </div>
            </div>
          </div>
          <h3>From Cool Blue to Persimmon: Meet the 2026 Pinterest Palette™</h3>
          <div class="card-meta">
            <span class="card-date">14 January 2026</span>
            <span class="card-tag">Trends</span>
          </div>
        </a>

        <!-- Card 7 – Predicts -->
        <a class="news-card" href="#" data-cat="trends partnerships">
          <div class="card-img">
            <div style="width:100%;height:100%;background:#111;display:grid;grid-template-columns:repeat(3,1fr);gap:2px;padding:2px;border-radius:16px;">
              <div style="background:#e8c0d0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.5rem;color:#fff;font-weight:700;padding:4px;text-align:center;">EMME GUMMY</div>
              <div style="background:#c0a8d8;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.55rem;color:#fff;font-weight:700;padding:4px;text-align:center;">Dario Romantic</div>
              <div style="background:#a8c0e0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.5rem;color:#111;font-weight:700;padding:4px;text-align:center;">COOL BLUE</div>
              <div style="background:#1a1a2e;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.5rem;color:#fff;font-weight:700;padding:2px;text-align:center;grid-column:span 1;"><span style="color:#e60023;">P</span>&nbsp;Pinterest<br>Predicts<br>2026</div>
              <div style="background:#d4b8f0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.5rem;color:#111;font-weight:700;padding:4px;text-align:center;">NeaDeco</div>
              <div style="background:#f0e0c8;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.5rem;color:#111;font-weight:700;padding:4px;text-align:center;">BROOCHED</div>
              <div style="background:#c8e0d8;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.5rem;color:#111;font-weight:700;padding:4px;text-align:center;">Pen Pals</div>
              <div style="background:#e0c8d0;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.5rem;color:#111;font-weight:700;padding:4px;text-align:center;">GABBAGE CRUSH</div>
              <div style="background:#d0d8e0;border-radius:4px;"></div>
            </div>
          </div>
          <h3>Pinterest Predicts™: Nonconformity, self-preservation, and escapism drive 21 trends for 2026</h3>
          <div class="card-meta">
            <span class="card-date">9 December 2025</span>
            <span class="card-tag">Trends</span>
            <span class="card-tag">Partnerships</span>
          </div>
        </a>

        <!-- Card 8 – Ireland Gender Pay -->
        <a class="news-card" href="#" data-cat="company">
          <div class="card-img" style="background:#e8f4f8;">
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
              <div style="width:70px;height:70px;background:var(--red);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <svg viewBox="0 0 24 24" width="36" height="36" fill="#fff"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
              </div>
            </div>
          </div>
          <h3>Our 2024/2025 Ireland Gender Pay Gap Report</h3>
          <div class="card-meta">
            <span class="card-date">28 November 2025</span>
            <span class="card-tag">Company</span>
          </div>
        </a>

        <!-- Card 9 – Festive -->
        <a class="news-card" href="#" data-cat="ads creators trends">
          <div class="card-img">
            <div style="width:100%;height:100%;background:#8b1a1a;border-radius:16px;padding:16px;position:relative;overflow:hidden;">
              <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(135deg,rgba(180,0,0,0.8),rgba(80,0,0,0.9));"></div>
              <div style="position:relative;z-index:1;">
                <div style="display:flex;gap:8px;margin-bottom:12px;">
                  <span style="background:rgba(255,255,255,0.2);border-radius:20px;padding:4px 10px;font-size:0.7rem;color:#fff;font-weight:600;">🏆 Trendsetter Gifts</span>
                  <span style="background:rgba(255,255,255,0.2);border-radius:20px;padding:4px 10px;font-size:0.7rem;color:#fff;font-weight:600;">✏️ Editor's Picks</span>
                </div>
                <div style="font-size:1.3rem;font-weight:900;color:#fff;line-height:1.2;">Shop the best<br>gift ideas on<br><span style="color:#e60023;">●</span> Pinterest</div>
              </div>
            </div>
          </div>
          <h3>Pinterest's Festive Season Edit brings precision to festive shopping</h3>
          <div class="card-meta">
            <span class="card-date">3 November 2025</span>
            <span class="card-tag">Ads</span>
            <span class="card-tag">Creators</span>
            <span class="card-tag">Trends</span>
          </div>
        </a>

      </div>

      <!-- Load More -->
      <div class="load-more-wrap">
        <button class="btn-load" id="loadMoreBtn" onclick="loadMore()">Load more</button>
      </div>
    </section>

    <!-- CEO SECTION -->
    <section class="ceo-section">
      <div class="ceo-photo">
        <div style="width:100%;height:100%;background:linear-gradient(160deg,#e8e0d8 0%,#c8bdb5 100%);display:flex;align-items:flex-start;justify-content:center;padding-top:30px;">
          <div style="width:80%;height:80%;background:rgba(150,130,120,0.3);border-radius:50% 50% 0 0;display:flex;align-items:center;justify-content:center;font-size:4rem;opacity:0.4;">👤</div>
        </div>
      </div>
      <div class="ceo-text">
        <h2>A word from our CEO</h2>
        <p>Pinterest CEO Bill Ready shares why making the phone a 'one-stop shop' for secure, private and effective age verification is essential to protect teens online.</p>
        <a href="#" class="btn-outline-pill">Read more</a>
      </div>
    </section>

    <!-- LOOKING FOR SOMETHING ELSE -->
    <section class="more-section">
      <h2>Looking for something else?</h2>
      <div class="more-cards">
        <a href="#" class="more-card">
          <i class="bi bi-briefcase" style="font-size:1.8rem;"></i>
          <span>For businesses <i class="bi bi-arrow-up-right" style="font-size:0.8rem;"></i></span>
        </a>
        <a href="#" class="more-card">
          <i class="bi bi-scissors" style="font-size:1.8rem;"></i>
          <span>For creators <i class="bi bi-arrow-up-right" style="font-size:0.8rem;"></i></span>
        </a>
        <a href="#" class="more-card">
          <i class="bi bi-graph-up" style="font-size:1.8rem;"></i>
          <span>For investors <i class="bi bi-arrow-up-right" style="font-size:0.8rem;"></i></span>
        </a>
      </div>
    </section>

    <!-- FOOTER -->
    <footer>
      <div class="nr-footer">
        <div>
          <div class="footer-logo"><i class="bi bi-pinterest"></i> Pinterest</div>
          <button class="lang-btn">
            <i class="bi bi-globe2"></i> English (UK) <i class="bi bi-chevron-down"></i>
          </button>
        </div>
        <div class="footer-cols">
          <div class="footer-col">
            <h6>Quick links</h6>
            <a href="#">Press assets</a>
            <a href="#">Subscribe via RSS</a>
            <a href="#">Contact us</a>
          </div>
          <div class="footer-col">
            <h6>Company</h6>
            <a href="#">About Pinterest</a>
            <a href="#">Careers</a>
            <a href="#">Investors</a>
          </div>
          <div class="footer-col">
            <h6>More from Pinterest</h6>
            <a href="#">Help Centre</a>
            <a href="#">Businesses</a>
            <a href="#">Creators</a>
            <a href="#">Developers</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 Pinterest</span>
        <a href="#">Copyright and Trademark</a>
        <a href="#">Terms of Service</a>
        <a href="#">Privacy and Cookies</a>
        <a href="#">Cookie preferences</a>
        <a href="#">Personalised ads</a>
        <a href="#">Pinterest status</a>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // ── CAROUSEL ──
      let currentSlide = 0;
      const totalSlides = 2;
      const track = document.getElementById('featuredTrack');
      const arrow = document.getElementById('featuredArrow');

      function advanceSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
        // Flip arrow direction on last slide
        arrow.style.right = currentSlide === totalSlides - 1 ? 'auto' : '60px';
        arrow.style.left  = currentSlide === totalSlides - 1 ? '60px'  : 'auto';
        arrow.querySelector('i').className = currentSlide === totalSlides - 1 ? 'bi bi-arrow-left' : 'bi bi-arrow-right';
        if (currentSlide === 0) {
          arrow.style.right = '60px'; arrow.style.left = 'auto';
          arrow.querySelector('i').className = 'bi bi-arrow-right';
        }
      }

      // ── FILTER ──
      function filterNews(btn, cat) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.news-card').forEach(card => {
          const cats = (card.dataset.cat || '').split(' ');
          if (cat === 'all' || cats.includes(cat)) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      }

      // ── LOAD MORE ──
      const extraCards = [
        {
          title: "How Pinterest is helping people discover the world around them",
          date: "18 October 2025",
          cat: "product",
          tags: ["Product"],
          bg: "linear-gradient(135deg,#d0e8f0,#b8d4e8)"
        },
        {
          title: "Pinterest's 2025 Global Sustainability Report",
          date: "5 September 2025",
          cat: "company",
          tags: ["Company"],
          bg: "linear-gradient(135deg,#d8e8d0,#b8d4b0)"
        },
        {
          title: "New shopping features make it easier to buy what you love",
          date: "22 August 2025",
          cat: "product ads",
          tags: ["Product", "Ads"],
          bg: "linear-gradient(135deg,#f0e0d8,#e0c8c0)"
        }
      ];
      let loadCount = 0;

      function loadMore() {
        if (loadCount >= extraCards.length) {
          document.getElementById('loadMoreBtn').textContent = 'No more articles';
          document.getElementById('loadMoreBtn').disabled = true;
          return;
        }
        const grid = document.getElementById('newsGrid');
        const data = extraCards[loadCount];
        const tagsHtml = data.tags.map(t => `<span class="card-tag">${t}</span>`).join('');
        const card = document.createElement('a');
        card.className = 'news-card'; card.href = '#'; card.dataset.cat = data.cat;
        card.innerHTML = `
          <div class="card-img">
            <div style="width:100%;height:100%;background:${data.bg};border-radius:16px;"></div>
          </div>
          <h3>${data.title}</h3>
          <div class="card-meta">
            <span class="card-date">${data.date}</span>
            ${tagsHtml}
          </div>`;
        grid.appendChild(card);
        loadCount++;
        if (loadCount >= extraCards.length) {
          document.getElementById('loadMoreBtn').textContent = 'No more articles';
          document.getElementById('loadMoreBtn').disabled = true;
        }
      }
    </script>
  </body>
<?php include "footer.php"; ?>