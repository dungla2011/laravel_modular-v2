#!/bin/bash

# Module Test Runner Script
# Usage: ./run-module-tests.sh [module-name]

MODULE_NAME=${1:-"Base"}
MODULE_PATH="modules/$MODULE_NAME"

echo "🧪 Running tests for $MODULE_NAME module..."

if [ ! -d "$MODULE_PATH/tests" ]; then
    echo "❌ No tests directory found for $MODULE_NAME module"
    exit 1
fi

# Change to module tests directory
cd "$MODULE_PATH/tests"

# Check if phpunit.xml exists
if [ -f "phpunit.xml" ]; then
    echo "📋 Using module-specific phpunit.xml"
    PHPUNIT_CONFIG="phpunit.xml"
else
    echo "📋 Using default phpunit configuration"
    PHPUNIT_CONFIG="../../../phpunit.xml"
fi

# Run PHPUnit with the appropriate configuration
echo "🏃 Running: vendor/bin/phpunit --configuration=$PHPUNIT_CONFIG"
../../../vendor/bin/phpunit --configuration="$PHPUNIT_CONFIG"

# Check exit code
if [ $? -eq 0 ]; then
    echo "✅ All tests passed for $MODULE_NAME module!"
else
    echo "❌ Some tests failed for $MODULE_NAME module"
    exit 1
fi
