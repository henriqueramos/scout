<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use RamosHenrique\Scout\ScoutApplication;

$appName = 'Scout';
$version = '0.0.1';

(new ScoutApplication($appName, $version))->run();