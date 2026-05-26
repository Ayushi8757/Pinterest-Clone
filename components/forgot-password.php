<?php include "header.php"; ?>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PinBoard – Reset Password</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <style>
      :root {
        --pin-red: #e60023;
        --pin-red-dark: #ad081b;
      }
      body {
        font-family: "DM Sans", sans-serif;
        background: #f8f7f5;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
      }
      .card {
        border-radius: 24px;
        border: none;
        box-shadow: 0 16px 60px rgba(0, 0, 0, 0.1);
        max-width: 440px;
        width: 100%;
        padding: 48px 40px;
      }
      .logo {
        font-family: "Playfair Display", serif;
        font-size: 35px;
        color: #e60023;
        text-align: center;
        margin-bottom: 24px;
      }
      .step {
        display: none;
      }
      .step.active {
        display: block;
      }
      h2 {
        font-family: "Playfair Display", serif;
        font-size: 1.5rem;
        margin-bottom: 8px;
      }

      .form-control {
        border-radius: 12px;
        border: 1.5px solid #e0e0e0;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition:
          border-color 0.2s,
          box-shadow 0.2s;
      }
      .form-control:focus {
        border-color: var(--pin-red);
        box-shadow: 0 0 0 3px rgba(230, 0, 35, 0.1);
        outline: none;
      }
      .form-control.is-valid {
        border-color: #198754 !important;
        box-shadow: none;
      }
      .form-control.is-valid:focus {
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
      }
      .form-control.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: none;
      }
      .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.12);
      }
      .invalid-feedback {
        font-size: 0.82rem;
        font-weight: 500;
        margin-top: 5px;
      }
      .valid-feedback {
        font-size: 0.82rem;
        font-weight: 500;
        margin-top: 5px;
      }

      .btn-pin {
        background: var(--pin-red);
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 12px;
        font-weight: 700;
        font-size: 1rem;
        width: 100%;
        transition:
          background 0.2s,
          opacity 0.2s;
        cursor: pointer;
      }
      .btn-pin:hover:not(:disabled) {
        background: var(--pin-red-dark);
      }
      .btn-pin:disabled {
        opacity: 0.55;
        cursor: not-allowed;
      }

      /* OTP inputs */
      .otp-wrap {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 24px 0;
      }
      .otp-input {
        width: 52px;
        height: 52px;
        text-align: center;
        font-size: 1.4rem;
        font-weight: 700;
        border: 1.5px solid #e0e0e0;
        border-radius: 12px;
        outline: none;
        transition:
          border-color 0.2s,
          box-shadow 0.2s;
        font-family: "DM Sans", sans-serif;
      }
      .otp-input:focus {
        border-color: var(--pin-red);
        box-shadow: 0 0 0 3px rgba(230, 0, 35, 0.1);
      }
      .otp-input.is-valid {
        border-color: #198754;
        background: #f6fff9;
      }
      .otp-input.is-invalid {
        border-color: #dc3545;
        background: #fff8f8;
      }

      /* Password eye */
      .pass-wrap {
        position: relative;
      }
      .pass-wrap .form-control {
        padding-right: 46px;
      }
      .eye-toggle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 4px;
        cursor: pointer;
        color: #767676;
        font-size: 1rem;
      }

      /* Strength bar */
      .strength-bar {
        height: 4px;
        border-radius: 4px;
        background: #e0e0e0;
        margin-top: 8px;
        overflow: hidden;
      }
      .strength-bar .fill {
        height: 100%;
        border-radius: 4px;
        width: 0;
        transition:
          width 0.35s,
          background 0.35s;
      }
      .strength-label {
        font-size: 0.78rem;
        font-weight: 600;
        margin-top: 4px;
      }

      .auth-footer {
        text-align: center;
        margin-top: 16px;
        font-size: 0.88rem;
        color: #767676;
      }
      .auth-footer a {
        color: var(--pin-red);
        text-decoration: none;
        font-weight: 600;
      }
      .success-icon {
        font-size: 4rem;
        text-align: center;
        margin: 16px 0;
      }

      /* Resend timer */
      .resend-timer {
        font-size: 0.82rem;
        color: #767676;
      }
      .resend-timer a {
        color: var(--pin-red);
        font-weight: 600;
        text-decoration: none;
        pointer-events: none;
        opacity: 0.5;
      }
      .resend-timer a.active {
        pointer-events: auto;
        opacity: 1;
      }
    </style>
  </head>
  <body>
    <div class="card">
      <div class="logo"><i class="fa-brands fa-pinterest"></i></div>

      <!-- ── Step 1: Email ── -->
      <div class="step active" id="step1">
        <h2>Reset your password</h2>
        <p class="text-muted mb-4">
          Enter the email linked to your account and we'll send a 6-digit reset
          code.
        </p>

        <form id="step1Form" novalidate>
          <div class="mb-4">
            <label class="form-label fw-semibold" for="resetEmail"
              >Email address</label
            >
            <input
              type="email"
              class="form-control"
              id="resetEmail"
              placeholder="you@example.com"
              required
              autocomplete="email"
            />
            <div class="invalid-feedback">
              Please enter a valid email address.
            </div>
            <div class="valid-feedback">We'll send a code here!</div>
          </div>
          <button type="submit" class="btn-pin" id="sendBtn">
            Send reset code
          </button>
        </form>
        <div class="auth-footer mt-3">
          <a href="login.php"
            ><i class="bi bi-arrow-left"></i> Back to login</a
          >
        </div>
      </div>

      <!-- ── Step 2: OTP ── -->
      <div class="step" id="step2">
        <h2>Enter the code</h2>
        <p class="text-muted">
          We sent a 6-digit code to
          <strong id="emailDisplay" style="color: #111"></strong>
        </p>
        <p class="text-muted small mb-0">Check your inbox and spam folder.</p>

        <div class="otp-wrap" id="otpWrap">
          <input
            class="otp-input"
            type="text"
            inputmode="numeric"
            maxlength="1"
            id="otp1"
          />
          <input
            class="otp-input"
            type="text"
            inputmode="numeric"
            maxlength="1"
            id="otp2"
          />
          <input
            class="otp-input"
            type="text"
            inputmode="numeric"
            maxlength="1"
            id="otp3"
          />
          <input
            class="otp-input"
            type="text"
            inputmode="numeric"
            maxlength="1"
            id="otp4"
          />
          <input
            class="otp-input"
            type="text"
            inputmode="numeric"
            maxlength="1"
            id="otp5"
          />
          <input
            class="otp-input"
            type="text"
            inputmode="numeric"
            maxlength="1"
            id="otp6"
          />
        </div>

        <!-- OTP error -->
        <div
          class="alert alert-danger py-2 px-3 small d-none mb-3"
          id="otpErr"
          role="alert"
        >
          <i class="bi bi-exclamation-circle me-1"></i
          ><span id="otpErrMsg">Please enter all 6 digits.</span>
        </div>

        <button class="btn-pin" id="verifyBtn" onclick="verifyOTP()">
          Verify code
        </button>
        <div class="resend-timer text-center mt-3">
          Didn't receive it?
          <a href="#" id="resendLink" onclick="resendCode(event)"
            >Resend code</a
          >
          <span id="timerText">in <span id="countdown">30</span>s</span>
        </div>
      </div>

      <!-- ── Step 3: New Password ── -->
      <div class="step" id="step3">
        <h2>Create new password</h2>
        <p class="text-muted mb-4">
          Your new password must be at least 8 characters.
        </p>

        <form id="step3Form" novalidate>
          <div class="mb-3">
            <label class="form-label fw-semibold" for="newPass"
              >New password</label
            >
            <div class="pass-wrap">
              <input
                type="password"
                class="form-control"
                id="newPass"
                placeholder="Min 8 characters"
                required
                minlength="8"
              />
              <button
                type="button"
                class="eye-toggle"
                onclick="toggleEye('newPass', this)"
              >
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="invalid-feedback">
              Password must be at least 8 characters.
            </div>
            <div class="valid-feedback">Strong password!</div>
            <div class="strength-bar">
              <div class="fill" id="strengthFill"></div>
            </div>
            <div
              class="strength-label"
              id="strengthLabel"
              style="color: #767676"
            >
              Enter a password
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold" for="confirmPass"
              >Confirm password</label
            >
            <div class="pass-wrap">
              <input
                type="password"
                class="form-control"
                id="confirmPass"
                placeholder="Repeat password"
                required
              />
              <button
                type="button"
                class="eye-toggle"
                onclick="toggleEye('confirmPass', this)"
              >
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="invalid-feedback" id="confirmErr2">
              Passwords do not match.
            </div>
            <div class="valid-feedback">Passwords match!</div>
          </div>

          <button type="submit" class="btn-pin">Reset password</button>
        </form>
      </div>

      <!-- ── Step 4: Success ── -->
      <div class="step" id="step4">
        <div class="success-icon text-center">🎉</div>
        <h2 class="text-center">Password reset!</h2>
        <p class="text-muted text-center mb-4">
          Your password has been updated. You can now log in with your new
          password.
        </p>
        <a
          href="login.php"
          class="btn-pin text-center text-decoration-none d-block"
          >Go to Login</a
        >
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // ── Utility ──
      function markValid(el) {
        el.classList.remove("is-invalid");
        el.classList.add("is-valid");
      }
      function markInvalid(el, msg) {
        el.classList.remove("is-valid");
        el.classList.add("is-invalid");
        const wrap =
          el.closest(".mb-3") || el.closest(".mb-4") || el.parentElement;
        const fb = wrap && wrap.querySelector(".invalid-feedback");
        if (fb && msg) fb.textContent = msg;
      }
      function goStep(n) {
        [1, 2, 3, 4].forEach((i) =>
          document
            .getElementById("step" + i)
            .classList.toggle("active", i === n),
        );
      }
      function toggleEye(id, btn) {
        const p = document.getElementById(id);
        const ic = btn.querySelector("i");
        p.type = p.type === "password" ? "text" : "password";
        ic.className = p.type === "password" ? "bi bi-eye" : "bi bi-eye-slash";
      }

      // ── Step 1: Email ──
      const emailEl = document.getElementById("resetEmail");
      emailEl.addEventListener("blur", () => validateEmail(emailEl));
      emailEl.addEventListener("input", () => {
        if (emailEl.classList.contains("is-invalid")) validateEmail(emailEl);
      });

      function validateEmail(el) {
        const v = el.value.trim();
        if (!v) return (markInvalid(el, "Email is required."), false);
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v))
          return (markInvalid(el, "Enter a valid email address."), false);
        return (markValid(el), true);
      }

      document
        .getElementById("step1Form")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          if (!validateEmail(emailEl)) return;

          const btn = document.getElementById("sendBtn");
          btn.disabled = true;
          btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';

          setTimeout(() => {
            document.getElementById("emailDisplay").textContent = emailEl.value;
            goStep(2);
            startTimer();
            setupOTP();
            btn.disabled = false;
            btn.textContent = "Send reset code";
          }, 900);
        });

      // ── OTP Setup ──
      function setupOTP() {
        const inputs = document.querySelectorAll(".otp-input");
        inputs.forEach((input, idx) => {
          input.value = "";
          input.classList.remove("is-valid", "is-invalid");

          input.addEventListener("input", function () {
            // only allow digits
            this.value = this.value.replace(/\D/g, "");
            if (this.value && idx < 5) inputs[idx + 1].focus();
            updateOTPState();
          });
          input.addEventListener("keydown", function (e) {
            if (e.key === "Backspace" && !this.value && idx > 0)
              inputs[idx - 1].focus();
          });
          input.addEventListener("paste", function (e) {
            e.preventDefault();
            const digits = e.clipboardData
              .getData("text")
              .replace(/\D/g, "")
              .slice(0, 6);
            inputs.forEach((inp, i) => {
              inp.value = digits[i] || "";
            });
            if (digits.length < 6) inputs[Math.min(digits.length, 5)].focus();
            updateOTPState();
          });
        });
        inputs[0].focus();
      }

      function updateOTPState() {
        const inputs = document.querySelectorAll(".otp-input");
        const allFilled = [...inputs].every((i) => i.value.length === 1);
        document.getElementById("otpErr").classList.add("d-none");
        inputs.forEach((i) => {
          i.classList.toggle("is-valid", i.value.length === 1);
        });
      }

      function verifyOTP() {
        const inputs = document.querySelectorAll(".otp-input");
        const otp = [...inputs].map((i) => i.value).join("");
        const err = document.getElementById("otpErr");

        if (otp.length < 6) {
          inputs.forEach((i) => {
            if (!i.value) i.classList.add("is-invalid");
          });
          document.getElementById("otpErrMsg").textContent =
            "Please enter all 6 digits.";
          err.classList.remove("d-none");
          return;
        }
        // Simulate verification (accept any 6 digits in demo)
        if (otp === "000000") {
          inputs.forEach((i) => i.classList.add("is-invalid"));
          document.getElementById("otpErrMsg").textContent =
            "Incorrect code. Please try again.";
          err.classList.remove("d-none");
          return;
        }
        inputs.forEach((i) => {
          i.classList.remove("is-invalid");
          i.classList.add("is-valid");
        });
        err.classList.add("d-none");

        const btn = document.getElementById("verifyBtn");
        btn.disabled = true;
        btn.innerHTML =
          '<span class="spinner-border spinner-border-sm me-2"></span>Verifying…';
        setTimeout(() => {
          goStep(3);
          btn.disabled = false;
          btn.textContent = "Verify code";
        }, 700);
      }

      // ── Resend timer ──
      let timerInterval;
      function startTimer() {
        let sec = 30;
        const link = document.getElementById("resendLink");
        const cd = document.getElementById("countdown");
        const txt = document.getElementById("timerText");
        link.classList.remove("active");
        txt.style.display = "inline";
        cd.textContent = sec;
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
          sec--;
          cd.textContent = sec;
          if (sec <= 0) {
            clearInterval(timerInterval);
            link.classList.add("active");
            txt.style.display = "none";
          }
        }, 1000);
      }
      function resendCode(e) {
        e.preventDefault();
        const link = document.getElementById("resendLink");
        if (!link.classList.contains("active")) return;
        startTimer();
        // show mini toast
        const t = document.createElement("div");
        t.style.cssText =
          "position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#111;color:#fff;padding:10px 20px;border-radius:20px;font-size:.85rem;font-weight:600;z-index:9999;";
        t.textContent = " Code resent!";
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 2500);
      }

      // ── Step 3: New Password ──
      const newPassEl = document.getElementById("newPass");
      const confirmEl = document.getElementById("confirmPass");

      newPassEl.addEventListener("input", function () {
        // strength
        const v = this.value;
        const fill = document.getElementById("strengthFill");
        const lbl = document.getElementById("strengthLabel");
        let sc = 0;
        if (v.length >= 8) sc++;
        if (/[A-Z]/.test(v)) sc++;
        if (/[0-9]/.test(v)) sc++;
        if (/[^A-Za-z0-9]/.test(v)) sc++;
        const m = [
          { w: "0%", c: "#e0e0e0", t: "" },
          { w: "25%", c: "#dc3545", t: "Weak" },
          { w: "50%", c: "#fd7e14", t: "Fair" },
          { w: "75%", c: "#ffc107", t: "Good" },
          { w: "100%", c: "#198754", t: "Strong 💪" },
        ];
        const s = m[sc] || m[0];
        fill.style.width = s.w;
        fill.style.background = s.c;
        lbl.textContent = s.t;
        lbl.style.color = s.c;
        if (this.classList.contains("is-invalid")) validateNewPass(this);
        if (
          confirmEl.classList.contains("is-invalid") ||
          confirmEl.classList.contains("is-valid")
        )
          validateConfirm();
      });

      function validateNewPass(el) {
        if (!el.value) return (markInvalid(el, "Password is required."), false);
        if (el.value.length < 8)
          return (
            markInvalid(el, "Password must be at least 8 characters."),
            false
          );
        return (markValid(el), true);
      }
      function validateConfirm() {
        const el = confirmEl;
        if (!el.value)
          return (markInvalid(el, "Please confirm your password."), false);
        if (el.value !== newPassEl.value)
          return (markInvalid(el, "Passwords do not match."), false);
        return (markValid(el), true);
      }

      newPassEl.addEventListener("blur", () => validateNewPass(newPassEl));
      confirmEl.addEventListener("blur", () => validateConfirm());
      confirmEl.addEventListener("input", () => {
        if (confirmEl.classList.contains("is-invalid")) validateConfirm();
      });

      document
        .getElementById("step3Form")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          const ok = [validateNewPass(newPassEl), validateConfirm()].every(
            Boolean,
          );
          if (!ok) return;
          const btn = this.querySelector("button[type=submit]");
          btn.disabled = true;
          btn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Updating…';
          setTimeout(() => {
            goStep(4);
            btn.disabled = false;
            btn.textContent = "Reset password";
          }, 900);
        });
    </script>
  </body>
<?php include "footer.php"; ?>
