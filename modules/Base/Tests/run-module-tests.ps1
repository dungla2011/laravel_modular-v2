# Module Test Runner Script for Windows PowerShell
# Usage: .\run-module-tests.ps1 [ModuleName]

param(
    [string]$ModuleName = "Base"
)

$ModulePath = "modules\$ModuleName"

Write-Host "🧪 Running tests for $ModuleName module..." -ForegroundColor Cyan

if (-not (Test-Path "$ModulePath\tests")) {
    Write-Host "❌ No tests directory found for $ModuleName module" -ForegroundColor Red
    exit 1
}

# Change to module tests directory
Push-Location "$ModulePath\tests"

try {
    # Check if phpunit.xml exists
    if (Test-Path "phpunit.xml") {
        Write-Host "📋 Using module-specific phpunit.xml" -ForegroundColor Yellow
        $PhpUnitConfig = "phpunit.xml"
    } else {
        Write-Host "📋 Using default phpunit configuration" -ForegroundColor Yellow
        $PhpUnitConfig = "..\..\..\phpunit.xml"
    }

    # Run PHPUnit with the appropriate configuration
    Write-Host "🏃 Running: vendor\bin\phpunit --configuration=$PhpUnitConfig" -ForegroundColor Green
    & "..\..\..\vendor\bin\phpunit" --configuration="$PhpUnitConfig"

    # Check exit code
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ All tests passed for $ModuleName module!" -ForegroundColor Green
    } else {
        Write-Host "❌ Some tests failed for $ModuleName module" -ForegroundColor Red
        exit 1
    }
} finally {
    # Return to original directory
    Pop-Location
}
