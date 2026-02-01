# 🐛 دليل استكشاف الأخطاء وحلها - نظام المراسلات

## 📋 الفهرس

1. [أخطاء قاعدة البيانات](#أخطاء-قاعدة-البيانات)
2. [أخطاء الملفات والوسائط](#أخطاء-الملفات-والوسائط)
3. [أخطاء PDF](#أخطاء-pdf)
4. [أخطاء البريد الإلكتروني](#أخطاء-البريد-الإلكتروني)
5. [أخطاء الصلاحيات](#أخطاء-الصلاحيات)
6. [أخطاء عامة](#أخطاء-عامة)

---

## 1. أخطاء قاعدة البيانات

### ❌ خطأ: Column 'deleted_at' not found

**الرسالة الكاملة:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'deleted_at' in 'where clause'
```

**السبب:**
Model يستخدم `SoftDeletes` لكن الجدول لا يحتوي على عمود `deleted_at`

**الحل:**
```bash
# 1. إنشاء migration
php artisan make:migration add_deleted_at_to_correspondences_table

# 2. في الـ migration:
public function up()
{
    Schema::table('correspondences', function (Blueprint $table) {
        $table->softDeletes();
    });
}

# 3. تشغيل الـ migration
php artisan migrate
```

---

### ❌ خطأ: Column 'member.name' not found

**الرسالة الكاملة:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'member.name'
```

**السبب:**
عمود `name` غير موجود في جدول `members`، الاسم الصحيح هو `full_name`

**الحل:**
```php
// ❌ خطأ:
->relationship('member', 'name')

// ✅ صحيح:
->relationship('member', 'full_name')
```

**ابحث واستبدل في:**
- `CorrespondenceForm.php`
- `CorrespondencesTable.php`
- `ViewCorrespondence.php`

---

### ❌ خطأ: Integrity constraint violation

**الرسالة الكاملة:**
```
SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row
```

**السبب:**
محاولة ربط مراسلة بـ conference_id أو member_id غير موجود

**الحل:**
```php
// تحقق من وجود الـ ID قبل الحفظ:
if ($data['conference_id'] && !Conference::find($data['conference_id'])) {
    $data['conference_id'] = null;
}
```

---

## 2. أخطاء الملفات والوسائط

### ❌ خطأ: Class SpatieMediaLibraryFileUpload not found

**الحل:**
```php
// أضف في أعلى الملف:
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
```

---

### ❌ خطأ: Class SpatieMediaLibraryImageEntry not found

**الحل:**
```php
// أضف في أعلى الملف:
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
```

---

### ❌ خطأ: Storage disk not found

**الرسالة:**
```
Disk [public] does not have a configured driver.
```

**الحل:**
```bash
# 1. تأكد من وجود storage link
php artisan storage:link

# 2. تحقق من config/filesystems.php:
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],

# 3. امسح الـ cache
php artisan config:clear
```

---

### ❌ خطأ: File upload exceeds maximum size

**الحل:**

**في `php.ini`:**
```ini
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 300
```

**في `config/media-library.php`:**
```php
'max_file_size' => 1024 * 1024 * 20, // 20MB
```

**في Form:**
```php
->maxSize(20480) // 20MB in KB
```

---

### ❌ خطأ: Conversion preview not generated

**السبب:**
GD Library غير مثبتة أو معطلة

**الحل:**
```bash
# تحقق من تثبيت GD:
php -m | grep -i gd

# إذا لم تكن مثبتة:
# Ubuntu/Debian:
sudo apt-get install php-gd

# CentOS/RHEL:
sudo yum install php-gd

# إعادة تشغيل Apache/Nginx
sudo service apache2 restart
```

**أو استخدم Imagick:**
```php
// في config/media-library.php:
'image_driver' => 'imagick',
```

---

## 3. أخطاء PDF

### ❌ خطأ: Browsershot not found

**الرسالة:**
```
Binary not found
```

**الحل:**
```bash
# تثبيت Puppeteer
npm install puppeteer

# أو تثبيت Chrome مباشرة
npx @puppeteer/browsers install chrome

# تحقق من المسار
which google-chrome
# أو
which chromium-browser
```

**تعيين المسار يدوياً:**
```php
Browsershot::html($html)
    ->setChromePath('/usr/bin/google-chrome')
    ->save($path);
```

---

### ❌ خطأ: PDF generation timeout

**الحل:**
```php
Browsershot::html($html)
    ->timeout(120) // 2 دقيقة
    ->waitUntilNetworkIdle()
    ->save($path);
```

---

### ❌ خطأ: PDF contains broken images

**السبب:**
روابط الصور نسبية أو غير صحيحة

**الحل:**
```blade
{{-- ❌ خطأ --}}
<img src="/images/logo.png">

{{-- ✅ صحيح --}}
<img src="{{ asset('images/logo.png') }}">
{{-- أو --}}
<img src="{{ public_path('images/logo.png') }}">
```

---

## 4. أخطاء البريد الإلكتروني

### ❌ خطأ: Connection refused

**الرسالة:**
```
Connection could not be established with host smtp.gmail.com
```

**الحل:**

**في `.env`:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**ملاحظة:** استخدم App Password وليس كلمة المرور العادية للـ Gmail

---

### ❌ خطأ: Attachment not found

**السبب:**
مسار الملف غير صحيح

**الحل:**
```php
// ❌ خطأ
->attach('/pdfs/file.pdf')

// ✅ صحيح
->attach(storage_path('app/public/pdfs/file.pdf'))
// أو
->attach($correspondence->getFirstMediaPath('generated_pdf'))
```

---

### ❌ خطأ: Mail queue not processed

**الحل:**
```bash
# تشغيل Queue Worker
php artisan queue:work

# أو في الخلفية:
nohup php artisan queue:work &

# تحقق من الـ jobs
php artisan queue:failed
```

---

## 5. أخطاء الصلاحيات

### ❌ خطأ: This action is unauthorized

**الحل:**
```bash
# 1. توليد الصلاحيات
php artisan shield:generate --resource=CorrespondenceResource --panel=admin

# 2. منح Super Admin
php artisan shield:super-admin --user=1

# 3. مسح الـ cache
php artisan optimize:clear
```

---

### ❌ خطأ: Policy class not found

**الحل:**
```bash
# إنشاء Policy
php artisan make:policy CorrespondencePolicy --model=Correspondence

# تسجيل في AuthServiceProvider:
protected $policies = [
    Correspondence::class => CorrespondencePolicy::class,
];
```

---

## 6. أخطاء عامة

### ❌ خطأ: Route not found

**الرسالة:**
```
Target class [CorrespondenceController] does not exist.
```

**الحل:**
```bash
# مسح route cache
php artisan route:clear
php artisan optimize:clear

# التحقق من الـ routes
php artisan route:list | grep correspondence
```

---

### ❌ خطأ: Class not found

**الحل:**
```bash
# إعادة تحميل Composer autoload
composer dump-autoload

# مسح الـ cache
php artisan optimize:clear
```

---

### ❌ خطأ: View not found

**الرسالة:**
```
View [emails.correspondence-sent] not found
```

**الحل:**
```bash
# مسح view cache
php artisan view:clear

# التحقق من وجود الملف:
ls resources/views/emails/correspondence-sent.blade.php
```

---

### ❌ خطأ: URL مكرر (/admin/correspondences/correspondences)

**السبب:**
Filament يستخدم namespace كـ slug

**الحل:**
```php
// في CorrespondenceResource.php:
protected static ?string $slug = 'correspondences';
```

---

### ❌ خطأ: Memory limit exceeded

**الحل:**

**في `php.ini`:**
```ini
memory_limit = 512M
```

**أو في الكود:**
```php
ini_set('memory_limit', '512M');
```

**أو عند تشغيل الأمر:**
```bash
php -d memory_limit=512M artisan command:name
```

---

## 🔍 أدوات التشخيص

### فحص حالة النظام:

```bash
# حالة الـ migrations
php artisan migrate:status

# Routes المتاحة
php artisan route:list | grep correspondence

# الـ Policies
php artisan shield:check

# Storage Link
ls -la public/storage

# PHP Info
php -i | grep -i gd
php -i | grep -i memory
```

---

### فحص Logs:

```bash
# Laravel Log
tail -f storage/logs/laravel.log

# Apache Error Log
tail -f /var/log/apache2/error.log

# Nginx Error Log
tail -f /var/log/nginx/error.log
```

---

### Tinker للتجربة:

```bash
php artisan tinker

# اختبار إنشاء مراسلة:
>>> $c = App\Models\Correspondence::first()
>>> $c->hasAttachments()
>>> $c->getMedia('attachments')
>>> $c->hasPdf()

# اختبار Media Library:
>>> use Spatie\MediaLibrary\MediaCollections\Models\Media;
>>> Media::count()
>>> Media::latest()->first()

# اختبار الصلاحيات:
>>> auth()->user()->can('view_correspondence')
```

---

## ⚠️ ملاحظات مهمة

### MediaResource مخفي مؤقتاً

**تم إخفاء MediaResource من القائمة الجانبية** لأننا سنستخدم **Media Manager Plugin** بدلاً منه.

**للوصول للملفات:**

- استخدم Media Manager Plugin (سيتم تثبيته لاحقاً)
- أو قم بتفعيل MediaResource بإزالة السطر:

```php
protected static bool $shouldRegisterNavigation = false;
```

---

## 📞 الحصول على المساعدة

إذا استمرت المشكلة:

**الخطوة 1: تحقق من Logs**

```bash
tail -100 storage/logs/laravel.log
```

**الخطوة 2: Enable Debug Mode**

```env
APP_DEBUG=true
```

**الخطوة 3: مسح جميع الـ Cache**

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

**الخطوة 4: إعادة تثبيت الحزم**

```bash
composer install
npm install
```

**الخطوة 5: راجع الوثائق الكاملة**

- `documentation/CORRESPONDENCE_MEDIA_LIBRARY_DOCUMENTATION.md`

---

**آخر تحديث:** 7 ديسمبر 2025  
**الإصدار:** 1.0.0
