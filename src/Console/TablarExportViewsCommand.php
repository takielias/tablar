<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarExportViewsCommand extends Command
{
    protected $signature = 'tablar:export-views';

    protected $description = 'Tablar Export Views.';

    public function handle()
    {
        TablarPreset::exportAllView();
        $this->info('Tablar views scaffolding has been exported successfully.');
    }
}
