<?php
//  就从核心文件CodeIgniter.php路由部分（Router)开始说起：

// system/core/CodeIgniter.php LINE 308:  装载Router核心类。
    $RTR =& load_class('Router', 'core', isset($routing) ? $routing : NULL);

//  system/core/Common.php  LINE 139: 常用函数 load_class();
    function &load_class($class, $directory = 'libraries', $param = NULL) {

        static $_classes = array();         //单例模式

        //判断这个class之前是否装载过，若装载过，直接返回；
        if (isset($_classes[$class])) {
            return $_classer[$class];
        }

        $name = FALSE;

        foreach (array(APPPATH, BASEPATH) as $path) {
            if (file_exists($path.$directory.'/'.$class.'.php')) {
                $name = 'CI_'.$class;

                //check to see whether the given class has been defined.
                //The seconde parameter FALSE, tell us not use "__autoload()" magic function;
                if(class_exists($name, FALSE) === FALSE) {
                    require_once($path.$directory.'/'.$class.'.php');
                }
                //这里的break表明：当在APPPATH(application)路径下发现了要装载的文件后，
                //便不会加载BASEPATH(system)下的文件 ----替换,
                //APPPATH下的文件应该和BASEPATH中文件名字一样（首字母大写），
                //并且class名应该以'CI_'开头;
                break;
            }
        }

        // 这里所做的就是加载系统核心文件的扩展;
        // system/core/Common config_item() 获取在application/config/config.php中定义的配置文件，稍后详解
        if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php')) {
            $name = config_item('subclass_prefix').$class;
            if(class_exists($name, FALSE) === FALSE) {
                require_once(APPPATH.$directory.'/'.$name.'.php');
            }
        }

        if ($name === FALSE) {
            // system/core/Common set_status_header() set HTTP Header STATUS;
            set_status_header(503);
            echo 'Unable to locate the specified class '.$class.'.php';
            //Terminates execution of the script.
            //Shutdown functions and object destructors will always be executed even if exit is called.
            exit(5);
        }

        is_loaded($class);

        $_classes[$class] = isset($param) ? new $name($param) : new $name();
        //注意一点：这里返回的是$_classes[$class]的引用，并不是对象的引用；
        return $_classes[$class];
    }



