<?php
if(!function_exists('_twoDimArrSearch')){
    function _twoDimArrSearch($array, $index, $value){
        $resArr = array();
        if(!empty($array)) {
            foreach ($array as $key => $arrayInf) {
                if ($arrayInf[$index] == $value) {
                    $resArr['key'] = $key;
                    $resArr['array'] = $arrayInf;
                    break;
                }
            }
        }
        return $resArr;
    }
}
?>