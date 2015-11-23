<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function getCssVars(){
    $media = MEDIA_SERVER;
    if(defined('FORCE_SERVER_MEDIA')){
        $media = MEDIA_SERVER_CDN;
    }
    
    return array(
    'gray1' => '#888',
    'blue1' => '#143D5D',
    'blue2' => '#1D5682',
    'blue3' => '#256FA8',
    'gradient1c1' => '#FFFFFF',
    'gradient1c2' => '#EAF0F5',
    'navigation_background' => '#EAF0F5',
    'nav_feed_color' => '#143D5D',
    'header_hover' => '#5581a4',
    'header_text' => '#ddd',
    'gray_border' => '#bdbdbd',
    'app_background' => '#ddd',
    'MEDIA_SERVER' => $media,
        
    'font_size_small1' => '10px',
    'font_size_small2' => '11px',
    'font_size_small3' => '12px',
);
}

//11 12 14 15 16 18 20 22 24 28 50 60