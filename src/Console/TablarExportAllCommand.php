<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarExportAllCommand extends Command
{
    protected $signature = 'tablar:export-all';

    protected $description = 'Tablar Config Export.';

    public function handle()
    {
        TablarPreset::exportConfig();
        $this->info('Tablar Config Exported successfully.');
        TablarPreset::exportAuth();
        $this->info('Tablar auth scaffolding installed successfully.');
        TablarPreset::exportAllView();
        $this->info('Tablar views scaffolding has been exported successfully.');
    }
}
