# Pinterest Project — Setup Guide

## Project Structure

```
Pinterest_Project/
├── index.php                  ← Entry point
├── landing.php                ← User landing page
├── home.php                   ← User home after login
├── business_request.php       ← User submits business account request
├── database_setup.sql         ← Run this first in phpMyAdmin
│
├── admin/
│   ├── login.php              ← Admin login (no credentials shown)
│   ├── dashboard.php          ← Admin dashboard (protected)
│   ├── business_requests.php  ← View pending business requests
│   ├── approve.php            ← Approve a business request
│   └── reject.php             ← Reject a business request
│
├── components/
│   ├── login.php              ← User login form
│   ├── register.php           ← User registration form
│   ├── forgot-password.php    ← Password reset
│   ├── profile.php            ← User profile page
│   ├── board.php              ← Pinterest boards
│   ├── create-pin.php         ← Create a pin
│   ├── notifications.php      ← Notifications
│   └── settings.php           ← Account settings
│
├── controller/
│   ├── AdminController.php    ← Admin login + all admin AJAX API
│   └── UserController.php     ← User register + login
│
├── config/
│   ├── database.php           ← MySQL credentials
│   └── mail.php               ← Gmail SMTP credentials
│
├── model/
│   ├── database.php           ← PDO DB class
│   └── func.php               ← Query helper
│
└── vendor/                    ← PHPMailer (pre-included)
```

---

## Step 1 — Database

1. Open **phpMyAdmin** → SQL tab
2. Paste and run the contents of `database_setup.sql`
3. Edit `config/database.php` with your MySQL password

---

## Step 2 — Mail (required for notifications)

Edit `config/mail.php`:
- `Username` = your Gmail address (the sender)
- `Password` = your **Gmail App Password** (not your Gmail login)

To get an App Password:
1. Enable 2FA on Gmail
2. Go to https://myaccount.google.com/apppasswords
3. Create one named "Pinterest" and paste it in

---

## Step 3 — Deploy

1. Copy `Pinterest_Project/` into `htdocs/` (XAMPP) or `www/` (WAMP)
2. Start Apache + MySQL
3. Visit: `http://localhost/Pinterest_Project/`

---

## Admin Login

- **URL:** `http://localhost/Pinterest_Project/admin/login.php`
- **Email:** `ayushisomya1611@gmail.com`
- **Password:** `admin123`

No credentials are shown on the login page.

---

## Flow

```
index.php
  ├── Open User Controller  → landing.php → Register/Login → home.php
  │                                ↓ on register
  │                         Admin gets email notification
  │
  └── Open Admin Controller → admin/login.php
             ↓ POST adminLogin → AdminController.php
             ↓ success
         admin/dashboard.php
             ├── All Users tab  (live from DB)
             ├── Business Requests tab (pending/approved/rejected)
             └── Approve / Reject → user gets email + admin notified
```

---

## Email Triggers

| Event | Who gets email |
|-------|---------------|
| User signs up | User (welcome) + Admin (new registration) |
| User requests business account | User (confirmation) + Admin (new request) |
| Admin approves business account | User (approved notification) |
| Admin rejects business account | User (rejected notification) |
