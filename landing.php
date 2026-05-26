<?php
$authError = trim($_GET['error'] ?? '');
$authTab = trim($_GET['auth'] ?? '');
require_once __DIR__ . '/config/captcha.php';
$captchaSiteKey = RECAPTCHA_SITE_KEY;
?>
<?php include "components/header.php"; ?>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pinterest – Find ideas for anything</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Nunito:wght@800;900&family=DM+Sans:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
      :root {
        --red: #e60023;
        --red-dark: #ad081b;
        --text: #111;
        --muted: #6b6b6b;
        --sbg: #f0eeeb;
        --nav-h: 70px;
      }
      *,
      *::before,
      *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }
      html {
        scroll-behavior: smooth;
      }
      body {
        font-family: "DM Sans", sans-serif;
        background: #fff;
        color: var(--text);
        overflow-x: hidden;
      }

      /* thin red bar at top */
      .top-bar {
        height: 4px;
        background: var(--red);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 2000;
      }

      /* ── NAV ── */
      .pnav {
        position: fixed;
        top: 4px;
        left: 0;
        right: 0;
        z-index: 1000;
        height: var(--nav-h);
        background: #fff;
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 40px;
      }
      .pnav .p-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        flex-shrink: 0;
      }
      .pnav .p-logo svg {
        width: 34px;
        height: 34px;
        fill: var(--red);
      }
      .pnav .explore-link {
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--text);
        text-decoration: none;
        padding: 8px 14px;
        border-radius: 20px;
        transition: background 0.2s;
        white-space: nowrap;
      }
      .pnav .explore-link:hover {
        background: var(--sbg);
      }
      .pnav .search-wrap {
        flex: 1;
        max-width: 580px;
        position: relative;
      }
      .pnav .search-wrap input {
        width: 100%;
        border-radius: 24px;
        border: none;
        background: #f0eeeb;
        padding: 11px 20px 11px 44px;
        font-size: 0.93rem;
        outline: none;
        font-family: "DM Sans", sans-serif;
        transition: box-shadow 0.2s;
      }
      .pnav .search-wrap input:focus {
        box-shadow: 0 0 0 2px var(--red);
      }
      .pnav .search-wrap .si {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #767676;
      }
      .pnav .nav-right {
        display: flex;
        gap: 4px;
        align-items: center;
        margin-left: auto;
        flex-shrink: 0;
      }
      .pnav .nav-right a,
      .pnav .nav-right button {
        font-weight: 500;
        font-size: 1.00rem;
        color: var(--text);
        text-decoration: none;
        padding: 8px 14px;
        border-radius: 20px;
        transition: background 0.2s;
        white-space: nowrap;
        border: none;
        background: transparent;
        cursor: pointer;
        font-family: "DM Sans", sans-serif;
      }
      .pnav .nav-right a:hover,
      .pnav .nav-right button:hover {
        background: var(--sbg);
      }
      .pnav .btn-login {
        background: var(--red) !important;
        color: #fff !important;
        border-radius: 24px !important;
        padding: 10px 22px !important;
        font-weight: 700 !important;
      }
      .pnav .btn-login:hover {
        background: var(--red-dark) !important;
      }
      .pnav .btn-signup {
        background: #efefef !important;
        color: #111 !important;
        border-radius: 24px !important;
        padding: 10px 22px !important;
        font-weight: 700 !important;
      }
      .pnav .btn-signup:hover {
        background: #e0e0e0 !important;
      }

      /* ── HERO ── */
      .hero {
        padding-top: calc(var(--nav-h)+8px);
        margin-top: 50px;
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: #fff;
        position: relative;
      }
      .hero-left {
        padding: 60px 60px 60px 80px;
        max-width: 520px;
        flex-shrink: 0;
      }
      .hero-left h1 {
        font-family: "Nunito", sans-serif;
        font-weight: 900;
        font-size: clamp(2.6rem, 4.5vw, 3.8rem);
        line-height: 1.12;
        margin-bottom: 8px;
      }
      .hero-left h1 .static {
        display: block;
      }
      .hero-left h1 .hl {
        display: block;
        color: var(--red);
      }
      .hero-dots {
        display: flex;
        gap: 8px;
        margin: 18px 0 30px;
      }
      .hero-dots span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ddd;
        cursor: pointer;
        transition:
          background 0.3s,
          transform 0.3s;
      }
      .hero-dots span.on {
        background: var(--red);
        transform: scale(1.4);
      }
      .hero-btns {
        display: flex;
        align-items: center;
        gap: 22px;
      }
      .btn-free {
        background: var(--red);
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 14px 28px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition:
          background 0.2s,
          transform 0.15s;
        font-family: "DM Sans", sans-serif;
      }
      .btn-free:hover {
        background: var(--red-dark);
        transform: scale(1.02);
      }
      .link-acc {
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text);
        background: none;
        border: none;
        cursor: pointer;
        font-family: "DM Sans", sans-serif;
        text-decoration: none;
      }
      .link-acc:hover {
        text-decoration: underline;
      }
      /* Hero visual */
      .hero-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 580px;
        padding: 60px 40px 60px 0;
        position: relative;
      }
      .pin-stack {
        position: relative;
        width: 380px;
        height: 460px;
      }
      .pin-img {
        position: absolute;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
      }
      .pin-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
      }
      .pin-main {
        width: 260px;
        height: 380px;
        left: 30px;
        top: 20px;
        z-index: 2;
        animation: fl1 4s ease-in-out infinite;
      }
      .pin-sec {
        width: 200px;
        height: 280px;
        right: 0;
        top: 110px;
        z-index: 3;
        transform: rotate(6deg);
        animation: fl2 4.5s ease-in-out infinite;
      }
      .pin-badge {
        position: absolute;
        top: -10px;
        right: 48px;
        z-index: 4;
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: #9b1ea8;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(155, 30, 168, 0.4);
      }
      .pin-badge svg {
        width: 32px;
        height: 32px;
        fill: #fff;
      }
      .pause-btn {
        position: absolute;
        bottom: 28px;
        right: 20px;
        z-index: 10;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      }
      @keyframes fl1 {
        0%,
        100% {
          transform: translateY(0);
        }
        50% {
          transform: translateY(-14px);
        }
      }
      @keyframes fl2 {
        0%,
        100% {
          transform: rotate(6deg) translateY(0);
        }
        50% {
          transform: rotate(6deg) translateY(-10px);
        }
      }

      /* ── FEATURE SECTIONS ── */
      .fsec {
        padding: 90px 0;
      }
      .fsec.alt {
        background: var(--sbg);
      }
      .ftitle {
        font-family: "Nunito", sans-serif;
        font-weight: 900;
        font-size: clamp(1.8rem, 3vw, 2.5rem);
        text-align: center;
        margin-bottom: 10px;
      }
      .fsub {
        color: var(--muted);
        text-align: center;
        font-size: 0.97rem;
        max-width: 500px;
        margin: 0 auto 56px;
        line-height: 1.65;
      }
      .frow {
        display: flex;
        align-items: center;
        gap: 56px;
        max-width: 1080px;
        margin: 0 auto;
        padding: 0 48px;
      }
      .frow.rev {
        flex-direction: row-reverse;
      }
      .fcard {
        flex: 1;
        display: flex;
        justify-content: center;
      }
      .ftxt {
        flex: 1;
      }
      .ftxt h2 {
        font-family: "Nunito", sans-serif;
        font-weight: 900;
        font-size: clamp(1.5rem, 2.8vw, 2rem);
        margin-bottom: 12px;
      }
      .ftxt p {
        color: var(--muted);
        font-size: 0.95rem;
        line-height: 1.65;
        margin-bottom: 24px;
        max-width: 380px;
      }
      .btn-join {
        background: var(--red);
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 12px 26px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.2s;
        font-family: "DM Sans", sans-serif;
      }
      .btn-join:hover {
        background: var(--red-dark);
      }

      /* mock cards */
      .mock-wrap {
        background: var(--sbg);
        border-radius: 22px;
        padding: 18px;
        max-width: 390px;
        width: 100%;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
      }
      .mock-inner {
        background: #fff;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
      }
      .msbar {
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1.5px solid #e8e8e8;
        border-radius: 11px;
        padding: 8px 12px;
        margin-bottom: 11px;
      }
      .msbar input {
        border: none;
        outline: none;
        font-size: 0.86rem;
        flex: 1;
        font-family: "DM Sans", sans-serif;
      }
      .mchips {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 11px;
      }
      .mchip {
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
      }
      .mc-dark {
        background: #2b2020;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 4px;
      }
      .mc-dark .d {
        width: 11px;
        height: 11px;
        border-radius: 3px;
      }
      .mc-g {
        background: #e6f4e7;
        color: #2d7a2d;
      }
      .mc-p {
        background: #fce4ec;
        color: #b71c5e;
      }
      .skin-lbl {
        font-size: 0.78rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 9px;
      }
      .skins {
        display: flex;
        gap: 7px;
      }
      .sw {
        width: 50px;
        height: 38px;
        border-radius: 8px;
        cursor: pointer;
        border: 3px solid transparent;
        transition:
          border-color 0.2s,
          transform 0.2s;
      }
      .sw:hover {
        transform: scale(1.08);
      }
      .sw.sel {
        border-color: #111;
      }
      .mgrid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5px;
        margin-top: 10px;
        border-radius: 9px;
        overflow: hidden;
      }
      .mgrid img {
        width: 100%;
        height: 86px;
        object-fit: cover;
        display: block;
      }
      .board-wrap {
        background: var(--sbg);
        border-radius: 22px;
        padding: 18px;
        max-width: 350px;
        width: 100%;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
      }
      .board-inner {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
      }
      .bimgs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        height: 180px;
        gap: 3px;
      }
      .bimgs img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .bimgs img:first-child {
        grid-row: 1/-1;
      }
      .bmeta {
        padding: 11px 13px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .btitle-t {
        font-weight: 700;
        font-size: 0.9rem;
      }
      .bcount-t {
        color: var(--muted);
        font-size: 0.78rem;
      }
      .bavs {
        display: flex;
      }
      .bavs img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #fff;
        margin-left: -7px;
        object-fit: cover;
      }
      .bavs img:first-child {
        margin-left: 0;
      }
      .vs-wrap2 {
        background: linear-gradient(135deg, #ffe0e6, #f0e8ff 60%, #d4f0ff);
        border-radius: 22px;
        padding: 20px;
        max-width: 350px;
        width: 100%;
      }
      .vs-inner {
        position: relative;
        border-radius: 14px;
        overflow: visible;
      }
      .vs-inner img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
        border-radius: 14px;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18);
      }
      .vt {
        position: absolute;
        background: #fff;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.13);
      }
      .vt .d {
        width: 11px;
        height: 11px;
        border-radius: 3px;
      }
      .vt.vt1 {
        top: 16px;
        right: -16px;
      }
      .vt.vt2 {
        top: 44%;
        left: -26px;
      }
      .vt.vt3 {
        bottom: 16px;
        right: -8px;
      }

      /* ── SIGNUP SECTION ── */
      .signup-sec {
        position: relative;
        overflow: hidden;
        min-height: 680px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 60px 80px;
      }
      .sg-grid {
        position: absolute;
        inset: 0;
        z-index: 0;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 5px;
        padding: 5px;
      }
      .sg-grid::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.46);
      }
      .sg-col {
        display: flex;
        flex-direction: column;
        gap: 5px;
      }
      .sg-col img {
        width: 100%;
        border-radius: 7px;
        object-fit: cover;
      }
      .sg-txt {
        position: absolute;
        left: 80px;
        bottom: 80px;
        z-index: 2;
        color: #fff;
        font-family: "Nunito", sans-serif;
        font-weight: 900;
        font-size: clamp(1.8rem, 3vw, 3rem);
        line-height: 1.15;
        max-width: 340px;
      }
      /* Mini form card in signup section */
      .mini-sfc {
        position: relative;
        z-index: 2;
        background: #fff;
        border-radius: 22px;
        padding: 36px 32px;
        width: 370px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.3);
      }
      .mini-sfc .mlogo {
        text-align: center;
        margin-bottom: 8px;
      }
      .mini-sfc h3 {
        font-family: "Nunito", sans-serif;
        font-weight: 900;
        font-size: 1.5rem;
        text-align: center;
        margin-bottom: 3px;
      }
      .mini-sfc .msub {
        color: var(--muted);
        text-align: center;
        font-size: 0.86rem;
        margin-bottom: 20px;
      }
      .mf-lbl {
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 4px;
        display: block;
      }
      .mf-inp {
        width: 100%;
        border: 1.5px solid #ddd;
        border-radius: 11px;
        padding: 10px 13px;
        font-size: 0.91rem;
        outline: none;
        font-family: "DM Sans", sans-serif;
        margin-bottom: 12px;
        transition: border-color 0.2s;
      }
      .mf-inp:focus {
        border-color: var(--red);
      }
      .pw-wrap {
        position: relative;
        margin-bottom: 4px;
      }
      .pw-wrap input {
        width: 100%;
        border: 1.5px solid #ddd;
        border-radius: 11px;
        padding: 10px 40px 10px 13px;
        font-size: 0.91rem;
        outline: none;
        font-family: "DM Sans", sans-serif;
        transition: border-color 0.2s;
      }
      .pw-wrap input:focus {
        border-color: var(--red);
      }
      .pw-eye {
        position: absolute;
        right: 12px;
        top: 11px;
        cursor: pointer;
        color: #767676;
      }
      .mf-hint {
        font-size: 0.73rem;
        color: var(--muted);
        margin-bottom: 10px;
      }
      .mf-ptips {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 12px;
        cursor: pointer;
      }
      .btn-cont {
        width: 100%;
        background: var(--red);
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        font-family: "DM Sans", sans-serif;
        transition: background 0.2s;
      }
      .btn-cont:hover {
        background: var(--red-dark);
      }
      .or-div {
        text-align: center;
        color: var(--muted);
        font-size: 0.8rem;
        margin: 11px 0;
      }
      .btn-goog {
        width: 100%;
        background: #fff;
        border: 1.5px solid #ddd;
        border-radius: 24px;
        padding: 9px;
        font-weight: 600;
        font-size: 0.87rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        font-family: "DM Sans", sans-serif;
        transition: background 0.2s;
      }
      .btn-goog:hover {
        background: #f6f6f6;
      }
      .mf-terms {
        font-size: 0.69rem;
        color: var(--muted);
        text-align: center;
        margin-top: 11px;
        line-height: 1.5;
      }
      .mf-terms a {
        color: var(--text);
      }
      .mf-member {
        font-size: 0.8rem;
        text-align: center;
        margin-top: 9px;
        color: var(--muted);
      }
      .mf-member a {
        color: var(--text);
        font-weight: 700;
        text-decoration: none;
      }
      .mf-biz {
        margin-top: 12px;
        border-top: 1px solid #f0f0f0;
        padding-top: 12px;
        text-align: center;
        font-weight: 700;
        font-size: 0.84rem;
        cursor: pointer;
      }
      .mf-biz:hover {
        text-decoration: underline;
      }
      .captcha-wrap {
        margin: 14px 0 10px;
      }
      .captcha-error {
        color: #dc3545;
        font-size: 0.82rem;
        font-weight: 600;
        margin-top: 8px;
        display: none;
      }
      .captcha-error.show {
        display: block;
      }

      /* ── MODAL OVERLAY ── */
      .m-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 5000;
        background: rgba(0, 0, 0, 0.55);
        align-items: flex-start;
        justify-content: center;
        padding-top: 50px;
        overflow-y: auto;
      }
      .m-overlay.show {
        display: flex;
      }
      .m-box {
        background: #fff;
        border-radius: 22px;
        padding: 36px 34px;
        width: 420px;
        max-width: 96vw;
        position: relative;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
        animation: mIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      }
      @keyframes mIn {
        from {
          transform: translateY(-28px);
          opacity: 0;
        }
        to {
          transform: translateY(0);
          opacity: 1;
        }
      }
      .m-close {
        position: absolute;
        top: 14px;
        right: 16px;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #888;
        line-height: 1;
      }
      .m-close:hover {
        color: #111;
      }
      .m-tabs {
        display: flex;
        border-bottom: 1px solid #eee;
        margin-bottom: 20px;
      }
      .m-tabs button {
        flex: 1;
        background: none;
        border: none;
        padding: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        color: #767676;
        border-bottom: 3px solid transparent;
        margin-bottom: -1px;
        transition: all 0.2s;
        font-family: "DM Sans", sans-serif;
      }
      .m-tabs button.on {
        color: var(--red);
        border-bottom-color: var(--red);
      }
      .m-form {
        display: none;
      }
      .m-form.on {
        display: block;
      }
      /* form field helper */
      .fi {
        font-size: 0.84rem;
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
      }
      .ff {
        width: 100%;
        border: 1.5px solid #ddd;
        border-radius: 11px;
        padding: 11px 13px;
        font-size: 0.92rem;
        outline: none;
        font-family: "DM Sans", sans-serif;
        margin-bottom: 14px;
        transition: border-color 0.2s;
      }
      .ff:focus {
        border-color: var(--red);
      }
      .fpw {
        position: relative;
        margin-bottom: 4px;
      }
      .fpw input {
        width: 100%;
        border: 1.5px solid #ddd;
        border-radius: 11px;
        padding: 11px 42px 11px 13px;
        font-size: 0.92rem;
        outline: none;
        font-family: "DM Sans", sans-serif;
        transition: border-color 0.2s;
      }
      .fpw input:focus {
        border-color: var(--red);
      }
      .feye {
        position: absolute;
        right: 12px;
        top: 12px;
        cursor: pointer;
        color: #767676;
      }
      .fhint {
        font-size: 0.74rem;
        color: var(--muted);
        margin-bottom: 12px;
      }
      .fptips {
        font-size: 0.81rem;
        font-weight: 600;
        margin-bottom: 14px;
        cursor: pointer;
      }
      .fbtn {
        width: 100%;
        background: var(--red);
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 13px;
        font-weight: 700;
        font-size: 0.96rem;
        cursor: pointer;
        font-family: "DM Sans", sans-serif;
        transition: background 0.2s;
      }
      .fbtn:hover {
        background: var(--red-dark);
      }
      .for {
        text-align: center;
        color: var(--muted);
        font-size: 0.82rem;
        margin: 12px 0;
      }
      
      .fterms {
        font-size: 0.7rem;
        color: var(--muted);
        text-align: center;
        margin-top: 12px;
        line-height: 1.5;
      }
      .fterms a {
        color: var(--text);
      }
      .fmember {
        font-size: 0.82rem;
        text-align: center;
        margin-top: 10px;
        color: var(--muted);
      }
      .fmember a {
        color: var(--text);
        font-weight: 700;
        text-decoration: none;
      }
      .fbiz {
        margin-top: 14px;
        border-top: 1px solid #f0f0f0;
        padding-top: 14px;
        text-align: center;
        font-weight: 700;
        font-size: 0.86rem;
        cursor: pointer;
      }
      .fbiz:hover {
        text-decoration: underline;
      }
      .biz-note {
        background: #fff7e6;
        border: 1px solid #fbbf24;
        border-radius: 11px;
        padding: 10px 13px;
        font-size: 0.8rem;
        margin-bottom: 16px;
        display: flex;
        gap: 8px;
        align-items: flex-start;
      }
      .biz-note i {
        color: #f59e0b;
        flex-shrink: 0;
        margin-top: 2px;
      }

      /* ── FOOTER ── */
      footer {
        background: #111;
        color: #fff;
        padding: 52px 80px 28px;
      }
      .flogo {
        font-family: "Nunito", sans-serif;
        font-weight: 900;
        font-size: 1.65rem;
        color: #fff;
        margin-bottom: 32px;
        display: block;
      }
      .fgrid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
        margin-bottom: 32px;
      }
      .fcol h6 {
        font-weight: 700;
        font-size: 0.8rem;
        margin-bottom: 13px;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #aaa;
      }
      .fcol a {
        display: block;
        color: #ccc;
        text-decoration: none;
        margin-bottom: 9px;
        font-size: 0.87rem;
        transition: color 0.2s;
      }
      .fcol a:hover {
        color: #fff;
      }
      .fbot {
        border-top: 1px solid #333;
        padding-top: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .fbot span {
        color: #666;
        font-size: 0.79rem;
      }

      .fade-up {
        opacity: 0;
        transform: translateY(32px);
        transition:
          opacity 0.6s,
          transform 0.6s;
      }
      .fade-up.vis {
        opacity: 1;
        transform: translateY(0);
      }

      @media (max-width: 900px) {
        .hero-left {
          padding: 36px 24px;
        }
        .hero-right {
          display: none;
        }
        .frow,
        .frow.rev {
          flex-direction: column;
          padding: 0 20px;
        }
        .signup-sec {
          padding: 36px 14px;
          justify-content: center;
        }
        .sg-txt {
          display: none;
        }
        .mini-sfc {
          width: 100%;
          max-width: 400px;
        }
        footer {
          padding: 36px 20px 20px;
        }
        .fgrid {
          grid-template-columns: 1fr 1fr;
        }
      }
      @media (max-width: 600px) {
        .pnav .nav-right a:not(.btn-login):not(.btn-signup) {
          display: none;
        }
        .pnav .explore-link {
          display: none;
        }
        .pnav .search-wrap {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <div class="top-bar"></div>

    <!-- ══ NAVBAR ══ -->
    <nav class="pnav">
      <a href="landing.php" class="p-logo">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"
          />
        </svg> <span style="color:#e60023;font-size:25px;font-weight: 900;padding-left:10px">Pinterest</span> 
      </a>
      <a href="index.php" class="explore-link">Explore</a>
      <!-- <div class="search-wrap">
        <i class="bi bi-search si"></i>
        <input
          type="text"
          placeholder="Search for easy dinners, fashion, etc."
        />
      </div> -->
      <div class="nav-right">
        <!-- All these open in new tab -->
        <a href="components/about.php" target="_blank">About</a>
        <a href="components/business.php" target="_blank">Businesses</a>
        <a href="components/create-pin.php" target="_blank">Create</a>
        <a href="components/news.php" target="_blank">News</a>
        <button class="btn-login" onclick="openMod('login')">Log in</button>
        <button class="btn-signup" onclick="window.location.href='components/register.php'">Sign up</button>
      </div>
    </nav>

    <!-- ══ HERO ══ -->
    <section class="hero">
      <div class="hero-left">
        <h1>
          <span class="static">Find ideas for</span>
          <span class="hl" id="heroHL">statement tattoos</span>
        </h1>
        <div class="hero-dots" id="hDots">
          <span onclick="setSl(0)"></span>
          <span onclick="setSl(1)"></span>
          <span onclick="setSl(2)"></span>
          <span onclick="setSl(3)"></span>
          <span class="on" onclick="setSl(4)"></span>
        </div>
        <div class="hero-btns">
          <button class="btn-free" onclick="window.location.href='components/register.php'">
            Join Pinterest for free
          </button>
          <button class="link-acc" onclick="openMod('login')">
            I already have an account
          </button>
        </div>
      </div>
      <div class="hero-right">
        <div class="pin-badge">
          <svg viewBox="0 0 24 24">
            <path
              d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"
            />
          </svg>
        </div>
        <div class="pin-stack">
          <div class="pin-img pin-main">
            <img
              src="https://picsum.photos/seed/hero1/520/760"
              alt=""
              id="hImg1"
            />
          </div>
          <div class="pin-img pin-sec">
            <img
              src="https://picsum.photos/seed/hero2/400/560"
              alt=""
              id="hImg2"
            />
          </div>
        </div>
        <button class="pause-btn" id="pauseBtn" onclick="togglePause()">
          <i class="bi bi-pause-fill"></i>
        </button>
      </div>
    </section>

    <!-- ── FEATURE 1 ── -->
    <section class="fsec alt">
      <div class="fade-up" style="padding: 0 48px">
        <h2 class="ftitle">Bring your favourite ideas to life</h2>
        <p class="fsub">
          With Pinterest, you can unlock tools that spark your creativity and
          help you find more inspiration.
        </p>
      </div>
      <div class="frow fade-up">
        <div class="fcard">
          <div class="mock-wrap">
            <div class="mock-inner">
              <div class="msbar">
                <i
                  class="bi bi-chevron-left"
                  style="color: #999; font-size: 0.82rem"
                ></i
                ><input value="bold lip" readonly /><i
                  class="bi bi-sliders"
                  style="color: #999; font-size: 0.82rem"
                ></i>
              </div>
              <div class="mchips">
                <button class="mchip mc-dark">
                  <span class="d" style="background: #5c2e0e"></span>Skin Tone ∨
                </button>
                <button class="mchip mc-g">Makeup ∨</button>
                <button class="mchip mc-p">Occasion ∨</button>
              </div>
              <div class="mock-inner" style="margin-top: 6px">
                <div class="skin-lbl">
                  Search by skin tone
                  <i
                    class="bi bi-info-circle"
                    style="font-size: 0.7rem; color: #999"
                  ></i>
                </div>
                <div class="skins">
                  <div
                    class="sw"
                    style="
                      background: linear-gradient(135deg, #f7cba8, #f0a87a);
                    "
                  ></div>
                  <div
                    class="sw"
                    style="
                      background: linear-gradient(135deg, #e8956d, #d4704a);
                    "
                  ></div>
                  <div
                    class="sw"
                    style="
                      background: linear-gradient(135deg, #b5622e, #8b3e1a);
                    "
                  ></div>
                  <div
                    class="sw sel"
                    style="
                      background: linear-gradient(135deg, #4a2010, #2a0d06);
                    "
                  ></div>
                </div>
              </div>
              <div class="mgrid">
                <img src="https://picsum.photos/seed/lip1/200/172" alt="" /><img
                  src="https://picsum.photos/seed/lip2/200/172"
                  alt=""
                />
                <img src="https://picsum.photos/seed/lip3/200/172" alt="" /><img
                  src="https://picsum.photos/seed/lip4/200/172"
                  alt=""
                />
              </div>
            </div>
          </div>
        </div>
        <div class="ftxt">
          <h2>Search by skin tone</h2>
          <p>
            Search with skin tone ranges for beauty ideas that represent you —
            because inspiration should be as diverse as you are.
          </p>
          <button class="btn-join" onclick="window.location.href='components/register.php'">
            Join Pinterest
          </button>
        </div>
      </div>
    </section>

    <!-- ── FEATURE 2 ── -->
    <section class="fsec">
      <div class="frow rev fade-up">
        <div class="fcard">
          <div class="board-wrap">
            <div class="board-inner">
              <div class="bimgs">
                <img src="https://picsum.photos/seed/room1/400/360" alt="" />
                <img src="https://picsum.photos/seed/room2/200/180" alt="" />
                <img src="https://picsum.photos/seed/room3/200/180" alt="" />
              </div>
              <div class="bmeta">
                <div>
                  <div class="btitle-t">Earthy space inspo</div>
                  <div class="bcount-t">80 Pins</div>
                </div>
                <div class="bavs">
                  <img src="https://i.pravatar.cc/64?img=33" alt="" /><img
                    src="https://i.pravatar.cc/64?img=52"
                    alt=""
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="ftxt">
          <h2>Collaborate with group boards</h2>
          <p>
            Visualise your ideas with others. Create shared boards, invite
            friends, and build mood boards together in real time.
          </p>
          <button class="btn-join" onclick="window.location.href='components/register.php'">
            Join Pinterest
          </button>
        </div>
      </div>
    </section>

    <!-- ── FEATURE 3 ── -->
    <section class="fsec alt">
      <div class="frow fade-up">
        <div class="fcard">
          <div class="vs-wrap2">
            <div class="vs-inner">
              <img src="https://picsum.photos/seed/fashionvs/380/600" alt="" />
              <div class="vt vt1">
                <span class="d" style="background: #c1121f"></span>Cherry red
              </div>
              <div class="vt vt2">
                <i
                  class="bi bi-grid-3x3-gap"
                  style="font-size: 0.78rem; color: #777"
                ></i>
                Knit sweater
              </div>
              <div class="vt vt3">
                <i
                  class="bi bi-stars"
                  style="font-size: 0.78rem; color: #9b1ea8"
                ></i>
                Preppy look
              </div>
            </div>
          </div>
        </div>
        <div class="ftxt">
          <h2>Search visually with images</h2>
          <p>
            Search objects within an image to find more styles you'll love.
            Point, click, and discover a world of ideas inspired by any pin.
          </p>
          <button class="btn-join" onclick="window.location.href='components/register.php'">
            Join Pinterest
          </button>
        </div>
      </div>
    </section>

    <!--  SIGNUP SECTION -->

<section class="signup-sec">

  <div class="sg-grid" id="sgGrid"></div>

  <div class="sg-txt">
    Sign up to get<br />your ideas
  </div>

  <div class="mini-sfc fade-up">

    <div class="mlogo">

      <svg viewBox="0 0 24 24" width="36" height="36" fill="#E60023">

        <path
          d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"
        />
      </svg>
    </div>

    <h3>Welcome to Pinterest</h3>
    <p class="msub">
      Find new ideas to try
    </p>
    <!-- FORM START  -->

    <form action="components/register.php" method="POST">
      <!-- NAME -->
      <label class="mf-lbl">
        Name
      </label>

      <input
        type="text"
        name="name"
        class="mf-inp form-control"
        placeholder="Enter your name"
        required
      />
      <!-- EMAIL -->

      <label class="mf-lbl">
        Email
      </label>
      <input
        type="email"
        name="email"
        class="mf-inp form-control"
        id="mini_email"
        placeholder="Email"
        required
      />
      <div
        class="invalid-feedback"
        id="mini_email_err"
        style="margin-top: -8px; margin-bottom: 8px; font-size: 0.75rem"
      >
        Please enter a valid email address.
      </div>

      <!-- PASSWORD -->

      <label class="mf-lbl" style="margin-top: 4px">
        Password
      </label>

      <div class="pw-wrap">

        <input
          class="form-control"
          type="password"
          name="password"
          id="sfcP"
          placeholder="Create a password"
          required
          minlength="8"
        />

        <i
          class="bi bi-eye pw-eye"
          onclick="tpw('sfcP', this)"
        ></i>

      </div>

      <div
        class="invalid-feedback"
        style="margin-top: 2px; margin-bottom: 6px; font-size: 0.75rem"
      >
        Password must be at least 8 characters.
      </div>

      <div class="mf-hint">
        Use 8 or more letters, numbers and symbols
      </div>

      <div class="mf-ptips">

        Password tips

        <i
          class="bi bi-info-circle"
          style="font-size: 0.75rem; color: #999"
        ></i>

      </div>
      <!-- DOB -->
     <label class="mf-lbl">

        Birthdate

        <i
          class="bi bi-info-circle"
          style="font-size: 0.73rem; color: #999"
        ></i>

      </label>

      <input
        type="date"
        name="dob"
        class="mf-inp form-control"
        id="mini_dob"
        required
      />

      <div
        class="invalid-feedback"
        id="mini_dob_err"
        style="margin-top: -8px; margin-bottom: 8px; font-size: 0.75rem"
      >
        Please enter your birthdate.
      </div>
      <!-- SUBMIT -->
      <button
        class="btn-cont"
        type="submit"
        
        id="mini_btn"
      >
        Continue
      </button>

    </form>
    <div class="mf-terms">

      By continuing, you agree to Pinterest's

      <a href="#">Terms of Service</a>

      and acknowledge you've read our

      <a href="#">Privacy Policy</a>.

      <a href="#">Notice at collection</a>.

    </div>
    <div class="mf-member">
      Already a member?
      <a
        href="#"
        onclick="
          openMod('login');
          return false;
        "
      >
        Log in
      </a>

    </div>
    <div class="mf-biz" onclick="openMod('business')">

      Create a free business account

    </div>

  </div>

</section>

    <!-- ══ FOOTER ══ -->
    <footer>
      <span class="flogo"
        ><i class="fa-brands fa-pinterest"></i> Pinterest</span
      >
      <div class="fgrid">
        <div class="fcol">
          <h6>Get the app</h6>
          <a href="#">iOS</a><a href="#">Android</a>
        </div>
        <div class="fcol">
          <h6>Quick links</h6>
          <a href="index.php">Explore</a><a href="#">Shop</a
          ><a href="#">Users</a><a href="#">Collections</a
          ><a href="#">Shopping</a><a href="#">Help Center</a>
        </div>
        <div class="fcol">
          <h6>Policies</h6>
          <a href="#">Terms of service</a><a href="#">Privacy policy</a
          ><a href="#">Non-user notice</a>
        </div>
      </div>
      <div class="fbot">
        <span>© 2026 Pinterest</span
        ><span style="color: #555">HTML · Bootstrap · JS</span>
      </div>
    </footer>

    <!--  MODAL  -->
    <div class="m-overlay" id="mOverlay" onclick="closeMod(event)">
      <div class="m-box" id="mBox">
        <button class="m-close" onclick="closeMod()">×</button>
        <!-- P logo -->
        <div style="text-align: center; margin-bottom: 8px">
          <svg viewBox="0 0 24 24" width="34" height="34" fill="#E60023">
            <path
              d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"
            />
          </svg>
        </div>
        <!-- Tabs (hidden for business) -->
        <div class="m-tabs" id="mTabs">
          <button id="tab-signup" onclick="switchTab('signup', this)">
            Sign up
          </button>
          <button id="tab-login" onclick="switchTab('login', this)">
            Log in
          </button>
        </div>

        <!-- ── SIGN UP ── -->
        <div class="m-form" id="mf-signup">
          <div
            style="
              text-align: center;
              font-family: &quot;Nunito&quot;, sans-serif;
              font-weight: 900;
              font-size: 1.45rem;
              margin-bottom: 3px;
            "
          >
            Welcome to Pinterest
          </div>
          <div
            style="
              text-align: center;
              color: #777;
              font-size: 0.85rem;
              margin-bottom: 18px;
            "
          >
            Find new ideas to try
          </div>
          <form action="controller/UserController.php" method="POST" id="signupForm">
            <span class="fi">Email</span>
            <input
              class="ff form-control"
              type="email"
              name="email" id="su_email"
              placeholder="Email"
              required
            />
            <div
              class="invalid-feedback"
              style="margin-top: -10px; margin-bottom: 8px; font-size: 0.78rem"
            >
              Please enter a valid email address.
            </div>

            <span class="fi" style="margin-top: 4px">Password</span>
            <div class="fpw">
              <input
                class="form-control"
                type="password"
                name="password" id="mp1"
                placeholder="Create a password"
                required
                minlength="8"
                style="padding-right: 42px"
              />
              <i class="bi bi-eye feye" onclick="tpw('mp1', this)"></i>
            </div>
            <div
              class="invalid-feedback"
              style="margin-top: 2px; margin-bottom: 6px; font-size: 0.78rem"
            >
              Password must be at least 8 characters.
            </div>
            <div class="fhint">Use 8 or more letters, numbers and symbols</div>
            <div class="fptips">
              Password tips
              <i
                class="bi bi-info-circle"
                style="font-size: 0.74rem; color: #999"
              ></i>
            </div>

            <span class="fi"
              >Birthdate
              <i
                class="bi bi-info-circle"
                style="font-size: 0.72rem; color: #999"
              ></i
            ></span>
            <input class="ff form-control" type="date" name="dob" id="su_dob" required />
            <div
              class="invalid-feedback"
              style="margin-top: -10px; margin-bottom: 8px; font-size: 0.78rem"
              id="su_dob_err"
            >
              Please enter your birthdate.
            </div>

            <div class="captcha-wrap">
              <div
                class="g-recaptcha"
                data-sitekey="<?= htmlspecialchars($captchaSiteKey, ENT_QUOTES) ?>"
                data-callback="onLandingSignupCaptchaSuccess"
              ></div>
              <div
                id="suCaptchaError"
                class="captcha-error<?= $authTab === 'signup' && $authError !== '' ? ' show' : '' ?>"
              >
                <?= $authTab === 'signup' && $authError !== '' ? htmlspecialchars($authError, ENT_QUOTES) : 'Please fill the CAPTCHA' ?>
              </div>
            </div>

            <button class="fbtn" type="submit" name="register" id="su_btn">Continue</button>
          </form>
          <div class="fterms">
            By continuing, you agree to PinBoard's
            <a href="#">Terms of Service</a> and acknowledge you've read our
            <a href="#">Privacy Policy</a>.
            <a href="#">Notice at collection</a>.
          </div>
          <div class="fmember">
            Already a member?
            <a
              href="#"
              onclick="
                switchTab('login', document.getElementById('tab-login'));
                return false;
              "
              >Log in</a
            >
          </div>
          <div class="fbiz" onclick="switchTab('business', null)">
            Create a free business account
          </div>
        </div>

        <!-- ── LOG IN ── -->
        <div class="m-form" id="mf-login">
          <div
            style="
              text-align: center;
              font-family: &quot;Nunito&quot;, sans-serif;
              font-weight: 900;
              font-size: 1.45rem;
              margin-bottom: 18px;
            "
          >
            Welcome back
          </div>
          <form action="controller/UserController.php" method="POST" id="loginForm">
            <span class="fi">Email</span>
            <input
              class="ff form-control"
              type="email"
              name="email" id="li_email"
              placeholder="Email"
              required
            />
            <div
              class="invalid-feedback"
              id="li_email_err"
              style="margin-top: -10px; margin-bottom: 8px; font-size: 0.78rem"
            >
              Please enter a valid email address.
            </div>
            <span class="fi" style="margin-top: 4px">Password</span>
            <div class="fpw">
              <input
                class="form-control"
                type="password"
                name="password" id="mp2"
                placeholder="Password"
                required
                minlength="6"
                style="padding-right: 42px"
              />
              <i class="bi bi-eye feye" onclick="tpw('mp2', this)"></i>
            </div>
            <div
              class="invalid-feedback"
              id="li_pass_err"
              style="margin-top: 2px; margin-bottom: 6px; font-size: 0.78rem"
            >
              Password must be at least 6 characters.
            </div>
            <div style="text-align: right; margin: 10px 0 14px">
              <a
                href="components/forgot-password.php"
                style="
                  font-size: 0.83rem;
                  color: var(--red);
                  text-decoration: none;
                  font-weight: 600;
                "
                >Forgot password?</a
              >
            </div>

            <div class="captcha-wrap">
              <div
                class="g-recaptcha"
                data-sitekey="<?= htmlspecialchars($captchaSiteKey, ENT_QUOTES) ?>"
                data-callback="onLandingLoginCaptchaSuccess"
              ></div>
              <div
                id="liCaptchaError"
                class="captcha-error<?= $authTab === 'login' && $authError !== '' ? ' show' : '' ?>"
              >
                <?= $authTab === 'login' && $authError !== '' ? htmlspecialchars($authError, ENT_QUOTES) : 'Please fill the CAPTCHA' ?>
              </div>
            </div>

            <button class="fbtn" type="submit" name="login" id="li_btn">Log In</button>
          </form>
          <div class="fterms">
            By continuing, you agree to PinBoard's
            <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
          </div>
          <div class="fmember">
            Don't have an account?
            <a
              href="#"
              onclick="
                switchTab('signup', document.getElementById('tab-signup'));
                return false;
              "
              >Sign up</a
            >
          </div>
        </div>

        <!-- ── BUSINESS ── -->
        <div class="m-form" id="mf-business">
          <div
            style="
              text-align: center;
              font-family: &quot;Nunito&quot;, sans-serif;
              font-weight: 900;
              font-size: 1.35rem;
              margin-bottom: 14px;
            "
          >
            Create a business account
          </div>
          <div class="biz-note">
            <i class="bi bi-briefcase-fill"></i>
            <div>
              Get analytics, promoted pins, and tools to grow your brand — all
              free.
            </div>
          </div>
          <form id="bizForm" novalidate>
            <span class="fi">Business name</span>
            <input
              class="ff form-control"
              type="text"
              id="biz_name"
              placeholder="Your business name"
              required
              minlength="2"
            />
            <div
              class="invalid-feedback"
              style="margin-top: -10px; margin-bottom: 8px; font-size: 0.78rem"
            >
              Business name is required.
            </div>
            <span class="fi" style="margin-top: 4px">Business email</span>
            <input
              class="ff form-control"
              type="email"
              id="biz_email"
              placeholder="business@example.com"
              required
            />
            <div
              class="invalid-feedback"
              style="margin-top: -10px; margin-bottom: 8px; font-size: 0.78rem"
            >
              Please enter a valid business email.
            </div>
            <span class="fi" style="margin-top: 4px">Website</span>
            <input
              class="ff form-control"
              type="url"
              id="biz_url"
              placeholder="https://yourbusiness.com"
              required
            />
            <div
              class="invalid-feedback"
              style="margin-top: -10px; margin-bottom: 8px; font-size: 0.78rem"
            >
              Please enter a valid URL including https://
            </div>
            <span class="fi" style="margin-top: 4px">Password</span>
            <div class="fpw">
              <input
                class="form-control"
                type="password"
                id="mp3"
                placeholder="Create a password"
                required
                minlength="8"
                style="padding-right: 42px"
              />
              <i class="bi bi-eye feye" onclick="tpw('mp3', this)"></i>
            </div>
            <div
              class="invalid-feedback"
              style="margin-top: 2px; margin-bottom: 6px; font-size: 0.78rem"
            >
              Password must be at least 8 characters.
            </div>
            <div style="margin-bottom: 14px"></div>
            <button class="fbtn" type="submit" id="biz_btn">
              Create business account
            </button>
          </form>
          <div class="fterms">
            By continuing, you agree to PinBoard's
            <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
          </div>
          <div
            class="fbiz"
            style="margin-top: 12px"
            onclick="
              switchTab('signup', null);
              document.getElementById('mTabs').style.display = 'flex';
              document.getElementById('tab-signup').classList.add('on');
              document.getElementById('tab-login').classList.remove('on');
            "
          >
            Create a personal account instead
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      /* Slideshow */
      const SL = [
        {
          t: "home decoration ideas",
          c: "#c45500",
          a: "homedec1",
          b: "homedec2",
        },
        { t: "weeknight dinner", c: "#9b1ea8", a: "food20", b: "food21" },
        { t: "statement tattoos", c: "#E60023", a: "tat1", b: "tat2" },
        { t: "classic nail art", c: "#007b7b", a: "nails1", b: "nails2" },
        { t: "minimal desk setups", c: "#0066cc", a: "desk20", b: "desk21" },
      ];
      let cur = 2,
        paused = false;
      function setSl(i) {
        cur = i;
        const s = SL[i];
        const hl = document.getElementById("heroHL");
        hl.textContent = s.t;
        hl.style.color = s.c;
        document.getElementById("hImg1").src =
          `https://picsum.photos/seed/${s.a}/520/760`;
        document.getElementById("hImg2").src =
          `https://picsum.photos/seed/${s.b}/400/560`;
        document
          .querySelectorAll("#hDots span")
          .forEach((d, j) => d.classList.toggle("on", j === i));
      }
      function togglePause() {
        paused = !paused;
        document.getElementById("pauseBtn").innerHTML = paused
          ? '<i class="bi bi-play-fill"></i>'
          : '<i class="bi bi-pause-fill"></i>';
      }
      setInterval(() => {
        if (!paused) setSl((cur + 1) % SL.length);
      }, 3200);
      setSl(2);

      /* BG grid */
      const SS = [
        "food1",
        "fashion1",
        "art1",
        "home1",
        "travel1",
        "food2",
        "fashion2",
        "art2",
        "home2",
        "travel2",
        "food3",
        "fashion3",
        "art3",
        "home3",
        "travel3",
        "food4",
        "fashion4",
        "art4",
        "home4",
        "travel4",
      ];
      const gg = document.getElementById("sgGrid");
      for (let c = 0; c < 5; c++) {
        const col = document.createElement("div");
        col.className = "sg-col";
        for (let r = 0; r < 4; r++) {
          const img = document.createElement("img");
          img.src = `https://picsum.photos/seed/${SS[c * 4 + r]}/300/340`;
          img.alt = "";
          img.style.height = 145 + Math.random() * 55 + "px";
          col.appendChild(img);
        }
        gg.appendChild(col);
      }

      /* Modal */
      function openMod(tab) {
        document.getElementById("mOverlay").classList.add("show");
        document.body.style.overflow = "hidden";
        switchTab(tab, null);
        if (tab === "signup") {
          document.getElementById("tab-signup").classList.add("on");
          document.getElementById("tab-login").classList.remove("on");
        } else if (tab === "login") {
          document.getElementById("tab-login").classList.add("on");
          document.getElementById("tab-signup").classList.remove("on");
        }
      }
      function closeMod(e) {
        if (!e || e.target === document.getElementById("mOverlay")) {
          document.getElementById("mOverlay").classList.remove("show");
          document.body.style.overflow = "";
        }
      }
      function switchTab(tab, btn) {
        document
          .querySelectorAll(".m-form")
          .forEach((f) => f.classList.remove("on"));
        document.getElementById("mf-" + tab).classList.add("on");
        if (btn) {
          document
            .querySelectorAll(".m-tabs button")
            .forEach((b) => b.classList.remove("on"));
          btn.classList.add("on");
        }
        // hide tabs for business
        document.getElementById("mTabs").style.display =
          tab === "business" ? "none" : "flex";
      }
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeMod();
      });

      /* Password toggle */
      function tpw(id, icon) {
        const p = document.getElementById(id);
        p.type = p.type === "password" ? "text" : "password";
        icon.className =
          p.type === "text" ? "bi bi-eye-slash feye" : "bi bi-eye feye";
      }

      /* Skin swatches */
      document.querySelectorAll(".sw").forEach((s) =>
        s.addEventListener("click", () => {
          document
            .querySelectorAll(".sw")
            .forEach((x) => x.classList.remove("sel"));
          s.classList.add("sel");
        }),
      );

      /* Scroll fade */
      const obs = new IntersectionObserver(
        (entries) =>
          entries.forEach((e) => {
            if (e.isIntersecting) e.target.classList.add("vis");
          }),
        { threshold: 0.1 },
      );
      document.querySelectorAll(".fade-up").forEach((el) => obs.observe(el));

      /* ── MINI SIGNUP CARD (bottom section) ── */
      const miniEmail = document.getElementById("mini_email");
      const miniPass = document.getElementById("sfcP");
      const miniDob = document.getElementById("mini_dob");

      function vMiniEmail() {
        const v = miniEmail.value.trim();
        if (!v || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
          miniEmail.classList.add("is-invalid");
          miniEmail.classList.remove("is-valid");
          document.getElementById("mini_email_err").textContent = !v
            ? "Email is required."
            : "Please enter a valid email address.";
          return false;
        }
        miniEmail.classList.add("is-valid");
        miniEmail.classList.remove("is-invalid");
        return true;
      }
      function vMiniPass() {
        if (miniPass.value.length < 8) {
          miniPass.classList.add("is-invalid");
          miniPass.classList.remove("is-valid");
          return false;
        }
        miniPass.classList.add("is-valid");
        miniPass.classList.remove("is-invalid");
        return true;
      }
      function vMiniDob() {
        if (!miniDob.value) {
          miniDob.classList.add("is-invalid");
          miniDob.classList.remove("is-valid");
          document.getElementById("mini_dob_err").textContent =
            "Please enter your birthdate.";
          return false;
        }
        const age =
          (new Date() - new Date(miniDob.value)) / (365.25 * 24 * 3600 * 1000);
        if (age < 13) {
          miniDob.classList.add("is-invalid");
          miniDob.classList.remove("is-valid");
          document.getElementById("mini_dob_err").textContent =
            "You must be at least 13 years old.";
          return false;
        }
        miniDob.classList.add("is-valid");
        miniDob.classList.remove("is-invalid");
        return true;
      }

      miniEmail.addEventListener("blur", vMiniEmail);
      miniEmail.addEventListener("input", () => {
        if (miniEmail.classList.contains("is-invalid")) vMiniEmail();
      });
      miniPass.addEventListener("blur", vMiniPass);
      miniPass.addEventListener("input", () => {
        if (miniPass.classList.contains("is-invalid")) vMiniPass();
      });
      miniDob.addEventListener("blur", vMiniDob);
      miniDob.addEventListener("input", () => {
        if (miniDob.classList.contains("is-invalid")) vMiniDob();
      });

      document
        .getElementById("miniSignupForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          if (![vMiniEmail(), vMiniPass(), vMiniDob()].every(Boolean)) return;
          const btn = document.getElementById("mini_btn");
          btn.disabled = true;
          btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Please wait…';
          setTimeout(() => {
            window.location.href = "components/register.php";
          }, 900);
        });

     
        // BOOTSTRAP FORM VALIDATION
        
      function markValid(el) {
        el.classList.remove("is-invalid");
        el.classList.add("is-valid");
      }
      function markInvalid(el, msg, errId) {
        el.classList.remove("is-valid");
        el.classList.add("is-invalid");
        if (errId) document.getElementById(errId).textContent = msg;
      }
      function isEmail(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
      }

      function toggleCaptchaError(id, shouldShow, message = "Please fill the CAPTCHA") {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = message;
        el.classList.toggle("show", shouldShow);
      }

      function onLandingSignupCaptchaSuccess() {
        toggleCaptchaError("suCaptchaError", false);
      }

      function onLandingLoginCaptchaSuccess() {
        toggleCaptchaError("liCaptchaError", false);
      }

      /* ── LOGIN ── */
      const liEmail = document.getElementById("li_email");
      const liPass = document.getElementById("mp2");

      function vLiEmail() {
        const v = liEmail.value.trim();
        if (!v) {
          markInvalid(liEmail, "Email is required.", "li_email_err");
          return false;
        }
        if (!isEmail(v)) {
          markInvalid(
            liEmail,
            "Please enter a valid email address.",
            "li_email_err",
          );
          return false;
        }
        markValid(liEmail);
        return true;
      }
      function vLiPass() {
        const v = liPass.value;
        if (!v) {
          markInvalid(liPass, "Password is required.", "li_pass_err");
          return false;
        }
        if (v.length < 6) {
          markInvalid(
            liPass,
            "Password must be at least 6 characters.",
            "li_pass_err",
          );
          return false;
        }
        markValid(liPass);
        return true;
      }

      liEmail.addEventListener("blur", vLiEmail);
      liEmail.addEventListener("input", () => {
        if (liEmail.classList.contains("is-invalid")) vLiEmail();
      });
      liPass.addEventListener("blur", vLiPass);
      liPass.addEventListener("input", () => {
        if (liPass.classList.contains("is-invalid")) vLiPass();
      });

      document
        .getElementById("loginForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          if (![vLiEmail(), vLiPass()].every(Boolean)) return;
          const captchaField = this.querySelector('textarea[name="g-recaptcha-response"]');
          if (!captchaField || !captchaField.value.trim()) {
            toggleCaptchaError("liCaptchaError", true);
            return;
          }
          toggleCaptchaError("liCaptchaError", false);
          const btn = document.getElementById("li_btn");
          btn.disabled = true;
          btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Logging in…';
          this.submit();
        });

      /* ── SIGNUP ── */
      const suEmail = document.getElementById("su_email");
      const suPass = document.getElementById("mp1");
      const suDob = document.getElementById("su_dob");

      function vSuEmail() {
        const v = suEmail.value.trim();
        if (!v) {
          markInvalid(suEmail, "Email is required.", null);
          suEmail.nextElementSibling.textContent = "Email is required.";
          return false;
        }
        if (!isEmail(v)) {
          markInvalid(suEmail, "", null);
          suEmail.nextElementSibling.textContent =
            "Please enter a valid email address.";
          return false;
        }
        markValid(suEmail);
        return true;
      }
      function vSuPass() {
        if (suPass.value.length < 8) {
          markInvalid(suPass, "", null);
          return false;
        }
        markValid(suPass);
        return true;
      }
      function vSuDob() {
        if (!suDob.value) {
          markInvalid(suDob, "", null);
          document.getElementById("su_dob_err").textContent =
            "Please enter your birthdate.";
          return false;
        }
        const age =
          (new Date() - new Date(suDob.value)) / (365.25 * 24 * 3600 * 1000);
        if (age < 13) {
          markInvalid(suDob, "", null);
          document.getElementById("su_dob_err").textContent =
            "You must be at least 13 years old.";
          return false;
        }
        markValid(suDob);
        return true;
      }

      suEmail.addEventListener("blur", vSuEmail);
      suEmail.addEventListener("input", () => {
        if (suEmail.classList.contains("is-invalid")) vSuEmail();
      });
      suPass.addEventListener("blur", vSuPass);
      suPass.addEventListener("input", () => {
        if (suPass.classList.contains("is-invalid")) vSuPass();
      });
      suDob.addEventListener("blur", vSuDob);
      suDob.addEventListener("input", () => {
        if (suDob.classList.contains("is-invalid")) vSuDob();
      });

      document
        .getElementById("signupForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          if (![vSuEmail(), vSuPass(), vSuDob()].every(Boolean)) return;
          const captchaField = this.querySelector('textarea[name="g-recaptcha-response"]');
          if (!captchaField || !captchaField.value.trim()) {
            toggleCaptchaError("suCaptchaError", true);
            return;
          }
          toggleCaptchaError("suCaptchaError", false);
          const btn = document.getElementById("su_btn");
          btn.disabled = true;
          btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Please wait…';
          setTimeout(() => {
            window.location.href = "components/register.php";
          }, 900);
        });

      /* ── BUSINESS ── */
      const bizName = document.getElementById("biz_name");
      const bizEmail = document.getElementById("biz_email");
      const bizUrl = document.getElementById("biz_url");
      const bizPass = document.getElementById("mp3");

      function vBizName() {
        if (bizName.value.trim().length < 2) {
          markInvalid(bizName, "", null);
          return false;
        }
        markValid(bizName);
        return true;
      }
      function vBizEmail() {
        if (!isEmail(bizEmail.value.trim())) {
          markInvalid(bizEmail, "", null);
          return false;
        }
        markValid(bizEmail);
        return true;
      }
      function vBizUrl() {
        try {
          new URL(bizUrl.value.trim());
          markValid(bizUrl);
          return true;
        } catch {
          markInvalid(bizUrl, "", null);
          return false;
        }
      }
      function vBizPass() {
        if (bizPass.value.length < 8) {
          markInvalid(bizPass, "", null);
          return false;    
        }
        markValid(bizPass);
        return true;
      }

      bizName.addEventListener("blur", vBizName);
      bizName.addEventListener("input", () => {
        if (bizName.classList.contains("is-invalid")) vBizName();
      });
      bizEmail.addEventListener("blur", vBizEmail);
      bizEmail.addEventListener("input", () => {
        if (bizEmail.classList.contains("is-invalid")) vBizEmail();
      });
      bizUrl.addEventListener("blur", vBizUrl);
      bizUrl.addEventListener("input", () => {
        if (bizUrl.classList.contains("is-invalid")) vBizUrl();
      });
      bizPass.addEventListener("blur", vBizPass);
      bizPass.addEventListener("input", () => {
        if (bizPass.classList.contains("is-invalid")) vBizPass();
      });

      document
        .getElementById("bizForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          if (![vBizName(), vBizEmail(), vBizUrl(), vBizPass()].every(Boolean))
            return;
          const btn = document.getElementById("biz_btn");
          btn.disabled = true;
          btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Creating account…';
          setTimeout(() => {
            window.location.href = "index.php";
          }, 900);
        });

      /* Reset validation state when switching tabs */
      document.querySelectorAll(".m-tabs button").forEach((btn) => {
        btn.addEventListener("click", () => {
          document.querySelectorAll(".form-control").forEach((el) => {
            el.classList.remove("is-valid", "is-invalid");
          });
          toggleCaptchaError("suCaptchaError", false);
          toggleCaptchaError("liCaptchaError", false);
        });
      });

      <?php if ($authError !== '' && in_array($authTab, ['login', 'signup'], true)): ?>
      openMod("<?= $authTab ?>");
      toggleCaptchaError(
        "<?= $authTab === 'login' ? 'liCaptchaError' : 'suCaptchaError' ?>",
        true,
        <?= json_encode($authError) ?>
      );
      <?php endif; ?>
    </script>
  </body>
<?php include "components/footer.php"; ?>
