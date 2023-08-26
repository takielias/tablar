<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarExportConfigCommand extends Command
{
    protected $signature = 'tablar:export-config';

    protected $description = 'Tablar Export Config.';

    public function handle()
    {
        TablarPreset::exportConfig();
        $this->info('Tablar Config Exported successfully.');
    }
}
