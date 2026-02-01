# ثلاث طرق لحل المشكلة:

## الطريقة 1: تعطيل strict mode في MariaDB (الأسرع)

افتح ملف `my.ini` في XAMPP (عادة في `C:\xampp\mysql\bin\my.ini`) وأضف هذا السطر تحت `[mysqld]`:

```ini
[mysqld]
sql_mode=""
```

ثم أعد تشغيل MySQL من XAMPP.

## الطريقة 2: إعادة بناء الجداول بـ VARCHAR

نفذ هذا الأمر:

```bash
php artisan migrate:fresh --seed
```

**تحذير**: هذا سيحذف جميع البيانات!

## الطريقة 3: تغيير نوع البيانات في المايجريشن

لكن هذا غير عملي لأن Laravel تستخدم LONGTEXT تلقائياً للـ string fields الطويلة.

## الحل الموصى به:

استخدم الطريقة 1 (تعطيل strict mode). هذه أسرع وأسهل طريقة وما تؤثر على البيانات.

بعد تعديل `my.ini` وإعادة تشغيل MySQL، كل الـ constraints ستختفي تلقائياً.
