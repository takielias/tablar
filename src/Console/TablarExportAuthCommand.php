<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarExportAuthCommand extends Command
{
    protected $signature = 'tablar:export-auth';

    protected $description = 'Tablar Export Auth.';

    public function handle()
    {
        TablarPreset::exportAuth();
        $this->info('Tablar auth scaffolding installed successfully.');
    }
}
