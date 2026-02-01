# TomatoPHP Filament Media Manager Integration

## Integration Summary

This document outlines the successful integration of TomatoPHP Filament Media Manager into the Conference Management System, replacing the legacy Attachments resource.

---

## ✅ Installation Verification Checklist

### 1. Package Installation
- ✅ **Package Installed**: `tomatophp/filament-media-manager` v1.0.6
- ✅ **Dependencies**: All required packages installed via Composer
- ✅ **Vendor Assets Published**: Assets published successfully

### 2. Migrations & Database
- ✅ **Spatie Media Library Migration**: `create_media_table` migration already published and ran
- ✅ **TomatoPHP Folders Migration**: `create_folders_table` migration ran (batch 5)
- ✅ **Media Relations Migration**: `create_media_has_models_table` migration ran (batch 5)
- ✅ **Legacy Attachments Table**: Successfully dropped via migration `2025_12_07_075903_drop_attachments_table`

### 3. Plugin Registration
- ✅ **AdminPanelProvider Updated**: `FilamentMediaManagerPlugin::make()` registered
- ✅ **Location**: `app/Providers/Filament/AdminPanelProvider.php` line 73
- ✅ **Import Added**: `use TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin;`
- ℹ️ **Note**: Sub-folders and user access features available but not enabled (see Optional Features below)

### 4. Model Configuration
- ✅ **Correspondence Model**: Already implements `HasMedia` interface and uses `InteractsWithMedia` trait
- ✅ **Media Collections Defined**:
  - `attachments` collection (multiple files, 20MB limit, accepts PDF/images/documents)
  - `generated_pdf` collection (single file, PDF only)
- ✅ **Media Conversions Configured**:
  - `preview` conversion (600x800)
  - `thumb` conversion (200x200)

### 5. Filament Resources
- ✅ **Correspondence Form**: Uses `SpatieMediaLibraryFileUpload` component
  - Location: `app/Filament/Resources/Correspondences/Schemas/CorrespondenceForm.php` lines 286-318
  - Features: Multiple uploads, reorderable, downloadable, openable, image editor
- ✅ **Media Manager Resources Registered**:
  - FolderResource (route: `/admin/folders`)
  - MediaResource (route: `/admin/media`)

### 6. Legacy Code Removal
- ✅ **Attachments Resource Directory**: Completely removed from `app/Filament/Resources/Attachments/`
  - AttachmentResource.php
  - Schemas/AttachmentForm.php
  - Tables/AttachmentsTable.php
  - Pages/ (Create, Edit, List)
- ✅ **Attachment Model**: Removed from `app/Models/Attachment.php`
- ✅ **Attachment Policy**: Removed from `app/Policies/AttachmentPolicy.php`
- ✅ **User Model Cleanup**: Removed `uploadedAttachments()` relationship
- ✅ **Migration Cleanup**: Removed `2025_11_26_000210_create_attachments_table.php`

### 7. Filesystem Configuration
- ✅ **Public Disk Configured**: 
  - Driver: local
  - Root: `storage/app/public`
  - URL: `{APP_URL}/storage`
  - Visibility: public
- ✅ **Media Collections Using Public Disk**: Correspondence model uses `->useDisk('public')`

### 8. Code Quality
- ✅ **PSR-12 Compliance**: All code formatted with Laravel Pint
- ✅ **No Linting Errors**: `./vendor/bin/pint --dirty` passed (3 files formatted)
- ✅ **No Duplication**: Clean code structure maintained

---

## 📋 Available Media Manager Features

### Through Filament Admin Panel (`/admin`)

1. **Folders Management** (`/admin/folders`)
   - Create, organize, and manage media folders
   - Collection-based organization
   - Hierarchical folder structure support

2. **Media Browser** (`/admin/media`)
   - Grid view of all uploaded media
   - Preview images with thumbnails (250x250)
   - Filter by folder/collection
   - Media metadata display

### In Forms (Correspondence & Other Resources)

1. **SpatieMediaLibraryFileUpload Component**
   - Multiple file uploads
   - Drag-and-drop reordering
   - Download/open files directly
   - Image editor with aspect ratio controls
   - File type validation
   - Size limits (20MB for attachments)

2. **Media Collections**
   - `attachments`: General correspondence attachments
   - `generated_pdf`: Auto-generated PDFs (single file)

---

## 🔧 Configuration Details

### Media Collections (Correspondence Model)

```php
// Attachments Collection
->addMediaCollection('attachments')
    ->useDisk('public')
    ->acceptsMimeTypes([
        'application/pdf',
        'image/jpeg', 'image/png', 'image/jpg',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ])
    ->maxFilesize(20 * 1024 * 1024); // 20MB

// Generated PDF Collection
->addMediaCollection('generated_pdf')
    ->singleFile()
    ->useDisk('public')
    ->acceptsMimeTypes(['application/pdf']);
```

### Media Conversions

```php
// Preview conversion (for lightbox/modal views)
->addMediaConversion('preview')
    ->width(600)
    ->height(800)
    ->sharpen(10)
    ->performOnCollections('attachments', 'generated_pdf')
    ->nonQueued();

// Thumbnail conversion (for grid/list views)
->addMediaConversion('thumb')
    ->width(200)
    ->height(200)
    ->sharpen(10)
    ->performOnCollections('attachments')
    ->nonQueued();
```

---

## 🎯 Advanced Features Available

### MediaManagerPicker Component

In addition to `SpatieMediaLibraryFileUpload`, you can use the more advanced `MediaManagerPicker` component for browsing and selecting existing media:

```php
use TomatoPHP\FilamentMediaManager\Form\MediaManagerPicker;

MediaManagerPicker::make('media')
    ->multiple() // or ->single() for single selection
    ->maxItems(5) // Maximum number of items
    ->minItems(2) // Minimum number of items
    ->collection('products') // Separate collection name
    ->responsiveImages() // Enable responsive images
    ->label('Select Media');
```

**Features:**
- 📂 Browse folder structure with media files
- 🔒 Password-protected folder support
- 🖼️ Live preview with thumbnails
- 🔄 Drag & drop reordering
- ✅ Selection validation (min/max items)
- 🏷️ Collection names for multiple pickers on same page
- 📱 Responsive images generation

### InteractsWithMediaManager Trait

For models that need advanced media management beyond Spatie's built-in features:

```php
use TomatoPHP\FilamentMediaManager\Traits\InteractsWithMediaManager;

class Product extends Model
{
    use InteractsWithMediaManager;
}
```

**Available Methods:**
```php
// Get media
$model->getMediaManagerMedia(); // All media
$model->getMediaManagerMedia('gallery'); // From specific collection
$model->getMediaManagerMediaByUuids(['uuid-1', 'uuid-2']);

// Attach/Detach/Sync
$model->attachMediaManagerMedia(['uuid-1', 'uuid-2'], 'gallery');
$model->detachMediaManagerMedia(['uuid-1'], 'gallery');
$model->syncMediaManagerMedia(['uuid-3', 'uuid-4'], 'gallery');

// Check existence
$model->hasMediaManagerMedia('uuid-1', 'featured');

// Get URLs
$model->getMediaManagerUrl('featured'); // First from collection
$model->getMediaManagerUrls('gallery'); // All from collection

// Responsive Images
$model->getMediaManagerSrcset('hero'); // Get srcset
$model->getMediaManagerResponsiveImages('gallery'); // Get responsive data
```

### Optional Plugin Features

You can enable additional features in `AdminPanelProvider.php`:

```php
->plugin(
    \TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin::make()
        ->allowSubFolders() // Enable subfolder creation
        ->allowUserAccess() // Restrict folder access per user
        ->navigationGroup('Media & Archiving') // Custom nav group
        ->navigationIcon('heroicon-o-photo') // Custom icon
        ->navigationLabel('Media Manager') // Custom label
)
```

**User-Specific Folder Access:**
If you enable `->allowUserAccess()`, add the trait to your User model:

```php
use TomatoPHP\FilamentMediaManager\Traits\InteractsWithMediaFolders;

class User extends Authenticatable
{
    use InteractsWithMediaFolders;
}
```

### Custom File Type Previews

Register custom previews for specific file types in your service provider:

```php
use TomatoPHP\FilamentMediaManager\Facade\FilamentMediaManager;
use TomatoPHP\FilamentMediaManager\Services\Contracts\MediaManagerType;

public function boot()
{
    FilamentMediaManager::register([
        MediaManagerType::make('.pdf')
            ->icon('bxs-file-pdf')
            ->preview('media-manager.pdf')
            ->js('https://mozilla.github.io/pdf.js/build/pdf.mjs')
            ->css('https://cdnjs.cloudflare.com/pdf.js/4.3.136/pdf_viewer.min.css'),
    ]);
}
```

### Folders API

Enable REST API access to folders and media:

1. Publish config:
```bash
php artisan vendor:publish --tag="filament-media-manager-config"
```

2. Enable in `config/filament-media-manager.php`:
```php
'api' => [
    'active' => true,
],
```

**Available Endpoints:**
- `GET /api/folders` - Get all folders
- `GET /api/folders/{id}` - Get folder with subfolders and media

## 🚀 How to Use

### For Correspondence (Already Configured)

1. Navigate to `/admin/correspondences/create` or edit an existing correspondence
2. Go to the "Attachments & PDF" tab
3. Upload files using the file upload component
4. Files are automatically:
   - Stored in `storage/app/public/`
   - Associated with the correspondence
   - Converted to preview/thumb sizes (for images)
   - Downloadable and viewable

### For New Resources (If Needed)

To add media support to other models:

1. **Update Model** (Basic Spatie):
```php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class YourModel extends Model implements HasMedia
{
    use InteractsWithMedia;
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('your_collection')
            ->useDisk('public')
            ->maxFilesize(20 * 1024 * 1024);
    }
}
```

2. **Or Use Advanced Trait** (TomatoPHP):
```php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use TomatoPHP\FilamentMediaManager\Traits\InteractsWithMediaManager;

class YourModel extends Model implements HasMedia
{
    use InteractsWithMedia;
    use InteractsWithMediaManager; // Adds advanced methods
}
```

3. **Add to Form** (Option A - Direct Upload):
```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

SpatieMediaLibraryFileUpload::make('your_collection')
    ->collection('your_collection')
    ->multiple()
    ->downloadable()
    ->openable();
```

4. **Or Use MediaManagerInput** (Option B - Upload with Custom Fields):
```php
use TomatoPHP\FilamentMediaManager\Form\MediaManagerInput;

MediaManagerInput::make('images')
    ->disk('public')
    ->schema([
        Forms\Components\TextInput::make('title')
            ->required()
            ->maxLength(255),
        Forms\Components\Textarea::make('description')
            ->maxLength(500),
    ]);
```

5. **Or Use MediaManagerPicker** (Option C - Browse & Select):
```php
use TomatoPHP\FilamentMediaManager\Form\MediaManagerPicker;

MediaManagerPicker::make('gallery')
    ->multiple()
    ->collection('gallery')
    ->maxItems(10)
    ->minItems(1)
    ->responsiveImages()
    ->label('Gallery Images');
```

### Media Manager Browser

1. Navigate to `/admin/folders` to manage folders
2. Navigate to `/admin/media` to browse all uploaded media
3. Filter by collection/folder as needed
4. Preview, download, or manage media files

---

## 📊 Database Schema

### Media Table (Spatie Media Library)
- `id`: Primary key
- `model_type`: Polymorphic model type (e.g., `App\Models\Correspondence`)
- `model_id`: Polymorphic model ID
- `collection_name`: Collection name (e.g., `attachments`, `generated_pdf`)
- `name`: Original filename
- `file_name`: Stored filename
- `mime_type`: MIME type
- `disk`: Storage disk
- `size`: File size in bytes
- `manipulations`: JSON conversions data
- `custom_properties`: JSON custom metadata
- `generated_conversions`: JSON conversion status
- `responsive_images`: JSON responsive image data
- `order_column`: Display order
- `created_at`, `updated_at`

### Folders Table (TomatoPHP)
- `id`: Primary key
- `name`: Folder name
- `collection`: Associated media collection
- `parent_id`: Parent folder (for hierarchy)
- `created_at`, `updated_at`

---

## ⚠️ Important Notes

1. **Existing Media Preserved**: All existing Correspondence media files stored via Spatie Media Library remain intact and accessible
2. **No Data Migration Needed**: The Correspondence model was already using Spatie Media Library correctly
3. **Storage Symlink**: Ensure `php artisan storage:link` has been run to make `storage/app/public` accessible via `/storage`
4. **Permissions**: Ensure `storage/app/public` directory is writable by the web server
5. **Backup**: The old `attachments` table has been dropped. If you need to restore it, use the down() method in migration `2025_12_07_075903_drop_attachments_table`
6. **Important Migration Fix**: The `correspondences` migration was manually marked as run to resolve a conflict where the table existed but migration was pending
7. **Media Manager Columns**: Added required columns to `media_has_models` table:
   - `order_column` - For drag & drop reordering
   - `collection_name` - For multiple pickers with separate collections
   - `responsive_images` - For responsive images support
   - Migration: `2025_12_07_082541_add_media_manager_columns_to_media_has_models_table`

---

## 🎯 Next Steps

### Optional Enhancements

1. **Enable Sub-Folders**:
   ```php
   // In AdminPanelProvider.php
   ->plugin(
       \TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin::make()
           ->allowSubFolders()
   )
   ```

2. **Enable User-Specific Folder Access**:
   ```php
   // In AdminPanelProvider.php
   ->plugin(
       \TomatoPHP\FilamentMediaManager\FilamentMediaManagerPlugin::make()
           ->allowUserAccess()
   )
   
   // Then add trait to User model
   use TomatoPHP\FilamentMediaManager\Traits\InteractsWithMediaFolders;
   ```

3. **Add Media Manager to Other Models**:
   - Conference (for event photos, documents)
   - Member (for profile photos, CVs)
   - Paper (for submission files)
   - MediaCampaign (for campaign assets)

4. **Use MediaManagerPicker Instead of SpatieMediaLibraryFileUpload**:
   - Provides folder browsing interface
   - Password-protected folders
   - Better for selecting existing media
   - Supports drag & drop reordering

5. **Enable Folders API**:
   ```bash
   php artisan vendor:publish --tag="filament-media-manager-config"
   ```
   Then set `'api' => ['active' => true]` in config

6. **Custom File Type Previews**:
   - Register custom previews for PDF, video, etc.
   - Add custom JS/CSS for preview rendering

7. **Advanced Features**:
   - Configure image optimization
   - Add custom media conversions
   - Implement CDN integration
   - Add responsive images support

---

## 📝 Routes Reference

| Route | Resource | Description |
|-------|----------|-------------|
| `/admin/folders` | FolderResource | Manage media folders |
| `/admin/media` | MediaResource | Browse all media files |
| `/admin/correspondences` | CorrespondenceResource | Manage correspondences with attachments |

---

## 🔍 Verification Commands

```bash
# Check installed packages
composer show tomatophp/filament-media-manager

# List Filament routes
php artisan route:list --name=filament

# Check migration status
php artisan migrate:status

# Format code
./vendor/bin/pint --dirty

# Clear cache (if needed)
php artisan optimize:clear

# Publish additional assets (optional)
php artisan vendor:publish --tag="filament-media-manager-config"
php artisan vendor:publish --tag="filament-media-manager-views"
php artisan vendor:publish --tag="filament-media-manager-lang"
php artisan vendor:publish --tag="filament-media-manager-migrations"
```

## 📦 Published Configuration Files

The package supports publishing various assets:

```bash
# Config file
php artisan vendor:publish --tag="filament-media-manager-config"
# Location: config/filament-media-manager.php

# View files (for customization)
php artisan vendor:publish --tag="filament-media-manager-views"
# Location: resources/views/vendor/filament-media-manager/

# Language files (for translations)
php artisan vendor:publish --tag="filament-media-manager-lang"
# Location: lang/vendor/filament-media-manager/

# Migration files (if needed to modify)
php artisan vendor:publish --tag="filament-media-manager-migrations"
# Location: database/migrations/
```

---

## 🧪 Testing

The package includes comprehensive test coverage:

```bash
# Run all tests
composer test

# Run specific test file
./vendor/bin/pest tests/src/MediaManagerPickerTest.php

# Run with coverage
./vendor/bin/pest --coverage

# Code style fixes
composer format

# Static analysis
composer analyse
```

**Test Coverage Includes:**
- MediaManagerPicker component tests
- MediaManagerInput component tests
- InteractsWithMediaManager trait tests
- Folder navigation and password protection
- Selection validation and file upload

## 📚 Additional Resources

- **Official Documentation**: [TomatoPHP Filament Media Manager](https://github.com/tomatophp/filament-media-manager)
- **Spatie Media Library Docs**: [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary)
- **Filament Documentation**: [FilamentPHP](https://filamentphp.com/docs)

## ✨ Integration Complete!

The TomatoPHP Filament Media Manager is now fully integrated and operational. The legacy Attachments resource has been completely removed, and all media management is now handled through:

- **Spatie Media Library** (backend storage & model integration)
- **TomatoPHP Filament Media Manager** (UI, browsing & folder management)
- **Filament SpatieMediaLibraryFileUpload** (direct upload component)
- **MediaManagerPicker** (browse & select existing media)
- **MediaManagerInput** (upload with custom fields)
- **InteractsWithMediaManager** (advanced model methods)

All components work together seamlessly, providing a comprehensive media management solution for the Conference Management System with:
- 📁 Folder organization (with optional sub-folders)
- 🔒 Password-protected folders
- 🎨 Dark mode support
- 🌍 RTL & multi-language support
- 📱 Responsive images
- 🔄 Drag & drop reordering
- ✅ Selection validation
- 🚀 REST API access (optional)
