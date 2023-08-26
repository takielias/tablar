<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarExportAssetsCommand extends Command
{
    protected $signature = 'tablar:export-assets';

    protected $description = 'Tablar Export Assets.';

    public function handle()
    {
        TablarPreset::exportAssets();
        $this->info('Tablar Assets Exported successfully.');
    }
}
