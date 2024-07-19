<?php

require_once '../../../classes/CreateDocx.php';

//$docx = new CreateDocxFromTemplate('../../files/TemplateHTML.docx');
$docx = new CreateDocxFromTemplate('../../files/TemplatePhil.docx');

$docx->replaceVariableByHTML('CHUNK_1', 'inline', '<p style="font-family: verdana; font-size: 11px">C/ Matías Turrión 24, Madrid 28043 <b>Spain</b></p>', array('isFile' => false, 'parseDivsAsPs' => true, 'downloadImages' => false));
//$docx->replaceVariableByHTML('CHUNK_1', 'block', 'http://www.2mdc.com/PHPDOCX/example.html', array('isFile' => true, 'parseDivsAsPs' => true,  'filter' => '#capa_bg_bottom', 'downloadImages' => true));
$docx->replaceVariableByHTML('BODY', 'block', 'http://www.2mdc.com/PHPDOCX/example.html', array('isFile' => true, 'parseDivsAsPs' => false,  'filter' => '#lateral', 'downloadImages' => true));

//$multiline = 'This is the first line.\nThis is the second line of text.';
//$variables = array('BODY' => $first, 'MULTILINETEXT' => $multiline);
//$options = array('parseLineBreaks' => true);
//$docx->replaceVariableByText($variables, $options);


$docx->createDocx('example_replaceTemplateVariableByHTML_1');