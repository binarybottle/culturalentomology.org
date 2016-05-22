<?php
// submit_labels.php is a form for a potential user to label uploaded images 
// and sends this information to submit_pending.php (called by submit_images.php).
//
// Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license
//
include_once("../../db/culturalentomology_db.php");
include_once("shared/header.html");
include_once("shared/banner.html");

?>

<div class="main">

<h1>Submissions: Label images</h1>

<div class="textblocks">

<p>Please annotate the images you uploaded with detailed information.<br />
When finished, click the "Submit" button.
</p>
</div>
<br />
<?php

   // Search for all unregistered submissions by user
      $sql_submissions    = 'SELECT * FROM images
                             WHERE fk_user_id="'.$userID.
                             '" AND registered="0" AND hide="0"';
      $result_submissions = mysql_query($sql_submissions) or die (mysql_error());
   // Number of submissions
      $num_submissions = mysql_num_rows($result_submissions);
      if ($num_submissions==1) {
         echo '<span class="font80"><i>1 image submitted for review: </i></span><br />';
      }
      else {
         echo '<span class="font80"><i>'.$num_submissions . ' images submitted for review: </i></span><br />';
      }

   //--------------------------------------------
   // Construct form with each submission's entry
   //--------------------------------------------
      echo '<form method="post" action="submit_pending.php" enctype="multipart/form-data">';

   // Line & buttons
      echo '<hr size="1" />';
      $spc1 = '&nbsp;&nbsp;';
      $spc2 = '&nbsp;&nbsp;&nbsp;&nbsp;';
      echo $spc1.'<input type="submit" value="Submit" />';
      echo $spc2.'<input type="reset"  value="Reset"  />';

   // Loop through submissions
      $irow=1;
      while($row = mysql_fetch_object($result_submissions))
      {
         $ID   = $row->pk_image_id;
         $name = $row->image_filename;

      // Line
         echo '<hr size="1" />';

      // Image
         echo '<table width="800" border="0" cellspacing="0" cellpadding="10">';
         echo ' <tr>';
         echo '  <td width="240">';
         echo '   <img src="images/'. $name . '" height="240">';
         echo '   <span class="font80">images/'.$name.' ('.$ID.')</span>';        
         echo '  </td>';
         echo '  <td width="560">';

      // Input text
         echo '<div class="font80"><i>';
         echo '<input type="hidden" name="update_id'  .$irow.'" value="'.$ID             .'"><br />';
         echo '<input type="hidden" name="update_name'.$irow.'" value="'.$name           .'"><br />';

         echo 'Title:          <br /><input type="text" size="65"              name="update_title'         .$irow.'"><br /><br />';
         echo 'Creator:  <br /><input type="text" size="65"           name="update_creator'       .$irow.'"><br /><br />';
         echo 'Medium:         <br /><input type="text" size="65"              name="update_medium'        .$irow.'"><br /><br />';
         echo 'Year (YYYY):    <input type="text" size="10"                    name="update_date'          .$irow.'"><br /><br />';
         echo 'Description (text/html):          <br /><textarea cols="64" rows="2"              name="update_notes'         .$irow.'"></textarea><br /><br />';
         echo 'URL:      <input type="text" size="15"                    name="update_url'           .$irow.'"><br /><br />';
         echo 'Collection:     <input type="text" size="15"                    name="update_collection'    .$irow.'"><br /><br />';
         echo '</div>';
         echo '   </td>';
         echo '  </tr>';
         echo ' </table>';

         $irow=$irow+1;

      } // while

      echo '<input type="hidden" name="submit_first"     value="'.$submit_first    .'">';
      echo '<input type="hidden" name="submit_last"      value="'.$submit_last     .'">';
      echo '<input type="hidden" name="submit_email"     value="'.$submit_email    .'">';
      echo '<input type="hidden" name="num_submissions"  value="'.$num_submissions .'">';
      echo '<input type="hidden" name="userID"           value="'.$userID          .'">';
      echo '</form>';

      echo '</div>';

      include_once("./shared/footer.php");

?>