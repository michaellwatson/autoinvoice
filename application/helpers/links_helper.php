<?php
function find_and_check_links($text) {
    // Find hyperlinks using regex
    preg_match_all('/<a\s+.*?href=["\'](.*?)["\'].*?>.*?<\/a>/i', $text, $matches);

    // Check each link for 404 status code
    foreach ($matches[1] as $link) {

        $page = file_get_contents($link);
        //echo $link;
        if(str_contains($page, 'error_pages/deleted')){
            return true;
        } else {
            return false;
        }
    }
}