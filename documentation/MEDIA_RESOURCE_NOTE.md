# ملاحظة: MediaResource مخفي حالياً

تم إخفاء `MediaResource` من القائمة الجانبية في Filament بإضافة:

```php
protected static bool $shouldRegisterNavigation = false;
```

## السبب:

سيتم استخدام **Media Manager Plugin** بدلاً من MediaResource المخصص لإدارة الملفات بشكل أفضل.

## للوصول للملفات حالياً:

1. **من خلال Correspondences:**
   - افتح أي مراسلة
   - شاهد المرفقات في تبويب "Attachments & PDF"

2. **من خلال Database:**
   ```bash
   php artisan tinker
   >>> Media::all()
   ```

3. **إعادة تفعيل MediaResource (مؤقت):**
   - احذف السطر `protected static bool $shouldRegisterNavigation = false;`
   - من `app/Filament/Resources/MediaResource.php`

## بعد تثبيت Media Manager Plugin:

```bash
# سيتم تثبيته لاحقاً:
composer require filament/media-manager
php artisan filament:install --panels
```

سيظهر في القائمة الجانبية تحت "Media & Archiving" بشكل احترافي أكثر.

---

**تاريخ الإخفاء:** 7 ديسمبر 2025
