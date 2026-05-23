<?php
// ============================================================
//  config/email.php — SMTP Email Configuration
//
//  ┌─────────────────────────────────────────────────────┐
//  │  HOW TO SET UP GMAIL SMTP:                          │
//  │                                                     │
//  │  1. Go to: https://myaccount.google.com/security    │
//  │  2. Enable 2-Step Verification (if not already)     │
//  │  3. Go to: https://myaccount.google.com/apppasswords│
//  │  4. Select "Mail" and "Other" → name it "FindHouses"│
//  │  5. Copy the 16-character password                  │
//  │  6. Paste it below as SMTP_PASS                     │
//  │                                                     │
//  │  The App Password is NOT your Gmail password.       │
//  │  It's a special password just for this app.         │
//  └─────────────────────────────────────────────────────┘
// ============================================================

// ── SMTP Server Settings ─────────────────────────────────────
define('SMTP_HOST',       'smtp.gmail.com');       // Gmail SMTP server
define('SMTP_PORT',       587);                     // TLS port
define('SMTP_USER',       'souadquzmar13@gmail.com');  // ← Your Gmail address
define('SMTP_PASS',       '');   // ← Your Gmail App Password (16 chars)

// ── Sender Info ──────────────────────────────────────────────
define('SMTP_FROM_EMAIL', 'souadquzmar13@gmail.com');  // ← Same as SMTP_USER
define('SMTP_FROM_NAME',  'FindHouses');             // Sender display name
