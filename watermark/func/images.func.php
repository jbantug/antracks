<?php
function allowed_image($file_name){
	$allowed_ext = array('jpg','jpeg','png','gif');
	$temp = explode('.',$file_name);
	$file_ext = $temp[1];
	return(in_array($file_ext, $allowed_ext)==true) ? true :false;
}
function watermark_image($user,$file, $destination, $wm,$x,$y){
	$watermark = imagecreatefrompng($wm);
	$file2 = "wp-content/uploads/wm_images/".$user.'/'.$file;
	$source = getimagesize($file2);
	$source_mime = $source['mime'];
	if($source_mime == 'image/png'){
		$image = imagecreatefrompng($file2);
	}else if($source_mime == 'image/jpeg'){
		$image = imagecreatefromjpeg($file2);
	}
	else if($source_mime == 'image/gif'){
		$image = imagecreatefromgif($file2);
	}
	$srcsize = getimagesize($wm);
    $dest_x = ($srcsize[0]*70)/$srcsize[1];//width
    $dest_y = $srcsize[1] * 0.3;//height
	imagecopyresampled($image, $watermark, $x, $y, 0, 0,$dest_x,70, imagesx($watermark), imagesy($watermark));
	imagejpeg($image,$destination);
	return $destination;
}
?>