# PHP Coursework 1 — Online Bookstore

A simple PHP script that simulates an online bookstore using Week 1 concepts:
arrays, functions (pass-by-reference), conditionals, loops, POST handling, date/time, and file logging.

## Files
- `index.php` — main script (inventory, form handling, discounts, totals, server info, logging, display)
- `bookstore_log.txt` — append-only log (can be empty initially)
- `nonrepudiation_essay.md` — 150–250 word essay

## How to run (local)
1. Install a local PHP server (XAMPP/WAMP/MAMP) or use PHP built-in server.
2. Place the folder in:
   - XAMPP: `htdocs/your-folder/`
   - WAMP: `www/your-folder/`

### Option A: XAMPP/WAMP
Open in browser:
`http://localhost/your-folder/index.php`

### Option B: PHP built-in server
Inside the project folder:
```bash
php -S localhost:8000
