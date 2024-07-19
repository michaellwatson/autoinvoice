<?php
if(!function_exists('getFileType')){
    function getFileType($filepath){
        $status = false; $message = ''; $responseData = array();
        $fileType = '';

        if (file_exists($filepath)) {
            $fileInfo = pathinfo($filepath);
            $extension = strtolower($fileInfo['extension']);

            $mimeType = mime_content_type($filepath);
            
            if($mimeType === 'application/pdf' && $extension == 'pdf'){
                $fileType = 'pdf';
            }
            else if( $mimeType === 'image/jpeg' && in_array($extension, array('jpg', 'jpeg')) ){
                $fileType = 'image';
            }
            else if($mimeType === 'image/png' && $extension == 'png'){
                $fileType = 'image';
            }
            else{
                $fileType = 'unknown';
            }

            $status = true;
            $responseData['fileType'] = $fileType;
        }
        else{
            $message = 'File not found';
        }

        $resultData = array(
            'status' => $status,
            'message' => $message,
            'data' => $responseData,
        );
        return $resultData;
    }
}
if (!function_exists('_generateRandomNumber')) {
    function _generateRandomNumber($length){
        $generator = "1209348756";
        $result = "";
        for ($i = 1; $i <= $length; $i++) {
            $result .= substr($generator, (rand()%(strlen($generator))), 1);
        }
        return $result;
    }
}
?>