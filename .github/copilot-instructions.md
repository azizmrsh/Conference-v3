# Conference Management System - AI Agent Instructions

## Project Overview
**Conference Management System** built with **Laravel 12** and **Filament v3** for managing academic/professional conferences end-to-end. The system handles pre-conference planning, logistics, scientific committee work, media campaigns, and conference operations.

## Architecture & Domain Structure

### Core Domain: Conference Lifecycle
The system follows a strict **parent-child cascade deletion** pattern where Conference is the central aggregate root. The lifecycle flows through distinct phases:

**Pre-Conference** (Planning & Preparation):
- `Conference` - Central aggregate root (all children cascade delete when conference is removed)
- `Member` - Potential participants/speakers (independent entity)
- `Invitation` - Links members to conferences (cascade deletes with conference)
- `Correspondence` - Communication tracking (nullable conference reference)
- `Task` - Work items with assignments and deadlines (cascade deletes with conference)
- `Attachment` - Document management

**Logistics**:
- `TravelBooking` - Flight and hotel reservations (cascade deletes with invitation)
- `Airline`, `Airport`, `Hotel` - Supporting reference entities
- Foreign key pattern: `airline_id`, `airport_from_id`, `airport_to_id`

**Scientific Committee**:
- `Paper` - Submissions linked to invitations (cascade deletes with invitation)
- `Review` - Peer review workflow with scores (cascade deletes with paper)
- `Committee`, `CommitteeMember` - Committee structure (cascade deletes with conference/committee)

**Conference Operations**:
- `ConferenceSession` - Conference sessions (cascade deletes with conference)
- `ConferenceTopic` - Organizing themes (cascade deletes with conference)
- `Attendance` - Session attendance tracking (cascade deletes with conference/invitation/member)
- `BadgesKit` - Attendee badges/materials (cascade deletes with conference)

**Finance & Media**:
- `Transaction` - Income/expense tracking (cascade deletes with conference, `tx_type` enum: budget_item/expense/payment)
- `MediaCampaign` - Media campaigns (cascade deletes with conference)
- `PressRelease` - Press releases (cascade deletes with media_campaign)

### Navigation Groups
Resources are organized in the Filament sidebar by `navigationGroup` and `navigationSort`:
- Pre-Conference (110: Conference, 130: Invitation, 160: Task)
- Logistics (310: TravelBooking, 340: Hotel)
- Scientific Committee (210: Paper, 220: Review)
- Conference Operations
- Conference Management (180: ConferenceSession)
- Finance (610: Transaction)
- Media & Archiving (520: PressRelease)
- System (10: User)

## Filament Resource Architecture Pattern

**CRITICAL**: This codebase uses a **strict separation-of-concerns pattern** that differs from standard Filament:

### Directory Structure Pattern (MANDATORY)
Every resource MUST follow this exact structure:
```
app/Filament/Resources/{PluralName}/
├── {SingularName}Resource.php       # Main resource (routing, model binding, relations)
├── Schemas/
│   └── {SingularName}Form.php       # Form definition ONLY
├── Tables/
│   └── {PluralName}Table.php        # Table definition ONLY
├── Pages/                            # CRUD pages (List, Create, Edit, View)
│   ├── List{PluralName}.php
│   ├── Create{SingularName}.php
│   ├── Edit{SingularName}.php
│   └── View{SingularName}.php
└── RelationManagers/                 # Related entities (optional)
```

**Real examples from codebase**:
- `Conferences/ConferenceResource.php` + `Schemas/ConferenceForm.php` + `Tables/ConferencesTable.php`
- `Correspondences/CorrespondenceResource.php` + `Schemas/CorrespondenceForm.php` + `Tables/CorrespondencesTable.php`

### Resource File Pattern
```php
class ConferenceResource extends Resource {
    protected static ?string $model = Conference::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationGroup = 'Pre-Conference';
    protected static ?int $navigationSort = 110;  // Controls sidebar order

    public static function form(Form $form): Form {
        return ConferenceForm::configure($form);  // ALWAYS delegate
    }

    public static function table(Table $table): Table {
        return ConferencesTable::configure($table);  // ALWAYS delegate
    }

    public static function getRelations(): array {
        return [TopicsRelationManager::class];
    }

    public static function getPages(): array {
        return [
            'index' => ListConferences::route('/'),
            'create' => CreateConference::route('/create'),
            'edit' => EditConference::route('/{record}/edit'),
            'view' => ViewConference::route('/{record}'),
        ];
    }
}
```

### Form Schema Classes (Schemas/)
```php
class ConferenceForm {
    public static function configure(Form $form): Form {
        return $form->schema([  // MUST use ->schema(), NOT ->components()
            Section::make('Basic Information')
                ->icon('heroicon-o-information-circle')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->required(),
                    DatePicker::make('start_date')->required(),
                    // ...
                ]),
        ]);
    }
}
```

### Table Classes (Tables/)
```php
class ConferencesTable {
    public static function configure(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('status')->badge()->color(fn($state) => match($state) {
                    'active' => 'warning', 'completed' => 'success', default => 'gray'
                }),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([...])
            ->actions([ViewAction::make(), EditAction::make()]);
    }
}
```

### Creating New Resources
1. Create domain directory: `app/Filament/Resources/{PluralName}/`
2. Create resource file: `{SingularName}Resource.php` with delegation pattern
3. Create `Schemas/{SingularName}Form.php` with `->schema()` method
4. Create `Tables/{PluralName}Table.php` with columns/filters/actions
5. Create Pages in `Pages/` (List/Create/Edit/View)
6. Set `navigationGroup` and `navigationSort` for sidebar organization

## Data Patterns & Conventions

### Cascade Deletion Strategy
**CRITICAL**: The system uses a strict cascade hierarchy with Conference as the root:

**Cascade Delete Chains**:
```
Conference (deleted)
  ├──> Invitations (cascade)
  │      ├──> Papers (cascade)
  │      │      └──> Reviews (cascade)
  │      └──> TravelBookings (cascade)
  ├──> ConferenceSessions (cascade)
  ├──> ConferenceTopics (cascade)
  ├──> Tasks (cascade, nullable conference_id)
  ├──> Transactions (cascade)
  ├──> MediaCampaigns (cascade)
  │      └──> PressReleases (cascade)
  ├──> Committees (cascade)
  │      └──> CommitteeMembers (cascade)
  ├──> BadgesKits (cascade)
  └──> Attendances (cascade)
```

**Migration Pattern**:
```php
// Parent owns child (data loss expected)
$table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();

// Soft dependency (preserve child if parent deleted)
$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

// Nullable cascade (optional parent)
$table->foreignId('conference_id')->nullable()->constrained('conferences')->cascadeOnDelete();
```

### User Tracking Pattern
ALL domain tables track creators/updaters:
```php
// Migration
$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
$table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

// Model
public function creator() {
    return $this->belongsTo(User::class, 'created_by');
}
public function updater() {
    return $this->belongsTo(User::class, 'updated_by');
}
```

### Computed/Cached Fields
Models auto-update counts to avoid expensive queries:
```php
// Conference model
public function updateSessionsCount(): void {
    $this->sessions_count = $this->sessions()->count();
    $this->saveQuietly();  // CRITICAL: Avoid observer loops
}
```

**When to use `saveQuietly()`**:
- Updating computed fields from observers/events
- System-triggered updates (not user actions)
- Preventing infinite loops in model events

### Enum Patterns
Consistent enum usage across migrations:
```php
// Status enums (most common)
enum('status', ['draft','sent','received','replied','approved','rejected','pending'])->default('draft')
enum('status', ['planning','active','completed','archived'])->default('planning')
enum('status', ['not_started','in_progress','waiting','completed','overdue','cancelled'])->default('not_started')

// Directional/type enums
enum('direction', ['outgoing','incoming'])->default('outgoing')
enum('tx_type', ['budget_item','expense','payment'])
enum('method', ['scan','manual'])->default('scan')
```

### Date/Time Field Conventions
```php
// Model casts
protected $casts = [
    'start_date' => 'datetime',
    'correspondence_date' => 'date',  // Date-only fields
    'response_received' => 'boolean',
    'review_scores' => 'array',       // JSON storage
];

// Migration
$table->date('correspondence_date')->nullable();
$table->datetime('invitation_sent_at')->nullable();
$table->timestamps();  // created_at, updated_at
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
Uses **SQLite by default** (`database/database.sqlite`). For MySQL:
1. Update `.env` database credentials
2. Run `php artisan migrate`
3. Optional: Delete `database/database.sqlite`

**Migration naming convention**:
- Pattern: `YYYY_MM_DD_HHMMSS_create_{table_name}_table.php`
- Foreign keys AFTER parent tables created
- Indexes for: status enums, date ranges, foreign keys, search fields

**Migration Fix Script**: 
`fix-migration.ps1` - PowerShell script for correcting Filament v4→v3 migration issues:
- Removes unused imports (BackedEnum, UnitEnum, Heroicon)
- Fixes icon syntax (e.g., `'heroicon-o-user'Group` → `'heroicon-o-user-group'`)
- Changes `->components()` to `->schema()` in form classes
- Updates form method parameters from `$schema` to `$form`

### Initial Setup (Fresh Install)
```bash
# 1. Create admin user
php artisan make:filament-user

# 2. Assign super admin role (user ID 1)
php artisan shield:super-admin --user=1 --panel=admin

# 3. Generate all permissions/policies
php artisan shield:generate --all --ignore-existing-policies --panel=admin
```

### Filament Panel Configuration
- Panel path: `/admin`
- Authentication: Filament defaults + Breezy (user profiles)
- Authorization: Shield + spatie/laravel-permission

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
- Follow cascade deletion hierarchy (Conference as root)
- Use enums consistently with migration patterns shown above

### ❌ DON'T:
- Define forms/tables inline in resource classes
- Use `->components()` in form schemas (use `->schema()`)
- Forget to update relationship counts when manipulating related data
- Mix Filament v4 syntax (heroicon imports, schema parameters)
- Break cascade chains by using `nullOnDelete()` for owned children
- Create orphaned records (always respect parent-child relationships)

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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.14
- filament/filament (FILAMENT) - v3
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v3
- larastan/larastan (LARASTAN) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- laravel-echo (ECHO) - v2
- tailwindcss (TAILWINDCSS) - v3

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== livewire/core rules ===

## Livewire Core
- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>


## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>


    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>


=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== pest/core rules ===

## Pest
### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest {name}`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>


=== pest/v4 rules ===

## Pest 4

- Pest v4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### Browser Testing
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest v4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### Example Tests

<code-snippet name="Pest Browser Test Example" lang="php">
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Pest Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v3 rules ===

## Tailwind 3

- Always use Tailwind CSS v3 - verify you're using only classes supported by this version.
</laravel-boost-guidelines>
