<?php
// submit_images.php is a form for a potential user to upload images 
// and includes submit_labels.php (called by submit.php).
//
// Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license
//

include_once("../db/culturalentomology_db.php");
include_once("shared/header.html");

$submit_first = $_POST['submit_first'];
$submit_last  = $_POST['submit_last'];
$submit_email = $_POST['submit_email'];
$userID = $_POST['userID'];

if(strlen($submit_first)==0 && strlen($submit_last)==0) {
               include_once("shared/header.html");
               include_once("shared/banner.html"); 
               echo '<div class="main"><br /><br /><span class="redfont"><p>
               Please type in a correct address.
               </p></span></div>';
               die;
               $max_uploads = 0;
} else {       $max_uploads = 10;
}

$max_size = 10000000; // Maximum image size
$max_MB   = '10';

// If image upload form submitted
if ($_POST['submitForm2'] == "Upload")
{

// Insert a new (unregistered) user into the database
   $sql_isuser = 'SELECT pk_user_id FROM users
                  WHERE user_name_first="'.$submit_first.'" AND user_name_last="'.$submit_last.'"';
   $result_isuser = mysql_query($sql_isuser) or die (mysql_error());
   $num_isuser    = mysql_num_rows($result_isuser);
// ...if it is a new user (unmatched first, last)
   if ($num_isuser==0) {
      $sql_user = 'INSERT INTO users (user_email,user_name_first,user_name_last,user_indate,user_registered)
                   VALUES ("'.$submit_email.'","'.$submit_first.'","'.$submit_last.'","'.date("Ymd").'","0")'; //,"'.$.'")';
      mysql_query($sql_user) or die (mysql_error());

   // Get the new user's ID
      $sql_userID    = 'SELECT pk_user_id FROM users
                        WHERE user_name_first="'.$submit_first.'" AND user_name_last="'.$submit_last.'"';
      $result_userID = mysql_query($sql_userID) or die (mysql_error());
      $row_userID    = mysql_fetch_row($result_userID);
      $userID        = $row_userID[0];
   }
// ...or if it is an existing user
   else {
   // Get the new user's ID
      $row_userID = mysql_fetch_row($result_isuser);
      $userID     = $row_userID[0];
   }

// Upload images and store their filenames in the database
// Determine if any fields were filled
   // (This is adapted from code downloaded from www.plus2net.com)
   // include_once("shared/uploadForm.php");
   $files_entered = 0;
   while(list($key2,$value2) = each($_FILES["files"]["name"]))
   {
      if(!empty($value2)) {

	 $files_entered = 1;
         $file_type = $_FILES["files"]["type"][$key2]; 
         $file_size = $_FILES["files"]["size"][$key2]; 
         //echo 'value2: '.$value2.' type: '.$file_type.' size: '.$file_size.'<br>';

         if ($file_type=="image/pjpeg" ||
             $file_type=="image/jpeg"  ||
             $file_type=="image/jpg"   ||
             $file_type=="image/gif"   ||
             $file_type=="image/png"   ||
             $file_type=="image/bmp")
         {

            if ($file_size < $max_size) {
            // Copy files to image upload directory (with new, unique names)
               $filename     = $value2;
               $time_now     = time();
               $uniq_index   = $key2+1;
               $new_filename = $submit_last.'_'.$time_now.'_'.$uniq_index.'_'.$filename;

               $new_file_sql = $new_filename;
               $new_file     = "images/".$new_filename;
               move_uploaded_file($_FILES['files']['tmp_name'][$key2], $new_file);  
               //rename($_FILES["files"]["tmp_name"][$key2], $new_file);

               $sql = 'INSERT INTO images (image_filename, entry_date, registered, fk_user_id)
                       VALUES ("'.$new_file_sql.'","'.date("Ymd").'","0","'.$userID.'")';
               mysql_query($sql) or die (mysql_error());
            }
         // File size error
            else {
               include_once("shared/header.html");
               include_once("shared/banner.html"); 
               echo '<br /><span class="redfont"><p>'.$file_size . ' is too large a file.</p><p>
               Please hit the back button on your browser and upload appropriately sized images.
               </p></span>';
               die;
            }
         }
      // File format error
         else {
            include_once("shared/header.html");
            include_once("shared/banner.html"); 
            echo '<br /><span class="redfont"><p>
            Please hit the back button on your browser and upload appropriately formatted images (jpg, gif, png, bmp).
            </p></span>';
            die;
         }
      }
   }

// If there were no filenames entered in the image upload form, ERROR
   if($files_entered == 0)
   {
      include_once("shared/header.html");
      include_once("shared/banner.html"); 
      echo '<br /><span class="redfont"><p>
            You must upload at least one image for review.</p><p>
            Please hit the back button on your browser and try again.</p></span>';
 	  die;
   }

// If there were files, go to the image label/annotation page.
   else {
      include_once("submit_labels.php");
      die;
   }
}     // if ($_POST['submitForm2'] == "Upload")



// HTML forms:

?>

   <div class="main">

   <h1>Submissions: Upload images</h1>
 
   <form method="post" action="submit_images.php" enctype="multipart/form-data">

<?php
      echo '<div class="textblocks">';

      echo "<p>For review purposes, please upload up to $max_uploads images. <br />
  After uploading them, you will be requested to provide further information.
  <br /><br />
  Images must be in either JPEG, GIF, PNG, or BMP format, and no larger than ".$max_MB."MB each.</p>
  <p>Content you submit to the database should not contain third party copyrighted material, or material that is subject to other third party proprietary rights, unless you have permission.</p>
  </div>
  <br />

  <table border='0' width='600' cellspacing='5' cellpadding='0' align='left'>";

   // Filenames
      for($i=1; $i<=$max_uploads; $i++) {
         echo "<tr><td>&nbsp;&nbsp;&nbsp;$i</td><td>
               <input type=file name='files[]' class='bginput' size='50'></td></tr>";
      }

      echo '<td>&nbsp;</td><tr><td colspan=2 align="left">';
      echo '<input type="hidden" name="submit_first"    value="'.$submit_first.'">';
      echo '<input type="hidden" name="submit_last"     value="'.$submit_last.'">';
      echo '<input type="hidden" name="submit_email"    value="'.$submit_email.'">';
      echo '<input type="hidden" name="userID"          value="'.$userID.'">';
      echo $spc.'<input type="submit" name="submitForm2" value="Upload">';
      echo '</td></tr></table>';
      echo '</form>';

      echo '</div>';

      //include_once("shared/footer.php");

?>