# PHP.INI Configuration Required

## Current Issues

Your PHP configuration has limits that are too low for the Correspondence Management System:

```
upload_max_filesize = 2M   ❌ TOO LOW
post_max_size = 8M         ❌ TOO LOW
memory_limit = 128M        ⚠️ RECOMMENDED TO INCREASE
max_execution_time = 0     ✅ OK (unlimited for CLI)
```

## Required Configuration

Open your `php.ini` file and update these values:

```ini
; File Upload Limits
upload_max_filesize = 20M
post_max_size = 25M

; Memory & Execution
memory_limit = 256M
max_execution_time = 300

; Optional but recommended for large PDFs
max_input_time = 300
```

## How to Find php.ini Location

Run this command:
```bash
php --ini
```

Look for "Loaded Configuration File" in the output.

## After Editing php.ini

1. Save the file
2. Restart your web server:
   - **Apache**: `sudo service apache2 restart` (Linux) or restart XAMPP/WAMP
   - **Nginx + PHP-FPM**: `sudo service php8.4-fpm restart`
   - **Built-in server**: Stop and run `php artisan serve` again

3. Verify changes:
   ```bash
   php -i | Select-String -Pattern "upload_max_filesize|post_max_size|memory_limit"
   ```

## Why These Values?

- **upload_max_filesize = 20M**: Matches Media Library config (`config/media-library.php`)
- **post_max_size = 25M**: Must be larger than upload_max_filesize (allows multiple files + form data)
- **memory_limit = 256M**: Required for PDF generation with Browsershot + image conversions
- **max_execution_time = 300**: 5 minutes for PDF generation of large documents

## Alternative: .htaccess (Apache Only)

If you can't edit php.ini, create `.htaccess` in `public/` folder:

```apache
php_value upload_max_filesize 20M
php_value post_max_size 25M
php_value memory_limit 256M
php_value max_execution_time 300
```

⚠️ **Note**: This only works with Apache + mod_php, not PHP-FPM or Nginx.
