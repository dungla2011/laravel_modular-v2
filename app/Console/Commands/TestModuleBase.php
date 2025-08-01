<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestModuleBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:module-base';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Base Module components';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Running Base Module Unit Tests...');

        // Run PHPUnit tests for Base module
        $testPath = base_path('modules/Base/tests');
        $configPath = $testPath . '/phpunit.xml';

        if (! file_exists($configPath)) {
            $this->error('❌ PHPUnit configuration not found at: ' . $configPath);

            return Command::FAILURE;
        }

        $this->info("📂 Test path: {$testPath}");
        $this->info("⚙️ Config: {$configPath}");
        $this->newLine();

        // Change to test directory and run PHPUnit
        $originalDir = getcwd();
        chdir($testPath);

        try {
            $phpunitPath = base_path('vendor/bin/phpunit');
            if (PHP_OS_FAMILY === 'Windows') {
                $phpunitPath = base_path('vendor\\bin\\phpunit.bat');
            }

            $command = "{$phpunitPath} --configuration=phpunit.xml --colors=always";
            $this->info("🏃 Executing: {$command}");
            $this->newLine();

            // Execute the command and get output
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);

            // Display output
            foreach ($output as $line) {
                $this->line($line);
            }

            if ($returnCode === 0) {
                $this->newLine();
                $this->info('✅ All Base Module tests passed!');

                return Command::SUCCESS;
            } else {
                $this->newLine();
                $this->error('❌ Some Base Module tests failed!');

                return Command::FAILURE;
            }

        } finally {
            // Restore original directory
            chdir($originalDir);
        }
    }
}
