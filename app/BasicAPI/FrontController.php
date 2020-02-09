<?php

namespace BasicAPI;

use Exception;
use SQLite3;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class FrontController
{
    private Router $routing;
    private Response $response;
    private Access $access;

    public function __construct(Router $routing, Response $response, Access $access)
    {
        $this->routing = $routing;
        $this->response = $response;
        $this->access = $access;
    }

    public function run()
    {
        $this->init();
        $this->loadLogger();
        $this->registerShutDown();
        $this->checkPHPVersion();
        $this->connectDatabase();
        $this->routing->applyRoutingRules($this->response, new Database(Registry::get('db_connection')), new Access());
    }

    private function init($main_settings = null)
    {
        if ($this->isSessionStarted() === false) {
            session_start();
        }

        if ($main_settings === null) {
            $possible_configs = $this->getPossibleConfigs();

            foreach ($possible_configs as $path) {
                if (is_readable($path)) {
                    $main_settings = require_once($path);
                    break;
                }
            }

            if (empty($main_settings)) {
                throw new Exception('Can\'t find any config!');
            }
        }

        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);

        if (isset($main_settings) && is_array($main_settings)) {
            foreach ($main_settings as $key => $value) {
                Registry::set($key, $value);
            }
        }
    }

    private function isSessionStarted()
    {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? true : false;
            } else {
                return session_id() === '' ? false : true;
            }
        }
        return false;
    }

    private function getPossibleConfigs()
    {
        $array = array();
        $config_dir = ROOT_DIR . DIRECTORY_SEPARATOR . 'config';
        //set a priority of configs here
        if (!empty($_REQUEST['config']) && preg_match('/^[\d\w]+$/', $_REQUEST['config'])) {
            $array[0] = $config_dir . DIRECTORY_SEPARATOR . $_REQUEST['config'] . '.php';
        }
        $array[1] = $config_dir . DIRECTORY_SEPARATOR . 'local.php';
        return $array;
    }

    private function loadLogger()
    {
        $file_name = ROOT_DIR . DIRECTORY_SEPARATOR . Registry::get('logs_dir') . DIRECTORY_SEPARATOR . date(
                'Y_m'
            ) . DIRECTORY_SEPARATOR . date(
                'Y_m_d'
            ) . Registry::get('logs_suffix') . '.log';
        $logger = new Logger('basic_api_logger');
        Registry::set('logger', $logger);
        $logger->pushHandler(new StreamHandler($file_name, Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());
    }

    private function registerShutDown()
    {
        register_shutdown_function(
            function () {
                /** @var Logger $logger */
                $error_get_last = error_get_last();
                if (!empty($error_get_last)) {
                    $logger = Registry::get('logger');
                    $logger->error(json_encode($error_get_last));
                    if (!headers_sent() && Registry::get('response_sent') !== true) {
                        throw new Exception(json_encode($error_get_last));
                    }
                }
            }
        );
    }

    private function checkPHPVersion()
    {
        if (version_compare(phpversion(), Registry::get('php_version'), '<')) {
            throw new Exception(
                'PHP version must be ' . Registry::get('php_version') . ' or newer! You have ' . phpversion()
            );
        }
    }

    private function connectDatabase()
    {
        $db = new SQLite3(
            ROOT_DIR . DIRECTORY_SEPARATOR . Registry::get('db_dir') . DIRECTORY_SEPARATOR . Registry::get('db_file')
        );
        if ($db->lastErrorMsg() === 'not an error') {
            Registry::set('db_connection', $db);
        } else {
            throw new Exception('Database error: ' . $db->lastErrorMsg());
        }
    }
}