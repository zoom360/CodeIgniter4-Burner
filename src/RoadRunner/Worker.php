<?php

if (file_exists('./vendor/autoload.php')) {
    require_once './vendor/autoload.php';
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
    chdir(FCPATH);
    $pathsConfig = realpath(FCPATH . '../../../../app/Config/Paths.php');
} elseif (file_exists('../../dev/vendor/autoload.php')) {
    require_once '../../dev/vendor/autoload.php';
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
    chdir(FCPATH);
    $pathsConfig = realpath(FCPATH . '../../dev/app/Config/Paths.php');
}
define('BURNER_DRIVER', 'RoadRunner');

use Config\Paths;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;

// CodeIgniter4 init
// Override is_cli()
if (! function_exists('is_cli')) {
    function is_cli(): bool
    {
        return false;
    }
}

/**
 * Ci4 4.2.0 init
 */
require realpath($pathsConfig) ?: $pathsConfig;
$paths     = new Paths();
$botstorap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
require realpath($botstorap);
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new \CodeIgniter\Config\DotEnv(ROOTPATH))->load();
$app = \Config\Services::codeigniter();
$app->initialize();
$app->setContext('web');

/**
 * RoadRunner worker init
 */
$worker     = Worker::create();
$psrFactory = new Psr17Factory();
$psr7       = new PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

while (true) {
    // get psr7 request
    try {
        $request = $psr7->waitRequest();
        if (! ($request instanceof ServerRequestInterface)) { // Termination request received
            break;
        }
    } catch (Exception $e) {
        $psr7->respond(new Response(400)); // Bad Request

        continue;
    }

    /** @var \Psr\Http\Message\ResponseInterface */
    $response = \Monken\CIBurner\App::run($request);

    // handle response object
    try {
        $psr7->respond($response);
        \Monken\CIBurner\App::clean();
    } catch (Exception $e) {
        $psr7->respond(new Response(500, [], 'Something Went Wrong!'));
    }
}