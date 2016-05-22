<?php
// Display submissions for review...
//
// Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license
//
include_once("../db/culturalentomology_db.php");
include_once("shared/header.html");
include_once("shared/banner.html");

$userID    = $_GET['userID'];
$firstname = $_GET['firstname'];
$lastname  = $_GET['lastname'];
$email     = $_GET['email'];

// Determine if the submit button has been clicked. 
// If so, begin validating form data.
// After http://msconline.maconstate.edu/Tutorials/PHP/PHP07/php07-02.php

if ($_POST['submitForm3'] == "Respond")
{
   // Search for all unregistered submissions by user
      $sql_submissions    = 'SELECT * FROM images
                             WHERE fk_user_id="'.$userID.
                             '" AND registered="0" AND hide="0"';
      $result_submissions = mysql_query($sql_submissions) or die (mysql_error());
      $num_submissions    = mysql_num_rows($result_submissions);

   // Loop through submissions
      $irow=0;
      $fileIDs = array();
      $filenames = array();
      while($row = mysql_fetch_object($result_submissions))
      {
         $fileIDs[$irow] = $row->pk_image_id;
         $filenames[$irow] = $row->image_filename;
         $irow = $irow + 1;
      }

      echo '<div class="main"><br /><br />';

      if ($_POST['decision'] == "accept")
      {

      // Register accepted user
         $sql_register_user = 'UPDATE users SET user_registered="1"
                               WHERE pk_user_id="'.$userID.'"';
         mysql_query($sql_register_user) or die (mysql_error());

      // Update image database
         for($isub=0; $isub<$num_submissions; $isub++) {
               
            $ID = $fileIDs[$isub];
            $filename = $filenames[$isub];

         // Register accepted images
            $sql_register  = 'UPDATE images SET 
                             entry_date="'.date("Ymt").'", 
                             registered="1" 
                             WHERE pk_image_id="'.$ID.'"';

            mysql_query($sql_register) or die (mysql_error());
         }
      }
      elseif ($_POST['decision'] == "decline")
      {
      // Update image database -- DECLINE
         for($isub=0; $isub<$num_submissions; $isub++) {
               
            $ID = $fileIDs[$isub];
            $filename = $filenames[$isub];

         // Hide declined images
            $sql_register  = 'UPDATE images SET 
                             entry_date="'.date("Ymt").'", 
                             hide="1" 
                             WHERE pk_image_id="'.$ID.'"';
            mysql_query($sql_register) or die (mysql_error());
         }
      }

   //-------------------------
   // Response: Accept/Decline
   //-------------------------
      $mail_to      = $email;  //$email.', barrett@pupating.org';
      $mail_from    = 'From: Insects Incorporated';
      $mail_subject = 'Submission to Insects Incorporated Database of Cultural Entomology';

      if ($_POST['decision'] == "accept")
      {

      // Email acceptance:

         $mail_body  = "

     Dear $firstname $lastname,

     Thank you for submitting images for review to the Insects Incorporated database of cultural entomology!

     We are happy to inform you that Insects Incorporated has accepted your images for inclusion in its expanding, searchable database. You will be able to search for your images by typing in your name or any keyword in the website search field.

     Please feel free to submit more works for review, and you are welcome to email us at barrett@pupating.org.

     Sincerely,
     Barrett Klein
     http://pupating.org
     ";

         if (!mail($mail_to, $mail_subject, $mail_body, $mail_from)) {
            echo '<h1>Message delivery failed.</h1></div><br><br>';
            die;
         }
         else
         {
            echo $spc.'<h1>Message sent!</h1><br>'.$spc.
                 'All submitted images from '.$firstname.' '.$lastname.' have been registered.</p></div><br><br>';
            die;
         }

      }
      if ($_POST['decision'] == "decline")
      {

      // Email rejection:

         $mail_body  = "

     Dear $firstname $lastname,

     Thank you for submitting works for review by Insects Incorporated.

     Unfortunately, we will not include these works in the Insects Incorporated database of cultural entomology at this time. Please feel free to submit other works for review, or email us at barrett@pupating.org.

     Sincerely,
     Barrett Klein
     http://pupating.org
     ";

         if (!mail($mail_to, $mail_subject, $mail_body, $mail_from)) {
            echo '<h1>Message delivery failed.</h1></div>';
            die;
		 }
         else
         {
            echo $spc.'<h1>Message sent.</h1><br>'.$spc.
                 'The submitted images from '.$firstname.' '.$lastname.' have NOT been registered.</div>';
            die;
         }
      }
}     // if ($_POST['submitForm3'] == "Respond")

?>

<div class="main">

<h1>Submissions: Review</h1>
<br>

<?php
      $spc = '&nbsp;&nbsp;&nbsp;';
   //------------
   // Create form
   //------------
      echo '<form method="post" enctype="multipart/form-data">';
      echo 'If accepting the submissions, rescale by viewing them at 
            <A HREF="javascript:popUp(\'http://culturalentomology.org/images\')">images</A><br /><br />';
      echo $spc.'  <b>Accept</b>  <input name="decision" type="radio" value="accept" /><br />';
      echo $spc.'  <b>Decline</b> <input name="decision" type="radio" value="decline" /><br /><br />';
      echo $spc.'  <input type="submit" name="submitForm3" value="Respond" /><br /><br />';

      echo '<input type="hidden" name="userID" class="bginput" value="'.$userID.'">';
      echo '<input type="hidden" name="num_submissions" class="bginput" value="'.$num_submissions.'">';
      echo '<input type="hidden" name="fileIDs" class="bginput" value="'.$fileIDs.'">';
      echo '<input type="hidden" name="filenames" class="bginput" value="'.$filenames.'">';
      echo '</form>';

   //------------------
   // Count submissions
   //------------------
   // Search for all unregistered submissions by user
      $sql_submissions    = 'SELECT * FROM images
                             WHERE fk_user_id="'.$userID.
                             '" AND registered="0" AND hide="0"';
      $result_submissions = mysql_query($sql_submissions) or die (mysql_error());
      $num_submissions    = mysql_num_rows($result_submissions);

   // Display number of submissions
      if ($num_submissions==1) {
         echo '<i>1 image submitted for review: </i><br />';
      }
      else {
         echo '<i>'.$num_submissions . ' images submitted for review: </i><br />';
      }

   //------------------------
   // Display each submission
   //------------------------
   // Loop through submissions
      $irow=0;
      $fileIDs = array();
      $filenames = array();
      while($row = mysql_fetch_object($result_submissions))
      {
         $ID =               $row->pk_image_id;
         $creator =          $row->creator;
         $title =            $row->title;
         $medium =           $row->object_medium;
         $date =             $row->year;
         $notes =            $row->description;
         $name =             $row->image_filename;
         $collection =       $row->collection;
         $url =              $row->url;

      // Store filenames
         $fileIDs[$irow] = $ID;
         $filenames[$irow] = $name;

      // Line
         echo '<hr size="1" />';

      // Image
         echo '<table width="800" border="0" cellspacing="0" cellpadding="10">';
         echo ' <tr>';
         echo '  <td width="240">';
         echo '   <img src="images/'.$name.'" height="240">';
         echo '   <span class="font80">'.$ID.'</span>';
         echo '  </td>';
         echo '  <td width="560">';

      // Input text
         echo '<i>Title:</i> '.stripslashes($title).'<br />';
         echo '<i>Creator:</i> '.stripslashes($creator).'<br />';
         echo '<i>Medium:</i> '.stripslashes($medium).'<br />';        
         echo '<i>Year:</i> ';
         if ($date!="0") {
            echo $date.' ';
         }
         echo '<br />';
         echo '<i>Description:</i> '.stripslashes($notes).'<br />';
         echo '<i>File name:</i> '.stripslashes($name).'<br />';
         echo '<i>Collection:</i> '.stripslashes($collection).'<br />';
         echo '<i>URL:</i> '.stripslashes($url).'<br />';
         echo '  </td>';
         echo ' </tr>';
         echo '</table>';

         $irow=$irow+1;

      } // while

      echo '</div>';

   // Footnote
      include_once("./shared/footer.php"); 

?>
