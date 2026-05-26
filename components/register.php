<?php
session_start();
require_once __DIR__ . '/../config/captcha.php';
// if (!empty($_SESSION['user_id'])) {
//     header('Location: ../home.php');
//     exit;
// }
$regError = trim($_GET['error'] ?? '');
$captchaSiteKey = RECAPTCHA_SITE_KEY;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>

        body{
            background:#f5f5f5;
            font-family:Arial,sans-serif;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
        }

        .register-box{
            background:white;
            width:500px;
            border-radius:20px;
            padding:40px;
            box-shadow:0 0 20px rgba(0,0,0,0.1);
        }

        h2{
            text-align:center;
            color:#E60023;
            margin-bottom:10px;
        }

        .sub{
            text-align:center;
            color:#666;
            margin-bottom:30px;
        }

        .form-control{
            padding:12px;
            border-radius:12px;
            margin-bottom:15px;
        }

        .btn-pin{
            width:100%;
            background:#E60023;
            color:white;
            border:none;
            padding:12px;
            border-radius:30px;
            font-weight:bold;
        }

        .btn-pin:hover{
            background:#ad081b;
        }

        .step{
            display:none;
        }

        .step.active{
            display:block;
        }

        .interest-grid{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:10px;
        }

        .interest-card{
            border:2px solid #ddd;
            border-radius:12px;
            overflow:hidden;
            cursor:pointer;
            text-align:center;
            transition:0.3s;
        }

        .interest-card img{
            width:100%;
            height:120px;
            object-fit:cover;
        }

        .interest-card p{
            padding:10px;
            margin:0;
            font-weight:bold;
        }

        .interest-card.selected{
            border-color:#E60023;
        }

        .captcha-wrap{
            margin:18px 0 10px;
        }

        .captcha-error{
            color:#dc3545;
            font-size:14px;
            font-weight:600;
            margin-top:8px;
            display:none;
        }

        .captcha-error.show{
            display:block;
        }

    </style>
</head>

<body>

<div class="register-box">

    <h2>Create Account</h2>

    <p class="sub">
        Join Pinterest and explore ideas
    </p>
    <?php if ($regError && $regError !== 'Please fill the CAPTCHA'): ?>
      <div class="alert alert-danger py-2 text-center mb-3" style="border-radius:10px;font-size:14px;">
        <?= $regError ?>
      </div>
    <?php endif; ?>
    <!-- FORM START  -->
    <form
        id="registerForm"
        action="../controller/UserController.php"
        method="POST"
    >
        <!-- hidden input -->
        <input type="hidden" name="register" value="1">
        <!-- STEP 1  -->
        <div class="step active" id="step1">
            <input
                type="text"
                name="name"
                id="name"
                class="form-control"
                placeholder="Full Name"
                required
            >
            <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                placeholder="Email Address"
                required
            >

            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Create Password"
                required
            >

            <input
                type="password"
                id="confirmPassword"
                class="form-control"
                placeholder="Confirm Password"
                required
            >

            <input
                type="date"
                name="dob"
                id="dob"
                class="form-control"
                required
            >

            <button
                type="button"
                class="btn-pin"
                onclick="nextStep(2)"
            >
                Continue
            </button>

        </div>



        <!--  STEP 2 -->

        <div class="step" id="step2">

            <h5 class="mb-3">
                Select Gender
            </h5>

            <select
                class="form-control"
                name="gender"
            >

                <option value="">
                    Select Gender
                </option>

                <option value="Male">
                    Male
                </option>

                <option value="Female">
                    Female
                </option>

                <option value="Other">
                    Other
                </option>

            </select>

            <div class="d-flex gap-2">

                <button
                    type="button"
                    class="btn btn-secondary w-50"
                    onclick="nextStep(1)"
                >
                    Back
                </button>

                <button
                    type="button"
                    class="btn-pin w-50"
                    onclick="nextStep(3)"
                >
                    Continue
                </button>

            </div>

        </div>



        <!--  STEP 3 -->

        <div class="step" id="step3">

            <h5 class="mb-3">
                Choose Your Interests
            </h5>

            <div class="interest-grid">

                <div class="interest-card" onclick="toggleInterest(this)">
                    <img src="https://picsum.photos/200/120?1">
                    <p>Fashion</p>
                </div>

                <div class="interest-card" onclick="toggleInterest(this)">
                    <img src="https://picsum.photos/200/120?2">
                    <p>Food</p>
                </div>

                <div class="interest-card" onclick="toggleInterest(this)">
                    <img src="https://picsum.photos/200/120?3">
                    <p>Travel</p>
                </div>

                <div class="interest-card" onclick="toggleInterest(this)">
                    <img src="https://picsum.photos/200/120?4">
                    <p>Art</p>
                </div>

            </div>

            <br>

            <div class="captcha-wrap">
                <div
                    class="g-recaptcha"
                    data-sitekey="<?= htmlspecialchars($captchaSiteKey, ENT_QUOTES) ?>"
                    data-callback="onRegisterCaptchaSuccess"
                ></div>
                <div
                    id="registerCaptchaError"
                    class="captcha-error<?= $regError === 'Please fill the CAPTCHA' ? ' show' : '' ?>"
                >
                    <?= $regError === 'Please fill the CAPTCHA' ? htmlspecialchars($regError, ENT_QUOTES) : 'Please fill the CAPTCHA' ?>
                </div>
            </div>

            <div class="d-flex gap-2">

                <button
                    type="button"
                    class="btn btn-secondary w-50"
                    onclick="nextStep(2)"
                >
                    Back
                </button>

                <button
                    type="submit"
                    class="btn-pin w-50"
                >
                    Create Account
                </button>

            </div>

        </div>

    </form>

</div>



<script>

function showRegisterCaptchaError(message){
    const errorBox = document.getElementById('registerCaptchaError');
    errorBox.textContent = message;
    errorBox.classList.add('show');
}

function hideRegisterCaptchaError(){
    document.getElementById('registerCaptchaError').classList.remove('show');
}

function onRegisterCaptchaSuccess(){
    hideRegisterCaptchaError();
}

function nextStep(step){

    let errorBox = document.querySelector('.alert-danger');

    if(step === 2){

        let name = document.getElementById('name').value.trim();
        let email = document.getElementById('email').value.trim();
        let password = document.getElementById('password').value.trim();
        let confirm = document.getElementById('confirmPassword').value.trim();
        let dob = document.getElementById('dob').value.trim();

        if(!name || !email || !password || !confirm || !dob){

            if(!errorBox){
                let alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger py-2 text-center mb-3';
                alertDiv.style.borderRadius = '10px';
                alertDiv.style.fontSize = '14px';
                alertDiv.innerText = 'All fields are required';

                document.querySelector('.sub').after(alertDiv);
            }

            return;
        }
    }

    if(step === 3){

        let gender = document.querySelector('[name="gender"]').value;

        if(!gender){

            if(!errorBox){
                let alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger py-2 text-center mb-3';
                alertDiv.style.borderRadius = '10px';
                alertDiv.style.fontSize = '14px';
                alertDiv.innerText = 'Please select gender';

                document.querySelector('.sub').after(alertDiv);
            }

            return;
        }
    }

    if(errorBox){
        errorBox.remove();
    }

    let steps = document.querySelectorAll('.step');

    steps.forEach(s => {
        s.classList.remove('active');
    });

    document.getElementById('step' + step).classList.add('active');
}



function toggleInterest(card){

    card.classList.toggle('selected');

}



// PASSWORD MATCH VALIDATION

document
.getElementById('registerForm')
.addEventListener('submit', function(e){

    let password =
        document.getElementById('password').value;

    let confirm =
        document.getElementById('confirmPassword').value;

    if(password !== confirm){

        e.preventDefault();

        alert("Passwords do not match");

        return;

    }

    const captchaField = this.querySelector('textarea[name="g-recaptcha-response"]');

    if(!captchaField || !captchaField.value.trim()){
        e.preventDefault();
        showRegisterCaptchaError('Please fill the CAPTCHA');
    }

});

</script>

</body>
</html>
