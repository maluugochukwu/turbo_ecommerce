<?php

if ($_FILES['upload']['size'] > 1000000) {
    return json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.'));
}

// DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
// Check MIME Type by yourself.
//    $finfo = new finfo(FILEINFO_MIME_TYPE);
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$ext = array_search(
    finfo_file($finfo,$_FILES['upload']['tmp_name']),
    array(
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    ),
    true
);

$file_alt_name = date("ymdhis");
if (!move_uploaded_file($_FILES['upload']['tmp_name'],'uploads/'.$file_alt_name.'.'.$ext)) {
    echo json_encode(array('response_code'=>'50','response_mesage'=>'Failed to move uploaded file.'));
}else
{
    echo json_encode(array('url'=>'http://asl.accessng.com/demo2/admin/uploads/'.$file_alt_name.'.'.$ext));
}