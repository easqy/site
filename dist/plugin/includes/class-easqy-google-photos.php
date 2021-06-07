
<?php


class Easqy_Google_Photos
{
    public static function parse_album($album_url)
    {
        //'https://photos.app.goo.gl/xDJQgFAXSoB535F48'  => maquette
        //'https://photos.app.goo.gl/YJQDYQDW3y6qMsg96'  => versailles
        // 'https://photos.app.goo.gl/zCBhXLbXR8r7FFeC6'  => test site
        // https://photos.app.goo.gl/idQTeL2HY5UuKQ1W8 => ouarville
        // https://photos.app.goo.gl/WF58Z2FoZMGsdae86 => eclairage rémi
        
        $response = wp_remote_get($album_url);
        if (is_wp_error($response)) {
            return false;
        }
    
        $images = [];
        $key= '';
    
        $body = wp_remote_retrieve_body($response);
    
        // parse og:tags
        $m = null;
        preg_match_all('~<\s*meta\s+property="(og:[^"]+)"\s+content="([^"]*)~i', $body, $m);
        $ogtags = array();
        for ($i = 0; $i < count($m[1]); $i++) {
            $ogtags[$m[1][$i]] = $m[2][$i];
        }
        $title = isset($ogtags['og:title']) ? html_entity_decode($ogtags['og:title'], ENT_QUOTES | ENT_HTML5) : '';
        $cover_url = isset($ogtags['og:image']) ? $ogtags['og:image'] : '';
        $cover_width = isset($ogtags['og:image:width']) ? max(1, intval($ogtags['og:image:width'])) : 0;
        $cover_height = isset($ogtags['og:image:height']) ? max(1, intval($ogtags['og:image:height'])) : 0;
        
        preg_match('@\["(AF1Q.*?)",".*?"\,@', $body, $rKey);
        if (count($rKey) > 1) {
            $key  = $rKey[1];
            // see https://stackoverflow.com/a/31097702/2764823
            //$title= json_decode('["'.$rTitle[2].'"]')[0];
        }
    
        preg_match_all('@\["(AF1Q.*?)",\["(.*?)"\,(\d*?)\,(\d*?)\,@', $body, $urls);
        if (count($urls) > 4) {
            if (
                (count($urls[1]) === count($urls[2])) &&
                (count($urls[1]) === count($urls[3])) &&
                (count($urls[1]) === count($urls[4]))
                ) {
                for ($i = 0; $i < count($urls[1]); $i++) {
                    $images[] = [
                        'key' => $urls[1][$i],
                        'url' => $urls[2][$i],
                        'width' => intval($urls[3][$i]),
                        'height' => intval($urls[4][$i]) ];
                }
            }
        }
        return array(
            'key' => $key,
            'url' => $album_url,
            'title' => $title,
            'cover_url' => $cover_url,
            'cover_width' => $cover_width,
            'cover_height' => $cover_height,
            'images' => $images
        );
    }
}