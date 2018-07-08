<?php

/**
 * Created by PhpStorm.
 * User: yanfei
 * Date: 17/8/7
 * Time: 17:32
 */
class MY_Loader extends CI_Loader {
    /**
     * List of loaded sercices
     *
     * @var array
     * @access protected
     */
    protected $_ci_services = array();
    /**
     * List of paths to load services from
     *
     * @var array
     * @access protected
     */
    protected $_ci_service_paths = array();

    /**
     * Constructor
     *
     * Set the path to the Service files
     */
    public function __construct() {

        parent::__construct();
        load_class('Service', 'core');
        $this->_ci_service_paths = array(APPPATH);
    }

    /**
     * Service Loader
     *
     * This function lets users load and instantiate classes.
     * It is designed to be called from a user's app controllers.
     *
     * @param    string    the name of the class
     * @param    mixed    the optional parameters
     * @param    string    an optional object name
     * @return    void
     */
    public function service($service = '', $params = NULL, $object_name = NULL) {
        if (is_array($service)) {
            foreach ($service as $class) {
                $this->service($class, $params);
            }

            return;
        }

        if ($service == '' or isset($this->_ci_services[$service])) {
            return;
        }

        if (!is_null($params) && !is_array($params)) {
            $params = NULL;
        }

        $subdir = '';

        // Is the service in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($service, '/')) !== FALSE) {
            // The path is in front of the last slash
            $subdir = substr($service, 0, $last_slash + 1);

            // And the service name behind it
            $service = substr($service, $last_slash + 1);
        }

        $name = config_item('subclass_prefix') . "Service.php";
        if (class_exists(config_item('subclass_prefix') . "Service") === FALSE && file_exists(APPPATH . 'core/' . $name)) {
            require(APPPATH . 'core/' . $name);
        }

        foreach ($this->_ci_service_paths as $path) {

            $filepath = $path . 'services/' . $subdir . $service . '.php';

            if (!file_exists($filepath)) {
                continue;
            }

            include_once($filepath);

            $service = strtolower($service);

            if (empty($object_name)) {
                $object_name = $service;
            }

            $service = ucfirst($service);
            $CI = &get_instance();
            if ($params !== NULL) {
                $CI->$object_name = new $service($params);
            } else {
                $CI->$object_name = new $service();
            }

            $this->_ci_services[] = $object_name;

            return;
        }
    }

    /**
     * Database Loader
     *
     * @param    string    the DB credentials
     * @param    bool    whether to return the DB object
     * @param    bool    whether to enable active record (this allows us to override the config setting)
     * @return    object
     */
    public function database($params = '', $return = FALSE, $active_record = NULL) {
        $active_group = empty($params) ? 'default' : $params;
        // Grab the super object
        $CI =& get_instance();

        // Do we even need to load the database class?
        if (class_exists('CI_DB') AND $return == FALSE AND $active_record == NULL AND isset($CI->db) AND is_object($CI->db)) {
            return FALSE;
        }

        if (file_exists(APPPATH . 'core/database/DB.php')) {
            require_once(APPPATH . 'core/database/DB.php');
        } else {
            require_once(BASEPATH . 'database/DB.php');
        }


        // 加载数据库配置文件，该部分逻辑由DB.php文件中逻辑前移得到
        if (!defined('ENVIRONMENT') OR !file_exists($file_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php')) {
            if (!file_exists($file_path = APPPATH . 'config/database.php')) {
                show_error('The configuration file database.php does not exist.');
            }
        }

        include($file_path);

        if ($params != '') {
            $active_group = $params;
        }

        if (!isset($active_group) OR !isset($db[$active_group])) {
            show_error('You have specified an invalid database connection group.');
        }

        if ($return === TRUE) {
            if (!empty($CI->mdb) && array_key_exists($active_group, $CI->mdb)) {
                $db = $CI->mdb[$active_group];
            } else {
                // Load the DB class
                $db = DB($params, $active_record);
                $CI->mdb[$active_group] = $db;
            }
            return $db;
        }

        // Initialize the db variable.  Needed to prevent
        // reference errors with some configurations
        $CI->db = '';

        // Load the DB class
        $CI->db =& DB($params, $active_record);
    }
}