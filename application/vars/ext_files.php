<?php
$ext_files = array(
    'main' => array(
        'name' => 'main',
        'css' => array('reset', 'global', 'classes/dialog'),
        'js' => array('lib/jquery', 'lib/base64', 'global', 'classes/dialog', 'classes/facebook', 'classes/twitter', 'classes/error', 'classes/ajax')
    ),
    'login' => array(
        'name' => 'login',
        'css' => array('login'),
        'js' => array('login/view', 'login/login')
    ),
    'ie' => array(
        'name' => 'ie',
        'css' => array('ie')
    ),
    'app' => array(
        'name' => 'app',
        'css' => array('main', 'classes/reader', 'classes/dropdown', 'classes/profile'),
        'js' => array('classes/reader', 'classes/items', 'classes/feeds', 'classes/settings', 'classes/filters', 'classes/dropdown', 'classes/reporting', 'classes/interface', 'classes/folders', 'classes/lus', 'classes/panels', 'classes/profile', 'lib/jquery.scrollto.min', 'app', 'model', 'view', 'common')
    ),
    'pages' => array(
        'name' => 'pages',
        'css' => array('pages/layout'),
        'js' => array('pages/main')
    ),
    'admin' => array(
        'name' => 'admin',
        'css' => array('admin/layout'),
        'js' => array('admin/main')
    ),
    'toolbar' => array(
        'name' => 'toolbar',
        'css' => array('reset', 'global', 'main', 'toolbar/toolbar'),
        'js' => array('lib/jquery', 'toolbar/iframe')
    ),
);