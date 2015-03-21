<?php


$filepath =  '/content/images/'; 
 
if(empty($_FILES)){	echo "error: file empty";	exit;}
 
$filename=file_newname(dirname( __FILE__ ).$filepath, $_FILES['file']['name']);

move_uploaded_file($_FILES['file']['tmp_name'],dirname( __FILE__ ).$filepath.$filename); 
echo  'content/images/'.$filename;


function file_newname($path, $filename){
    if ($pos = strrpos($filename, '.')) {
           $name = substr($filename, 0, $pos);
           $ext = substr($filename, $pos);
    } else {
           $name = $filename;
    }

    $newpath = $path.$filename;
    $newname = $filename;
    $counter = 0;
    while (file_exists($newpath)) {
           $newname = $name .'_'. $counter . $ext;
           $newpath = $path.$newname;
           $counter++;
     }

    return $newname;
}

?>    