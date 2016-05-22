<?php

// convert_images.php is taken from Qdig's "Alternate-sized Image Conversion" utility.
//
// @rno klein, 2007
/*
+----------------------------------------------------------------------+
| Qdig - A Quick Digital Image Gallery
|
| Qdig is an easy-to-use script that dynamically creates an image
| gallery or set of galleries from image files stored on your web
| server's filesystem.  The script is simple to install, just place it
| in a directory that contains images and/or subdirectories with images.
| You can navigate among directories for an organized presentation of
| any size image collection.  Qdig can generate thumbnail images and
| web-sized resampled versions of large images such as digital camera
| photos.  Converting (resampling) images requires either Image Magick
| or PHP's GD extensions and some quick-and-simple additional setup.
| Qdig supports image captions and includes built-in caption editing
| capability.  Images with EXIF metadata can include an EXIF summary
| line, including a link that exposes detailed EXIF information.  There
| are dozens of configurable options for customizing your galleries.
| The script runs stand-alone, or a gallery may be included within
| another page such as a weblog.  Enjoy!
+----------------------------------------------------------------------+
| Copyright 2002, 2003, 2004, 2005, 2006, 2007 Hagan Fox
| This program is distributed under the terms of the
| GNU General Public License, Version 2
|
| This program is free software; you can redistribute it and/or modify
| it under the terms of the GNU General Public License, Version 2 as
| published by the Free Software Foundation.
|
| This program is distributed in the hope that it will be useful,
| but WITHOUT ANY WARRANTY; without even the implied warranty of
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
| GNU General Public License for more details.
|
| You should have received a copy of the GNU General Public License,
| Version 2 along with this program; if not, visit GNU's Home Page
| http://www.gnu.org/
+----------------------------------------------------------------------+
CVS: $Id: index.php,v 1.135 2007/02/11 06:37:03 haganfox Exp $#
*/

/*
* Paths: @rno
*/
$img_path     = '../infovisuals/visualcomplexity';
$cnvrt_path   = '../infovisuals_converted';
$convert_cmd  = '/usr/bin/convert';  // Full pathname to ImageMagick's `convert'

/*
* Alternate-sized Image Conversion Settings
*
* ['prefix']  is the filename prefix for the generated file.
* ['sharpen'] is the sharpen pramater passed to ImageMagick.
* ['maxwid']  is the size setting.  Other dimensions are calculated.
* ['qual']    is the compression quality level.
*/
$cnvrt_alt['mesg_on']     = TRUE; // Produce a message when an image is converted.
$cnvrt_alt['no_prof']     = TRUE; // Strip image profile data to reduce size.
                                   // (May be incompatible with some servers.)
$cnvrt_alt['aspect']      = 0.75;  // Default inverted aspect ratio (H/W) (experimental) // TODO
$cnvrt_alt['by_height']   = TRUE;  // Convert by height, not "height or width"...
$cnvrt_alt['limit_width'] = TRUE;  // ...except extra-wide images. 
// small
$cnvrt_size[0]['prefix']  = 'thum_';
$cnvrt_size[0]['sharpen'] = '0.6x0.8';
$cnvrt_size[0]['maxwid']  = 120;
$cnvrt_size[0]['qual']    = 87;
// medium
$cnvrt_size[1]['prefix']  = 'conv_';
$cnvrt_size[1]['sharpen'] = '0.6x0.9';
$cnvrt_size[1]['maxwid']  = 640;
$cnvrt_size[1]['qual']    = 100;

/**
* Et-cetera
*/
$excl_imgs[] = 'qdig-bg.jpg'; // | Ignore any image with its name
$excl_imgs[] = 'favicon.png'; // | included here.  Add as many of
$excl_imgs[] = '';            // | these as you wish.
$excl_img_preg = '/^thumb_/'; // Ignore images by perl-compatible regex.
$excl_img_pattern = '_thumb'; // Don't display files containing this string.

$exclude_gif      = 0;
$extra_paranoia   = FALSE;
$ignore_dotfiles  = TRUE;
$imgname_maxlen   = 50;


/*
* RESIZE!
*/
$imgs        = getImageFilenames($img_path);
$resize_msg  = '';
$resize_msg .= resizeImage($cnvrt_size[0]);
$resize_msg .= resizeImage($cnvrt_size[1]);
print $resize_msg;


/*
* Generate images of alternate sizes.
*/
function resizeImage($cnvrt_arry)
{
	global  $imgs, $img_path, $cnvrt_path, $convert_cmd, $cnvrt_alt;

	if (empty($imgs)) { return; }
        $strip_prof = ($cnvrt_alt['no_prof'] == TRUE) ? ' +profile "*"' : '';
	if ($cnvrt_alt['mesg_on'] == TRUE) { $str = ''; }
	if (@$cnvrt_alt['aspect'] < 0.59 || @$cnvrt_alt['aspect'] > 0.81) {
		$cnvrt_alt['aspect'] = 0.75; }
	foreach ($imgs as $img_file) {
		$orig_img   = $img_path.'/'.$img_file;
		$cnvrtd_img = $cnvrt_path.'/'.$cnvrt_arry['prefix'].$img_file;
		if (! is_file($cnvrtd_img)) {
			$img_size = GetImageSize($orig_img);
			$height   = $img_size[1];
			$width    = $img_size[0];
			$area     = $height * $width;
			$maxarea  = $cnvrt_arry['maxwid'] * $cnvrt_arry['maxwid'] * 0.9;
			$maxheight = ($cnvrt_arry['maxwid'] * $cnvrt_alt['aspect'] + 1);
			if (($width - 0.2) / $height > 1 / $cnvrt_alt['aspect']) {  // TODO was +1.2
				$factor = 1;
				if ($cnvrt_alt['limit_width']) $cnvrt_alt['by_height'] = FALSE;
			} else { $factor = 0.9375; }
			if ($area > $maxarea
				|| $width > $cnvrt_arry['maxwid']
				|| $height > $maxheight)
			{
				if (($width / $cnvrt_arry['maxwid']) >= ($height / $maxheight)) {
					$dim = 'W'; }
				if (($height / $maxheight) >= ($width / $cnvrt_arry['maxwid'])
					|| $cnvrt_alt['by_height'] == TRUE)
				{
					$dim = 'H'; }
				if ($dim == 'W') {
					$cnvt_percent =
						round((($factor * $cnvrt_arry['maxwid']) / $width) * 100, 2);
				}
				if ($dim == 'H') {
					$cnvt_percent =
						round((($cnvrt_alt['aspect'] * $cnvrt_arry['maxwid']) / $height) * 100, 2);
				}
			    // convert it
			    // Image Magick image conversion
				exec($convert_cmd
				.' -geometry '.$cnvt_percent.'%'
				.' -quality '.$cnvrt_arry['qual']
				.' -sharpen '.$cnvrt_arry['sharpen'].$strip_prof
				.' "'.$orig_img.'"'.' "'.$cnvrtd_img.'"');
	 			fixPerms($cnvrtd_img);

				if ($cnvrt_alt['mesg_on'] == TRUE
					&& is_file($cnvrtd_img))
				{
					$str .= "  <small>\n"
						.' Generated a new '.$cnvrt_arry['txt'].' converted'
						.'image for '.$img_file.$using.".\n"
						."  </small>\n  <br />\n";
				}
			}
		}
	}
	if (isset($str)) { return $str; }
} //End resizeImage()


/*
* Get the names of image files in a directory.
*/
function getImageFilenames($path)
{
	global $imgname_maxlen, $exclude_gif, $excl_imgs,
	       $excl_img_pattern, $excl_img_preg, $extra_paranoia, $ignore_dotfiles;

	$img_exts = '\.jpg$|\.jpeg$|\.jpe$|\.png$|\.bmp$';
	if ($exclude_gif == FALSE) { $img_exts .= '|\.gif$'; }
	$pwd_handle = opendir($path);
	$i = 0;
	while (($file = readdir($pwd_handle)) != false) {
		if ($file == '.' || $file == '..' || strpos('X'.$file, '*')
			|| strpos('X'.$file, '?') || strpos('X'.$file, '/')) { continue; }
		if ($extra_paranoia == TRUE && is_file($path.'/'.$file)) {
			if (strpos(stripslashes(rawurldecode($file)), '..')
				|| ($file[0] == '.' && $file[1] == '.'))
			{
				securityExit('Updir ("..") is not allowed in a filename.');
			}
			if (strlen($file) > $imgname_maxlen) {
				securityExit('Filename length exceed.  Increase $imgname_maxlen?');
			}
		}
		if ($ignore_dotfiles == TRUE && $file[0] == '.') { continue; }
		if (in_array($file, $excl_imgs)) { continue; }
		if (preg_match($excl_img_preg, $file)) { continue; }
		if (strpos('*'.$file, $excl_img_pattern)) { continue; }
		if (is_file($path.'/'.$file))
		{
			$img_files[$i] = $file;
			$i++;
		}
	}
	closedir($pwd_handle);
	if (isset($img_files)) {
           return $img_files;
	}
} // End getImageFilenames()


/*
* Fix permissions of a file or directory.
* 
* Adjusts permissions so both the script and account owner can modify / delete.
* Adapted from PmWiki's fixperms() function copyright by Patrick R. Michaud.
*/
function fixPerms($file) {
	clearstatcache();
	if (!file_exists($file)) { return; }
	$bp = 0;
	if (fileowner($file)!=@fileowner('.')) { $bp = (is_dir($file)) ? 007 : 006; }
	if (filegroup($file)==@filegroup('.')) { $bp <<= 3; }
	if ($bp && (fileperms($file) & $bp) != $bp) {
		chmod($file, fileperms($file)|$bp);
	}
} // end of fixPerms()
