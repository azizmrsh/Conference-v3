# Conference Management System - AI Agent Instructions

## Project Overview
This is a **Conference Management System** built with **Laravel 12** and **Filament v3** for managing academic/professional conferences end-to-end. The system handles pre-conference planning, logistics, scientific committee work, media campaigns, and conference operations.

## Architecture & Domain Structure

### Core Domain: Conference Lifecycle
The system is organized around the conference lifecycle with distinct phases:

**Pre-Conference** (Planning & Preparation):
- `Conference` - Central entity with sessions, topics, and dates
- `Member` - Potential participants/speakers 
- `Invitation` - Links members to conferences
- `Correspondence` - Communication tracking
- `Task` - Work items with assignments and deadlines
- `Attachment` - Document management

**Logistics**:
- `TravelBooking` - Flight and hotel reservations
- `Airline`, `Airport`, `Hotel` - Supporting entities
- Foreign key pattern: `airline_id`, `airport_from_id`, `airport_to_id`

**Scientific Committee**:
- `Paper` - Submissions linked to invitations
- `Review` - Peer review workflow with scores
- `Committee`, `CommitteeMember` - Committee structure

**Conference Operations**:
- `ConferenceSession` - Conference sessions with topics
- `ConferenceTopic` - Organizing themes
- `Attendance` - Session attendance tracking
- `BadgesKit` - Attendee badges/materials

**Finance & Media**:
- `Transaction` - Income/expense tracking (`tx_type`: income/expense)
- `MediaCampaign`, `PressRelease` - Media management

### Navigation Groups
Resources are organized in the Filament sidebar by `navigationGroup`:
- Pre-Conference
- Logistics  
- Scientific Committee
- Conference Operations
- Conference Management
- Finance
- Media & Archiving
- System

## Filament Resource Architecture Pattern

**CRITICAL**: This codebase uses a **specialized structure** that differs from standard Filament:

### 1. Schema/Table Separation Pattern
Resources delegate form/table definitions to dedicated classes:

```php
// Resource file (e.g., ConferenceResource.php)
public static function form(Form $form): Form {
    return ConferenceForm::configure($form);  // Delegates to Schemas/
}
public static function table(Table $table): Table {
    return ConferencesTable::configure($table);  // Delegates to Tables/
}
```

Directory structure per resource:
```
app/Filament/Resources/Conferences/
├── ConferenceResource.php           # Main resource
├── Schemas/
│   └── ConferenceForm.php          # Form definition
├── Tables/
│   └── ConferencesTable.php        # Table definition
├── Pages/                           # CRUD pages
└── RelationManagers/                # Related entities
```

### 2. Form Schema Classes
Located in `Schemas/` subdirectories:
```php
class ConferenceForm {
    public static function configure(Form $form): Form {
        return $form->schema([...]);  // NOT ->components([...])
    }
}
```

### 3. Table Classes  
Located in `Tables/` subdirectories:
```php
class ConferencesTable {
    public static function configure(Table $table): Table {
        return $table->columns([...])->filters([...])->actions([...]);
    }
}
```

### 4. Creating New Resources
When scaffolding resources:
1. Create base resource in `app/Filament/Resources/{Domain}/`
2. Add `Schemas/{Name}Form.php` for form logic
3. Add `Tables/{Name}sTable.php` for table logic
4. Create CRUD pages in `Pages/`
5. Add relation managers if needed

**Example**:
```php
// BadgesKitResource.php structure
protected static ?string $navigationGroup = 'Media & Archiving';
protected static ?int $navigationSort = 180;  // Controls sidebar order
```

## Data Patterns & Conventions

### User Tracking Pattern
Most tables track `created_by` and `updated_by`:
```php
// Migration pattern
$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
$table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

// Model relationships
public function creator() {
    return $this->belongsTo(User::class, 'created_by');
}
```

### Cascade vs Null Deletion
- `cascadeOnDelete()`: Parent owns child (e.g., `invitation_id` in `papers`)
- `nullOnDelete()`: Soft dependency (e.g., `created_by` user references)

### Computed Fields
Some models auto-calculate counts:
```php
// Conference model
public function updateSessionsCount(): void {
    $this->sessions_count = $this->sessions()->count();
    $this->saveQuietly();  // Avoid triggering observers
}
```

## Development Workflows

### Running the Application
```bash
composer dev  # Runs server, queue worker, and Vite in parallel
```
Equivalent to:
```bash
php artisan serve &
php artisan queue:listen --tries=1 &
npm run dev
```

### Code Quality Checks
```bash
composer check  # Runs Pint (formatter), PHPStan (static analysis), and tests
```

Individual commands:
```bash
./vendor/bin/pint --dirty        # Format only changed files
php artisan test                  # Run Pest tests
./vendor/bin/phpstan analyse --memory-limit=2G  # Static analysis at level 5
```

### Database Workflow
Uses SQLite by default (`database/database.sqlite`). For MySQL, update `.env` and run:
```bash
php artisan migrate
```

**Migration Fix Script**: `fix-migration.ps1` - PowerShell script for correcting Filament v4→v3 migration issues (heroicon syntax, form schema methods)

### Initial Setup (Fresh Install)
```bash
php artisan make:filament-user
php artisan shield:super-admin --user=1 --panel=admin
php artisan shield:generate --all --ignore-existing-policies --panel=admin
```

## Technology Stack Specifics

### Installed Filament Plugins
- **Shield**: Role/permission management via spatie/laravel-permission
- **Breezy**: User profile pages (`myProfile` enabled)
- **Themes**: User-selectable themes
- **Backgrounds**: Auth page backgrounds
- **Logger**: Activity logging (resource at `config('filament-logger.activity_resource')`)

### Frontend Build
- Vite for asset bundling
- Custom theme: `resources/css/filament/admin/theme.css`
- Panel path: `/admin`

## Common Patterns & Anti-Patterns

### ✅ DO:
- Keep form/table logic in `Schemas/` and `Tables/` classes
- Use static `::configure()` methods for reusable components
- Set `navigationGroup` and `navigationSort` on resources
- Track `created_by`/`updated_by` for audit trails
- Use `saveQuietly()` for system updates to avoid event loops

### ❌ DON'T:
- Define forms/tables inline in resource classes
- Use `->components()` in form schemas (use `->schema()`)
- Forget to update relationship counts when manipulating related data
- Mix Filament v4 syntax (heroicon imports, schema parameters)

## Model Relationships Cheat Sheet
```
Conference
├── hasMany: sessions, topics, invitations, tasks, transactions, 
│            mediaCampaigns, committees, badgesKits, correspondences
│
Invitation
├── belongsTo: conference, member
├── hasMany: papers, travelBookings
│
Paper
├── belongsTo: invitation
├── hasMany: reviews
│
ConferenceSession
├── belongsTo: conference, topic
├── hasMany: attendances
```

## Testing
- Framework: **Pest v4** (not PHPUnit)
- DB: `RefreshDatabase` trait enabled in `tests/Pest.php`
- Test location: `tests/Feature/`

## Key Files to Reference
- `app/Filament/Resources/Conferences/` - Complete example of the schema/table pattern
- `app/Models/Conference.php` - Relationship structure, computed fields
- `database/migrations/2025_11_26_000140_create_conferences_table.php` - Migration patterns
- `composer.json` scripts - Available commands
- `fix-migration.ps1` - Migration helper script patterns
