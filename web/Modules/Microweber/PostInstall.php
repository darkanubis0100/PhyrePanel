<?php

namespace Modules\Microweber;

use App\ModulePostInstall;
use Modules\Microweber\Filament\Clusters\Microweber\Pages\Version;

class PostInstall extends ModulePostInstall
{
    public function run()
    {
        $version = new Version();
        $version->checkForUpdates();

        return true;
    }
}
