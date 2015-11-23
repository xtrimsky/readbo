<?php

class URLManager{
    
    /*
     * this function returns all OG meta tags that could be useful
     */
    function grabOGMeta($url){
        
        $html = $this->file_get_contents_curl($url);

        //parsing begins here:
        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $metas = $doc->getElementsByTagName('meta');

        $open_graph = array();
        for ($i = 0; $i < $metas->length; $i++)
        {
            $meta = $metas->item($i);
            
            $property = $meta->getAttribute('property');
            if($property && strtolower( substr($property,0,2) ) == 'og'){
                $content = $meta->getAttribute('content');
                
                //replacing url with current url
                if(strtolower($property) == 'og:url'){
                    $content = 'http://readbo.com'.$_SERVER['PATH_INFO'];
                }
                
                $open_graph[] = array(
                    'property' => $property,
                    'content' => $content
                );
            }
        }
        
        return $open_graph;
    }
    
    function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}