<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );
jimport('joomla.filesystem.file');

global $mctrl;

include_once($mctrl->templatePath .'/lib/rtimage.class.php');

$params = $mctrl->params;

$error = null;
$output = null;
$uploaded_image = false;
$this_url = "?process=ajax&amp;model=logoupload";
$dest = 'images/missioncontrol-logo.png';

// handle a file upload???
$file = JRequest::getVar('file_upload', null, 'files', 'array');

if ($file) {
    //Import filesystem libraries. Perhaps not necessary, but does not hurt
    

    //Clean up filename to get rid of strange characters like spaces etc
    $filename = JFile::makeSafe($file['name']);

    //Set up the source and destination of the file
    $src = $file['tmp_name'];
    $size = getimagesize($src);

    //First check if the file has the right extension, we need jpg only
    if ( $size['mime'] == 'image/png' || 
    	 $size['mime'] == 'image/jpeg' ||
    	 $size['mime'] == 'image/gif' ) {
    	 
    	if ($size[1] < 53) 
    		$height = $size[1];
    	else
    		$height = 53;

       if ($size[1] > 53 || $size['mime'] != 'image/png') {
       		$rtimage = new RTImage();
       		$rtimage->smartImageResize($src,0,$height,IMAGETYPE_PNG);
           
       }
       
       if ( JFile::upload($src, $dest) ) {
          $success = "successfully updated logo";
       } else {
          $error = "Error: could not copy file to destination";
       }

    } else {
       $error = "Error: only PNG or JPEG files allowed";
    }
    if ($error)
        $output = '<div class="mc-uploaderror">'.$error.'</div>';
    else
        $output = '<div class="mc-uploadsuccess">'.$success.'</div>';
} else {
	//check for deletion
	if (JRequest::getString('action')=='delete') {
		
		if (file_exists($dest)) {
			JFile::delete($dest);
		}
	}
	
}

if (file_exists($dest)) {
    $logo_url = $dest;
	$uploaded_image = true;
} else {
    $logo_url = $mctrl->templateUrl."/images/logo.png";
}
$logo_url .= '?'.intval(microtime(true));

?>
<html>
<head>
<style>
    body {font-family:Helvetica, arial,sans-serif;font-size:12px;}
	.mc-delete {position:absolute;top:0px;right:0px;display:block;background:url(templates/rt_missioncontrol_j15/images/bullet_delete.png);width:16px;height:16px;}
    .mc-logobg {margin-bottom:10px;position:relative;padding: 10px 20px;display:inline-block;background:<?php echo $params->get('header_bg_color'); ?>;}
    .mc-logothumb {vertical-align:middle;}
    .mc-output {display:inline-block;padding:10px;}
    .mc-uploaderror {color:#c00;font-weight:bold;}
    .mc-uploadsuccess {color:#000;font-weight:bold;}

</style>
</head>
<body>
<div class="mc-logobg">
	<img src="<?php echo $logo_url; ?>" alt="logo" class="mc-logothumb" />
	<?php if ($uploaded_image) : ?>
	<a href="<?php echo $this_url; ?>&action=delete" class="mc-delete"><span></span></a>	
	<?php endif; ?>
</div>
<div class="mc-output"><?php echo $output; ?></div>
<form name="logoform" method="post" action="" enctype="multipart/form-data" onSubmit="if(file_upload.value=='') {alert('Choose a file!');return false;}">
<input type="file" name="file_upload" size="10" />
<input name="submit" type="submit" value="Upload" />
<input name="image_path" type="hidden" value="images/" />
</form>
</body>
</html>
