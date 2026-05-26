#  Pinterest Clone

A Pinterest-inspired web app built with PHP, MySQL, and Vanilla JavaScript. Includes real-time chat, pin feeds, boards, likes, comments, notifications, and an admin dashboard.

---

## ⚙️ Tech Stack

- **Backend** — PHP 8+, MySQL
- **Frontend** — Bootstrap 5, Vanilla JS
- **Real-time Chat** — Native PHP WebSocket Server
- **Email** — PHPMailer (Gmail SMTP)
- **Bot Protection** — Google reCAPTCHA v2

---

##  Getting Started

### 1. Clone & Place the Project

```bash
git clone https://github.com/yourusername/pinterest-clone.git
```

Copy the folder into your server root:
- **XAMPP Windows** → `C:/xampp/htdocs/Pinterest_Final/`
- **XAMPP Mac** → `/Applications/XAMPP/htdocs/Pinterest_Final/`

---

### 2. Create the Database

1. Start **Apache** and **MySQL** from XAMPP Control Panel
2. Open `http://localhost/phpmyadmin`
3. Create a new database named `pinterest`
4. Import `database_setup.sql` → SQL tab → Choose file → Go

---

### 3. Set Your Credentials

Open these 3 files and update the values marked with `← change this`:

**`config/database.php`**
```php
$host     = 'localhost';
$username = 'root';
$password = 'YOUR_MYSQL_PASSWORD';   // ← change this
$database = 'pinterest';
```

**`model/database.php`**
```php
$this->conn = new PDO(
    "mysql:host=localhost;dbname=pinterest",
    "root",
    "YOUR_MYSQL_PASSWORD"            // ← change this
);
```

**`server.php`**
```php
define('DB_PASS', 'YOUR_MYSQL_PASSWORD');  // ← change this
```

---

### 4. Set Up Email (Gmail)

You need a **Gmail App Password** (not your regular password):
1. Enable 2-Step Verification on your Google account
2. Go to → [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
3. Generate a password → copy the 16-character code

Open **`config/mail.php`** and update:
```php
$mail->Username = 'your-email@gmail.com';    // ← your Gmail
$mail->Password = 'xxxx xxxx xxxx xxxx';     // ← App Password
$mail->setFrom('your-email@gmail.com', 'Pinterest Clone');
```

Also update the admin notification email in **`controller/UserController.php`**
and **`controller/AdminController.php`** — search for `ayushisomya1611@gmail.com`
and replace it with your own email.

---

### 5. Set Up reCAPTCHA

1. Go to → [https://www.google.com/recaptcha/admin/create](https://www.google.com/recaptcha/admin/create)
2. Choose **reCAPTCHA v2 → "I'm not a robot"**
3. Add `localhost` as an allowed domain
4. Copy your **Site Key** and **Secret Key**

Open **`config/captcha.php`** and update:
```php
define('RECAPTCHA_SITE_KEY',   'your-site-key');    // ← change this
define('RECAPTCHA_SECRET_KEY', 'your-secret-key');  // ← change this
```

Also replace the `data-sitekey` value in **`landing.php`** and **`components/register.php`**:
```html
<div class="g-recaptcha" data-sitekey="your-site-key"></div>
```

---

### 6. Create Upload Folders

**Windows:**
```cmd
mkdir uploads\pins
mkdir uploads\profiles
type nul > uploads\pins\.gitkeep
type nul > uploads\profiles\.gitkeep
```

**Mac / Linux:**
```bash
mkdir -p uploads/pins uploads/profiles
touch uploads/pins/.gitkeep
touch uploads/profiles/.gitkeep
```

---

### 7. Install Dependencies

```bash
composer install
```

---

### 8. Run the App

Open your browser and go to:
```
http://localhost/Pinterest_Final/
```

---

### 9. Start the Chat Server

Open a **separate terminal**, navigate to the project folder, and run:

```bash
php server.php
```

You should see:
```
[WebSocket Server] Listening on ws://0.0.0.0:8080
```

> Keep this terminal open while using the app. Chat won't work without it.

---

## 🔐 Admin Panel

```
URL:      http://localhost/Pinterest_Final/admin/login.php
Email:    ayushisomya1611@gmail.com
Password: admin123
```

> Change these in `controller/AdminController.php` before pushing to GitHub.

---

## 📁 Key Files — What to Change

| File | What to update |
|---|---|
| `config/database.php` | MySQL password |
| `model/database.php` | MySQL password |
| `server.php` | MySQL password |
| `config/mail.php` | Gmail address + App Password |
| `config/captcha.php` | reCAPTCHA site key + secret key |
| `landing.php` | reCAPTCHA site key (in HTML) |
| `components/register.php` | reCAPTCHA site key (in HTML) |
| `controller/AdminController.php` | Admin email + password |
| `controller/UserController.php` | Admin notification email |

---

##  Before Pushing to GitHub

Create a `.gitignore` file:

```gitignore
.env
vendor/
uploads/pins/*
uploads/profiles/*
!uploads/pins/.gitkeep
!uploads/profiles/.gitkeep
.DS_Store
*.log
```

Never push your real passwords, Gmail App Password, or reCAPTCHA secret key to GitHub.

---

## ✨ Features

- Register / Login with reCAPTCHA
- Pin feed with search and category filters
- Create pins (upload image or paste URL)
- Boards, likes, saves, comments
- Follow / unfollow users
- Real-time notifications
- Live chat with typing indicators
- Profile and settings pages
- Forgot password (OTP via email)
- Admin dashboard (manage users, pins, boards, business requests)

---

## 📄 License

MIT — free to use and modify.
