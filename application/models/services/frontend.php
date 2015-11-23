<?php

Class Frontend extends CI_Model {
    protected $minify = false;
    protected $useClosure = false;

    protected $variables = array();
    protected $jsfiles = array();
    protected $cssfiles = array();

    function addVariable($name, $content) {
        $this->variables[$name] = $content;
    }

    private function addJSFile($name) {
        $this->jsfiles[] = $name;
    }

    private function addCSSFile($name) {
        $this->cssfiles[] = $name;
    }

    function outputJS() {
        $render = '';

        if (count($this->variables) > 0) {
            $render = '<script type="text/javascript">' . "\r\n//<![CDATA[\r\n";
            foreach ($this->variables as $key => $value) {
                $display = json_encode($value);
                if($value === true){
                    $display = 'true';
                }else if($value === false){
                    $display = 'false';
                }
                $render = $render . 'var ' . $key . '=' . $display . ';';
            }
            $render = $render . "\r\n//]]>\r\n</script>";
        }

        if (count($this->jsfiles) > 0) {
            foreach ($this->jsfiles as $file) {
                $render .= "<script type='text/javascript' src='".MEDIA_SERVER."/link/{$file}.js'></script>";
            }
        }

        return $render;
    }

    function outputCSS() {
        $render = '';

        if (count($this->cssfiles) > 0) {
            foreach ($this->cssfiles as $file) {
                $render .= "<link rel='stylesheet' type='text/css' href='".MEDIA_SERVER."/link/{$file}.css'/>";
            }
        }

        return $render;
    }

    function sendToAjax($content) {
        die(json_encode($content));
    }
    
    function addExtGroup($name, $ie = false, $force = false){
        if (ON_SERVER && !$force) {
            $this->addCSSFile("{$name}-min");
            $this->addJSFile("{$name}-min");
            return;
        }
        
        include(APPPATH.'vars/ext_groups.php');
        include(APPPATH.'vars/ext_files.php');
        
        $group = $ext_groups[$name];
        
        if($ie){ $group[] = 'ie'; }
        
        $css_files = array();
        $js_files = array();
        foreach($group as $g){
            $list = $ext_files[$g];
            
            if(isset($list['css'])){
                $css_files = array_merge($css_files, $list['css']);
            }
            if(isset($list['js'])){
                $js_files = array_merge($js_files, $list['js']);
            }
        }
        
        $this->generateCSSFile($name, $css_files);
        $this->generateJSFile($name, $js_files);
    }
    
    function generateCSSFile($name, $list){
        include_once(APPPATH.'config/css.php');
        $css_vars = getCssVars();
        
        $url_default = base_url() . 'min/?b=styles&f=';

        $url_list = array();
        $url = $url_default;
        foreach ($list as $file) {
            if($this->minify){
                if(strlen($url.$file . '.css,') > 220){
                    $url_list[] = trim($url,',');
                    $url = $url_default;
                }
                $url .= $file . '.css,';
            }else{
                $url_list[] = 'styles/'.$file.'.css';
            }
        }
        if($url != $url_default){
            $url_list[] = trim($url, ',');
        }

        $content = '';
        foreach($url_list as $url){
            $content .= @file_get_contents($url);
        }
        //$lc = new lessc();
        //$content = $lc->parse($content);

        foreach($css_vars as $key => $var){
            $content = str_replace('_'.$key.'_', $var, $content);
        }

        @file_put_contents("public/link/{$name}-min.css", $content);

        $this->addCSSFile("{$name}-min");
    }
    
    function generateJSFile($name, $list){
        $url_default = base_url() . 'min/?b=scripts&f=';

        $url_list = array();
        $url = $url_default;
        foreach ($list as $file) {
           $url_list[] = 'scripts/'.$file.'.js';
        }
        if($url != $url_default){
            $url_list[] = trim($url, ',');
        }

        $content = '';
        foreach($url_list as $url){
            $content .= @file_get_contents($url)."\n\n";
        }

        $content = str_replace(";\n;", "; \n", $content);
        if($this->useClosure){
            date_default_timezone_set('America/Los_Angeles');
            require_once(APPPATH.'models/libs/google-closure/php-closure.php');
            $c = new PhpClosure();
            $content = $c->setSourceCode($content)
                    ->simpleMode()
                    //->advancedMode()
                    ->quiet()
                    ->write();
        }
        @file_put_contents("public/link/{$name}-min.js", $content);

        $this->addJSFile("{$name}-min");
    }
    
    function useGoogleClosure(){
        $this->useClosure = true;
    }

}

?>