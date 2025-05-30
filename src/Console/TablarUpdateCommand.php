<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarUpdateCommand extends Command
{
    protected $signature = 'tablar:update';

    protected $description = 'Update Tablar scaffolding and export config';

    public function handle()
    {
        TablarPreset::update();
        $this->updateTablarJsImport();
        $this->updateTablerCssImport();
        $this->info('Tablar has been updated successfully.');
        $this->comment('Please run "npm install" first. Once the installation is done, run "npm run dev"');
    }


    /**
     * Update Tablar import - comment out old demo-theme.js import and add new one
     *
     * @param string $filePath Path to the tablar-init.js file
     * @return void True if update was successful, false otherwise
     */
    private function updateTablarJsImport($filePath = null): void
    {
        // Default path if none provided
        if (!$filePath) {
            $filePath = resource_path('js/tabler-init.js');
        }

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return;
        }

        // Read the file content
        $content = file_get_contents($filePath);
        $originalContent = $content;

        // Define patterns to search for /assets/demo-theme.js
        $patterns = [
            '/^(\s*)(import\s+[\'"][^\'\"]*\/assets\/demo-theme\.js[\'"];?\s*)$/m',
            '/^(\s*)(import\s+[\'"][^\'\"]*\/assets\/demo-theme[\'"];?\s*)$/m'
        ];

        // Define the new import
        $newImport = "import '../../node_modules/@tabler/core/dist/js/tabler-theme.js';";

        $found = false;

        // Check and comment out old imports
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, '$1// $2' . "\n$1" . $newImport, $content);
                $found = true;
                $this->info("Found and commented out old demo-theme.js import");
                break;
            }
        }

        if (!$found) {
            $this->info("No /assets/demo-theme.js import found in file.");
            return;
        }

        // Write the updated content back to file
        $result = file_put_contents($filePath, $content);

        if ($result !== false) {
            $this->info("Successfully updated {$filePath}");
            $this->info("Old import commented out and new import added");
        } else {
            $this->error("Failed to write to {$filePath}");
        }
    }

    /**
     * Update Tabler CSS import - replace old tabler-icons import with new one
     *
     * @param string $filePath Path to the tabler.scss file
     * @return void
     */
    private function updateTablerCssImport($filePath = null): void
    {
        // Default path if none provided
        if (!$filePath) {
            $filePath = resource_path('sass/tabler.scss');
        }

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return;
        }

        // Read the file content
        $content = file_get_contents($filePath);

        // Define the old import pattern
        $oldImportPattern = '/^(\s*)@import\s+[\'"]\.\.\/\.\.\/node_modules\/@tabler\/icons-webfont\/dist\/tabler-icons[\'"];?\s*$/m';

        // Define the new import
        $newImport = '@import "@tabler/icons-webfont/dist/tabler-icons.css";' . "\n";

        $found = false;

        // Check if old import exists
        if (preg_match($oldImportPattern, $content)) {
            $content = preg_replace($oldImportPattern, '$1' . $newImport, $content);
            $found = true;
            $this->info("Found and updated old tabler-icons import");
        }

        if (!$found) {
            $this->info("No old tabler-icons import found in file.");
            return;
        }

        // Write the updated content back to file
        $result = file_put_contents($filePath, $content);

        if ($result !== false) {
            $this->info("Successfully updated {$filePath}");
            $this->info("Old CSS import replaced with new import");
        } else {
            $this->error("Failed to write to {$filePath}");
        }
    }

}
