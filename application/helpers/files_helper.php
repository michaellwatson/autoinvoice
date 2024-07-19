<?php
function get_file_icon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    switch ($ext) {
        case 'pdf':
            return 'fa-file-pdf-o';
        case 'doc':
        case 'docx':
            return 'fa-file-word-o';
        case 'xls':
        case 'xlsx':
            return 'fa-file-excel-o';
        case 'ppt':
        case 'pptx':
            return 'fa-file-powerpoint-o';
        case 'zip':
        case 'rar':
        case '7z':
            return 'fa-file-archive-o';
        case 'mp3':
        case 'wav':
            return 'fa-file-audio-o';
        case 'mp4':
        case 'avi':
        case 'mov':
        case 'wmv':
            return 'fa-file-video-o';
        default:
            return 'fa-file-o';
    }
}