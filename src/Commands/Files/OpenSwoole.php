<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Config\Factories;
use Swoole\HTTP\Server;

class OpenSwoole extends BaseConfig
{
    /**
     * TCP HTTP service listening ip
     *
     * @var string
     */
    public $listeningIp = '0.0.0.0';

    /**
     * TCP HTTP service listening port
     *
     * @var int
     */
    public $listeningPort = 8080;

    /**
     * SWOOLE_PROCESS or SWOOLE_BASE
     *
     * @var int
     *
     * @see https://openswoole.com/docs/modules/swoole-server-reload#server-modes-and-reloading
     */
    public $mode = SWOOLE_BASE;

    /**
     * Swoole Setting Configuration Options
     *
     * @var array
     *
     * @see https://openswoole.com/docs/modules/swoole-http-server/configuration
     * @see https://openswoole.com/docs/modules/swoole-server/configuration
     */
    public $config = [
        'worker_num'            => 1,
        'max_request'           => 0,
        'document_root'         => '{{static_path}}',
        'enable_static_handler' => true,
        'log_level'             => 0,
        'log_file'              => '{{log_path}}',
    ];

    /**
     * You can declare some additional server setting in this method.
     *
     * @return void
     */
    public function initServer(Server $server)
    {
        $server->on('start', static function (Server $server) {
            $openSwooleConfig = Factories::config('OpenSwoole');
            echo "Swoole http server is started at {$openSwooleConfig->listeningIp}:{$openSwooleConfig->listeningPort}\n";
        });
    }
}
