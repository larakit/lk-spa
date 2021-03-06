<?php
namespace Larakit;

class Boot {
    
    static public    $aliases           = [];
    static public    $configs           = [];
    protected static $middlewares       = [];
    protected static $middlewares_route = [];
    protected static $middlewares_group = [];
    static public    $service_providers = [];
    static public    $policies_model    = [];
    static public    $commands          = [];
    static public    $view_paths        = [];
    static public    $migrations        = [];
    static public    $observers         = [];
    static public    $boots             = [];
    static public    $langs             = [];
    
    /**
     * Поставить в очередь регистрацию алиаса
     *
     * @param $alias
     * @param $facade
     */
    static function register_alias($alias, $facade) {
        self::$aliases[$alias] = $facade;
    }
    
    static function register_config($path_config, $deploy_path = null) {
        self::$configs[$path_config] = (bool) $deploy_path;
    }
    
    static function register_boot($dir_boot) {
        self::$boots[$dir_boot] = $dir_boot;
    }
    
    static function register_lang($dir_boot, $alias) {
        self::$langs[$alias][$dir_boot] = $dir_boot;
    }
    
    static function register_migrations($migrate_path) {
        self::$migrations[$migrate_path] = $migrate_path;
    }
    
    static function register_observer($model_class, $observer) {
        self::$observers[$model_class][$observer] = $observer;
    }
    
    static function register_observer_user($observer) {
        self::$observers['_USER_'][$observer] = $observer;
    }
    
    static function init_package($package, $dir = 'init') {
        $path = dirname(dirname(dirname(__DIR__))) . '/' . $package . '/src/' . $dir;
        if(file_exists($path)) {
            $inits = rglob('*.php', 0, $path);
            foreach($inits as $init) {
                include_once $init;
            }
        }
    }
    
    /**
     * Поставить в очередь регистрацию сервис-провайдера
     *
     * @param $provider
     */
    static function register_provider($provider) {
        self::$service_providers[$provider] = $provider;
    }
    
    static function register_middleware($middleware, $priority = 0) {
        self::$middlewares[(int) $priority][] = $middleware;
    }
    
    static function register_view_path($view_path, $namespace) {
        self::$view_paths[$namespace . $view_path] = compact('namespace', 'view_path');
    }
    
    static function register_middleware_group($group, $middleware, $priority = 0) {
        $middlewares = (array) $middleware;
        foreach($middlewares as $middleware) {
            self::$middlewares_group[$group][(int) $priority][] = $middleware;
        }
    }
    
    static function register_middleware_route($name, $class) {
        self::$middlewares_route[$name] = $class;
    }
    
    static function register_policy_model($model_class, $policy_class) {
        self::$policies_model[$model_class] = $policy_class;
    }
    
    static function register_command($class) {
        self::$commands[$class] = $class;
    }
    
    /**
     * Вызвать в config/app.php
     * <?php
     *
     * return [
     *
     * @return array
     */
    static function aliases() {
        return self::$aliases;
    }
    
    static function middlewares() {
        $ret = [];
        krsort(self::$middlewares);
        foreach(self::$middlewares as $priority => $middlewares) {
            foreach($middlewares as $middleware) {
                $ret[] = $middleware;
            }
        }
        
        return $ret;
    }
    
    static function middlewares_route() {
        return self::$middlewares_route;
    }
    
    static function middlewares_group() {
        $ret = [];
        foreach(self::$middlewares_group as $group => $priorities) {
            krsort($priorities);
            foreach($priorities as $priority => $middlewares) {
                foreach($middlewares as $middleware) {
                    $ret[$group][] = $middleware;
                }
                
            }
        }
        
        return $ret;
    }
    
    static function providers() {
        return array_values(self::$service_providers);
    }
    
    static function policies_model() {
        return self::$policies_model;
    }
    
    static function commands() {
        return array_values(self::$commands);
    }
    
    static function view_paths() {
        return self::$view_paths;
    }
}
