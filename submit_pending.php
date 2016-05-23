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
$update_image_filename1 = $_POST[update_image_filename1];

$title="Insects Incorporated: Database of Cultural Entomology";

?>

<div class="main">

<h1>Submissions: Pending review</h1>

<div class="textblocks">

<p>
Thank you for submitting works for review by the Insects Incorporated database of cultural entomology! <br />
If you have any comments or questions, please contact us at: barrett[at]pupating.org.
<br /><br />
</p>

</div>

<?php

      if ($num_submissions>0) {

      //--------------------
      // Update images table
      //--------------------
         if(isset($update_image_filename1)) {

            $iImage=0;

            while($iImage < $num_submissions) {
              $iImage=$iImage+1;

              $update_id = $_POST[update_id.$iImage];
              $update_image_filename = $_POST[update_image_filename.$iImage];
              $update_title = $_POST[update_title.$iImage];
              $update_primary_category = $_POST[update_primary_category.$iImage];
              $update_category2 = $_POST[update_category2.$iImage];
              $update_category3 = $_POST[update_category3.$iImage];
              $update_category4 = $_POST[update_category4.$iImage];
              $update_creator = $_POST[update_creator.$iImage];
              $update_year = $_POST[update_year.$iImage];
              $update_object_medium = $_POST[update_object_medium.$iImage];
              $update_object_dimensions = $_POST[update_object_dimensions.$iImage];
              $update_time_period = $_POST[update_time_period.$iImage];
              $update_nation = $_POST[update_nation.$iImage];
              $update_city = $_POST[update_city.$iImage];
              $update_taxon_common_name = $_POST[update_taxon_common_name.$iImage];
              $update_taxon_order = $_POST[update_taxon_order.$iImage];
              $update_taxon_family = $_POST[update_taxon_family.$iImage];
              $update_taxon_species = $_POST[update_taxon_species.$iImage];
              $update_url = $_POST[update_url.$iImage];
              $update_collection = $_POST[update_collection.$iImage];
              $update_citation = $_POST[update_citation.$iImage];
              $update_description = $_POST[update_description.$iImage];
              $update_comments = $_POST[update_comments.$iImage];
              $update_permission_information = $_POST[update_permission_information.$iImage];

              $ID_iImage = trim(mysql_real_escape_string($update_id));
              $image_filename_iImage = trim(mysql_real_escape_string($update_image_filename));
              $title_iImage = trim(mysql_real_escape_string($update_title));
              $primary_category_iImage = trim(mysql_real_escape_string($update_primary_category));
              $category2_iImage = trim(mysql_real_escape_string($update_category2));
              $category3_iImage = trim(mysql_real_escape_string($update_category3));
              $category4_iImage = trim(mysql_real_escape_string($update_category4));
              $creator_iImage = trim(mysql_real_escape_string($update_creator));
              $year_iImage  = trim(mysql_real_escape_string($update_year));
              $object_medium_iImage = trim(mysql_real_escape_string($update_object_medium));
              $object_dimensions_iImage = trim(mysql_real_escape_string($update_object_dimensions));
              $time_period_iImage = trim(mysql_real_escape_string($update_time_period));
              $nation_iImage = trim(mysql_real_escape_string($update_nation));
              $city_iImage = trim(mysql_real_escape_string($update_city));
              $taxon_common_name_iImage = trim(mysql_real_escape_string($update_taxon_common_name));
              $taxon_order_iImage = trim(mysql_real_escape_string($update_taxon_order));
              $taxon_family_iImage = trim(mysql_real_escape_string($update_taxon_family));
              $taxon_species_iImage = trim(mysql_real_escape_string($update_taxon_species));
              $url_iImage = trim(mysql_real_escape_string($update_url));
              $collection_iImage = trim(mysql_real_escape_string($update_collection));
              $citation_iImage = trim(mysql_real_escape_string($update_citation));
              $description_iImage = trim(mysql_real_escape_string($update_description));
              $comments_iImage = trim(mysql_real_escape_string($update_comments));
              $permission_information_iImage = trim(mysql_real_escape_string($update_permission_information));

              if (strlen($image_filename_iImage)>0) {

                 $sql_image  = 'UPDATE images SET ';
                 $sql_image .= 'title="'           .$title_iImage.'", ';
                 $sql_image .= 'primary_category="'.$primary_category_iImage.'", ';
                 $sql_image .= 'category2="'.$category2_iImage.'", ';
                 $sql_image .= 'category3="'.$category3_iImage.'", ';
                 $sql_image .= 'category4="'.$category4_iImage.'", ';
                 $sql_image .= 'creator="'.$creator_iImage.'", ';
                 $sql_image .= 'year="'.$year_iImage.'", ';
                 $sql_image .= 'object_medium="'.$object_medium_iImage.'", ';
                 $sql_image .= 'object_dimensions="'.$object_dimensions_iImage.'", ';
                 $sql_image .= 'nation="'.$nation_iImage.'", ';
                 $sql_image .= 'city="'.$city_iImage.'", ';
                 $sql_image .= 'taxon_common_name="'.$taxon_common_name_iImage.'", ';
                 $sql_image .= 'taxon_order="'.$taxon_order_iImage.'", ';
                 $sql_image .= 'taxon_family="'.$taxon_family_iImage.'", ';
                 $sql_image .= 'taxon_species="'.$taxon_species_iImage.'", ';
                 $sql_image .= 'url="'.$url_iImage.'", ';
                 $sql_image .= 'collection="'.$collection_iImage.'", ';
                 $sql_image .= 'citation="'.$citation_iImage.'", ';
                 $sql_image .= 'description="'.$description_iImage.'", ';
                 $sql_image .= 'comments="'.$comments_iImage.'", ';
                 $sql_image .= 'permission_information="'.$permission_information_iImage.'", ';
                 $sql_image .= 'image_filename="'.$image_filename_iImage.'"';
                 $sql_image .= ' WHERE pk_image_id="'.$ID_iImage.'"';

                 //echo $sql_image.'<br>';

                 mysql_query($sql_image) or die (mysql_error());           

              }
           }     //while($iImage < $num_submissions) {
         }       //if(isset($update_image_filename1)) {
      }          //if ($num_submissions>0) {


$review_link  = 'http://culturalentomology.org/submit_review.php?userID='.$userID.
                '&firstname='.$submit_first.'&lastname='.$submit_last.'&email='.$submit_email;
$mail_to      = 'barrett@pupating.org';
$mail_from    = 'From: Insects Incorporated database of cultural entomology';
$mail_subject = 'Submission to the Insects Incorporated database of cultural entomology';
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
