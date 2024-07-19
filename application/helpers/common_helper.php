<?php
if (!function_exists('_pre')) {
    function _pre($array, $exit = 1) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        if ($exit == 1) {
            exit();
        }
    }
}
if (!function_exists('_set_dash')) {
    function _set_dash($str = '') {
        $str = trim($str);
        if ($str == '') {
            $str = '-';
        }
        return $str;
    }
}
if (!function_exists('_number_format')) {
    function _number_format($amount)
    {
        $amount = number_format((float)$amount, 2, '.', '');
        return $amount;
    }
}
?>