<?php
// Display submissions for review...
//
// Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license
//
include_once("../db/culturalentomology_db.php"); // includes $admin_email
include_once("shared/header.html");
include_once("shared/banner.html");

$userID       = $_GET['userID'];
$submit_first = $_GET['submit_first'];
$submit_last  = $_GET['submit_last'];
$submit_email = $_GET['submit_email'];
$entry_time   = $_GET['entry_time'];

  // PHPMailer SMTP settings:
  //SMTP needs accurate times, and the PHP timezone MUST be set
  //This should be done in your php.ini, but this is how to do it if you don't have access to that
  date_default_timezone_set('Etc/UTC');
  require $phpmailer_path;
  //Create a new PHPMailer instance
  $mail = new PHPMailer;
  //Tell PHPMailer to use SMTP
  $mail->isSMTP();
  $mail->SMTPDebug = $smtp_debug;
  //Ask for HTML-friendly debug output
  $mail->Debugoutput = 'html';
  //Set the hostname of the mail server
  $mail->Host = $smtp_host;
  //Set the SMTP port number - likely to be 25, 465 or 587
  $mail->Port = $smtp_port;
  //Whether to use SMTP authentication
  $mail->SMTPAuth = true;
  //Username to use for SMTP authentication
  $mail->Username = $smtp_username;
  //Password to use for SMTP authentication
  $mail->Password = $smtp_password;
  //Set who the message is to be sent from
  $mail->setFrom($smtp_from, $smtp_from_text);
  //Set an alternative reply-to address
  $mail->addReplyTo($smtp_replyto, $smtp_replyto_name);

$image_extensions_for_viewing = array(
  "bmp",
  "gif",
  "jpg",
  "jpeg",
  "pjpeg",
  "png",
  "BMP",
  "GIF",
  "JPG",
  "JPEG",
  "PJPEG",
  "PNG"
); 

// Determine if the submit button has been clicked.
// If so, begin validating form data.
// (After http://msconline.maconstate.edu/Tutorials/PHP/PHP07/php07-02.php)
if ($_POST['submitForm2'] == "Respond")
{
    // Search for specific entry_time unregistered submission by user
    $sql_submissions    = 'SELECT * FROM objects
                           WHERE fk_user_id="'.$userID.
                           '" AND entry_time= "'.$entry_time.
                           '" AND registered="0" AND hide="0"';
    $result_submissions = mysql_query($sql_submissions) or die (mysql_error());

    $row = mysql_fetch_object($result_submissions);
    $fileID = $row->pk_object_id;
    $filename = $row->filename1;

    echo '<div class="main"><br /><br />';

    if ($_POST['decision'] == "accept") {

        // Register accepted user
        $sql_register_user = 'UPDATE users SET user_registered="1"
                             WHERE pk_user_id="'.$userID.'"';
        mysql_query($sql_register_user) or die (mysql_error());

        // Update object database
        $sql_register  = 'UPDATE objects SET 
                         entry_date="'.date("Ymt").'", 
                         registered="1" 
                         WHERE pk_object_id="'.$fileID.'"';
        mysql_query($sql_register) or die (mysql_error());
    }
    elseif ($_POST['decision'] == "decline") {
        // Update object database -- DECLINE
        // Hide declined objects
        $sql_register  = 'UPDATE objects SET 
                         entry_date="'.date("Ymt").'", 
                         hide="1" 
                         WHERE pk_object_id="'.$fileID.'"';
        mysql_query($sql_register) or die (mysql_error());
    }

    // Search for user
    $sql_user = 'SELECT * FROM users WHERE pk_user_id="'.$userID.'"';
    $result_user = mysql_query($sql_user) or die (mysql_error());
    $row = mysql_fetch_object($result_user);
    $mail_to = $row->user_email;
    $firstname = $row->user_name_first;
    $lastname = $row->user_name_last;
    $mail->addAddress($mail_to, $firstname." ".$row->lastname);

    //-------------------------
    // Response: Accept/Decline
    //-------------------------
    $mail->Subject = 'Submission to the Insects Incorporated database of cultural entomology';
    
    // Email acceptance:
    if ($_POST['decision'] == "accept") {
        $mail->Body = "
        
        Dear $firstname $lastname,
        
        Thank you for submitting an object for review to the Insects Incorporated database of cultural entomology!
        
        We are happy to inform you that Insects Incorporated has accepted your objects for inclusion in its expanding, searchable database. You will be able to search for your objects by typing in your name or any keyword in the website search field.
        
        Please feel free to submit more works for review, and you are welcome to email us at barrett@pupating.org.
        
        Sincerely,
        Barrett Klein
        http://pupating.org
        ";
        
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            echo '<h1>Message delivery failed to reach '.$firstname.' '.$lastname.' ('.$mail_to.').</h1></div><br><br>';
            die;
        }
        else
        {
            echo $spc.'<h1>Message sent to '.$firstname.' '.$lastname.' ('.$mail_to.').</h1><br>'.$spc.
            'The object submitted by '.$firstname.' '.$lastname.' has been registered in the database.</p></div><br><br>';
            die;
        }
    }
    // Email rejection:
    if ($_POST['decision'] == "decline") {
        $mail->Body = "
        
        Dear $firstname $lastname,
        
        Thank you for submitting a work for review by Insects Incorporated.
        
        Unfortunately, we will not include these works in the Insects Incorporated database of cultural entomology at this time. Please feel free to submit other works for review, or email us at barrett@pupating.org.
        
        Sincerely,
        Barrett Klein
        http://pupating.org
        ";

        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            echo '<h1>Message delivery failed to reach '.$firstname.' '.$lastname.' ('.$mail_to.').</h1></div>';
            die;
        }
        else {
            echo $spc.'<h1>Message sent to '.$firstname.' '.$lastname.' ('.$mail_to.').</h1><br>'.$spc.
            'The object submitted by '.$firstname.' '.$lastname.' has NOT been registered in the database.</p></div><br><br>';
            die;
        }
    }
}     // if ($_POST['submitForm2'] == "Respond")

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
//  echo 'If accepting the submissions, rescale by viewing them at 
//        <A HREF="javascript:popUp(\'http://culturalentomology.org/objects\')">objects</A><br /><br />';
  echo $spc.'  <b>Accept</b>  <input name="decision" type="radio" value="accept" /><br />';
  echo $spc.'  <b>Decline</b> <input name="decision" type="radio" value="decline" /><br /><br />';
  echo $spc.'  <input type="submit" name="submitForm2" value="Respond" /><br /><br />';
  echo '</form>';

  //-------------------
  // Display submission
  //-------------------
  // Search for specific entry_time unregistered submission by user
  $sql = 'SELECT * FROM objects
          WHERE fk_user_id="'.$userID.
         '" AND entry_time= "'.$entry_time.
         '" AND registered="0" AND hide="0"';
  $result = mysql_query($sql) or die (mysql_error());

  // This whole loop is repeated in submit_info.php
  if ($result) {

    // Loop through search results
    while($row = mysql_fetch_object($result)) {
        $object_ID = $row->pk_object_id;
        $filename1 = $row->filename1;
        $filename2 = $row->filename2;
        $filename3 = $row->filename3;
        $filename4 = $row->filename4;
        $filename5 = $row->filename5;
        $filename6 = $row->filename6;
        $filename7 = $row->filename7;
        $filename8 = $row->filename8;
        $filename9 = $row->filename9;
        $filename10 = $row->filename10;
        $title = $row->title;
        $category1 = $row->category1;
        $category2 = $row->category2;
        $category3 = $row->category3;
        $category4 = $row->category4;
        $creator = $row->creator;
        $year = $row->year;
        $object_medium = $row->object_medium;
        $object_dimensions = $row->object_dimensions;
        $time_period = $row->time_period;
        $nation = $row->nation;
        $state = $row->state;
        $city = $row->city;
        $taxon_common_name = $row->taxon_common_name;
        $taxon_common_name2 = $row->taxon_common_name2;
        $taxon_common_name3 = $row->taxon_common_name3;
        $taxon_common_name4 = $row->taxon_common_name4;
        $taxon_order = $row->taxon_order;
        $taxon_order2 = $row->taxon_order2;
        $taxon_order3 = $row->taxon_order3;
        $taxon_order4 = $row->taxon_order4;
        $taxon_family = $row->taxon_family;
        $taxon_family2 = $row->taxon_family2;
        $taxon_family3 = $row->taxon_family3;
        $taxon_family4 = $row->taxon_family4;
        $taxon_species = $row->taxon_species;
        $taxon_species2 = $row->taxon_species2;
        $taxon_species3 = $row->taxon_species3;
        $taxon_species4 = $row->taxon_species4;
        $url = $row->url;
        $collection = $row->collection;
        $citation = $row->citation;
        $description = $row->description;
        $comments = $row->comments;
        $permission_information = $row->permission_information;

        // Line
        echo '<hr size="1" />';
  
        echo '<div class="collection">';

        // Text
        if (strlen(trim($title))>0) {
            echo '<b>'.$title.'</b><br>';
        }

        if (strlen(trim($description))>0) {
            $pattern = '/[\r\n]+/';
            $replacement = '<br><br>';
            $description = preg_replace($pattern, $replacement, $description);
            if (strlen(trim($description))>0) {
                echo '<div class="notes">';
                echo $description;
                echo '</div><br>';
            }
        }

        if (strlen(trim($creator))>0) {
            echo $creator;
        }
        if (strlen(trim($creator))>0) {
            echo ', ';
        }
        if (strlen(trim($year))>0) {
            echo $year;
        }
        if (((strlen(trim($creator))>0) || (strlen(trim($year))>0)) && ((strlen(trim($object_medium))>0) || (strlen(trim($object_dimensions))>0))) {
            echo ', ';
        }
        if (strlen(trim($object_medium))>0) {
            echo $object_medium. ' ';
        }
        if (strlen(trim($object_dimensions))>0) {
            echo '('.$object_dimensions.')';
        }
        if ((strlen(trim($creator))>0) || (strlen(trim($year))>0) || (strlen(trim($object_medium))>0) || (strlen(trim($object_dimensions))>0)) {
            echo '<br>';
        }

        if (strlen(trim($url))>0) {
           echo '<span class="tip">Website: </span><a href="'.$url.'" target=“_blank”>'.$url.'</a><br>';
        }

        if ((strlen(trim($city))>0) || (strlen(trim($state))>0) || (strlen(trim($nation))>0)) {
            echo '<span class="tip">Location: </span>';
        }

        if (strlen(trim($city))>0) {
            echo $city;
            if ((strlen(trim($state))>0) || (strlen(trim($nation))>0)) {
                echo ', ';
            } else {
                echo '<br>';
            }
        }
        if (strlen(trim($state))>0) {
            echo $state;
            if (strlen(trim($nation))>0) {
                echo ', '.$nation.'<br>';
            } else {
                echo '<br>';
            }
        } else {
            if (strlen(trim($nation))>0) {
                echo $nation.'<br>';
            }
        }

        if (strlen(trim($collection))>0) {
          echo '<span class="tip">Collection: </span>'.$collection.'';
          echo '<br>';
        }
        
        if (strlen(trim($citation))>0) {
          echo '<span class="tip">Citation: </span>'.$citation.'';
          echo '<br>';
        }
        
        if (strlen(trim($category1))>0) {
            if ((strlen(trim($category2))==0) && (strlen(trim($category3))==0) && (strlen(trim($category3))==0)) { 
                if (strlen(trim($category1))>0) {
                  echo '<span class="tip">Category: </span>'.$category1.'';
                }
            } else {
                echo '<span class="tip">Categories: </span>'.$category1;
                if (strlen(trim($category2))>0) {
                  echo ', '.$category2;
                }
                if (strlen(trim($category3))>0) {
                  echo ', '.$category3;
                }
                if (strlen(trim($category4))>0) {
                  echo ', '.$category4;
                }
            }
            echo '<br>';
        }

        if (strlen(trim($time_period))>0) {
          echo '<span class="tip">Time period: </span>'.$time_period.'';
          echo '<br>';
        }
        
        if (strlen(trim($taxon_common_name))>0) {
          echo '<span class="tip">Taxon common name: </span>'.$taxon_common_name.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_order))>0) {
          echo '<span class="tip">Taxon order: </span>'.$taxon_order.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_family))>0) {
          echo '<span class="tip">Taxon family: </span>'.$taxon_family.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_species))>0) {
          echo '<span class="tip">Taxon species: </span>'.$taxon_species.'';
          echo '<br>';
        }
        

        if (strlen(trim($taxon_common_name2))>0) {
          echo '<span class="tip">Taxon common name 2: </span>'.$taxon_common_name2.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_order2))>0) {
          echo '<span class="tip">Taxon order 2: </span>'.$taxon_order2.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_family2))>0) {
          echo '<span class="tip">Taxon family 2: </span>'.$taxon_family2.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_species2))>0) {
          echo '<span class="tip">Taxon species 2: </span>'.$taxon_species2.'';
          echo '<br>';
        }
        

        if (strlen(trim($taxon_common_name3))>0) {
          echo '<span class="tip">Taxon common name 3: </span>'.$taxon_common_name3.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_order3))>0) {
          echo '<span class="tip">Taxon order 3: </span>'.$taxon_order3.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_family3))>0) {
          echo '<span class="tip">Taxon family 3: </span>'.$taxon_family3.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_species3))>0) {
          echo '<span class="tip">Taxon species 3: </span>'.$taxon_species3.'';
          echo '<br>';
        }
        

        if (strlen(trim($taxon_common_name4))>0) {
          echo '<span class="tip">Taxon common name 4: </span>'.$taxon_common_name4.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_order))>0) {
          echo '<span class="tip">Taxon order 4: </span>'.$taxon_order.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_family4))>0) {
          echo '<span class="tip">Taxon family 4: </span>'.$taxon_family4.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_species4))>0) {
          echo '<span class="tip">Taxon species 4: </span>'.$taxon_species4.'';
          echo '<br>';
        }
        

        if (strlen(trim($permission_information))>0) {
          echo '<span class="tip">File permission: </span>'.$permission_information.'';
          echo '<br>';
        }

        echo '</div>';

        // Show files if images
        $filenames = array($filename1, $filename2, $filename3, $filename4, $filename5, $filename6, $filename7, $filename8, $filename9, $filename10);
        for ( $i = 0; $i <=9; $i += 1) {
            $filename = $filenames[$i];
            if (strlen($filename) > 0) {
                $extension = end(explode(".", $filename));
                if ( in_array($extension, $image_extensions_for_viewing ) ) {
                    $converted_filename = str_replace($extension, $converted_image_extension, $filename);
                    echo '<a href="'.$converted_images_path.'/'.$converted_filename.'" target="_blank"><img src="'.$converted_images_path.'/'.$converted_filename.'" width="480" border="0"></a><span class="font80">'.$converted_images_path.'/'.$converted_filename.'</span><br>';
                } else {
                    echo '<span class="tip">File: </span><a href="'.$moved_nonimages_path.'/'.$filename.'" target="_blank">'.$moved_nonimages_path.'/'.$filename.'</a><br>';
                }
            }
        }
        echo '<span class="idfont">#'.$object_ID.'</span><br>';
    }
  }  // This whole loop is repeated in submit_info.php

  echo '</div>';
  // Footer
  include_once("./shared/footer.php"); 

?>
