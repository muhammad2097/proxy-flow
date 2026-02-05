# React + PHP API (Hostinger-ready)

Place your React build files (index.html and static assets) into `public_html/` root.
API endpoints live under `public_html/api/*.php`.
Config files are in `public_html/config/`.

Environment variables supported (optional):
- DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT

To test locally with PHP builtin server:
1. cd public_html
2. php -S localhost:8000

Then call endpoints like:
http://localhost:8000/api/login.php
