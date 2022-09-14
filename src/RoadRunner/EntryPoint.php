<?php

namespace Monken\CIBurner\RoadRunner;

use CodeIgniter\CLI\CLI;

class EntryPoint
{
    public static function run(\Config\Burner $config)
    {
        $workDir = __DIR__ . DIRECTORY_SEPARATOR;

        if (file_exists('./vendor/autoload.php')) {
            $rrPath = realpath('./vendor/bin/rr_server');
        } elseif (file_exists('../../dev/vendor/autoload.php')) {
            $rrPath = realpath('../../dev/vendor/bin/rr_server');
        }

        if ($rrPath === false) {
            CLI::write(
                CLI::color(
                    "Error! Roadrunner Server is not init. Please use 'burner:init [driver]' to init Roadrunner.",
                    'red'
                )
            );

            return;
        }

        $configFile = $config->serverConfigPath;

        $start = popen("{$rrPath} -w {$workDir} -c {$configFile} serve", 'w');
        pclose($start);
    }
}