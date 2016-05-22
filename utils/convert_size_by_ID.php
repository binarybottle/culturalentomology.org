<?php

include("../../db/infovis_db.php");
$sql = "SELECT image_file FROM images WHERE pk_image_id = $ID";
$result = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_row($result);
$file = $row[0];

$file_bits = explode('/',$file);
$size_file_bits = count($file_bits);
$iter=1;
$file_path='.';
while($iter<$size_file_bits-1) {  
  $file_path = $file_path.'/'.$file_bits[$iter];
  $iter+=1;
}
$file_name = $file_bits[$size_file_bits-1];

// Image Magick image conversion

$cnvrt_height = 180;

$convert_cmd = '/usr/bin/convert';  // Full pathname to IM's `convert'
$cnvrt_path  = 'visuals/qdig-files/converted-images/'.$file_path;

$cnvrt_thmb['prefix'] = 'sm_';
$cnvrt_thmb['size'] = 40; // Thunbnail image height in pixels.
                          // Sizes: 10 is tiny, 20 is small, 35 is medium,
                          //        50 is large, 75 is jumbo
$cnvrt_thmb['qual'] = 60; // Thumbnail image quality.  Large thumbnails
                          // may look better, but will have increased file
                          // size, if you increase this a bit.
$cnvrt_thmb['sharpen'] = '0.6x0.6'; // Level of sharpening for thumbnails.
$cnvrt_thmb['no_prof'] = TRUE; // Strip image profile data to reduce size.
                                // (May be incompatible with some servers.)

echo $cnvrtd_img = $cnvrt_path.'/'.$cnvrt_thmb['prefix'].$file_name;

$command = $convert_cmd
.' -geometry '.$cnvrt_height.'x'
.' -quality '.$cnvrt_thmb['qual']
.' -sharpen '.$cnvrt_thmb['sharpen']
.' '.$file.' '.$cnvrtd_img;

echo $command;

exec($command);

 
?>