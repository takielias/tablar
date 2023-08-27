<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarExportJsCommand extends Command
{
    protected $signature = 'tablar:export-js';

    protected $description = 'Tablar Export JS.';

    public function handle()
    {
        TablarPreset::exportJs();
        $this->info('Tablar Js Exported successfully.');
    }
}
