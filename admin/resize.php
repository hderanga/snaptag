<?php
//$fileName = "00003543.jpg";#FileName of the image to resize
//$resizeToWidth = 200;#Width to be be resized to
//$resizeToHeight = 150;#Height to be resized to

#Original image width and height proportions will be maintained
#Additional part of the image will be stripped off
#If addtional part is found
# $type = -1; will copy from the left/top part of the image
# $type = 0; will copy from the center/middle part of the image
# $type = 1; will copy from the right/bottom part of the image
$type = 0;

#Define the directory where the image file is stored
//define("_BASEDIR", "");

#Usage1
#Displays reszized image on the browser
//resizeImage(_BASEDIR.$fileName, $resizeToWidth, $resizeToHeight, $type);

#Usage2
#Writes image to the filesytem with the specified filename
//resizeImage(_BASEDIR.$fileName, $resizeToWidth, $resizeToHeight, $type, _BASEDIR."resized_".$fileName, 100);

#Function
function resizeImage($fileName, $newWidth = 100 , $newHeight = 100, $cropType = 0, $outputFileName = "", $quality = 75){
    $imgCreateFun=array(1 => "imagecreatefromgif", 2 => "imagecreatefromjpeg", 3 => "imagecreatefrompng");
    $imgOutputFun=array(1 => "imagegif", 2 => "imagejpeg", 3 => "imagepng");
    
    if(file_exists($fileName)){
        $myFileInfo = getimagesize($fileName);
        $imgWidth = $myFileInfo[0];
        $imgHeight = $myFileInfo[1];
        
        $resCords = getPropSizes($imgWidth, $imgHeight, $newWidth, $newHeight, $cropType);
        
        $imgType = $myFileInfo[2];
        if(in_array($imgType,array_keys($imgCreateFun))){
            $image_p = imagecreatetruecolor($newWidth, $newHeight);
            $image = $imgCreateFun[$imgType]($fileName);
            imagecopyresampled($image_p, $image, 0, 0, $resCords["srcX"], $resCords["srcY"], $newWidth, $newHeight, $resCords["srcW"], $resCords["srcH"]);                
            if(!file_exists($outputFileName)){
                if(!$outputFileName){
                    header("Content-type: ".$myFileInfo["mime"]);
                }
                $imgOutputFun[$imgType]($image_p, $outputFileName, $quality);
                imagedestroy($image_p);
                imagedestroy($image);    
            }
            else{
                imagedestroy($image_p);
                imagedestroy($image);
                die("Cannot write output image - Filename already exists");
            }
            
        }
        else{
            die("Image Type not supported");
        }
    }
    else{
        die("Source file not found");
    }
}

function getPropSizes($orgWidth, $orgHeight, $setWidth, $setHeight, $cropType = 0){    
    $divXFac = $orgWidth / $setWidth;
    $divYFac = $orgHeight / $setHeight;
    $divFac = ($divXFac <= $divYFac)?$divXFac:$divYFac;
    $newWidth = $orgWidth / $divFac;
    $newHeight = $orgHeight / $divFac;
    $orgWidthDef = ($newWidth - $setWidth) * $divFac;
    $orgHeightDef = ($newHeight - $setHeight) * $divFac;
    if($cropType == -1){
        $retArr["srcX"] = 0;
        $retArr["srcY"] = 0;
    }
    elseif($cropType == 0){
        $retArr["srcX"] = $orgWidthDef/2;
        $retArr["srcY"] = $orgHeightDef/2;
    }
    elseif($cropType == 1){
        $retArr["srcX"] = $orgWidthDef;
        $retArr["srcY"] = $orgHeightDef;
        
    }    
    $retArr["srcW"] = $orgWidth - $orgWidthDef;
    $retArr["srcH"] = $orgHeight - $orgHeightDef;
    return $retArr;
}
?>