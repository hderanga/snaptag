<?php
$err = "";
$max_file = "1148576";
$upload_dir = "upload_pic/";
$max_width = "500";

if(isset($_POST['upload'])){
    
    $user_filename = $_FILES["image"]["name"];
    $user_filename_temp = $_FILES["image"]["tmp_name"];
    $user_file_size = $_FILES["image"]["size"];
    $filename = basename($_FILES["image"]["name"]);
    $file_ext = substr($filename, strrpos($filename, ".") + 1);
    
   if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
        if (($file_ext!="jpg") && ($file_ext!="gif") ) {
                $err.= "ONLY jpg images are accepted for upload";
        }
   }
        else{
            $err.="Select a jpg image for upload";
	}
       echo $err;
       
       if($err==""){ 
           $image_loc =$upload_dir.$user_filename;
           
           if (isset($_FILES["image"]["name"])){ 
               
               if(file_exists($upload_dir.$user_filename)){
                   
                    echo $user_filename. " already exists. ";
                }
                else{
                    move_uploaded_file($user_filename_temp,$image_loc);    
                    chmod($image_loc, 0777);
                    $width = getWidth($image_loc);
                    $height = getHeight($image_loc);
                    echo"<img src='$image_loc' />";
                    
                    if ($width > $max_width){
				$scale = $max_width/$width;
				$uploaded = resizeImage($image_loc,$width,$height,$scale);
			}else{
				$scale = 1;
				$uploaded = resizeImage($image_loc,$width,$height,$scale);
			}
                }
               
           }
       }
}
function getWidth($image) {
	$sizes = getimagesize($image);
	$width = $sizes[0];
	return $width;
}
function getHeight($image) {
	$sizes = getimagesize($image);
	$height = $sizes[1];
	return $height;
}
function resizeImage($image,$width,$height,$scale) {
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	$source = imagecreatefromjpeg($image);
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	imagejpeg($newImage,$image,90);
	chmod($image, 0777);
	return $image;
}
?>

<form action="" enctype="multipart/form-data" method="post">  
Photo  
<input name="image" size="30" type="file">  
<input name="upload" value="Upload" type="submit">  
</form> 

<div id="loader" class="loading"></div>
<input name="view" value="view" type="submit" onsubmit="loadImage()"> 