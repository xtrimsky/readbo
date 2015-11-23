<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

define('MODE_DEVELOPER', ENVIRONMENT !== 'production');

abstract class Base_controller extends CI_Controller {

    protected $params = array();        //contains all params from  url
    protected $view;                    //contains the view (not layout)
    protected $layout = 'index';        //used layout
    protected $layout_vars = array();   //contains all the view layout variables
    protected $guestAllowed = array('welcome/loginAction', 'welcome/indexAction', 'link/indexAction', 'pages/indexAction','pages/termsAction','pages/forgotPasswordAction','pages/resetPasswordAction', 'pages/privacyAction','pages/contactAction','pages/sendEmailAction','pages/shutDownAction','pages/unsubscribeAction','pages/trendingAction','pages/whatIsReadboAction','users/registerAjax','users/addInviteAjax', 'users/loginAction', 'shares/indexAction', 'ovh/indexAction');
    protected $showDebugToolbar = false;
    
    function __construct() {
        parent::__construct();
        
        if ($this->auth->isLogged()) {
            $user = $this->auth->getUser();
            
            $this->setLayoutVar('username', $user->username);
            
            //if($this->router->class != 'admin' && isset($this->auth->getUser()->properties->isAdmin)){
            //    $this->output->enable_profiler(TRUE);
            //    $this->showDebugToolbar = true;
            //}
        }
    }

    function _remap($method, $arguments) {
        //stores all the current params
        $name = null;
        $param = null;
        foreach ($arguments as $argument) {
            if (is_null($name)) {
                $param = null;
                $name = $argument;
            } else {
                $param = $argument;

                $this->params[$name] = $param;
                $name = null;
            }
        }

        //selects current view
        $action = 'Action';
        if ($this->isAjax()) {
            $action = 'Ajax';
        }
        
        $method = $method . $action;
        
        $redirect = false;
        if($this->router->class == 'admin' && (!$this->auth->isLogged() || !isset($this->auth->getUser()->properties->isAdmin))){
            $redirect = true;
        }
        if(!$this->auth->isLogged() && !in_array($this->router->class.'/'.$method, $this->guestAllowed) && !$this->input->is_cli_request()){
            $redirect = true;
        }
        /*
        if($this->router->class == 'server' && !$this->input->is_cli_request()){
            $redirect = true;
        } */
        
        if ($redirect) {
            $method = 'loginAction';
            redirect('/', 'refresh');
            exit;
        }

        if (method_exists($this, $method) || $this->router->class == 'link') {
            $this->{$method}();
        } else {
            $this->forward404($method);
        }

        if (!$this->isAjax()) {
            //loads current layout
            $data = array(
                'css' => $this->frontend->outputCSS(),
                'js' => $this->frontend->outputJS()
            );

            //sets layout vars
            foreach ($this->layout_vars as $key => $var) {
                $data[$key] = $var;
            }

            //if there is a view, sends it to the layout
            if ($this->view != null){
                $data['content'] = $this->view;
            }

            //renders the layout
            $this->load->view('layouts/' . $this->layout, $data);
        }
    }

    function forward404($page) {
        redirect('/404', 'refresh');
        exit;
    }

    //gets param from url : /example/hello ,  getParam('example') returns hello
    function getParam($name) {
        if (isset($this->params[$name]))
            return $this->params[$name];

        return null;
    }

    function setView($name, $controller = null, $data = null) {
        if (is_null($controller)) {
            $this->view = null;
        } else {
            if(!is_file('application/views/'.$controller . '/' . $name.'.php')){
                return false;
            }
            
            $this->view = $this->load->view($controller . '/' . $name, $data, true);
        }
    }

    function setLayout($name) {
        $this->layout = $name;
    }

    function setLayoutVar($name, $content) {
        $this->layout_vars[$name] = $content;
    }

    function redirect($method) {
        //selects current view
        $this->setView($method);

        //loads method with action in it
        $new_method = $method . 'Action';
        if (method_exists($this, $new_method)) {
            $this->{$new_method}();
        } else {
            $this->forward404($new_method);
        }

        return true;
    }

    function sendToAjax($data) {
		echo json_encode($data);
		exit;
        //$this->load->view('ajax/json.php', array('data' => $data));
    }

    function isIE() {
        if (isset($_SERVER['HTTP_USER_AGENT']) &&
                (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
            return true;
        else
            return false;
    }

    function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }

}