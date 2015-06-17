<?php
    function pip() {
	    global $config;

        // Set our defaults
        $controller = $config['default_controller'];
        $action = 'index';
        $url = '';
        $request_url = '';
        $script_url = '';
    
        // Get request url and script url
        if(isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) $request_url = $_SERVER['REQUEST_URI'];
        if(isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF'])) $script_url = $_SERVER['PHP_SELF'];

	    // Get our url path and trim the / of the left and the right
	    if($request_url != $script_url) $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');
    
	    // Split the url into segments
	    $segments = explode('/', $url);
	
	    // Do our default checks
	    if(isset($segments[0]) && $segments[0] != '') $controller = $segments[0];
	    if(isset($segments[1]) && $segments[1] != '') $action = $segments[1];

        // Get our controller file (and check it's valid to protect from LFI)
        $path = APP_DIR . 'controllers/' . $controller . '.php';
        if(in_array($controller, $config['valid_controllers']) && file_exists($path)) {
            require_once($path);
	    } else {
            $controller = $config['error_controller'];
            require_once(APP_DIR . 'controllers/' . $controller . '.php');
	    }
    
        // Check the action exists
        if(!method_exists($controller, $action)){
            $controller = $config['error_controller'];
            require_once(APP_DIR . 'controllers/' . $controller . '.php');
            $action = 'index';
        }
	
	    // Create object and call method
        die(trim(call_user_func_array(array(new $controller, $action), array_slice($segments, 2))));
    }
?>
