<?php
if (!function_exists('get_current_date_time')) {
    function get_current_date_time()
    {
        return date('Y-m-d H:i:s');
    }
}
if (!function_exists('get_current_date')) {
    function get_current_date()
    {
        return date('Y-m-d');
    }
}
if(!function_exists('_getDateFromDatetimeString')){
    function _getDateFromDatetimeString($datetime){
        return (new \DateTime($datetime))->format("Y-m-d");
    }
}
if(!function_exists('_getTimeFromDatetimeString')){
    function _getTimeFromDatetimeString($datetime){
        return (new \DateTime($datetime))->format("H:i:s");
    }
}
if(!function_exists('_getCurrentDateTimeObject')){
    function _getCurrentDateTimeObject(){
        return new \DateTimeImmutable('now');
        // return new \DateTime("2022-11-03 17:30");
    }
}

if(!function_exists('getTomorrowDate')){
    function getTomorrowDate(){
        $today = new DateTime();
        $tomorrow = $today->modify('+1 day');
        return $tomorrow->format('Y-m-d');
    }
}

?>