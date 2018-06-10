<?php

/**
 * Class Framework
 *
 * Init path, dispatch controller and action.
 */
class Framework
{

    public static function run()
    {
        self::init_request();
        self::init_path();
        self::load_config();

        // Register custom autoload function
        spl_autoload_register(['Framework', 'my_autoload']);

        self::dispatch();
    }

    private static function load_config()
    {
        $GLOBALS['config'] = require CONFIG_DIR . 'app.config.php';
    }

    private static function init_path()
    {
        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT_DIR', dirname(dirname(__FILE__)) . DS);
        define('APP_DIR', ROOT_DIR . 'app' . DS);
        define('CONT_DIR', APP_DIR . 'controller' . DS);
        define('VIEW_DIR', APP_DIR . 'view' . DS);
        define('MODEL_DIR', APP_DIR . 'model' . DS);
        define('FRAME_DIR', ROOT_DIR . 'framework' . DS);
        define('CONFIG_DIR', APP_DIR . 'config' . DS);
        // SESSION等工具类
        define('TOOL_DIR', FRAME_DIR . 'tool' . DS);
        define('UPLOAD_DIR', APP_DIR . 'upload' . DS);
    }

    private static function init_request()
    {
        define('CONTROLLER', isset($_GET['c']) ? $_GET['c'] : 'Index');
        define('ACTION', isset($_GET['a']) ? $_GET['a'] : 'index');
    }

    private static function my_autoload($class_name)
    {

        $map = [
            'MYSQLDB' => FRAME_DIR . 'MYSQLDB.class.php',
            'Model' => FRAME_DIR . 'Model.class.php',
            'Controller' => FRAME_DIR . 'Controller.class.php',
        ];

        if (!empty($map[$class_name])) {
            require $map[$class_name];
        } elseif (substr($class_name, -10) == 'Controller') {
            require CONT_DIR . $class_name . '.class.php';
        } elseif (substr($class_name, -5) == 'Model') {
            require MODEL_DIR . $class_name . '.class.php';
        } elseif (substr($class_name, -4) == 'Tool') {
            require TOOL_DIR . $class_name . '.class.php';
        }
    }

    private static function dispatch()
    {
        $c_name = CONTROLLER . 'Controller';
        $controller = new $c_name;

        $action_name = ACTION . 'Action';
        $controller->$action_name();
    }
}
