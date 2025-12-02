#!/usr/bin/env pwsh
# Improved script to complete Filament v4 to v3 migration

$resourcesPath = "c:\Users\MSI\Desktop\projocts\Mabda\Conference\app\Filament\Resources"

Write-Host "Starting comprehensive Filament v4 to v3 migration..." -ForegroundColor Cyan

# Function to safely replace regex patterns
function Replace-Regex {
    param(
        [string]$FilePath,
        [string]$Pattern,
        [string]$Replacement
    )
    
    if (Test-Path $FilePath) {
        $content = Get-Content $FilePath -Raw
        if ($content -match $Pattern) {
            $newContent = $content -replace $Pattern, $Replacement
            Set-Content $FilePath -Value $newContent -NoNewline
            return $true
        }
    }
    return $false
}

# 1. Fix all Resource files - remove unused imports and fix icons
Write-Host "`n1. Fixing Resource files..." -ForegroundColor Yellow
Get-ChildItem -Path $resourcesPath -Recurse -Filter "*Resource.php" -Exclude "*Relation*" | ForEach-Object {
    $file = $_.FullName
    $updated = $false
    
    # Remove BackedEnum import line
    if (Replace-Regex $file "use BackedEnum;[\r\n]+" "") { $updated = $true }
    
    # Remove UnitEnum import line
    if (Replace-Regex $file "use UnitEnum;[\r\n]+" "") { $updated = $true }
    
    # Remove Heroicon import line
    if (Replace-Regex $file "use Filament\\Support\\Icons\\Heroicon;[\r\n]+" "") { $updated = $true }
    
    # Fix partial icon replacements (e.g., 'heroicon-o-user'Group -> 'heroicon-o-user-group')
    if (Replace-Regex $file "'heroicon-o-(\w+)'(\w+);" "'heroicon-o-`$1`$2';") { $updated = $true }
    
    # Fix form method parameter from $schema to $form
    if (Replace-Regex $file "MemberForm::configure\(\`$schema\)" "MemberForm::configure(`$form)") { $updated = $true }
    if (Replace-Regex $file "(\w+)Form::configure\(\`$schema\)" "`$1Form::configure(`$form)") { $updated = $true }
    
    if ($updated) {
        Write-Host "  Fixed: $(Split-Path $file -Leaf)" -ForegroundColor Green
    }
}

# 2. Fix all Form schema files - change ->components to ->schema
Write-Host "`n2. Fixing Form schema files..." -ForegroundColor Yellow
Get-ChildItem -Path $resourcesPath -Recurse -Filter "*Form.php" | ForEach-Object {
    $file = $_.FullName
    $updated = $false
    
    # Fix return $schema ->components to return $form ->schema
    if (Replace-Regex $file "return \`$schema[\r\n\s]+->components\(\[" "return `$form`r`n            ->schema([") { $updated = $true }
    
    if ($updated) {
        Write-Host "  Fixed: $(Split-Path $file -Leaf)" -ForegroundColor Green
    }
}

# 3. Fix all RelationManagers
Write-Host "`n3. Fixing RelationManagers..." -ForegroundColor Yellow
Get-ChildItem -Path $resourcesPath -Recurse -Filter "*RelationManager.php" | ForEach-Object {
    $file = $_.FullName
    $updated = $false
    
    # Fix return $schema ->components to return $form ->schema  
    if (Replace-Regex $file "return \`$schema[\r\n\s]+->components\(\[" "return `$form`r`n            ->schema([") { $updated = $true }
    
    if ($updated) {
        Write-Host "  Fixed: $(Split-Path $file -Leaf)" -ForegroundColor Green
    }
}

Write-Host "`nMigration fixes completed!" -ForegroundColor Cyan
