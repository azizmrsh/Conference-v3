# 📌 مرجع سريع - نظام المراسلات

## 🚀 الأوامر الأساسية

```bash
# تشغيل المشروع
composer dev

# إنشاء مراسلة جديدة
/admin/correspondences → New Correspondence

# عرض مكتبة الوسائط
/admin/media

# مسح Cache
php artisan optimize:clear
```

---

## 📁 المسارات المهمة

| الوظيفة | المسار |
|---------|--------|
| قائمة المراسلات | `/admin/correspondences` |
| إنشاء مراسلة | `/admin/correspondences/create` |
| عرض مراسلة | `/admin/correspondences/{id}` |
| مكتبة الوسائط | `/admin/media` |

---

## 🔧 Model: Correspondence

### Helper Methods

```php
// التحقق من الملفات
$correspondence->hasAttachments()      // bool
$correspondence->hasPdf()              // bool
$correspondence->getAttachmentsCount() // int

// الحصول على الملفات
$correspondence->latestPdf()           // string|null (URL)
$correspondence->getMedia('attachments') // Collection
```

### Collections

- `attachments` → ملفات متعددة (PDF, Images, DOC)
- `generated_pdf` → ملف PDF واحد

### Conversions

- `preview` → 600x800px
- `thumb` → 200x200px

---

## 📧 إرسال بريد

```php
use App\Mail\CorrespondenceSent;

Mail::to('email@example.com')
    ->cc(['cc1@example.com', 'cc2@example.com'])
    ->send(new CorrespondenceSent($correspondence, 'رسالة إضافية'));
```

---

## 📄 توليد PDF

```php
use App\Services\CorrespondencePdfService;

$service = new CorrespondencePdfService();
$pdfPath = $service->generatePdf($correspondence);
```

---

## 📎 رفع ملفات

```php
// رفع ملف واحد
$correspondence->addMedia($filePath)
    ->toMediaCollection('attachments');

// رفع متعدد
foreach ($files as $file) {
    $correspondence->addMedia($file)
        ->toMediaCollection('attachments');
}

// حذف ملف
$correspondence->clearMediaCollection('attachments');
```

---

## 🔍 الفلترة والبحث

### في الجدول:

```php
// البحث بالرقم المرجعي
reference_number: CORR-2025-0001

// البحث بالموضوع
subject: دعوة مؤتمر

// الفلترة بالحالة
status: sent, draft, replied, etc.

// الفلترة بالاتجاه
direction: outgoing, incoming
```

---

## 🎨 حالات المراسلات (Status)

| الحالة | الوصف | اللون |
|--------|--------|-------|
| draft | مسودة | رمادي |
| sent | مُرسلة | أزرق |
| received | مُستلمة | أصفر |
| replied | تم الرد | أخضر |
| approved | موافق عليها | أخضر |
| rejected | مرفوضة | أحمر |
| pending | قيد الانتظار | أصفر |

---

## 🔐 الصلاحيات

### Correspondence Permissions:

- `view_correspondence`
- `view_any_correspondence`
- `create_correspondence`
- `update_correspondence`
- `delete_correspondence`
- ... إلخ (12 صلاحية)

### Media Permissions:

- `view_media`
- `create_media`
- `update_media`
- `delete_media`
- ... إلخ (12 صلاحية)

### منح صلاحيات:

```bash
php artisan shield:super-admin --user=1
```

---

## 🐛 المشاكل الشائعة

### المشكلة: عمود deleted_at مفقود

```bash
php artisan make:migration add_deleted_at_to_correspondences_table
# ثم أضف: $table->softDeletes();
php artisan migrate
```

### المشكلة: SpatieMediaLibraryImageEntry غير معرّف

```php
// أضف في أعلى الملف:
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
```

### المشكلة: URL مكرر

```php
// في CorrespondenceResource.php:
protected static ?string $slug = 'correspondences';
```

---

## 📦 الحزم المستخدمة

```json
{
  "spatie/laravel-medialibrary": "^11.0",
  "spatie/browsershot": "^5.1",
  "filament/spatie-laravel-media-library-plugin": "^3.2"
}
```

---

## 🔄 Workflow سريع

```
1. إنشاء → ملء البيانات → رفع ملفات → حفظ
2. توليد PDF → Generate PDF Action
3. إرسال → Send Email Action → ملء البيانات → Submit
4. المتابعة → Mark as Replied/Approved/Rejected
```

---

## 💾 Database

### الجداول:

- `correspondences` → المراسلات
- `media` → الملفات
- `users` → المستخدمين
- `conferences` → المؤتمرات
- `members` → الأعضاء

### العلاقات:

```php
Correspondence
  → belongsTo: conference, member, creator, updater
  → morphMany: media
```

---

## 🎯 أمثلة سريعة

### إنشاء مراسلة:

```php
Correspondence::create([
    'reference_number' => 'CORR-2025-0001',
    'subject' => 'الموضوع',
    'content' => 'المحتوى',
    'direction' => 'outgoing',
    'status' => 'draft',
]);
```

### البحث:

```php
Correspondence::where('status', 'sent')
    ->where('direction', 'outgoing')
    ->with('media')
    ->get();
```

### إحصائيات:

```php
// عدد المراسلات المرسلة
Correspondence::where('status', 'sent')->count()

// عدد المراسلات بملفات
Correspondence::has('media')->count()

// عدد المراسلات لمؤتمر معين
Correspondence::where('conference_id', 1)->count()
```

---

## 📞 للمساعدة

- **Logs:** `storage/logs/laravel.log`
- **Tinker:** `php artisan tinker`
- **Cache:** `php artisan optimize:clear`
- **الوثائق:** `documentation/CORRESPONDENCE_MEDIA_LIBRARY_DOCUMENTATION.md`

---

**آخر تحديث:** 7 ديسمبر 2025
