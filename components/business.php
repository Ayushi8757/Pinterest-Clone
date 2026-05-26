<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pinterest Business – Grow your business on Pinterest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@800;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
      :root {
        --red: #e60023;
        --red-dark: #ad081b;
        --text: #111;
        --muted: #767676;
        --sbg: #f0eeeb;
        --nav-h: 64px;
      }
      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
      html { scroll-behavior: smooth; }
      body { font-family: "DM Sans", sans-serif; background: #fff; color: var(--text); overflow-x: hidden; }

      /* ── NAV ── */
      .bnav {
        position: sticky; top: 0; z-index: 1000;
        height: var(--nav-h);
        background: #fff;
        border-bottom: 1px solid #e8e8e8;
        display: flex; align-items: center;
        padding: 0 40px; gap: 32px;
      }
      .bnav .b-logo {
        display: flex; align-items: center; gap: 8px;
        text-decoration: none; color: var(--text); font-weight: 700; font-size: 1rem;
        flex-shrink: 0;
      }
      .bnav .b-logo svg { width: 32px; height: 32px; fill: var(--red); }
      .bnav .b-logo span { font-weight: 700; font-size: 1rem; }
      .bnav .nav-links { display: flex; gap: 4px; }
      .bnav .nav-links a {
        font-size: 0.9rem; font-weight: 500; color: var(--text);
        text-decoration: none; padding: 8px 14px; border-radius: 20px;
        transition: background 0.2s; white-space: nowrap;
      }
      .bnav .nav-links a:hover { background: var(--sbg); }
      .bnav .nav-right { margin-left: auto; display: flex; gap: 8px; align-items: center; }
      .btn-login-b {
        border: 2px solid var(--text); border-radius: 24px;
        padding: 8px 22px; font-weight: 700; font-size: 0.88rem;
        background: transparent; color: var(--text); cursor: pointer;
        text-decoration: none; transition: background 0.2s;
        font-family: "DM Sans", sans-serif;
      }
      .btn-login-b:hover { background: var(--sbg); }
      .btn-signup-b {
        background: var(--text); color: #fff; border: none; border-radius: 24px;
        padding: 10px 22px; font-weight: 700; font-size: 0.88rem; cursor: pointer;
        text-decoration: none; transition: background 0.2s;
        font-family: "DM Sans", sans-serif;
      }
      .btn-signup-b:hover { background: #333; }

      /* ── ANNOUNCEMENT BAR ── */
      .ann-bar {
        background: #111; color: #fff;
        text-align: center; padding: 12px 20px;
        font-size: 0.88rem;
      }
      .ann-bar a { color: #fff; font-weight: 700; text-decoration: none; }
      .ann-bar a:hover { text-decoration: underline; }

      /* ── HERO ── */
      .b-hero {
        text-align: center;
        padding: 80px 24px 60px;
        max-width: 780px; margin: 0 auto;
      }
      .b-hero h1 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(2.4rem, 5vw, 3.8rem); line-height: 1.1;
        margin-bottom: 28px;
      }
      .b-hero p {
        color: var(--muted); font-size: 1rem; line-height: 1.7;
        max-width: 580px; margin: 0 auto 36px;
      }
      .b-hero-btns { display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; }
      .btn-bsignup {
        background: var(--text); color: #fff; border: none; border-radius: 24px;
        padding: 14px 30px; font-weight: 700; font-size: 0.95rem; cursor: pointer;
        font-family: "DM Sans", sans-serif; text-decoration: none;
        transition: background 0.2s;
      }
      .btn-bsignup:hover { background: #333; color: #fff; }
      .btn-brequest {
        border: 2px solid var(--text); border-radius: 24px;
        padding: 12px 28px; font-weight: 700; font-size: 0.95rem;
        background: transparent; color: var(--text); cursor: pointer;
        text-decoration: none; font-family: "DM Sans", sans-serif;
        transition: background 0.2s;
      }
      .btn-brequest:hover { background: var(--sbg); }

      /* ── MASONRY COLLAGE ── */
      .b-collage {
        display: grid;
        grid-template-columns: 1fr 1.8fr 1.8fr 1.8fr 1fr;
        gap: 8px; padding: 0 8px 60px;
        max-width: 1200px; margin: 0 auto;
        align-items: center;
      }
      .b-collage .col-pin {
        display: flex; flex-direction: column; gap: 8px;
      }
      .b-collage .col-pin img {
        width: 100%; border-radius: 14px;
        object-fit: cover; display: block;
      }
      .perf-card {
        background: #fff; border-radius: 16px;
        padding: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.1);
        margin-bottom: 8px;
      }
      .perf-card .pc-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 12px; }
      .perf-card .pc-line {
        height: 2px; background: #e8e8e8; border-radius: 2px;
        position: relative; margin-bottom: 6px;
      }
      .perf-card .pc-line::after {
        content: ''; position: absolute; left: 0; top: 0;
        width: 70%; height: 100%; background: #4caf50; border-radius: 2px;
      }
      .perf-card .pc-label { font-size: 0.75rem; color: var(--muted); }

      /* ── SECTION DIVIDER ── */
      .b-section { padding: 80px 24px; }
      .b-section.alt { background: var(--sbg); }
      .b-section .sec-inner {
        max-width: 1080px; margin: 0 auto;
        display: flex; align-items: center; gap: 60px;
      }
      .b-section .sec-inner.rev { flex-direction: row-reverse; }
      .b-section .sec-txt { flex: 1; }
      .b-section .sec-txt h2 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(1.6rem, 3vw, 2.4rem); margin-bottom: 20px;
        line-height: 1.2;
      }
      .b-section .sec-txt p {
        color: var(--muted); font-size: 0.95rem; line-height: 1.7;
        margin-bottom: 28px; max-width: 440px;
      }
      .b-section .sec-txt a {
        color: var(--text); text-decoration: underline;
      }
      .b-section .sec-img { flex: 1; display: flex; justify-content: center; }
      .b-section .sec-img img {
        width: 100%; max-width: 480px; border-radius: 18px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
      }
      .btn-sec {
        display: inline-block; background: var(--text); color: #fff; border: none;
        border-radius: 24px; padding: 12px 26px; font-weight: 700; font-size: 0.92rem;
        cursor: pointer; text-decoration: none; font-family: "DM Sans", sans-serif;
        transition: background 0.2s; margin-right: 12px; margin-bottom: 8px;
      }
      .btn-sec:hover { background: #333; color: #fff; }
      .btn-sec.outline {
        background: transparent; color: var(--text);
        border: 2px solid var(--text);
      }
      .btn-sec.outline:hover { background: var(--sbg); }

      /* ── AD GOALS TABS ── */
      .goals-section { padding: 80px 24px; background: var(--sbg); }
      .goals-section h2 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(1.6rem, 3vw, 2.2rem);
        text-align: center; margin-bottom: 40px;
      }
      .goals-tabs {
        display: flex; justify-content: center; gap: 8px;
        margin-bottom: 36px; flex-wrap: wrap;
      }
      .gtab {
        border: 2px solid #ddd; border-radius: 24px;
        padding: 10px 22px; font-weight: 700; font-size: 0.88rem;
        background: #fff; cursor: pointer; font-family: "DM Sans", sans-serif;
        transition: all 0.2s;
      }
      .gtab.on {
        background: var(--text); color: #fff; border-color: var(--text);
      }
      .gtab:hover:not(.on) { background: var(--sbg); }
      .goals-content {
        max-width: 860px; margin: 0 auto;
        background: #fff; border-radius: 20px;
        padding: 36px; display: flex; gap: 36px; align-items: center;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
      }
      .goals-content .gc-img { flex: 1; }
      .goals-content .gc-img img {
        width: 100%; border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.13);
      }
      .goals-content .gc-txt { flex: 1; }
      .goals-content .gc-txt h3 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: 1.3rem; margin-bottom: 12px;
      }
      .goals-content .gc-txt p {
        color: var(--muted); font-size: 0.9rem; line-height: 1.65;
      }

      /* ── BRANDS MARQUEE ── */
      .brands-section { padding: 60px 0; overflow: hidden; }
      .brands-section h2 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(1.4rem, 2.5vw, 1.9rem);
        text-align: center; margin-bottom: 8px;
      }
      .brands-section p {
        color: var(--muted); text-align: center; font-size: 0.92rem;
        max-width: 500px; margin: 0 auto 28px;
      }
      .brands-cta { text-align: center; margin-bottom: 32px; }
      .marquee-wrap { overflow: hidden; width: 100%; }
      .marquee-track {
        display: flex; gap: 48px; align-items: center;
        animation: marquee 28s linear infinite; width: max-content;
      }
      .marquee-track img { height: 28px; filter: grayscale(1); opacity: 0.6; }
      @keyframes marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
      }

      /* ── PINTEREST PALETTE ── */
      .palette-section {
        padding: 80px 24px;
        display: flex; align-items: center; justify-content: center; gap: 60px;
        max-width: 1080px; margin: 0 auto; flex-wrap: wrap;
      }
      .palette-txt { max-width: 360px; }
      .palette-txt .tag {
        font-size: 0.78rem; color: var(--muted); margin-bottom: 8px;
        text-transform: uppercase; letter-spacing: 0.06em;
      }
      .palette-txt h2 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(1.5rem, 2.5vw, 2rem); margin-bottom: 20px;
        line-height: 1.2;
      }
      .palette-collage {
        position: relative; width: 380px; height: 380px; flex-shrink: 0;
      }
      .pal-img {
        position: absolute; border-radius: 14px;
        box-shadow: 0 6px 22px rgba(0,0,0,0.14);
        overflow: hidden;
      }
      .pal-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
      .pal-dot {
        position: absolute; border-radius: 50%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      }

      /* ── BUSINESS ACCOUNT CTA ── */
      .biz-cta-section {
        padding: 60px 24px;
        display: flex; align-items: center; gap: 48px;
        max-width: 900px; margin: 0 auto; flex-wrap: wrap;
      }
      .biz-cta-img {
        width: 220px; border-radius: 18px; overflow: hidden;
        box-shadow: 0 8px 28px rgba(0,0,0,0.13); flex-shrink: 0;
        position: relative;
      }
      .biz-cta-img img { width: 100%; display: block; }
      .promote-badge {
        position: absolute; bottom: 12px; right: 12px;
        background: var(--red); color: #fff;
        padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;
      }
      .biz-cta-txt { flex: 1; min-width: 240px; }
      .biz-cta-txt h2 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(1.4rem, 2.5vw, 1.9rem); margin-bottom: 14px;
      }
      .biz-cta-txt p { color: var(--muted); font-size: 0.92rem; margin-bottom: 22px; line-height: 1.6; }

      /* ── RESOURCES ── */
      .resources-section { padding: 60px 24px; background: var(--sbg); }
      .resources-section h2 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(1.4rem, 2.5vw, 1.9rem);
        text-align: center; margin-bottom: 36px;
      }
      .res-grid {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 24px; max-width: 920px; margin: 0 auto;
      }
      .res-card { background: #fff; border-radius: 16px; padding: 28px 24px; }
      .res-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; margin-bottom: 16px;
      }
      .res-card h4 { font-weight: 700; font-size: 0.95rem; margin-bottom: 8px; }
      .res-card p { color: var(--muted); font-size: 0.86rem; line-height: 1.6; margin-bottom: 14px; }
      .res-card a { font-size: 0.86rem; font-weight: 600; color: var(--text); }

      /* ── GET STARTED CTA ── */
      .getstarted-section { text-align: center; padding: 60px 24px; }
      .getstarted-section h2 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(1.4rem, 2.5vw, 1.9rem); margin-bottom: 24px;
      }

      /* ── FAQ ── */
      .faq-section { padding: 60px 24px; max-width: 680px; margin: 0 auto; }
      .faq-section h2 {
        font-family: "Nunito", sans-serif; font-weight: 900;
        font-size: clamp(1.4rem, 2.5vw, 1.9rem); margin-bottom: 28px;
        text-align: center;
      }
      .faq-item { border-top: 1px solid #e8e8e8; }
      .faq-item:last-child { border-bottom: 1px solid #e8e8e8; }
      .faq-q {
        width: 100%; text-align: left; background: none; border: none;
        padding: 18px 0; font-size: 0.93rem; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; justify-content: space-between;
        font-family: "DM Sans", sans-serif; color: var(--text);
      }
      .faq-a { display: none; padding: 0 0 16px; color: var(--muted); font-size: 0.88rem; line-height: 1.65; }
      .faq-item.open .faq-a { display: block; }
      .faq-item.open .faq-icon { transform: rotate(45deg); }
      .faq-icon { transition: transform 0.2s; font-size: 1.2rem; }

      /* ── FOOTER ── */
      .b-footer {
        background: #fff; border-top: 1px solid #e8e8e8;
        padding: 40px 80px 28px;
      }
      .bf-logo {
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 28px; text-decoration: none; color: var(--text);
      }
      .bf-logo svg { width: 28px; height: 28px; fill: var(--red); }
      .bf-logo span { font-weight: 700; font-size: 1rem; }
      .bf-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 24px; margin-bottom: 28px;
      }
      .bf-col h6 {
        font-size: 0.76rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--muted); margin-bottom: 12px;
      }
      .bf-col a {
        display: block; color: #555; font-size: 0.85rem;
        text-decoration: none; margin-bottom: 8px; transition: color 0.2s;
      }
      .bf-col a:hover { color: var(--text); }
      .bf-bot {
        border-top: 1px solid #e8e8e8; padding-top: 16px;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;
      }
      .bf-bot span { font-size: 0.78rem; color: var(--muted); }
      .bf-bot select {
        border: 1px solid #ddd; border-radius: 8px; padding: 4px 8px;
        font-size: 0.78rem; background: #fff; cursor: pointer;
      }

      /* Fade animations */
      .fade-up { opacity: 0; transform: translateY(28px); transition: opacity 0.6s, transform 0.6s; }
      .fade-up.vis { opacity: 1; transform: translateY(0); }

      @media (max-width: 900px) {
        .b-section .sec-inner, .b-section .sec-inner.rev { flex-direction: column; }
        .b-collage { grid-template-columns: 1fr 1fr 1fr; }
        .b-collage .col-pin:first-child, .b-collage .col-pin:last-child { display: none; }
        .goals-content { flex-direction: column; }
        .res-grid { grid-template-columns: 1fr; }
        .bf-grid { grid-template-columns: 1fr 1fr; }
        .b-footer { padding: 32px 20px 20px; }
        .bnav { padding: 0 16px; gap: 8px; }
        .bnav .nav-links { display: none; }
      }
    </style>
  </head>
  <body>

    <!-- ── ANNOUNCEMENT BAR ── -->
    <div class="ann-bar">
      Pinterest Predicts™ is here: See the top trends for 2026.
      <a href="#" target="_blank">Explore trends →</a>
    </div>

    <!-- ── NAVBAR ── -->
    <nav class="bnav">
      <a href="business.php" class="b-logo">
        <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
        <span>Business</span>
      </a>
      <div class="nav-links">
        <a href="#">About Pinterest</a>
        <a href="#">Create content</a>
        <a href="#">Advertise</a>
        <a href="#">News + insights</a>
        <a href="#">Resources</a>
      </div>
      <div class="nav-right">
        <a href="landing.php" class="btn-login-b">Log in</a>
        <a href="components/register.php" class="btn-signup-b">Sign up</a>
      </div>
    </nav>

    <!-- ── HERO ── -->
    <section class="b-hero fade-up">
      <h1>Grow your business<br>on Pinterest</h1>
      <p>
        Pinterest is where people discover new ideas, plan and shop.
        With Pinterest ads, you can reach your audience at every stage
        of the consumer journey. Sign up for a free business account to
        access ads and other marketing tools.
      </p>
      <div class="b-hero-btns">
        <a href="components/register.php" class="btn-bsignup">Sign up</a>
        <a href="#" class="btn-brequest">Request meeting</a>
      </div>
    </section>

    <!-- ── COLLAGE ── -->
    <div class="b-collage fade-up">
      <div class="col-pin">
        <div class="perf-card">
          <div class="pc-title">Performance</div>
          <div class="pc-line"></div>
          <div class="pc-label">Sales</div>
        </div>
        <img src="https://picsum.photos/seed/biz_plants/300/340" alt="" style="height:200px;" />
      </div>
      <div class="col-pin">
        <img src="https://picsum.photos/seed/biz_chair/300/460" alt="" style="height:360px;" />
        <img src="https://picsum.photos/seed/biz_food/300/200" alt="" style="height:160px;" />
      </div>
      <div class="col-pin">
        <img src="https://picsum.photos/seed/biz_fashion/300/520" alt="" style="height:420px;" />
      </div>
      <div class="col-pin">
        <img src="https://picsum.photos/seed/biz_beauty/300/340" alt="" style="height:280px;" />
        <img src="https://picsum.photos/seed/biz_run/300/240" alt="" style="height:200px;" />
      </div>
      <div class="col-pin">
        <img src="https://picsum.photos/seed/biz_style/300/420" alt="" style="height:340px;" />
      </div>
    </div>

    <!-- ── SECTION: ADS DON'T FEEL LIKE ADS ── -->
    <section class="b-section fade-up">
      <div class="sec-inner">
        <div class="sec-img">
          <img src="https://picsum.photos/seed/biz_headphones/600/500" alt="Person wearing headphones" />
        </div>
        <div class="sec-txt">
          <h2>The No. 1 reason people use Pinterest is to find new products and brands</h2>
          <h3 style="font-size:1.15rem; font-weight:700; margin-bottom:14px;">A place where ads don't feel like ads</h3>
          <p>
            Weekly Pinterest users are more likely to say that ads feel relevant
            on Pinterest, compared to people using other platforms.
            Because <a href="#">people on Pinterest</a> are here to take action,
            ads actually enhance their experience — they don't detract from it.
          </p>
          <a href="#" class="btn-sec">How Pinterest works</a>
          <a href="#" class="btn-sec outline">Learn about ads</a>
        </div>
      </div>
    </section>

    <!-- ── AD GOALS TABS ── -->
    <section class="goals-section fade-up">
      <h2>Effective solutions for every goal</h2>
      <div class="goals-tabs">
        <button class="gtab on" onclick="switchGoal('awareness', this)">Awareness</button>
        <button class="gtab" onclick="switchGoal('consideration', this)">Consideration</button>
        <button class="gtab" onclick="switchGoal('conversion', this)">Conversion</button>
      </div>
      <div class="goals-content">
        <div class="gc-img">
          <img src="https://picsum.photos/seed/biz_ad_awareness/400/280" alt="" id="goalImg" />
        </div>
        <div class="gc-txt">
          <h3 id="goalTitle">Build awareness</h3>
          <p id="goalDesc">Grow your reach and build brand or product awareness. Formats such as Image ads, Idea ads or Premiere Spotlight videos can help to tell your story and connect with new customers.</p>
        </div>
      </div>
    </section>

    <!-- ── BRANDS MARQUEE ── -->
    <section class="brands-section fade-up">
      <h2>Brands win big with Pinterest ads</h2>
      <p>Brands such as Nestlé, Urban Outfitters and CeraVe are already using Pinterest to reach their goals. Explore our success story section to see how brands have achieved results for a variety of objectives.</p>
      <div class="brands-cta">
        <a href="#" class="btn-sec">Read success stories</a>
        <a href="components/register.php" class="btn-sec outline" style="margin-left:8px;">Sign up</a>
      </div>
      <div class="marquee-wrap">
        <div class="marquee-track" id="marqueeTrack">
          <!-- logos will be injected by JS -->
        </div>
      </div>
    </section>

    <!-- ── PINTEREST PALETTE ── -->
    <section style="padding: 80px 24px; background: #fff;">
      <div class="palette-section fade-up">
        <div class="palette-txt">
          <div class="tag">Year 2026 colour forecast</div>
          <h2>Meet the 2026 Pinterest Palette™</h2>
          <a href="#" class="btn-sec">See trending colours</a>
        </div>
        <div class="palette-collage">
          <div class="pal-img" style="width:190px;height:240px;top:60px;left:60px;transform:rotate(-3deg);">
            <img src="https://picsum.photos/seed/pal1/190/240" alt="" />
          </div>
          <div class="pal-img" style="width:130px;height:160px;top:0;right:20px;">
            <img src="https://picsum.photos/seed/pal2/130/160" alt="" />
          </div>
          <div class="pal-img" style="width:140px;height:170px;bottom:0;left:30px;transform:rotate(2deg);">
            <img src="https://picsum.photos/seed/pal3/140/170" alt="" />
          </div>
          <div class="pal-img" style="width:110px;height:130px;bottom:30px;right:10px;transform:rotate(-4deg);">
            <img src="https://picsum.photos/seed/pal4/110/130" alt="" />
          </div>
          <div class="pal-dot" style="width:28px;height:28px;background:#4e9af1;top:20px;left:30px;"></div>
          <div class="pal-dot" style="width:22px;height:22px;background:#f7c948;top:48%;right:60px;"></div>
          <div class="pal-dot" style="width:26px;height:26px;background:#e84b4b;bottom:90px;left:20px;"></div>
          <div class="pal-dot" style="width:18px;height:18px;background:#3a3a3a;bottom:30px;right:30px;"></div>
        </div>
      </div>
    </section>

    <!-- ── BUSINESS ACCOUNT CTA ── -->
    <section style="background: var(--sbg); padding: 60px 24px;">
      <div class="biz-cta-section fade-up" style="max-width:860px;margin:0 auto;">
        <div class="biz-cta-img">
          <img src="https://picsum.photos/seed/bluxome/220/280" alt="Bluxome" />
          <div class="promote-badge">Promote</div>
        </div>
        <div class="biz-cta-txt">
          <h2>It all starts with a business account</h2>
          <p>A business account unlocks our full suite of business tools, such as ads and analytics. It's free to sign up, and easy to get started.</p>
          <a href="components/register.php" class="btn-sec">Sign up</a>
          <a href="#" class="btn-sec outline">Create ad</a>
        </div>
      </div>
    </section>

    <!-- ── RESOURCES ── -->
    <section class="resources-section fade-up">
      <h2>Resources to guide you</h2>
      <div class="res-grid">
        <div class="res-card">
          <div class="res-icon" style="background:#e8f5e9; color:#2e7d32;">
            <i class="bi bi-calendar-check"></i>
          </div>
          <h4>Get a free ads consultation</h4>
          <p>Whether you're new to Pinterest ads or hoping to optimise existing campaigns, we're here to help. Talk to our account team for personalised tips and new campaign ideas.</p>
          <a href="#">Request meeting →</a>
        </div>
        <div class="res-card">
          <div class="res-icon" style="background:#e3f2fd; color:#1565c0;">
            <i class="bi bi-send"></i>
          </div>
          <h4>Join our newsletter</h4>
          <p>Receive regular updates about Pinterest products, insights and programming.</p>
          <a href="#">Subscribe →</a>
        </div>
        <div class="res-card">
          <div class="res-icon" style="background:#fce4ec; color:#c62828;">
            <i class="bi bi-list-ul"></i>
          </div>
          <h4>Explore our free learning platform</h4>
          <p>Pinterest Academy offers tactical, in-depth training — powered by our in-house experts. You can take courses, watch webinars or earn badges to show off your skills.</p>
          <a href="#">Enrol now →</a>
        </div>
      </div>
    </section>

    <!-- ── GET STARTED ── -->
    <section class="getstarted-section fade-up">
      <h2>Get started today</h2>
      <a href="components/register.php" class="btn-bsignup">Sign up</a>
    </section>

    <!-- ── FAQ ── -->
    <section class="faq-section fade-up">
      <h2>Frequently asked questions</h2>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">
          How do I use Pinterest for business?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Create a free business account to access Pinterest's full suite of tools including ads manager, analytics, and audience insights. Once set up, you can create pins, run ad campaigns, and track performance.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">
          What is a business account?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">A Pinterest business account gives you access to analytics, advertising tools, and the ability to create rich product pins. It's free and separate from a personal account.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">
          Should I have a separate Pinterest account for my business?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Yes, we recommend keeping your business and personal accounts separate. A dedicated business account keeps your brand presence professional and gives you access to business-only tools.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">
          What are the benefits of using Pinterest for my business?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Pinterest helps you reach people who are actively looking for ideas and ready to take action. With over 500 million monthly active users, you can drive brand awareness, website traffic, and sales through organic pins and paid ads.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">
          Is it easy to sell on Pinterest?
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-a">Yes! Pinterest offers Shopping ads, product pins, and catalog uploads that let you showcase your products directly. Shoppers can discover and purchase products without ever leaving the platform.</div>
      </div>
    </section>

    <!-- ── FOOTER ── -->
    <footer class="b-footer">
      <a href="business.php" class="bf-logo">
        <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
        <span>Pinterest</span>
      </a>
      <div class="bf-grid">
        <div class="bf-col">
          <h6>Quick links</h6>
          <a href="#">Business newsletter</a>
          <a href="#">Business community</a>
          <a href="#">Brand guidelines</a>
        </div>
        <div class="bf-col">
          <h6>Company</h6>
          <a href="#">About Pinterest</a>
          <a href="#">Newsroom</a>
          <a href="#">Careers</a>
          <a href="#">Investors</a>
        </div>
        <div class="bf-col">
          <h6>More from Pinterest</h6>
          <a href="#">Help Centre</a>
          <a href="#">Creators</a>
          <a href="#">Developers</a>
        </div>
        <div class="bf-col">
          <h6>Policies</h6>
          <a href="#">Terms of service</a>
          <a href="#">Privacy policy</a>
          <a href="#">Cookie preferences</a>
        </div>
      </div>
      <div class="bf-bot">
        <span>© 2026 Pinterest</span>
        <select>
          <option>English (GB)</option>
          <option>English (US)</option>
          <option>Hindi</option>
        </select>
        <span>Follow Pinterest Business</span>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      /* Scroll fade */
      const obs = new IntersectionObserver(entries =>
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('vis'); }),
        { threshold: 0.08 }
      );
      document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));

      /* FAQ toggle */
      function toggleFaq(btn) {
        const item = btn.parentElement;
        item.classList.toggle('open');
      }

      /* Goals tabs */
      const goals = {
        awareness: {
          img: 'biz_ad_awareness',
          title: 'Build awareness',
          desc: 'Grow your reach and build brand or product awareness. Formats such as Image ads, Idea ads or Premiere Spotlight videos can help to tell your story and connect with new customers.'
        },
        consideration: {
          img: 'biz_ad_consider',
          title: 'Drive consideration',
          desc: 'Get people to think about your products or services. Use Collection ads or Carousel ads to showcase multiple products and encourage people to learn more about your brand.'
        },
        conversion: {
          img: 'biz_ad_convert',
          title: 'Boost conversions',
          desc: 'Turn inspiration into action. Shopping ads, dynamic retargeting, and conversion-optimised campaigns help you reach shoppers who are ready to make a purchase.'
        }
      };
      function switchGoal(key, btn) {
        document.querySelectorAll('.gtab').forEach(t => t.classList.remove('on'));
        btn.classList.add('on');
        const g = goals[key];
        document.getElementById('goalImg').src = `https://picsum.photos/seed/${g.img}/400/280`;
        document.getElementById('goalTitle').textContent = g.title;
        document.getElementById('goalDesc').textContent = g.desc;
      }

      /* Brand logos marquee */
      const brands = [
        { name: 'MAC', w: 60 }, { name: 'TikTok', w: 72 }, { name: 'Bacardi', w: 80 },
        { name: 'Uber', w: 64 }, { name: 'PayPal', w: 80 }, { name: 'Maybelline', w: 100 },
        { name: 'LEGO', w: 60 }, { name: 'Hyatt', w: 72 }, { name: 'IKEA', w: 60 },
        { name: 'Mercedes-Benz', w: 100 }, { name: 'HP', w: 44 }, { name: "Ray·Ban", w: 80 },
        { name: 'Louis Vuitton', w: 110 }, { name: 'Amex', w: 72 }, { name: "McDonald's", w: 100 },
      ];
      const track = document.getElementById('marqueeTrack');
      // duplicate for seamless loop
      [...brands, ...brands].forEach(b => {
        const span = document.createElement('span');
        span.style.cssText = 'font-weight:700;font-size:0.9rem;color:#aaa;white-space:nowrap;letter-spacing:0.04em;';
        span.textContent = b.name;
        track.appendChild(span);
      });
    </script>
  </body>
</html>
<?php include "footer.php"; ?>