<?php
// submit_reviewed.php updates the database based on reviewed images.
//
// Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license
//
include_once("../db/culturalentomology_db.php");
include_once("shared/header.html");
include_once("shared/banner.html");

$userID = $_POST[userID];
$submit_email = $_POST[submit_email];
$submit_first = $_POST[submit_first];
$submit_last = $_POST[submit_last];
$num_submissions = $_POST[num_submissions];
$update_name1 = $_POST[update_name1];

$title="Insects Incorporated: Database of Cultural Entomology";

?>

<div class="main">

<h1>Submissions: Pending review</h1>

<div class="textblocks">

<p>
Thank you for submitting works for review by the Database of Cultural Entomology! <br />
If you have any comments or questions, please contact us at: barrett[at]pupating.org.
<br /><br />
</p>

</div>

<?php

      if ($num_submissions>0) {

      //--------------------
      // Update images table
      //--------------------
         if(isset($update_name1)) {

            $iImage=0;

            while($iImage < $num_submissions) {
              $iImage=$iImage+1;

              $update_id = $_POST[update_id.$iImage];
              $update_name = $_POST[update_name.$iImage];
              $update_title = $_POST[update_title.$iImage];
              $update_creator = $_POST[update_creator.$iImage];
              $update_medium = $_POST[update_medium.$iImage];
              $update_date = $_POST[update_date.$iImage];
              $update_notes = $_POST[update_notes.$iImage];
              $update_url = $_POST[update_url.$iImage];
              $update_collection = $_POST[update_collection.$iImage];

              $ID_iImage             = trim(mysql_real_escape_string($update_id));
              $name_iImage           = trim(mysql_real_escape_string($update_name));
              $title_iImage          = trim(mysql_real_escape_string($update_title));
              $creator_iImage        = trim(mysql_real_escape_string($update_creator));
              $medium_iImage         = trim(mysql_real_escape_string($update_medium));
              $date_iImage           = trim(mysql_real_escape_string($update_date));
              $notes_iImage          = trim(mysql_real_escape_string($update_notes));
              $url_iImage            = trim(mysql_real_escape_string($update_url));
              $collection_iImage     = trim(mysql_real_escape_string($update_collection));

              if (strlen($name_iImage)>0) {

                 $sql_image  = 'UPDATE images SET ';
                 $sql_image .= 'creator="'         .$creator_iImage.'", ';
                 $sql_image .= 'title="'           .$title_iImage.'", ';
                 $sql_image .= 'object_medium="'   .$medium_iImage.'", ';
                 $sql_image .= 'year="'            .$date_iImage.'", ';
                 $sql_image .= 'description="'     .$notes_iImage.'", ';
                 $sql_image .= 'image_filename="'  .$name_iImage.'", ';
                 $sql_image .= 'collection="'      .$collection_iImage.'", ';
                 $sql_image .= 'url="'             .$url_iImage.'" ';
                 $sql_image .= ' WHERE pk_image_id="' .$ID_iImage.'"';

                 //echo $sql_image.'<br>';
				 
                 mysql_query($sql_image) or die (mysql_error());           

			  }
           }     //while($iImage < $num_submissions) {
         }       //if(isset($update_image_name1)) {
      }          //if ($num_submissions>0) {


$review_link  = 'http://culturalentomology.org/submit_review.php?userID='.$userID.
                '&firstname='.$submit_first.'&lastname='.$submit_last.'&email='.$submit_email;
$mail_to      = 'barrett@pupating.org';
$mail_from    = 'From: Database of Cultural Entomology';
$mail_subject = 'Submission to the Database of Cultural Entomology';
$mail_body    = "

                 New submission for review ($num_submissions)!

                 $review_link

                 Name:  $submit_first $submit_last (ID: $userID)
                 Email: $submit_email

                ";

if (!mail($mail_to, $mail_subject, $mail_body, $mail_from)) {
  echo '<p><span class="redfont">Notification message delivery failed. Please contact barrett[at]pupating.org.</span></p><br /><br />';
}

?>

</div>

<? include_once("shared/footer.php"); ?>
