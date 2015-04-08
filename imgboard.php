<?php
# TinyIB converted to a single file by ~malamut2014
#
# https://github.com/tslocum/TinyIB
// Start config

// Administrator/moderator credentials
define('MALAIB_ADMINPASS', "password");       // Administrators have full access to the board
define('MALAIB_MODPASS', "");         // Moderators only have access to delete (and moderate if MALAIB_REQMOD is set) posts  ["" to disable]

// Board description and behavior
define('MALAIB_BOARD', "b");          // Unique identifier for this board using only letters and numbers
define('MALAIB_BOARDDESC', "/b/ - Random"); // Displayed at the top of every page
define('MALAIB_CAPTCHA', false);      // Will crash the script. Don't touch.
define('MALAIB_REQMOD', "disable");   // Require moderation before displaying posts: disable / files / all  (see README for instructions, only MySQL is supported)

// Board appearance
define('MALAIB_LOGO', "");            // Logo HTML
define('MALAIB_THREADSPERPAGE', 10);  // Amount of threads shown per index page
define('MALAIB_PREVIEWREPLIES', 3);   // Amount of replies previewed on index pages
define('MALAIB_TRUNCATE', 15);        // Messages are truncated to this many lines on board index pages  [0 to disable]

// Post control
define('MALAIB_DELAY', 30);           // Delay (in seconds) between posts from the same IP address to help control flooding  [0 to disable]
define('MALAIB_MAXTHREADS', 100);     // Oldest threads are discarded when the thread count passes this limit  [0 to disable]
define('MALAIB_MAXREPLIES', 0);       // Maximum replies before a thread stops bumping  [0 to disable]

// File types
define('MALAIB_PIC', true);           // Enable .jpg, .png and .gif image file upload
define('MALAIB_SWF', true);          // Enable .swf Flash file upload
define('MALAIB_WEBM', true);         // Enable .weba and .webm audio/video file upload  (see README for instructions)

// File control
define('MALAIB_MAXKB', 10240);         // Maximum file size in kilobytes  [0 to disable]
define('MALAIB_MAXKBDESC', "10 MB");   // Human-readable representation of the maximum file size
define('MALAIB_NOFILEOK', false);     // Allow the creation of new threads without uploading a file

// Thumbnail size - new thread
define('MALAIB_MAXWOP', 250);         // Width
define('MALAIB_MAXHOP', 250);         // Height

// Thumbnail size - reply
define('MALAIB_MAXW', 250);           // Width
define('MALAIB_MAXH', 250);           // Height

// Tripcode seed - Must not change once set!
define('MALAIB_TRIPSEED', "26358g5gr7f95g287rf297rvf2g9b9643992rg3rt6gf43922sosihyi");        // Enter some random text  (used when generating secure tripcodes)

define('MALAIB_DBMODE', "flatfile");  // Mode, this is the only one which will work
define('MALAIB_DBBANS', "bans");      // Bans table name (use the same bans table across boards for global bans)
define('MALAIB_DBPOSTS', MALAIB_BOARD . "_posts"); // Posts table name

/********************************************************************/
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
ob_implicit_flush();
if (function_exists('ob_get_level')) {
	while (ob_get_level() > 0) {
		ob_end_flush();
	}
}

if (get_magic_quotes_gpc()) {
	foreach ($_GET as $key => $val) {
		$_GET[$key] = stripslashes($val);
	}
	foreach ($_POST as $key => $val) {
		$_POST[$key] = stripslashes($val);
	}
}
if (get_magic_quotes_runtime()) {
	set_magic_quotes_runtime(0);
}

function fancyDie($message) {
	die('<body text="#800000" bgcolor="#FFFFEE" align="center"><br><div style="display: inline-block; background-color: #F0E0D6;font-size: 1.25em;font-family: Tahoma, Geneva, sans-serif;padding: 7px;border: 1px solid #D9BFB7;border-left: none;border-top: none;">' . $message . '</div><br><br>- <a href="javascript:history.go(-1)">Click here to go back</a> -</body>');
}

// Check directories are writable by the script
$writedirs = array("res", "src", "thumb");
if (MALAIB_DBMODE == 'flatfile') {
	$writedirs[] = "flatfile/";
}
foreach ($writedirs as $dir) {
	if (!is_writable($dir)) {
		fancyDie("Directory '" . $dir . "' can not be written to.  Please modify its permissions.");
	}
}

/*$includes = array("inc/defines.php", "inc/functions.php", "inc/html.php");
*if (in_array(MALAIB_DBMODE, array('flatfile', 'mysql', 'mysqli', 'sqlite', *'pdo'))) {
*	$includes[] = 'inc/database_' . MALAIB_DBMODE . '.php';
*} else {
*	fancyDie("Unknown database mode specificed");
*}
*/

// Defines

if (!defined('MALAIB_BOARD')) {
	die('');
}

define('MALAIB_NEWTHREAD', '0');
define('MALAIB_INDEXPAGE', false);
define('MALAIB_RESPAGE', true);

// The following are provided for backward compatibility and should not be relied upon
// Copy new settings from settings.default.php to settings.php
if (!defined('MALAIB_MAXREPLIES')) {
	define('MALAIB_MAXREPLIES', 0);
}
if (!defined('MALAIB_MAXWOP')) {
	define('MALAIB_MAXWOP', MALAIB_MAXW);
}
if (!defined('MALAIB_MAXHOP')) {
	define('MALAIB_MAXHOP', MALAIB_MAXH);
}
if (!defined('MALAIB_PIC')) {
	define('MALAIB_PIC', true);
}
if (!defined('MALAIB_SWF')) {
	define('MALAIB_SWF', false);
}
if (!defined('MALAIB_WEBM')) {
	define('MALAIB_WEBM', false);
}
if (!defined('MALAIB_NOFILEOK')) {
	define('MALAIB_NOFILEOK', false);
}
if (!defined('MALAIB_CAPTCHA')) {
	define('MALAIB_CAPTCHA', false);
}
if (!defined('MALAIB_REQMOD')) {
	define('MALAIB_REQMOD', 'disable');
}
if (!defined('MALAIB_DBMIGRATE')) {
	define('MALAIB_DBMIGRATE', false);
}
if (!defined('MALAIB_DBPORT')) {
	define('MALAIB_DBPORT', 3306);
}
if (!defined('MALAIB_DBDRIVER')) {
	define('MALAIB_DBDRIVER', 'pdo');
}
if (!defined('MALAIB_DBDSN')) {
	define('MALAIB_DBDSN', '');
}

// Functions
if (!defined('MALAIB_BOARD')) {
	die('');
}

$posts_sql = "CREATE TABLE `" . MALAIB_DBPOSTS . "` (
	`id` mediumint(7) unsigned NOT NULL auto_increment,
	`parent` mediumint(7) unsigned NOT NULL,
	`timestamp` int(20) NOT NULL,
	`bumped` int(20) NOT NULL,
	`ip` varchar(15) NOT NULL,
	`name` varchar(75) NOT NULL,
	`tripcode` varchar(10) NOT NULL,
	`email` varchar(75) NOT NULL,
	`nameblock` varchar(255) NOT NULL,
	`subject` varchar(75) NOT NULL,
	`message` text NOT NULL,
	`password` varchar(255) NOT NULL,
	`file` varchar(75) NOT NULL,
	`file_hex` varchar(75) NOT NULL,
	`file_original` varchar(255) NOT NULL,
	`file_size` int(20) unsigned NOT NULL default '0',
	`file_size_formatted` varchar(75) NOT NULL,
	`image_width` smallint(5) unsigned NOT NULL default '0',
	`image_height` smallint(5) unsigned NOT NULL default '0',
	`thumb` varchar(255) NOT NULL,
	`thumb_width` smallint(5) unsigned NOT NULL default '0',
	`thumb_height` smallint(5) unsigned NOT NULL default '0',
	`stickied` tinyint(1) NOT NULL default '0',
	`moderated` tinyint(1) NOT NULL default '1',
	PRIMARY KEY	(`id`),
	KEY `parent` (`parent`),
	KEY `bumped` (`bumped`),
	KEY `stickied` (`stickied`),
	KEY `moderated` (`moderated`)
)";

$bans_sql = "CREATE TABLE `" . MALAIB_DBBANS . "` (
	`id` mediumint(7) unsigned NOT NULL auto_increment,
	`ip` varchar(15) NOT NULL,
	`timestamp` int(20) NOT NULL,
	`expire` int(20) NOT NULL,
	`reason` text NOT NULL,
	PRIMARY KEY	(`id`),
	KEY `ip` (`ip`)
)";

function cleanString($string) {
	$search = array("<", ">");
	$replace = array("&lt;", "&gt;");

	return str_replace($search, $replace, $string);
}

function plural($singular, $count, $plural = 's') {
	if ($plural == 's') {
		$plural = $singular . $plural;
	}
	return ($count == 1 ? $singular : $plural);
}

function threadUpdated($id) {
	rebuildThread($id);
	rebuildIndexes();
}

function newPost($parent = MALAIB_NEWTHREAD) {
	return array('parent' => $parent,
		'timestamp' => '0',
		'bumped' => '0',
		'ip' => '',
		'name' => '',
		'tripcode' => '',
		'email' => '',
		'nameblock' => '',
		'subject' => '',
		'message' => '',
		'password' => '',
		'file' => '',
		'file_hex' => '',
		'file_original' => '',
		'file_size' => '0',
		'file_size_formatted' => '',
		'image_width' => '0',
		'image_height' => '0',
		'thumb' => '',
		'thumb_width' => '0',
		'thumb_height' => '0',
		'stickied' => '0',
		'moderated' => '1');
}

function convertBytes($number) {
	$len = strlen($number);
	if ($len < 4) {
		return sprintf("%dB", $number);
	} elseif ($len <= 6) {
		return sprintf("%0.2fKB", $number / 1024);
	} elseif ($len <= 9) {
		return sprintf("%0.2fMB", $number / 1024 / 1024);
	}

	return sprintf("%0.2fGB", $number / 1024 / 1024 / 1024);
}

function nameAndTripcode($name) {
	if (preg_match("/(#|!)(.*)/", $name, $regs)) {
		$cap = $regs[2];
		$cap_full = '#' . $regs[2];

		if (function_exists('mb_convert_encoding')) {
			$recoded_cap = mb_convert_encoding($cap, 'SJIS', 'UTF-8');
			if ($recoded_cap != '') {
				$cap = $recoded_cap;
			}
		}

		if (strpos($name, '#') === false) {
			$cap_delimiter = '!';
		} elseif (strpos($name, '!') === false) {
			$cap_delimiter = '#';
		} else {
			$cap_delimiter = (strpos($name, '#') < strpos($name, '!')) ? '#' : '!';
		}

		if (preg_match("/(.*)(" . $cap_delimiter . ")(.*)/", $cap, $regs_secure)) {
			$cap = $regs_secure[1];
			$cap_secure = $regs_secure[3];
			$is_secure_trip = true;
		} else {
			$is_secure_trip = false;
		}

		$tripcode = "";
		if ($cap != "") { // Copied from Futabally
			$cap = strtr($cap, "&amp;", "&");
			$cap = strtr($cap, "&#44;", ", ");
			$salt = substr($cap . "H.", 1, 2);
			$salt = preg_replace("/[^\.-z]/", ".", $salt);
			$salt = strtr($salt, ":;<=>?@[\\]^_`", "ABCDEFGabcdef");
			$tripcode = substr(crypt($cap, $salt), -10);
		}

		if ($is_secure_trip) {
			if ($cap != "") {
				$tripcode .= "!";
			}

			$tripcode .= "!" . substr(md5($cap_secure . MALAIB_TRIPSEED), 2, 10);
		}

		return array(preg_replace("/(" . $cap_delimiter . ")(.*)/", "", $name), $tripcode);
	}

	return array($name, "");
}

function nameBlock($name, $tripcode, $email, $timestamp, $rawposttext) {
	$output = '<span class="postername">';
	$output .= ($name == '' && $tripcode == '') ? 'Anonymous' : $name;

	if ($tripcode != '') {
		$output .= '</span><span class="postertrip">!' . $tripcode;
	}

	$output .= '</span>';

	if ($email != '' && strtolower($email) != 'noko') {
		$output = '<a href="mailto:' . $email . '">' . $output . '</a>';
	}

	return $output . $rawposttext . ' ' . date('y/m/d(D)H:i:s', $timestamp);
}

function writePage($filename, $contents) {
	$tempfile = tempnam('res/', MALAIB_BOARD . 'tmp'); /* Create the temporary file */
	$fp = fopen($tempfile, 'w');
	fwrite($fp, $contents);
	fclose($fp);
	/* If we aren't able to use the rename function, try the alternate method */
	if (!@rename($tempfile, $filename)) {
		copy($tempfile, $filename);
		unlink($tempfile);
	}

	chmod($filename, 0664); /* it was created 0600 */
}

function fixLinksInRes($html) {
	$search = array(' href="css/', ' src="js/', ' href="src/', ' href="thumb/', ' href="res/', ' href="imgboard.php', ' href="favicon.ico', 'src="thumb/', 'src="inc/', ' action="imgboard.php');
	$replace = array(' href="../css/', ' src="../js/', ' href="../src/', ' href="../thumb/', ' href="../res/', ' href="../imgboard.php', ' href="../favicon.ico', 'src="../thumb/', 'src="../inc/', ' action="../imgboard.php');

	return str_replace($search, $replace, $html);
}

function _postLink($matches) {
	$post = postByID($matches[1]);
	if ($post) {
		return '<a href="res/' . ($post['parent'] == MALAIB_NEWTHREAD ? $post['id'] : $post['parent']) . '.html#' . $matches[1] . '">' . $matches[0] . '</a>';
	}
	return $matches[0];
}

function postLink($message) {
	return preg_replace_callback('/&gt;&gt;([0-9]+)/', '_postLink', $message);
}

function colorQuote($message) {
	if (substr($message, -1, 1) != "\n") {
		$message .= "\n";
	}
	return preg_replace('/^(&gt;[^\>](.*))\n/m', '<span class="unkfunc">\\1</span>' . "\n", $message);
}

function deletePostImages($post) {
	if ($post['file'] != '') {
		@unlink('src/' . $post['file']);
	}
	if ($post['thumb'] != '') {
		@unlink('thumb/' . $post['thumb']);
	}
}

function checkCAPTCHA() {
	if (!MALAIB_CAPTCHA) {
		return; // CAPTCHA is disabled
	}

	$captcha = isset($_POST['captcha']) ? strtolower(trim($_POST['captcha'])) : '';
	$captcha_solution = isset($_SESSION['tinyibcaptcha']) ? strtolower(trim($_SESSION['tinyibcaptcha'])) : '';

	if ($captcha == '') {
		fancyDie('Please enter the CAPTCHA text.');
	} else if ($captcha != $captcha_solution) {
		fancyDie('Incorrect CAPTCHA text entered.  Please try again.<br>Click the image to retrieve a new CAPTCHA.');
	}
}

function checkBanned() {
	$ban = banByIP($_SERVER['REMOTE_ADDR']);
	if ($ban) {
		if ($ban['expire'] == 0 || $ban['expire'] > time()) {
			$expire = ($ban['expire'] > 0) ? ('<br>This ban will expire ' . date('y/m/d(D)H:i:s', $ban['expire'])) : '<br>This ban is permanent and will not expire.';
			$reason = ($ban['reason'] == '') ? '' : ('<br>Reason: ' . $ban['reason']);
			fancyDie('Your IP address ' . $ban['ip'] . ' has been banned from posting on this image board.  ' . $expire . $reason);
		} else {
			clearExpiredBans();
		}
	}
}

function checkFlood() {
	if (MALAIB_DELAY > 0) {
		$lastpost = lastPostByIP();
		if ($lastpost) {
			if ((time() - $lastpost['timestamp']) < MALAIB_DELAY) {
				fancyDie("Please wait a moment before posting again.  You will be able to make another post in " . (MALAIB_DELAY - (time() - $lastpost['timestamp'])) . " " . plural("second", (MALAIB_DELAY - (time() - $lastpost['timestamp']))) . ".");
			}
		}
	}
}

function checkMessageSize() {
	if (strlen($_POST["message"]) > 8000) {
		fancyDie("Please shorten your message, or post it in multiple parts. Your message is " . strlen($_POST["message"]) . " characters long, and the maximum allowed is 8000.");
	}
}

function manageCheckLogIn() {
	$loggedin = false;
	$isadmin = false;
	if (isset($_POST['password'])) {
		if ($_POST['password'] === MALAIB_ADMINPASS) {
			$_SESSION['tinyib'] = MALAIB_ADMINPASS;
		} elseif (MALAIB_MODPASS != '' && $_POST['password'] === MALAIB_MODPASS) {
			$_SESSION['tinyib'] = MALAIB_MODPASS;
		}
	}

	if (isset($_SESSION['tinyib'])) {
		if ($_SESSION['tinyib'] === MALAIB_ADMINPASS) {
			$loggedin = true;
			$isadmin = true;
		} elseif (MALAIB_MODPASS != '' && $_SESSION['tinyib'] === MALAIB_MODPASS) {
			$loggedin = true;
		}
	}

	return array($loggedin, $isadmin);
}

function setParent() {
	if (isset($_POST["parent"])) {
		if ($_POST["parent"] != MALAIB_NEWTHREAD) {
			if (!threadExistsByID($_POST['parent'])) {
				fancyDie("Invalid parent thread ID supplied, unable to create post.");
			}

			return $_POST["parent"];
		}
	}

	return MALAIB_NEWTHREAD;
}

function isRawPost() {
	if (isset($_POST['rawpost'])) {
		list($loggedin, $isadmin) = manageCheckLogIn();
		if ($loggedin) {
			return true;
		}
	}

	return false;
}

function validateFileUpload() {
	switch ($_FILES['file']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_FORM_SIZE:
			fancyDie("That file is larger than " . MALAIB_MAXKBDESC . ".");
			break;
		case UPLOAD_ERR_INI_SIZE:
			fancyDie("The uploaded file exceeds the upload_max_filesize directive (" . ini_get('upload_max_filesize') . ") in php.ini.");
			break;
		case UPLOAD_ERR_PARTIAL:
			fancyDie("The uploaded file was only partially uploaded.");
			break;
		case UPLOAD_ERR_NO_FILE:
			fancyDie("No file was uploaded.");
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			fancyDie("Missing a temporary folder.");
			break;
		case UPLOAD_ERR_CANT_WRITE:
			fancyDie("Failed to write file to disk");
			break;
		default:
			fancyDie("Unable to save the uploaded file.");
	}
}

function checkDuplicateFile($hex) {
	$hexmatches = postsByHex($hex);
	if (count($hexmatches) > 0) {
		foreach ($hexmatches as $hexmatch) {
			fancyDie("Duplicate file uploaded. That file has already been posted <a href=\"res/" . (($hexmatch["parent"] == MALAIB_NEWTHREAD) ? $hexmatch["id"] : $hexmatch["parent"]) . ".html#" . $hexmatch["id"] . "\">here</a>.");
		}
	}
}

function thumbnailDimensions($post) {
	if ($post['parent'] == MALAIB_NEWTHREAD) {
		$max_width = MALAIB_MAXWOP;
		$max_height = MALAIB_MAXHOP;
	} else {
		$max_width = MALAIB_MAXW;
		$max_height = MALAIB_MAXH;
	}
	return ($post['image_width'] > $max_width || $post['image_height'] > $max_height) ? array($max_width, $max_height) : array($post['image_width'], $post['image_height']);
}

function createThumbnail($name, $filename, $new_w, $new_h) {
	$system = explode(".", $filename);
	$system = array_reverse($system);
	if (preg_match("/jpg|jpeg/", $system[0])) {
		$src_img = imagecreatefromjpeg($name);
	} else if (preg_match("/png/", $system[0])) {
		$src_img = imagecreatefrompng($name);
	} else if (preg_match("/gif/", $system[0])) {
		$src_img = imagecreatefromgif($name);
	} else {
		return false;
	}

	if (!$src_img) {
		fancyDie("Unable to read uploaded file during thumbnailing. A common cause for this is an incorrect extension when the file is actually of a different type.");
	}
	$old_x = imageSX($src_img);
	$old_y = imageSY($src_img);
	$percent = ($old_x > $old_y) ? ($new_w / $old_x) : ($new_h / $old_y);
	$thumb_w = round($old_x * $percent);
	$thumb_h = round($old_y * $percent);

	$dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
	if (preg_match("/png/", $system[0]) && imagepng($src_img, $filename)) {
		imagealphablending($dst_img, false);
		imagesavealpha($dst_img, true);

		$color = imagecolorallocatealpha($dst_img, 0, 0, 0, 0);
		imagefilledrectangle($dst_img, 0, 0, $thumb_w, $thumb_h, $color);
		imagecolortransparent($dst_img, $color);

		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
	} else {
		fastimagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
	}

	if (preg_match("/png/", $system[0])) {
		if (!imagepng($dst_img, $filename)) {
			return false;
		}
	} else if (preg_match("/jpg|jpeg/", $system[0])) {
		if (!imagejpeg($dst_img, $filename, 70)) {
			return false;
		}
	} else if (preg_match("/gif/", $system[0])) {
		if (!imagegif($dst_img, $filename)) {
			return false;
		}
	}

	imagedestroy($dst_img);
	imagedestroy($src_img);

	return true;
}

function fastimagecopyresampled(&$dst_image, &$src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3) {
	// Author: Tim Eckel - Date: 12/17/04 - Project: FreeRingers.net - Freely distributable.
	if (empty($src_image) || empty($dst_image)) {
		return false;
	}

	if ($quality <= 1) {
		$temp = imagecreatetruecolor($dst_w + 1, $dst_h + 1);

		imagecopyresized($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
		imagecopyresized($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
		imagedestroy($temp);
	} elseif ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
		$tmp_w = $dst_w * $quality;
		$tmp_h = $dst_h * $quality;
		$temp = imagecreatetruecolor($tmp_w + 1, $tmp_h + 1);

		imagecopyresized($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
		imagecopyresampled($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
		imagedestroy($temp);
	} else {
		imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
	}

	return true;
}

function addVideoOverlay($thumb_location) {
	if (file_exists('video_overlay.png')) {
		if (substr($thumb_location, -4) == ".jpg") {
			$thumbnail = imagecreatefromjpeg($thumb_location);
		} else {
			$thumbnail = imagecreatefrompng($thumb_location);
		}
		list($width, $height, $type, $attr) = getimagesize($thumb_location);

		$overlay_play = imagecreatefrompng('video_overlay.png');
		imagealphablending($overlay_play, false);
		imagesavealpha($overlay_play, true);
		list($overlay_width, $overlay_height, $overlay_type, $overlay_attr) = getimagesize('video_overlay.png');

		if (substr($thumb_location, -4) == ".png") {
			imagecolortransparent($thumbnail, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
			imagealphablending($thumbnail, true);
			imagesavealpha($thumbnail, true);
		}

		imagecopy($thumbnail, $overlay_play, ($width / 2) - ($overlay_width / 2), ($height / 2) - ($overlay_height / 2), 0, 0, $overlay_width, $overlay_height);

		if (substr($thumb_location, -4) == ".jpg") {
			imagejpeg($thumbnail, $thumb_location);
		} else {
			imagepng($thumbnail, $thumb_location);
		}
	}
}

function strallpos($haystack, $needle, $offset = 0) {
	$result = array();
	for ($i = $offset; $i < strlen($haystack); $i++) {
		$pos = strpos($haystack, $needle, $i);
		if ($pos !== False) {
			$offset = $pos;
			if ($offset >= $i) {
				$i = $offset;
				$result[] = $offset;
			}
		}
	}
	return $result;
}

// html.php
if (!defined('MALAIB_BOARD')) {
	die('');
}

function pageHeader() {
	$return = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">
		<meta http-equiv="cache-control" content="max-age=0">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="expires" content="0">
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
		<meta http-equiv="pragma" content="no-cache">
		<title>
EOF;
	$return .= MALAIB_BOARDDESC . <<<EOF
		</title>
		<link rel="shortcut icon" href="favicon.ico">
		<style>
body {
	margin: 0;
	padding: 8px;
	margin-bottom: auto;
}

blockquote blockquote {
	margin-left: 0em;
}

form {
	margin-bottom: 0px;
}

.postarea {
	text-align: center;
}

.postarea table {
	margin: 0px auto;
	text-align: left;
}

.aa {
	white-space: pre;
	text-align: left;
	font-family: IPAMonaPGothic, Mona, /'MS PGothic/', YOzFontAA97 !important;
}

.thumb {
	border: none;
	float: left;
	margin: 2px 20px;
}

.nothumb {
	float: left;
	background: #eee;
	border: 2px dashed #aaa;
	text-align: center;
	margin: 2px 20px;
	padding: 1em 0.5em 1em 0.5em;
}

.message {
	margin-top: 1em;
	margin-bottom: 1em;
	margin-left: 25px;
	margin-right: 25px;
}

.reply .message {
	margin-bottom: 5px;
}

.reflink a {
	color: inherit;
	text-decoration: none;
}

.reflink a:hover {
	color: #800000;
}

.reply .filesize {
	margin-left: 20px;
}

.userdelete {
	float: right;
	text-align: center;
	white-space: nowrap;
}

.doubledash {
	vertical-align: top;
	clear: both;
	float: left;
	font-size: 1.75em;
}

.moderator {
	color: #FF0000;
}

.managebutton {
	font-size: 15px;
	height: 28px;
	margin: 0.2em;
}

.footer {
	clear: both;
	text-align: center;
}

.rules {
	padding-left: 5px;
}

.rules ul {
	margin: 0;
	padding-left: 0px;
}

.floatpost {
	float: right;
	clear: both;
}

.login {
	text-align: center;
}

.adminbar {
	text-align: right;
	clear: both;
	float: right;
}

.adminbar a:link, .adminbar a:visited, .adminbar a:active, .adminbar a:hover {
	text-decoration: none;
}
</style>
		<style>

	html, body{
		background:#121212;
		color:#999999;
		font-family:sans-serif;
		font-size:10pt;
	}
	.replymode {
		  background: #222;
		text-align: center;
		padding: 2px;
		color: #CCCCC;
		clear: both;
		font-weight: bold;
		margin-bottom: .5em;
		border: solid 1px #CCCCCC;
		-moz-border-radius: 5px;
		-webkit-border-bottom-left-radius: 5px;
		-webkit-border-bottom-right-radius: 5px;
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
	}
	.logo {
        clear:both;
        text-align:center;
        font-size:2em;
        color:#DD3232;
        width:100%;
	}
	blockquote blockquote {
		margin-left: 0em;
		color: #789922;
	}
	.theader {
		background: #000000;
		text-align: center;
		padding: 2px;
		color: #FFFFFF;
		width: 100%;
	}
	.reply {
		background: #222222;
		margin: 0.2em 16px;
		padding: 0.2em 0.3em 0.5em 0.6em;
		border: 1px solid #444444;
		border-radius: 3px;
		text-align: inherit;
	}
	tbody {
		display: table-row-group;
		vertical-align: middle;
		border-color: inherit;
	}
	table {
		display: table;
		border-collapse: separate;
		border-spacing: 2px;
		border-color: gray;
	}
	td, th {
		display: table-cell;
		vertical-align: inherit;
	}
	.doubledash {
		vertical-align:top;
		clear:both;
		float:left;
	}
	.highlight {
		background: #444444;
		margin: 0.2em 16px;
		padding: 0.2em 0.3em 0.5em 0.6em;
		border: 1px solid #444444;
		border-radius: 3px;
		display: inline-block;
		text-align: left;
	}
	.postername, .commentpostername{
		color: #DD3232;
		font-weight: bold;
	}
	table *{
		margin:0;
	}
	a,a:visited{
		color:#CCCCCC;
	}
	a:hover,p.intro a.post_no:hover{
		color:#DD3232;
	}
	a.post_no{
		text-decoration:none;
		margin:0;
		padding:0;
	}
	p.intro a.post_no{
		color:inherit;
	}
	p.intro a.post_no,p.intro a.email{
		margin:0;
	}
	p.intro a.email span.name{
		color:#CCCCCC;
	}
	p.intro a.email:hover span.name{
		color:#ff0000;
	}
	p.intro label{
		display:inline;
	}
	p.intro time,p.intro a.ip-link,p.intro a.capcode{
		direction:ltr;
		unicode-bidi:embed;
	}
	input[type="text"],textarea,select{
		color:#CCCCCC;
		background:#222222;
		border:1px solid #444;
	}
	h2{
		color:#DD3232;
		font-size:11pt;
		margin:0;
		padding:0;
	}
	header{
		margin:1em 0;
	}
	h1{
		font-family:tahoma;
		letter-spacing:-2px;font-size:20pt;margin:0;}header div.subtitle,hauto;}form table input{height:auto;}input,textarea{color:#CCCCCC;background:none repeat scroll 0% 0% #222;border:1px solid #444444;text-indent:0;text-shadow:none;text-transform:none;word-spacing:normal;padding:4px;}form table tr td{text-align:left;margin:0;padding:0;}form table.mod tr td{padding:2px;}form table tr th{text-align:left;padding:4px;}form table tr th{background:#222222;border:1px solid #444444;}form table tr td div.center{text-align:center;float:left;padding-left:3px;}form table tr td div input{display:block;margin:2px auto 0 auto;}form table tr td div label{font-size:10px;}.unimportant,.unimportant *{font-size:10px;}p.fileinfo{display:block;margin:0;padding-right:7em;}div.banner{background-color:#DD3232;font-size:12pt;font-weight:bold;text-align:center;margin:1em 0;}div.banner,div.banner a{color:#121212;}div.banner a:hover{color:#222222;text-decoration:none;}img.banner,img.board_image{display:block;border:1px solid #444444;margin:12px auto;}img.post-image{display:block;float:left;margin:10px 20px;border:none;}div.mascot{background:url('/web/20140610072543/https://www.nullchan.org/img/mascot.png') repeat scroll 0% 0% transparent;width:350px;height:350px;margin-left:auto;margin-right:auto;}div.post img.post-image{padding:5px;margin:5px 20px 0 0;}div.post img.icon{display:inline;margin:0 5px;padding:0;}div.post i.fa{margin:0 4px;font-size:16px;}div.post.op{margin-right:20px;margin-bottom:5px;}div.post.op hr{border-color:#D9BFB7;}p.intro{margin:0.5em 0;padding:0;padding-bottom:0.2em;}p.intro{margin:0.5em 0;padding:0;padding-bottom:0.2em;}input.delete{float:left;margin:1px 6px 0 0;}p.intro span.subject{color:#222288;font-weight:bold;}p.intro span.name{color:#DD3232;font-weight:bold;}p.intro span.capcode,p.intro a.capcode,p.intro a.nametag{color:#F00000;margin-left:0;}p.intro a{margin-left:8px;}div.delete{float:right;}div.post.reply p{margin:0.3em 0 0 0;}div.post.reply div.body{margin-left:1.8em;margin-top:0.8em;padding-right:3em;padding-bottom:0.3em;}div.post.reply.highlighted{background:#444444;}div.post.reply div.body a{color:#CCCCCC;}div.post.reply div.body a:hover{color:#DD3232;}div.post{max-width:97%;}div.post div.body{word-wrap:break-word;white-space:pre-wrap;}div.post.reply{background:#222222;margin:0.2em 16px;padding:0.2em 0.3em 0.5em 0.6em;border:1px solid #444444;border-radius:3px;display:inline-block;}span.trip{color:#228854;}span.quote{color:#789922;}span.omitted{display:block;margin-top:1em;}br.clear{clear:left;display:block;}span.controls{float:right;margin:0;padding:0;font-size:80%;}span.controls.op{float:none;margin-left:10px;}span.controls a{margin:0;}div#wrap{width:900px;margin:0 auto;}div.ban{max-width:700px;margin:30px auto;}div.ban p,div.ban h2{padding:3px 7px;}div.ban h2{background:#222222;font-size:12pt;border:1px solid #444444;}div.ban p{font-size:12px;margin-bottom:12px;}div.ban p.reason{font-weight:bold;}span.heading{color:#DD3232;font-size:11pt;font-weight:bold;}span.spoiler{background:black;color:black;padding:0px 1px;}div.post.reply div.body span.spoiler a{color:black;}span.spoiler:hover,div.post.reply div.body span.spoiler:hover a{color:white;}div.styles{float:right;padding-bottom:20px;}div.styles a{margin:0 10px;}div.styles a.selected{text-decoration:none;}table.test{width:100%;}table.test td,table.test th{text-align:left;padding:5px;}table.test tr.h th{background:#222222;}table.test td img{margin:0;}fieldset label{display:block;}div.pages{color:#AAAAAA;background:#222222;display:inline;padding:8px;border:#444444 1px solid;}div.pages.top{display:block;padding:5px 8px;margin-bottom:5px;position:fixed;top:0;right:0;opacity:0.9;}@media screen and (max-width: 800px) {div.pages.top{display:none!important;}}div.pages a.selected{color:#CCCCCC;font-weight:bolder;}div.pages a{text-decoration:none;}div.pages form{margin:0;padding:0;display:inline;}div.pages form input{margin:0 5px;display:inline;}hr{border:#222222 1px solid;height:1px;clear:left;}div.boardlist{color:#89A;font-size:9pt;margin-top:3px;text-align:center;}div.boardlist.bottom{margin-top:20px;}div.boardlist a{text-decoration:none;}div.report{color:#222222;}table.modlog{margin:auto;width:100%;}table.modlog tr td{text-align:left;margin:0;padding:4px 15px 0 0;}table.modlog tr th{text-align:left;padding:4px 15px 5px 5px;white-space:nowrap;}table.modlog tr th{background:#222222;}td.minimal,th.minimal{width:1%;white-space:nowrap;}div.top_notice{text-align:center;margin:5px auto;}span.public_ban{display:block;color:red;font-weight:bold;margin-top:15px;}span.toolong{display:block;margin-top:15px;}div.blotter{color:red;font-weight:bold;text-align:center;}table.mod.config-editor{font-size:9pt;width:100%;}table.mod.config-editor td{text-align:left;padding:5px;border-bottom:1px solid #222222;}table.mod.config-editor input[type="text"]{width:98%;}.desktop-style div.boardlist:nth-child(1){position:fixed;top:0px;left:0px;right:0px;margin-top:0px;z-index:30;}.desktop-style div.boardlist:nth-child(1):hover,.desktop-style div.boardlist:nth-child(1).cb-menu{background-color:rgba(90%,90%,90%,0.6);}.desktop-style body{padding-top:20px;}.desktop-style .sub{background:inherit;}.desktop-style .sub .sub{display:inline-block;text-indent:-9000px;width:7px;background:url('/web/20140610072543/http://2ch-an.ru/b/css/img/arrow.png') right center no-repeat;}.desktop-style .sub .sub:hover,.desktop-style .sub .sub.hover{display:inline;text-indent:0px;background:inherit;}#attention_bar{height:1.5em;max-height:1.5em;width:100%;max-width:100%;text-align:center;overflow:hidden;}#attention_bar_form{display:none;padding:0;margin:0;}#attention_bar_input{width:100%;padding:0;margin:0;text-align:center;}#attention_bar:hover{background-color:rgba(100%,100%,100%,0.2);}p.intro.thread-hidden{margin:0px;padding:0px;}form.ban-appeal{margin:9px 20px;}form.ban-appeal textarea{display:block;}.theme-catalog div.thread img{float:none!important;margin:auto;margin-bottom:12px;max-height:150px;max-width:200px;box-shadow:0 0 4px rgba(0,0,0,0.55);border:2px solid rgba(153,153,153,0);}.theme-catalog div.thread{display:inline-block;vertical-align:top;margin-bottom:25px;margin-left:20px;margin-right:15px;text-align:center;font-weight:normal;width:205px;overflow:hidden;position:relative;font-size:11px;padding:15px;max-height:300px;background:rgba(182,182,182,0.12);border:2px solid rgba(111,111,111,0.34);}.theme-catalog div.thread strong{display:block;}.compact-boardlist{padding:3px;padding-bottom:0px;}.compact-boardlist .cb-item{display:inline-block;vertical-align:middle;}.compact-boardlist .cb-icon{padding-bottom:1px;}.compact-boardlist .cb-fa{font-size:21px;padding:2px;padding-top:0;}.compact-boardlist .cb-cat{padding:5px 6px 8px 6px;}.cb-menuitem{display:table-row;}.cb-menuitem span{padding:5px;display:table-cell;text-align:left;border-top:1px solid rgba(0,0,0,0.5);}.cb-menuitem span.cb-uri{text-align:right;}.boardlist:not(.compact-boardlist) #watch-pinned::before{content:" [ ";}.boardlist:not(.compact-boardlist) #watch-pinned::after{content:" ] ";}.boardlist:not(.compact-boardlist) #watch-pinned{display:inline;}.boardlist:not(.compact-boardlist) #watch-pinned a{margin-left:3pt;}.boardlist:not(.compact-boardlist) #watch-pinned a:first-child{margin-left:0pt;}.compact-boardlist #watch-pinned{display:inline-block;vertical-align:middle;}video.post-image{display:block;float:left;margin:10px 20px;border:none;}div.post video.post-image{padding:0px;margin:10px 25px 5px 5px;}
</style>
		<script>function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}

function storePassword() {
	var newpostpassword = document.getElementById("newpostpassword");
	if (newpostpassword) {
		var expiration_date = new Date();
		expiration_date.setFullYear(expiration_date.getFullYear() + 7);
		document.cookie = "MALAIB_password=" + encodeURIComponent(newpostpassword.value) + "; path=/; expires=" + expiration_date.toGMTString();
	}
}

function quotePost(postID) {
	var message_element = document.getElementById("message");
	if (message_element) {
		message_element.focus();
		message_element.value += \'>>\' + postID + "\n";
	}

	return false;
}

function reloadCAPTCHA() {
	var captcha_element = document.getElementById("captcha");
	if (captcha_element) {
		captcha_element.focus();
		captcha_element.value = "";
	}

	var captchaimg_element = document.getElementById("captchaimage");
	if (captchaimg_element) {
		captchaimg_element.src += "#new";
	}

	return false;
}

document.addEventListener(\'DOMContentLoaded\', function () {
	var newpostpassword = document.getElementById("newpostpassword");
	if (newpostpassword) {
		newpostpassword.addEventListener("change", storePassword);
	}

	var password = getCookie("MALAIB_password");
	if (password && password != "") {
		if (newpostpassword) {
			newpostpassword.value = password;
		}

		var deletepostpassword = document.getElementById("deletepostpassword");
		if (deletepostpassword) {
			deletepostpassword.value = password;
		}
	}

	if (window.location.hash) {
		if (window.location.hash.match(/^#q[0-9]+$/i) !== null) {
			var quotePostID = window.location.hash.match(/^#q[0-9]+$/i)[0].substr(2);
			if (quotePostID != \'\') {
				quotePost(quotePostID);
			}
		}
	}
});</script>
	</head>
EOF;
	return $return;
}

function pageFooter() {
	// If the footer link is removed from the page, please link to TinyIB somewhere on the site.
	// This is all I ask in return for the free software you are using.

	return <<<EOF
		<div class="footer">
			- MalaIB  -
		</div>
	</body>
</html>
EOF;
}

function supportedFileTypes() {
	$types_allowed = array();
	if (MALAIB_PIC) {
		array_push($types_allowed, "GIF", "JPG", "PNG");
	}
	if (MALAIB_SWF) {
		array_push($types_allowed, "SWF");
	}
	if (MALAIB_WEBM) {
		array_push($types_allowed, "WebM");
	}

	$i = 0;
	$types_count = count($types_allowed);
	$types_formatted = "";
	foreach ($types_allowed as $type) {
		if (++$i >= $types_count - 1) {
			$types_formatted .= $type . ($i == $types_count - 1 && $types_count > 1 ? " and " : "");
		} else {
			$types_formatted .= $type . ", ";
		}
	}

	if ($types_formatted != "") {
		return "Supported file type" . ($types_count != 1 ? "s are " : " is ") . $types_formatted . ".";
	}

	return $types_formatted;
}

function buildPost($post, $res) {
	$return = "";
	$threadid = ($post['parent'] == MALAIB_NEWTHREAD) ? $post['id'] : $post['parent'];

	if ($res == MALAIB_RESPAGE) {
		$reflink = "<a href=\"$threadid.html#{$post['id']}\">No.</a><a href=\"$threadid.html#q{$post['id']}\" onclick=\"javascript:quotePost('{$post['id']}')\">{$post['id']}</a>";
	} else {
		$reflink = "<a href=\"res/$threadid.html#{$post['id']}\">No.</a><a href=\"res/$threadid.html#q{$post['id']}\">{$post['id']}</a>";
	}

	if (!isset($post["omitted"])) {
		$post["omitted"] = 0;
	}

	if ($post["parent"] != MALAIB_NEWTHREAD) {
		$return .= <<<EOF
<table>
<tbody>
<tr>
<td class="doubledash">
	&#0168;
</td>
<td class="reply" id="reply${post["id"]}">
EOF;
	} elseif ($post["file"] != "") {
		$return .= <<<EOF
<span class="filesize">File: <a href="src/${post["file"]}">${post["file"]}</a>&ndash;(${post["file_size_formatted"]}, ${post["image_width"]}x${post["image_height"]}, ${post["file_original"]})</span>
<br>
<a target="_blank" href="src/${post["file"]}">
<span id="thumb${post['id']}"><img src="thumb/${post["thumb"]}" alt="${post["id"]}" class="thumb" width="${post["thumb_width"]}" height="${post["thumb_height"]}"></span>
</a>
EOF;
	}

	$return .= <<<EOF
<a name="${post['id']}"></a>
<label>
	<input type="checkbox" name="delete" value="${post['id']}"> 
EOF;

	if ($post['subject'] != '') {
		$return .= '	<span class="filetitle">' . $post['subject'] . '</span> ';
	}

	$return .= <<<EOF
${post["nameblock"]}
</label>
<span class="reflink">
	$reflink
</span>
EOF;

	if ($post['parent'] != MALAIB_NEWTHREAD && $post["file"] != "") {
		$return .= <<<EOF
<br>
<span class="filesize"><a href="src/${post["file"]}">${post["file"]}</a>&ndash;(${post["file_size_formatted"]}, ${post["image_width"]}x${post["image_height"]}, ${post["file_original"]})</span>
<br>
<a target="_blank" href="src/${post["file"]}">
	<span id="thumb${post["id"]}"><img src="thumb/${post["thumb"]}" alt="${post["id"]}" class="thumb" width="${post["thumb_width"]}" height="${post["thumb_height"]}"></span>
</a>
EOF;
	}

	if ($post['parent'] == MALAIB_NEWTHREAD && $res == MALAIB_INDEXPAGE) {
		$return .= "&nbsp;[<a href=\"res/${post["id"]}.html\">Reply</a>]";
	}

	if (MALAIB_TRUNCATE > 0 && !$res && substr_count($post['message'], '<br>') > MALAIB_TRUNCATE) { // Truncate messages on board index pages for readability
		$br_offsets = strallpos($post['message'], '<br>');
		$post['message'] = substr($post['message'], 0, $br_offsets[MALAIB_TRUNCATE - 1]);
		$post['message'] .= '<br><span class="omittedposts">Post truncated.  Click Reply to view.</span><br>';
	}
	$return .= <<<EOF
<div class="message">
${post["message"]}
</div>
EOF;

	if ($post['parent'] == MALAIB_NEWTHREAD) {
		if ($res == MALAIB_INDEXPAGE && $post['omitted'] > 0) {
			$return .= '<span class="omittedposts">' . $post['omitted'] . ' ' . plural('post', $post['omitted']) . ' omitted. Click Reply to view.</span>';
		}
	} else {
		$return .= <<<EOF
</td>
</tr>
</tbody>
</table>
EOF;
	}

	return $return;
}

function buildPage($htmlposts, $parent, $pages = 0, $thispage = 0) {
	$managelink = basename($_SERVER['PHP_SELF']) . "?manage";
	$maxdimensions = MALAIB_MAXWOP . 'x' . MALAIB_MAXHOP;
	if (MALAIB_MAXW != MALAIB_MAXWOP || MALAIB_MAXH != MALAIB_MAXHOP) {
		$maxdimensions .= ' (new thread) or ' . MALAIB_MAXW . 'x' . MALAIB_MAXH . ' (reply)';
	}

	$postingmode = "";
	$pagenavigator = "";
	if ($parent == MALAIB_NEWTHREAD) {
		$pages = max($pages, 0);
		$previous = ($thispage == 1) ? "index" : $thispage - 1;
		$next = $thispage + 1;

		$pagelinks = ($thispage == 0) ? "<td>Previous</td>" : '<td><form method="get" action="' . $previous . '.html"><input value="Previous" type="submit"></form></td>';

		$pagelinks .= "<td>";
		for ($i = 0; $i <= $pages; $i++) {
			if ($thispage == $i) {
				$pagelinks .= '&#91;' . $i . '&#93; ';
			} else {
				$href = ($i == 0) ? "index" : $i;
				$pagelinks .= '&#91;<a href="' . $href . '.html">' . $i . '</a>&#93; ';
			}
		}
		$pagelinks .= "</td>";

		$pagelinks .= ($pages <= $thispage) ? "<td>Next</td>" : '<td><form method="get" action="' . $next . '.html"><input value="Next" type="submit"></form></td>';

		$pagenavigator = <<<EOF
<table border="1">
	<tbody>
		<tr>
			$pagelinks
		</tr>
	</tbody>
</table>
EOF;
	} else {
		$postingmode = '&#91;<a href="../">Return</a>&#93;<div class="replymode">Posting mode: Reply</div> ';
	}

	$max_file_size_input_html = '';
	$max_file_size_rules_html = '';
	$reqmod_html = '';
	$filetypes_html = '';
	$file_input_html = '';
	$unique_posts_html = '';

	$captcha_html = '';
	if (MALAIB_CAPTCHA) {
		$captcha_html = <<<EOF
					<tr>
						<td class="postblock">
							CAPTCHA
						</td>
						<td>
							<input type="text" name="captcha" id="captcha" autocomplete="off" size="6" accesskey="c">&nbsp;&nbsp;(enter the text below)<br>
							<img id="captchaimage" src="inc/captcha.php" width="175" height="55" alt="CAPTCHA" onclick="javascript:reloadCAPTCHA()" style="margin-top: 5px;cursor: pointer;">
						</td>
					</tr>
EOF;
	}

	if (MALAIB_PIC || MALAIB_WEBM || MALAIB_SWF) {
		if (MALAIB_MAXKB > 0) {
			$max_file_size_input_html = '<input type="hidden" name="MAX_FILE_SIZE" value="' . strval(MALAIB_MAXKB * 1024) . '">';
			$max_file_size_rules_html = '<li>Maximum file size allowed is ' . MALAIB_MAXKBDESC . '.</li>';
		}

		$filetypes_html = '<li>' . supportedFileTypes() . '</li>';

		$file_input_html = <<<EOF
					<tr>
						<td class="postblock">
							File
						</td>
						<td>
							<input type="file" name="file" size="35" accesskey="f">
						</td>
					</tr>
EOF;
	}

	if (MALAIB_REQMOD != 'disable') {
		$reqmod_html = '<li>All posts' . (MALAIB_REQMOD == 'files' ? ' with a file attached' : '') . ' will be moderated before being shown.</li>';
	}

	$thumbnails_html = '';
	if (MALAIB_PIC) {
		$thumbnails_html = "<li>Images greater than $maxdimensions will be thumbnailed.</li>";
	}

	$unique_posts = uniquePosts();
	if ($unique_posts > 0) {
		$unique_posts_html = "<li>Currently $unique_posts unique user posts.</li>\n";
	}

	$body = <<<EOF
	<body>
		<div class="adminbar">
			[<a href="$managelink" style="text-decoration: underline;">Manage</a>]
		</div>
		<div class="logo">
EOF;
	$body .= MALAIB_LOGO . MALAIB_BOARDDESC . <<<EOF
		</div>
		<hr width="90%" size="1">
		$postingmode
<div class="spoil">
<div style="  font-size: 1.2em;
  text-align: center;">[<span onclick="if (this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display != '') { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = ''; this.innerText = 'Закрыть форму постинга'; this.value = '-'; } else { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = 'none'; this.innerText = 'Открыть форму постинга'; }" type="button">Открыть форму постинга</span>]<br>
</div>
<div class="alt2">
<div style="display: none;">
		<div class="postarea">
			<form name="postform" id="postform" action="imgboard.php" method="post" enctype="multipart/form-data">
			$max_file_size_input_html
			<input type="hidden" name="parent" value="$parent">
			<table class="postform">
				<tbody>
					<!--tr>
						<td class="postblock">
							Name
						</td>
						<td>
							<input type="text" name="name" size="28" maxlength="75" accesskey="n">
						</td>
					</tr>
					<tr>
						<td class="postblock">
							E-mail
						</td>
						<td>
							<input type="text" name="email" size="28" maxlength="75" accesskey="e">
						</td>
					</tr-->
					<tr>
						<td class="postblock">
							Subject
						</td>
						<td>
							<input type="text" name="subject" size="35" maxlength="75" accesskey="s">
							<input type="submit" value="Submit" accesskey="z">
						</td>
					</tr>
					<tr>
						<td class="postblock">
							Message
						</td>
						<td>
							<textarea id="message" name="message" cols="35" rows="4" accesskey="m"></textarea>
						</td>
					</tr>
					$captcha_html
					$file_input_html
					<tr>
						<td class="postblock">
							Password
						</td>
						<td>
							<input type="password" name="password" id="newpostpassword" size="8" accesskey="p">&nbsp;&nbsp;(for post and file deletion)
						</td>
					</tr>
					<tr>
						<td colspan="2" class="rules">
							<ul>
								$reqmod_html
								$filetypes_html
								$max_file_size_rules_html
								$thumbnails_html
								$unique_posts_html
							</ul>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div></div></div></div>
		<hr>
		<form id="delform" action="imgboard.php?delete" method="post">
		<input type="hidden" name="board" 
EOF;
	$body .= 'value="' . MALAIB_BOARD . '">' . <<<EOF
		$htmlposts
		<table class="userdelete">
			<tbody>
				<tr>
					<td>
						Delete Post <input type="password" name="password" id="deletepostpassword" size="8" placeholder="Password">&nbsp;<input name="deletepost" value="Delete" type="submit">
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		$pagenavigator
		<br>
EOF;
	return pageHeader() . $body . pageFooter();
}

function rebuildIndexes() {
	$page = 0;
	$i = 0;
	$htmlposts = '';
	$threads = allThreads();
	$pages = ceil(count($threads) / MALAIB_THREADSPERPAGE) - 1;

	foreach ($threads as $thread) {
		$replies = postsInThreadByID($thread['id']);
		$thread['omitted'] = max(0, count($replies) - MALAIB_PREVIEWREPLIES - 1);

		// Build replies for preview
		$htmlreplies = array();
		for ($j = count($replies) - 1; $j > $thread['omitted']; $j--) {
			$htmlreplies[] = buildPost($replies[$j], MALAIB_INDEXPAGE);
		}

		$htmlposts .= buildPost($thread, MALAIB_INDEXPAGE) . implode('', array_reverse($htmlreplies)) . "<br clear=\"left\">\n<hr>";

		if (++$i >= MALAIB_THREADSPERPAGE) {
			$file = ($page == 0) ? 'index.html' : $page . '.html';
			writePage($file, buildPage($htmlposts, 0, $pages, $page));

			$page++;
			$i = 0;
			$htmlposts = '';
		}
	}

	if ($page == 0 || $htmlposts != '') {
		$file = ($page == 0) ? 'index.html' : $page . '.html';
		writePage($file, buildPage($htmlposts, 0, $pages, $page));
	}
}

function rebuildThread($id) {
	$htmlposts = "";
	$posts = postsInThreadByID($id);
	foreach ($posts as $post) {
		$htmlposts .= buildPost($post, MALAIB_RESPAGE);
	}

	$htmlposts .= "<br clear=\"left\">\n<hr>\n";

	writePage('res/' . $id . '.html', fixLinksInRes(buildPage($htmlposts, $id)));
}

function adminBar() {
	global $loggedin, $isadmin, $returnlink;
	$return = '[<a href="' . $returnlink . '" style="text-decoration: underline;">Return</a>]';
	if (!$loggedin) {
		return $return;
	}
	return '[<a href="?manage">Status</a>] [' . (($isadmin) ? '<a href="?manage&bans">Bans</a>] [' : '') . '<a href="?manage&moderate">Moderate Post</a>] [<a href="?manage&rawpost">Raw Post</a>] [' . (($isadmin) ? '<a href="?manage&rebuildall">Rebuild All</a>] [' : '') . (($isadmin && MALAIB_DBMIGRATE) ? '<a href="?manage&dbmigrate"><b>Migrate Database</b></a>] [' : '') . '<a href="?manage&logout">Log Out</a>] &middot; ' . $return;
}

function managePage($text, $onload = '') {
	$adminbar = adminBar();
	$body = <<<EOF
	<body$onload>
		<div class="adminbar">
			$adminbar
		</div>
		<div class="logo">
EOF;
	$body .= MALAIB_LOGO . MALAIB_BOARDDESC . <<<EOF
		</div>
		<hr width="90%" size="1">
		<div class="replymode">Manage mode</div>
		$text
		<hr>
EOF;
	return pageHeader() . $body . pageFooter();
}

function manageOnLoad($page) {
	switch ($page) {
		case 'login':
			return ' onload="document.tinyib.password.focus();"';
		case 'moderate':
			return ' onload="document.tinyib.moderate.focus();"';
		case 'rawpost':
			return ' onload="document.tinyib.message.focus();"';
		case 'bans':
			return ' onload="document.tinyib.ip.focus();"';
	}
}

function manageLogInForm() {
	return <<<EOF
	<form id="tinyib" name="tinyib" method="post" action="?manage">
	<fieldset>
	<legend align="center">Enter an administrator or moderator password</legend>
	<div class="login">
	<input type="password" id="password" name="password"><br>
	<input type="submit" value="Log In" class="managebutton">
	</div>
	</fieldset>
	</form>
	<br>
EOF;
}

function manageBanForm() {
	return <<<EOF
	<form id="tinyib" name="tinyib" method="post" action="?manage&bans">
	<fieldset>
	<legend>Ban an IP address</legend>
	<label for="ip">IP Address:</label> <input type="text" name="ip" id="ip" value="${_GET['bans']}"> <input type="submit" value="Submit" class="managebutton"><br>
	<label for="expire">Expire(sec):</label> <input type="text" name="expire" id="expire" value="0">&nbsp;&nbsp;<small><a href="#" onclick="document.tinyib.expire.value='3600';return false;">1hr</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='86400';return false;">1d</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='172800';return false;">2d</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='604800';return false;">1w</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='1209600';return false;">2w</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='2592000';return false;">30d</a>&nbsp;<a href="#" onclick="document.tinyib.expire.value='0';return false;">never</a></small><br>
	<label for="reason">Reason:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label> <input type="text" name="reason" id="reason">&nbsp;&nbsp;<small>optional</small>
	<legend>
	</fieldset>
	</form><br>
EOF;
}

function manageBansTable() {
	$text = '';
	$allbans = allBans();
	if (count($allbans) > 0) {
		$text .= '<table border="1"><tr><th>IP Address</th><th>Set At</th><th>Expires</th><th>Reason Provided</th><th>&nbsp;</th></tr>';
		foreach ($allbans as $ban) {
			$expire = ($ban['expire'] > 0) ? date('y/m/d(D)H:i:s', $ban['expire']) : 'Does not expire';
			$reason = ($ban['reason'] == '') ? '&nbsp;' : htmlentities($ban['reason']);
			$text .= '<tr><td>' . $ban['ip'] . '</td><td>' . date('y/m/d(D)H:i:s', $ban['timestamp']) . '</td><td>' . $expire . '</td><td>' . $reason . '</td><td><a href="?manage&bans&lift=' . $ban['id'] . '">lift</a></td></tr>';
		}
		$text .= '</table>';
	}
	return $text;
}

function manageModeratePostForm() {
	return <<<EOF
	<form id="tinyib" name="tinyib" method="get" action="?">
	<input type="hidden" name="manage" value="">
	<fieldset>
	<legend>Moderate a post</legend>
	<div valign="top"><label for="moderate">Post ID:</label> <input type="text" name="moderate" id="moderate"> <input type="submit" value="Submit" class="managebutton"></div><br>
	<small><b>Tip:</b> While browsing the image board, you can easily moderate a post if you are logged in:<br>
	Tick the box next to a post and click "Delete" at the bottom of the page with a blank password.</small><br>
	</fieldset>
	</form><br>
EOF;
}

function manageRawPostForm() {
	$max_file_size_input_html = '';
	if (MALAIB_MAXKB > 0) {
		$max_file_size_input_html = '<input type="hidden" name="MAX_FILE_SIZE" value="' . strval(MALAIB_MAXKB * 1024) . '">';
	}

	return <<<EOF
	<div class="postarea">
		<form id="tinyib" name="tinyib" method="post" action="?" enctype="multipart/form-data">
		<input type="hidden" name="rawpost" value="1">
		$max_file_size_input_html
		<table class="postform">
			<tbody>
				<tr>
					<td class="postblock">
						Reply to
					</td>
					<td>
						<input type="text" name="parent" size="28" maxlength="75" value="0" accesskey="t">&nbsp;0 to start a new thread
					</td>
				</tr>
				<tr>
					<td class="postblock">
						Name
					</td>
					<td>
						<input type="text" name="name" size="28" maxlength="75" accesskey="n">
					</td>
				</tr>
				<tr>
					<td class="postblock">
						E-mail
					</td>
					<td>
						<input type="text" name="email" size="28" maxlength="75" accesskey="e">
					</td>
				</tr>
				<tr>
					<td class="postblock">
						Subject
					</td>
					<td>
						<input type="text" name="subject" size="40" maxlength="75" accesskey="s">
						<input type="submit" value="Submit" accesskey="z">
					</td>
				</tr>
				<tr>
					<td class="postblock">
						Message
					</td>
					<td>
						<textarea name="message" cols="48" rows="4" accesskey="m"></textarea>
					</td>
				</tr>
				<tr>
					<td class="postblock">
						File
					</td>
					<td>
						<input type="file" name="file" size="35" accesskey="f">
					</td>
				</tr>
				<tr>
					<td class="postblock">
						Password
					</td>
					<td>
						<input type="password" name="password" size="8" accesskey="p">&nbsp;(for post and file deletion)
					</td>
				</tr>
				<tr>
					<td colspan="2" class="rules">
						<ul>
							<li>Text entered in the Message field will be posted as is with no formatting applied.</li>
							<li>Line-breaks must be specified with "&lt;br&gt;".</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
EOF;
}

function manageModeratePost($post) {
	global $isadmin;
	$ban = banByIP($post['ip']);
	$ban_disabled = (!$ban && $isadmin) ? '' : ' disabled';
	$ban_info = (!$ban) ? ((!$isadmin) ? 'Only an administrator may ban an IP address.' : ('IP address: ' . $post["ip"])) : (' A ban record already exists for ' . $post['ip']);
	$delete_info = ($post['parent'] == MALAIB_NEWTHREAD) ? 'This will delete the entire thread below.' : 'This will delete the post below.';
	$post_or_thread = ($post['parent'] == MALAIB_NEWTHREAD) ? 'Thread' : 'Post';

	if ($post["parent"] == MALAIB_NEWTHREAD) {
		$post_html = "";
		$posts = postsInThreadByID($post["id"]);
		foreach ($posts as $post_temp) {
			$post_html .= buildPost($post_temp, MALAIB_INDEXPAGE);
		}
	} else {
		$post_html = buildPost($post, MALAIB_INDEXPAGE);
	}

	return <<<EOF
	<fieldset>
	<legend>Moderating No.${post['id']}</legend>
	
	<fieldset>
	<legend>Action</legend>
	
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr><td align="right" width="50%;">
	
	<form method="get" action="?">
	<input type="hidden" name="manage" value="">
	<input type="hidden" name="delete" value="${post['id']}">
	<input type="submit" value="Delete $post_or_thread" class="managebutton" style="width: 50%;">
	</form>
	
	</td><td><small>$delete_info</small></td></tr>
	<tr><td align="right" width="50%;">
	
	<form method="get" action="?">
	<input type="hidden" name="manage" value="">
	<input type="hidden" name="bans" value="${post['ip']}">
	<input type="submit" value="Ban Poster" class="managebutton" style="width: 50%;"$ban_disabled>
	</form>
	
	</td><td><small>$ban_info</small></td></tr>
	
	</table>
	
	</fieldset>
	
	<fieldset>
	<legend>$post_or_thread</legend>	
	$post_html
	</fieldset>
	
	</fieldset>
	<br>
EOF;
}

function manageStatus() {
	global $isadmin;
	$threads = countThreads();
	$bans = count(allBans());
	$info = $threads . ' ' . plural('thread', $threads) . ', ' . $bans . ' ' . plural('ban', $bans);
	$output = '';

	if ($isadmin && MALAIB_DBMODE == 'mysql' && function_exists('mysqli_connect')) { // Recommend MySQLi
		$output .= <<<EOF
	<fieldset>
	<legend>Notice</legend>
	<p><b>MALAIB_DBMODE</b> is currently <b>mysql</b> in <b>settings.php</b>, but <a href="http://www.php.net/manual/en/book.mysqli.php">MySQLi</a> is installed.  Please change it to <b>mysqli</b>.  This will not affect your data.</p>
	</fieldset>
EOF;
	}

	$reqmod_html = '';

	if (MALAIB_REQMOD != 'disable') {
		$reqmod_post_html = '';

		$reqmod_posts = latestPosts(false);
		foreach ($reqmod_posts as $post) {
			if ($reqmod_post_html != '') {
				$reqmod_post_html .= '<tr><td colspan="2"><hr></td></tr>';
			}
			$reqmod_post_html .= '<tr><td>' . buildPost($post, MALAIB_INDEXPAGE) . '</td><td valign="top" align="right">
			<table border="0"><tr><td>
			<form method="get" action="?"><input type="hidden" name="manage" value=""><input type="hidden" name="approve" value="' . $post['id'] . '"><input type="submit" value="Approve" class="managebutton"></form>
			</td><td>
			<form method="get" action="?"><input type="hidden" name="manage" value=""><input type="hidden" name="moderate" value="' . $post['id'] . '"><input type="submit" value="More Info" class="managebutton"></form>
			</td></tr><tr><td align="right" colspan="2">
			<form method="get" action="?"><input type="hidden" name="manage" value=""><input type="hidden" name="delete" value="' . $post['id'] . '"><input type="submit" value="Delete" class="managebutton"></form>
			</td></tr></table>
			</td></tr>';
		}

		if ($reqmod_post_html != '') {
			$reqmod_html = <<<EOF
	<fieldset>
	<legend>Pending posts</legend>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	$reqmod_post_html
	</table>
	</fieldset>
EOF;
		}
	}

	$post_html = '';
	$posts = latestPosts(true);
	$i = 0;
	foreach ($posts as $post) {
		if ($post_html != '') {
			$post_html .= '<tr><td colspan="2"><hr></td></tr>';
		}
		$post_html .= '<tr><td>' . buildPost($post, MALAIB_INDEXPAGE) . '</td><td valign="top" align="right"><form method="get" action="?"><input type="hidden" name="manage" value=""><input type="hidden" name="moderate" value="' . $post['id'] . '"><input type="submit" value="Moderate" class="managebutton"></form></td></tr>';
	}

	$output .= <<<EOF
	<fieldset>
	<legend>Status</legend>
	
	<fieldset>
	<legend>Info</legend>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tbody>
	<tr><td>
		$info
	</td>
EOF;
	if ($isadmin) {
		$output .= <<<EOF
	<td valign="top" align="right">
		<form method="get" action="?">
			<input type="hidden" name="manage">
			<input type="hidden" name="update">
			<input type="submit" value="Update TinyIB" class="managebutton">
		</form>
	</td>
EOF;
	}
	$output .= <<<EOF
	</tr>
	</tbody>
	</table>
	</fieldset>

	$reqmod_html
	
	<fieldset>
	<legend>Recent posts</legend>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	$post_html
	</table>
	</fieldset>
	
	</fieldset>
	<br>
EOF;

	return $output;
}

function manageInfo($text) {
	return '<div class="manageinfo">' . $text . '</div>';
}
// Flatfile classes
if (!defined('MALAIB_BOARD')) {
	die('');
}

# Post Structure
define('POSTS_FILE', '.posts');
define('POST_ID', 0);
define('POST_PARENT', 1);
define('POST_TIMESTAMP', 2);
define('POST_BUMPED', 3);
define('POST_IP', 4);
define('POST_NAME', 5);
define('POST_TRIPCODE', 6);
define('POST_EMAIL', 7);
define('POST_NAMEBLOCK', 8);
define('POST_SUBJECT', 9);
define('POST_MESSAGE', 10);
define('POST_PASSWORD', 11);
define('POST_FILE', 12);
define('POST_FILE_HEX', 13);
define('POST_FILE_ORIGINAL', 14);
define('POST_FILE_SIZE', 15);
define('POST_FILE_SIZE_FORMATTED', 16);
define('POST_IMAGE_WIDTH', 17);
define('POST_IMAGE_HEIGHT', 18);
define('POST_THUMB', 19);
define('POST_THUMB_WIDTH', 20);
define('POST_THUMB_HEIGHT', 21);

# Ban Structure
define('BANS_FILE', '.bans');
define('BAN_ID', 0);
define('BAN_IP', 1);
define('BAN_TIMESTAMP', 2);
define('BAN_EXPIRE', 3);
define('BAN_REASON', 4);

// flatfile.php

/*
Copyright (c) 2005 Luke Plant <L.Plant.98@cantab.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and 
associated documentation files (the "Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or 
sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject 
to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial
 portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN 
NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/**
 * Simple but powerful flatfile database
 * See http://lukeplant.me.uk/resources/flatfile/ for documentation and examples
 *
 * @tutorial flatfile.pkg
 * @package flatfile
 * @license http://www.opensource.org/licenses/mit-license.php
 */

// Utilities for flatfile functions

/** Constant to indicating a column holding floating point numbers */
define('FLOAT_COL', 'float');
/** Constant to indicating a column holding integers */
define('INT_COL', 'int');
/** Constant to indicating a column holding strings */
define('STRING_COL', 'string');
/** Constant to indicating a column holding unix timestamps */
define('DATE_COL', 'date');


/** EXPERIMENTAL: Encapsulates info about a column in a flatfile DB */
class Column {
	/**
	 * Create a new column object
	 */
	function Column($index, $type) {
		$this->index = $index;
		$this->type = $type;
	}
}

/** EXPERIMENTAL: Represent a column that is a foreign key.  Used for temporarily building tables array */
class JoinColumn {
	function JoinColumn($index, $tablename, $columnname) {
		$this->index = $index;
		$this->tablename = $tablename;
		$this->columnname = $columnname;
	}
}

/**
 * EXPERIMENTAL: Utilities for handling definitions of tables.
 */
class TableUtils {
	/**
	 * Finds JoinColumns in an array of tables, and adds 'type' fields by looking up the columns
	 *
	 * @param tables This should be an associative array containing 'tablename' => tabledefinition
	 * tabledefinition is itself an associativive array of 'COLUMN_NAME_CONSTANT' => columndefintion
	 * COLUMN_NAME_CONSTANT should be a unique constant within the table, and
	 * column definition should be a Column object or JoinColumn object
	 */
	function resolveJoins(&$tables) {
		foreach ($tables as $tablename => $discard) {
			// PHP4 compatible: can't do :  foreach ($tables as $tablename => &$tabledef)
			// and strangely, if we do
			// foreach ($tables as $tablename => &$tabledef)
			// 	$tabledef =& $tables[$tablename];
			// then we get bugs
			$tabledef =& $tables[$tablename];
			foreach ($tabledef as $colname => $discard) {
				$coldef =& $tabledef[$colname]; // PHP4 compatible
				if (is_a($coldef, 'JoinColumn') or is_subclass_of($coldef, 'JoinColumn')) {
					TableUtils::resolveColumnJoin($coldef, $tables);
				}
			}
		}
	}

	/** @access private */
	function resolveColumnJoin(&$columndef, &$tables) {
		// Doesn't work if the column it is joined to is also
		// a JoinColumn, but I can't think of ever wanting to do that
		$columndef->type = $tables[$columndef->tablename][$columndef->columnname]->type;
	}

	/** Uses 'define' to create global constants for all the column names */
	function createDefines(&$tables) {
		foreach ($tables as $tablename => $discard) {
			$tabledef = &$tables[$tablename]; // PHP4 compatible
			foreach ($tabledef as $colname => $discard) {
				$coldef = &$tabledef[$colname];
				define(strtoupper($tablename) . '_' . $colname, $coldef->index);
			}
		}
	}

	/**
	 * Creates a 'row schema' for a given table definition.
	 *
	 * A row schema is just an array of the column types for a table,
	 * using the constants defined above.
	 */
	function createRowSchema(&$tabledef) {
		$row_schema = array();
		foreach ($tabledef as $colname => $coldef) {
			$row_schema[$coldef->index] = $coldef->type;
		}
		return $row_schema;
	}
}

/** Used to indicate the default comparison should be done, which is STRING_COMPARISON in the absence of a schema, or whatever the schema specifies if one has been added */
define('DEFAULT_COMPARISON', '');
/** Used to indicate a comparison should be done as a string comparison */
define('STRING_COMPARISON', 'strcmp');
/** Used to indicate a comparison should be done as an integer comparison */
define('INTEGER_COMPARISON', 'intcmp');
/** Used to indicate a comparison should be done as a numeric (float) comparison */
define('NUMERIC_COMPARISON', 'numcmp');

/** Indicates ascending order */
define('ASCENDING', 1);
/** Indicates descending order */
define('DESCENDING', -1);

$comparison_type_for_col_type = array(
	INT_COL => INTEGER_COMPARISON,
	DATE_COL => INTEGER_COMPARISON, // assume Unix timestamps
	STRING_COL => STRING_COMPARISON,
	FLOAT_COL => NUMERIC_COMPARISON
);

function get_comparison_type_for_col_type($coltype) {
	global $comparison_type_for_col_type;
	return $comparison_type_for_col_type[$coltype];
}

/**
 * Provides simple but powerful flatfile database storage and retrieval
 *
 * Includes equivalents to SELECT * FROM table WHERE..., DELETE WHERE ...
 * UPDATE and more.  All files are stored in the {@link Flatfile::$datadir $datadir} directory,
 * and table names are just filenames in that directory.  Subdirectories
 * can be used just by specifying a table name that includes the directory name.
 * @package flatfile
 */
class Flatfile {
	/** @access private */
	var $tables;

	/** @access private */
	var $schemata;

	/** The directory to store files in.
	 * @var string
	 */
	var $datadir;

	function Flatfile() {
		$this->schemata = array();
	}

	/**
	 * Get all rows from a table
	 * @param string $tablename The table to get rows from
	 * @return array The table as an array of rows, where each row is an array of columns
	 */
	function selectAll($tablename) {
		if (!isset($this->tables[$tablename]))
			$this->loadTable($tablename);
		return $this->tables[$tablename];
	}

	/**
	 * Selects rows from a table that match the specified criteria
	 *
	 * This simulates the following SQL query:
	 * <pre>
	 *   SELECT LIMIT $limit * FROM  $tablename
	 *   WHERE $whereclause
	 *   ORDER BY $orderBy [ASC | DESC] [, $orderBy2 ...]
	 * </pre>
	 *
	 * @param string $tablename The table (file) to get the data from
	 * @param object $whereClause Either a {@link WhereClause WhereClause} object to do selection of rows, or NULL to select all
	 * @param mixed $limit Specifies limits for the rows returned:
	 * - use -1 or omitted to return all rows
	 * - use an integer n to return the first n rows
	 * - use a two item array ($startrow, $endrow) to return rows $startrow to $endrow - 1 (zero indexed)
	 * - use a two item array ($startrow, -1) to return rows $startrow to the end (zero indexed)
	 * @param mixed $orderBy Either an {@link OrderBy} object or an array of them, defining the sorting that should be applied (if an array, then the first object in the array is the first key to sort on etc).  Use NULL for no sorting.
	 * @return array The matching data, as an array of rows, where each row is an array of columns
	 */
	function selectWhere($tablename, $whereClause, $limit = -1, $orderBy = NULL) {
		if (!isset($this->tables[$tablename]))
			$this->loadTable($tablename);

		$table = $this->selectAll($tablename); // Get a copy

		$schema = $this->getSchema($tablename);
		if ($orderBy !== NULL)
			usort($table, $this->getOrderByFunction($orderBy, $schema));

		$results = array();
		$count = 0;

		if ($limit == -1)
			$limit = array(0, -1);
		else if (!is_array($limit))
			$limit = array(0, $limit);

		foreach ($table as $row) {
			if ($whereClause === NULL || $whereClause->testRow($row, $schema)) {
				if ($count >= $limit[0])
					$results[] = $row;
				++$count;
				if (($count >= $limit[1]) && ($limit[1] != -1))
					break;
			}
		}
		return $results;
	}

	/**
	 * Select a row using a unique ID
	 * @param string $tablename The table to get data from
	 * @param string $idField The index of the field containing the ID
	 * @param string $id The ID to search for
	 * @return array    The row of the table as an array
	 */
	function selectUnique($tablename, $idField, $id) {
		$result = $this->selectWhere($tablename, new SimpleWhereClause($idField, '=', $id));
		if (count($result) > 0)
			return $result[0];
		else
			return array();
	}

	/*
	 * To correctly write a file, and not overwrite the changes
	 * another process is making, we need to:
	 *  - get a lock for writing
	 *  - read its contents from disc
	 *  - modify the contents in memory
	 *  - write the contents
	 *  - release lock
	 * Because opening for writing truncates the file, we must get
	 * the lock on a different file.  getLock and releaseLock
	 * are helper functions to allow us to do this with little fuss
	 */

	/** Get a lock for writing a file
	 * @access private
	 */
	function getLock($tablename) {
		ignore_user_abort(true);
		$fp = fopen($this->datadir . $tablename . '.lock', 'w');
		if (!flock($fp, LOCK_EX)) {
			// log error?
		}
		$this->loadTable($tablename);
		return $fp;
	}

	/** Release a lock
	 * @access private
	 */
	function releaseLock($lockfp) {
		flock($lockfp, LOCK_UN);
		ignore_user_abort(false);
	}

	/**
	 * Inserts a row with an automatically generated ID
	 *
	 * The autogenerated ID will be the highest ID in the column so far plus one. The
	 * supplied row should include all fields required for the table, and the
	 * ID field it contains will just be ignored
	 *
	 * @param string $tablename The table to insert data into
	 * @param int $idField The index of the field which is the ID field
	 * @param array $newRow The new row to add to the table
	 * @return int        The newly assigned ID
	 */
	function insertWithAutoId($tablename, $idField, $newRow) {
		$lockfp = $this->getLock($tablename);
		$rows = $this->selectWhere($tablename, null, 1,
			new OrderBy($idField, DESCENDING, INTEGER_COMPARISON));
		if ($rows) {
			$newId = $rows[0][$idField] + 1;
		} else {
			$newId = 1;
		}
		$newRow[$idField] = $newId;
		$this->tables[$tablename][] = $newRow;
		$this->writeTable($tablename);
		$this->releaseLock($lockfp);
		return $newId;
	}

	/**
	 * Inserts a row in a table
	 *
	 * @param string $tablename The table to insert data into
	 * @param array $newRow The new row to add to the table
	 */
	function insert($tablename, $newRow) {
		$lockfp = $this->getLock($tablename);
		$this->tables[$tablename][] = $newRow;
		$this->writeTable($tablename);
		$this->releaseLock($lockfp);
	}

	/**
	 * Updates an existing row using a unique ID
	 *
	 * @param string $tablename The table to update
	 * @param int $idField The index of the field which is the ID field
	 * @param array $updatedRow The updated row to add to the table
	 */
	function updateRowById($tablename, $idField, $updatedRow) {
		$this->updateSetWhere($tablename, $updatedRow,
			new SimpleWhereClause($idField, '=', $updatedRow[$idField]));
	}

	/**
	 * Updates fields in a table for rows that match the provided criteria
	 *
	 * $newFields can be a complete row or it can be a sparsely populated
	 * hashtable of values (where the keys are integers which are the column
	 * indexes to update)
	 *
	 * @param string $tablename The table to update
	 * @param array $newFields A hashtable (with integer keys) of fields to update
	 * @param WhereClause $whereClause The criteria or NULL to update all rows
	 */
	function updateSetWhere($tablename, $newFields, $whereClause) {
		$schema = $this->getSchema($tablename);
		$lockfp = $this->getLock($tablename);
		for ($i = 0; $i < count($this->tables[$tablename]); ++$i) {
			if ($whereClause === NULL ||
				$whereClause->testRow($this->tables[$tablename][$i], $schema)
			) {
				foreach ($newFields as $k => $v) {
					$this->tables[$tablename][$i][$k] = $v;
				}
			}
		}
		$this->writeTable($tablename);
		$this->releaseLock($lockfp);
		$this->loadTable($tablename);
	}

	/**
	 * Deletes all rows in a table that match specified criteria
	 *
	 * @param string $tablename The table to alter
	 * @param object $whereClause .  {@link WhereClause WhereClause} object that will select
	 * rows to be deleted.  All rows are deleted if $whereClause === NULL
	 */
	function deleteWhere($tablename, $whereClause) {
		$schema = $this->getSchema($tablename);
		$lockfp = $this->getLock($tablename);
		for ($i = count($this->tables[$tablename]) - 1; $i >= 0; --$i) {
			if ($whereClause === NULL ||
				$whereClause->testRow($this->tables[$tablename][$i], $schema)
			) {
				unset($this->tables[$tablename][$i]);
			}
		}
		$this->writeTable($tablename);
		$this->releaseLock($lockfp);
		$this->loadTable($tablename); // reset array indexes
	}

	/**
	 * Delete all rows in a table
	 *
	 * @param string $tablename The table to alter
	 */
	function deleteAll($tablename) {
		$this->deleteWhere($tablename, NULL);
	}

	/**#@+
	 * @access private
	 */

	/** Gets a function that can be passed to usort to do the ORDER BY clause
	 * @param mixed $orderBy Either an OrderBy object or an array of them
	 * @return string function name
	 */
	function getOrderByFunction($orderBy, $rowSchema = null) {
		$orderer = new Orderer($orderBy, $rowSchema);
		return array(&$orderer, 'compare');
	}

	function loadTable($tablename) {
		$filedata = @file($this->datadir . $tablename);
		$table = array();
		if (is_array($filedata)) {
			foreach ($filedata as $line) {
				$line = rtrim($line, "\n");
				$table[] = explode("\t", $line);
			}
		}
		$this->tables[$tablename] = $table;
	}

	function writeTable($tablename) {
		$output = '';

		foreach ($this->tables[$tablename] as $row) {
			$keys = array_keys($row);
			rsort($keys, SORT_NUMERIC);
			$max = $keys[0];
			for ($i = 0; $i <= $max; ++$i) {
				if ($i > 0) $output .= "\t";
				$data = (!isset($row[$i]) ? '' : $row[$i]);
				$output .= str_replace(array("\t", "\r", "\n"), array(''), $data);
			}
			$output .= "\n";
		}
		$fp = @fopen($this->datadir . $tablename, "w");
		fwrite($fp, $output, strlen($output));
		fclose($fp);
	}

	/**#@-*/
	/**
	 * Adds a schema definition to the DB for a specified regular expression
	 *
	 * Schemas are optional, and are only used for automatically determining
	 * the comparison types that should be used when sorting and selecting.
	 *
	 * @param string $fileregex A regular expression used to match filenames
	 * @param string $rowSchema An array specifying the column types for data
	 *                           files that match the regex, using constants defined in flatfile_utils.php
	 */
	function addSchema($fileregex, $rowSchema) {
		array_push($this->schemata, array($fileregex, $rowSchema));
	}

	/** Retrieves the schema for a given filename */
	function getSchema($filename) {
		foreach ($this->schemata as $rowSchemaPair) {
			$fileregex = $rowSchemaPair[0];
			if (preg_match($fileregex, $filename)) {
				return $rowSchemaPair[1];
			}
		}
		return null;
	}


}

/////////////////////////// UTILITY FUNCTIONS ////////////////////////////////////

/**
 * equivalent of strcmp for comparing integers, used internally for sorting and comparing
 */
function intcmp($a, $b) {
	return (int)$a - (int)$b;
}

/**
 * equivalent of strcmp for comparing floats, used internally for sorting and comparing
 */
function numcmp($a, $b) {
	return (float)$a - (float)$b;
}

/////////////////////////// WHERE CLAUSE CLASSES ////////////////////////////////////

/**
 * Used to test rows in a database table, like the WHERE clause in an SQL statement.
 *
 * @abstract
 * @package flatfile
 */
class WhereClause {
	/**
	 * Tests a table row object
	 * @abstract
	 * @param array $row The row to test
	 * @param array $rowSchema An optional array specifying the schema of the table, using the INT_COL, STRING_COL etc constants
	 * @return bool True if the $row passes the WhereClause
	 * selection criteria, false otherwise
	 */
	function testRow($row, $rowSchema = null) {
	}
}

/**
 * Negates a where clause
 * @package flatfile
 */
class NotWhere extends WhereClause {
	/** @access private */
	var $clause;

	/**
	 * Contructs a new NotWhere object
	 *
	 * The constructed WhereClause will return the negation
	 * of the WhereClause object passed in when testing rows.
	 * @param WhereClause $whereclause The WhereClause object to negate
	 */
	function NotWhere($whereclause) {
		$this->clause = $whereclause;
	}

	function testRow($row, $rowSchema = null) {
		return !$this->clause->testRow($row, $rowSchema);
	}
}

/**
 * Implements a single WHERE clause that does simple comparisons of a field
 * with a value.
 *
 * @package flatfile
 */
class SimpleWhereClause extends WhereClause {
	/**#@+
	 * @access private
	 */
	var $field;
	var $operator;
	var $value;
	var $compare_type;

	/**#@-*/

	/**
	 * Creates a new {@link WhereClause WhereClause} object that does a comparison
	 * of a field and a value.
	 *
	 * This will be the most commonly used type of WHERE clause.  It can do comparisons
	 * of the sort "$tablerow[$field] operator $value"
	 * where 'operator' is one of:<br>
	 * - = (equals)
	 * - != (not equals)
	 * - > (greater than)
	 * - < (less than)
	 * - >= (greater than or equal to)
	 * - <= (less than or equal to)
	 * There are 3 pre-defined constants (STRING_COMPARISON, NUMERIC COMPARISON and
	 * INTEGER_COMPARISON) that modify the behaviour of these operators to do the comparison
	 * as strings, floats and integers respectively.  Howevers, these constants are
	 * just the names of functions that do the comparison (the first being the builtin
	 * function {@link strcmp strcmp()}, so you can supply your own function here to customise the
	 * behaviour of this class.
	 *
	 * @param int $field The index (in the table row) of the field to test
	 * @param string $operator The comparison operator, one of "=", "!=", "<", ">", "<=", ">="
	 * @param mixed $value The value to compare to.
	 * @param string $compare_type The comparison method to use - either
	 * STRING_COMPARISON (default), NUMERIC COMPARISON or INTEGER_COMPARISON
	 *
	 */
	function SimpleWhereClause($field, $operator, $value, $compare_type = DEFAULT_COMPARISON) {
		$this->field = $field;
		$this->operator = $operator;
		$this->value = $value;
		$this->compare_type = $compare_type;
	}

	function testRow($tablerow, $rowSchema = null) {
		if ($this->field < 0)
			return TRUE;

		$cmpfunc = $this->compare_type;
		if ($cmpfunc == DEFAULT_COMPARISON) {
			if ($rowSchema != null) {
				$cmpfunc = get_comparison_type_for_col_type($rowSchema[$this->field]);
			} else {
				$cmpfunc = STRING_COMPARISON;
			}
		}

		if ($this->field >= count($tablerow)) {
			$dbval = "";
		} else {
			$dbval = $tablerow[$this->field];
		}
		$cmp = $cmpfunc($dbval, $this->value);
		if ($this->operator == '=')
			return ($cmp == 0);
		else if ($this->operator == '!=')
			return ($cmp != 0);
		else if ($this->operator == '>')
			return ($cmp > 0);
		else if ($this->operator == '<')
			return ($cmp < 0);
		else if ($this->operator == '<=')
			return ($cmp <= 0);
		else if ($this->operator == '>=')
			return ($cmp >= 0);

		return FALSE;
	}
}

/**
 * {@link WhereClause WhereClause} class to work like a SQL 'LIKE' clause
 * @package flatfile
 */
class LikeWhereClause extends WhereClause {
	/**
	 * Creates a new LikeWhereClause
	 *
	 * @param int $field Index of the field to look at
	 * @param string $value Value to look for.  Supports using '%' as a
	 *                       wildcard, and is case insensitve.  e.g. 'test%' will match 'TESTS' and 'Testing'
	 */

	function LikeWhereClause($field, $value) {
		$this->field = $field;
		$this->regexp = '/^' . str_replace('%', '.*', preg_quote($value)) . '$/i';
	}

	function testRow($tablerow, $rowSchema = NULL) {
		return preg_match($this->regexp, $tablerow[$this->field]);
	}
}


/**
 * {@link WhereClause WhereClause} class to match a value from a list of items
 * @package flatfile
 */
class ListWhereClause extends WhereClause {

	/** @access private */
	var $field;
	/** @access private */
	var $list;
	/** @access private */
	var $compareAs;

	/**
	 * Creates a new ListWhereClause object
	 *
	 * The resulting WhereClause will pass rows (return true) if the value of the specified
	 * field is in the array.
	 *
	 * @param int $field Field to match
	 * @param array $list List of items
	 * @param string $compare_type Comparison type, string by default.
	 */
	function ListWhereClause($field, $list, $compare_type = DEFAULT_COMPARISON) {
		$this->list = $list;
		$this->field = (int)$field;
		$this->compareAs = $compare_type;
	}

	function testRow($tablerow, $rowSchema = null) {
		$func = $this->compareAs;
		if ($func == DEFAULT_COMPARISON) {
			if ($rowSchema) {
				$func = get_comparison_type_for_col_type($rowSchema[$this->field]);
			} else {
				$func = STRING_COMPARISON;
			}
		}

		foreach ($this->list as $item) {
			if ($func($tablerow[$this->field], $item) == 0)
				return true;
		}
		return false;
	}
}

/**
 * Abstract class that combines zero or more {@link WhereClause WhereClause} objects
 * together.
 * @package flatfile
 */
class CompositeWhereClause extends WhereClause {
	/**
	 * @var array Stores the child clauses
	 * @access protected
	 */
	var $clauses = array();

	/**
	 * Add a {@link WhereClause WhereClause} to the list of clauses to be used for testing
	 * @param WhereClause $whereClause The WhereClause object to add
	 */
	function add($whereClause) {
		$this->clauses[] = $whereClause;
	}
}

/**
 * {@link CompositeWhereClause CompositeWhereClause} that does an OR on all its
 * child WhereClauses.
 *
 * Use the {@link CompositeWhereClause::add() add()} method and/or the constructor
 * to add WhereClause objects
 * to the list of clauses to check.  The testRow function of the resulting object
 * will then return true if any of its child clauses return true (and returns
 * false if no clauses have been added for consistency).
 * @package flatfile
 */
class OrWhereClause extends CompositeWhereClause {
	function testRow($tablerow, $rowSchema = null) {
		foreach ($this->clauses as $clause) {
			if ($clause->testRow($tablerow, $rowSchema))
				return true;
		}
		return false;
	}

	/**
	 * Creates a new OrWhereClause
	 * @param WhereClause $whereClause,... optional unlimited list of WhereClause objects to be added
	 */
	function OrWhereClause() {
		$this->clauses = func_get_args();
	}
}

/**
 * {@link CompositeWhereClause CompositeWhereClause} that does an AND on all its
 * child WhereClauses.
 *
 * Use the {@link CompositeWhereClause::add() add()} method to add WhereClause objects
 * to the list of clauses to check.  The testRow function of the resulting object
 * will then return false if any of its child clauses return false (and returns
 * true if no clauses have been added for consistency).
 * @package flatfile
 */
class AndWhereClause extends CompositeWhereClause {
	function testRow($tablerow, $rowSchema = null) {
		foreach ($this->clauses as $clause) {
			if (!$clause->testRow($tablerow, $rowSchema))
				return false;
		}
		return true;
	}

	/**
	 * Creates a new AndWhereClause
	 * @param WhereClause $whereClause,... optional unlimited list of WhereClause objects to be added
	 */
	function AndWhereClause() {
		$this->clauses = func_get_args();
	}
}


/////////////////////////// ORDER BY CLASSES ////////////////////////////////////

/**
 * Stores information about an ORDER BY clause
 *
 * Can be passed to selectWhere to order the output.  It is easiest to use
 * the constructor to set the fields, rather than setting each individually
 * @package flatfile
 */
class OrderBy {
	/** @var int Index of field to order by */
	var $field;
	/** @var int Order type - ASCENDING or DESCENDING */
	var $orderType;
	/** @var string Comparison type  - usually either DEFAULT_COMPARISON, STRING_COMPARISON, INTEGER_COMPARISION, or NUMERIC_COMPARISON */
	var $compareAs;

	/** Creates a new OrderBy structure
	 *
	 * The $compareAs parameter can be supplied using one of the pre-defined constants, but
	 * this is actually implemented by defining the constants as names of functions to do the
	 *  comparison.  You can therefore supply the name of any function that works like
	 * {@link strcmp strcmp()} to implement custom ordering.
	 * @param int $field The index of the field to order by
	 * @param int $orderType ASCENDING or DESCENDING
	 * @param int $compareAs Comparison type: DEFAULT_COMPARISON, STRING_COMPARISON, INTEGER_COMPARISION,
	 * or NUMERIC_COMPARISON, or the name of a user defined function that you want to use for doing the comparison.
	 */
	function OrderBy($field, $orderType, $compareAs = DEFAULT_COMPARISON) {
		$this->field = $field;
		$this->orderType = $orderType;
		$this->compareAs = $compareAs;
	}
}

/**
 * Implements the sorting defined by an array of OrderBy objects.  This class
 * is used by {@link Flatfile::selectWhere()}
 * @access private
 * @package flatfile
 */
class Orderer {
	/**
	 * @var array Stores the OrderBy objects
	 * @access private
	 */
	var $orderByList;

	/**
	 * Creates new Orderer that will provide a sort function
	 * @param mixed $orderBy An OrderBy object or an array of them
	 * @param array $rowSchema Option row schema
	 */
	function Orderer($orderBy, $rowSchema = null) {
		if (!is_array($orderBy))
			$orderBy = array($orderBy);
		if ($rowSchema) {
			// Fix the comparison types
			foreach ($orderBy as $index => $discard) {
				$item =& $orderBy[$index]; // PHP4
				if ($item->compareAs == DEFAULT_COMPARISON) {
					$item->compareAs = get_comparison_type_for_col_type($rowSchema[$item->field]);
				}
			}
		}
		$this->orderByList = $orderBy;
	}

	/**
	 * Compares two table rows using the comparisons defined by the OrderBy
	 * objects.  This function is of the type that can be used passed to usort().
	 */
	function compare($row1, $row2) {
		return $this->compare_priv($row1, $row2, 0);
	}

	/**
	 * @access private
	 */
	function compare_priv($row1, $row2, $index) {
		$orderBy = $this->orderByList[$index];
		$cmpfunc = $orderBy->compareAs;
		if ($cmpfunc == DEFAULT_COMPARISON) {
			$cmpfunc = STRING_COMPARISON;
		}
		$cmp = $orderBy->orderType * $cmpfunc($row1[$orderBy->field], $row2[$orderBy->field]);
		if ($cmp == 0) {
			if ($index == (count($this->orderByList) - 1))
				return 0;
			else
				return $this->compare_priv($row1, $row2, $index + 1);
		} else
			return $cmp;
	}
}


/**********************************/
$db = new Flatfile();
$db->datadir = 'flatfile/';

# Post Functions
function uniquePosts() {
	return 0; // Unsupported by this database option
}

function postByID($id) {
	return convertPostsToSQLStyle($GLOBALS['db']->selectWhere(POSTS_FILE, new SimpleWhereClause(POST_ID, '=', $id, INTEGER_COMPARISON), 1), true);
}

function threadExistsByID($id) {
	$compClause = new AndWhereClause();
	$compClause->add(new SimpleWhereClause(POST_ID, '=', $id, INTEGER_COMPARISON));
	$compClause->add(new SimpleWhereClause(POST_PARENT, '=', 0, INTEGER_COMPARISON));

	return count($GLOBALS['db']->selectWhere(POSTS_FILE, $compClause, 1)) > 0;
}

function insertPost($newpost) {
	$post = array();
	$post[POST_ID] = '0';
	$post[POST_PARENT] = $newpost['parent'];
	$post[POST_TIMESTAMP] = time();
	$post[POST_BUMPED] = time();
	$post[POST_IP] = $newpost['ip'];
	$post[POST_NAME] = $newpost['name'];
	$post[POST_TRIPCODE] = $newpost['tripcode'];
	$post[POST_EMAIL] = $newpost['email'];
	$post[POST_NAMEBLOCK] = $newpost['nameblock'];
	$post[POST_SUBJECT] = $newpost['subject'];
	$post[POST_MESSAGE] = $newpost['message'];
	$post[POST_PASSWORD] = $newpost['password'];
	$post[POST_FILE] = $newpost['file'];
	$post[POST_FILE_HEX] = $newpost['file_hex'];
	$post[POST_FILE_ORIGINAL] = $newpost['file_original'];
	$post[POST_FILE_SIZE] = $newpost['file_size'];
	$post[POST_FILE_SIZE_FORMATTED] = $newpost['file_size_formatted'];
	$post[POST_IMAGE_WIDTH] = $newpost['image_width'];
	$post[POST_IMAGE_HEIGHT] = $newpost['image_height'];
	$post[POST_THUMB] = $newpost['thumb'];
	$post[POST_THUMB_WIDTH] = $newpost['thumb_width'];
	$post[POST_THUMB_HEIGHT] = $newpost['thumb_height'];
	$post[POST_THUMB_HEIGHT] = $newpost['thumb_height'];

	return $GLOBALS['db']->insertWithAutoId(POSTS_FILE, POST_ID, $post);
}

function bumpThreadByID($id) {
	$rows = $GLOBALS['db']->selectWhere(POSTS_FILE, new SimpleWhereClause(POST_ID, '=', $id, INTEGER_COMPARISON), 1);
	if (count($rows) > 0) {
		foreach ($rows as $post) {
			$post[POST_BUMPED] = time();
			$GLOBALS['db']->updateRowById(POSTS_FILE, POST_ID, $post);
		}
	}
}

function countThreads() {
	$rows = $GLOBALS['db']->selectWhere(POSTS_FILE, new SimpleWhereClause(POST_PARENT, '=', 0, INTEGER_COMPARISON));
	return count($rows);
}

function convertPostsToSQLStyle($posts, $singlepost = false) {
	$newposts = array();
	foreach ($posts as $oldpost) {
		$post = newPost();
		$post['id'] = $oldpost[POST_ID];
		$post['parent'] = $oldpost[POST_PARENT];
		$post['timestamp'] = $oldpost[POST_TIMESTAMP];
		$post['bumped'] = $oldpost[POST_BUMPED];
		$post['ip'] = $oldpost[POST_IP];
		$post['name'] = $oldpost[POST_NAME];
		$post['tripcode'] = $oldpost[POST_TRIPCODE];
		$post['email'] = $oldpost[POST_EMAIL];
		$post['nameblock'] = $oldpost[POST_NAMEBLOCK];
		$post['subject'] = $oldpost[POST_SUBJECT];
		$post['message'] = $oldpost[POST_MESSAGE];
		$post['password'] = $oldpost[POST_PASSWORD];
		$post['file'] = $oldpost[POST_FILE];
		$post['file_hex'] = $oldpost[POST_FILE_HEX];
		$post['file_original'] = $oldpost[POST_FILE_ORIGINAL];
		$post['file_size'] = $oldpost[POST_FILE_SIZE];
		$post['file_size_formatted'] = $oldpost[POST_FILE_SIZE_FORMATTED];
		$post['image_width'] = $oldpost[POST_IMAGE_WIDTH];
		$post['image_height'] = $oldpost[POST_IMAGE_HEIGHT];
		$post['thumb'] = $oldpost[POST_THUMB];
		$post['thumb_width'] = $oldpost[POST_THUMB_WIDTH];
		$post['thumb_height'] = $oldpost[POST_THUMB_HEIGHT];

		if ($post['parent'] == '') {
			$post['parent'] = MALAIB_NEWTHREAD;
		}

		if ($singlepost) {
			return $post;
		}
		$newposts[] = $post;
	}
	return $newposts;
}

function allThreads() {
	$rows = $GLOBALS['db']->selectWhere(POSTS_FILE, new SimpleWhereClause(POST_PARENT, '=', 0, INTEGER_COMPARISON), -1, new OrderBy(POST_BUMPED, DESCENDING, INTEGER_COMPARISON));
	return convertPostsToSQLStyle($rows);
}

function numRepliesToThreadByID($id) {
	$rows = $GLOBALS['db']->selectWhere(POSTS_FILE, new SimpleWhereClause(POST_PARENT, '=', $id, INTEGER_COMPARISON));
	return count($rows);
}

function postsInThreadByID($id, $moderated_only = true) {
	$compClause = new OrWhereClause();
	$compClause->add(new SimpleWhereClause(POST_ID, '=', $id, INTEGER_COMPARISON));
	$compClause->add(new SimpleWhereClause(POST_PARENT, '=', $id, INTEGER_COMPARISON));

	$rows = $GLOBALS['db']->selectWhere(POSTS_FILE, $compClause, -1, new OrderBy(POST_ID, ASCENDING, INTEGER_COMPARISON));
	return convertPostsToSQLStyle($rows);
}

function postsByHex($hex) {
	$rows = $GLOBALS['db']->selectWhere(POSTS_FILE, new SimpleWhereClause(POST_FILE_HEX, '=', $hex, STRING_COMPARISON), 1);
	return convertPostsToSQLStyle($rows);
}

function latestPosts($moderated = true) {
	$rows = $GLOBALS['db']->selectWhere(POSTS_FILE, NULL, 10, new OrderBy(POST_TIMESTAMP, DESCENDING, INTEGER_COMPARISON));
	return convertPostsToSQLStyle($rows);
}

function deletePostByID($id) {
	$posts = postsInThreadByID($id, false);
	foreach ($posts as $post) {
		if ($post['id'] != $id) {
			deletePostImages($post);
			$GLOBALS['db']->deleteWhere(POSTS_FILE, new SimpleWhereClause(POST_ID, '=', $post['id'], INTEGER_COMPARISON));
		} else {
			$thispost = $post;
		}
	}

	if (isset($thispost)) {
		if ($thispost['parent'] == 0) {
			@unlink('res/' . $thispost['id'] . '.html');
		}
		deletePostImages($thispost);
		$GLOBALS['db']->deleteWhere(POSTS_FILE, new SimpleWhereClause(POST_ID, '=', $thispost['id'], INTEGER_COMPARISON));
	}
}

function trimThreads() {
	if (MALAIB_MAXTHREADS > 0) {
		$numthreads = countThreads();
		if ($numthreads > MALAIB_MAXTHREADS) {
			$allthreads = allThreads();
			for ($i = MALAIB_MAXTHREADS; $i < $numthreads; $i++) {
				deletePostByID($allthreads[$i]['id']);
			}
		}
	}
}

function lastPostByIP() {
	$rows = $GLOBALS['db']->selectWhere(POSTS_FILE, new SimpleWhereClause(POST_IP, '=', $_SERVER['REMOTE_ADDR'], STRING_COMPARISON), 1, new OrderBy(POST_ID, DESCENDING, INTEGER_COMPARISON));
	return convertPostsToSQLStyle($rows, true);
}

# Ban Functions
function banByID($id) {
	return convertBansToSQLStyle($GLOBALS['db']->selectWhere(BANS_FILE, new SimpleWhereClause(BAN_ID, '=', $id, INTEGER_COMPARISON), 1), true);
}

function banByIP($ip) {
	return convertBansToSQLStyle($GLOBALS['db']->selectWhere(BANS_FILE, new SimpleWhereClause(BAN_IP, '=', $ip, STRING_COMPARISON), 1), true);
}

function allBans() {
	$rows = $GLOBALS['db']->selectWhere(BANS_FILE, NULL, -1, new OrderBy(BAN_TIMESTAMP, DESCENDING, INTEGER_COMPARISON));
	return convertBansToSQLStyle($rows);
}

function convertBansToSQLStyle($bans, $singleban = false) {
	$newbans = array();
	foreach ($bans as $oldban) {
		$ban = array();
		$ban['id'] = $oldban[BAN_ID];
		$ban['ip'] = $oldban[BAN_IP];
		$ban['timestamp'] = $oldban[BAN_TIMESTAMP];
		$ban['expire'] = $oldban[BAN_EXPIRE];
		$ban['reason'] = $oldban[BAN_REASON];

		if ($singleban) {
			return $ban;
		}
		$newbans[] = $ban;
	}
	return $newbans;
}

function insertBan($newban) {
	$ban = array();
	$ban[BAN_ID] = '0';
	$ban[BAN_IP] = $newban['ip'];
	$ban[BAN_TIMESTAMP] = time();
	$ban[BAN_EXPIRE] = $newban['expire'];
	$ban[BAN_REASON] = $newban['reason'];

	return $GLOBALS['db']->insertWithAutoId(BANS_FILE, BAN_ID, $ban);
}

function clearExpiredBans() {
	$compClause = new AndWhereClause();
	$compClause->add(new SimpleWhereClause(BAN_EXPIRE, '>', 0, INTEGER_COMPARISON));
	$compClause->add(new SimpleWhereClause(BAN_EXPIRE, '<=', time(), INTEGER_COMPARISON));

	$bans = $GLOBALS['db']->selectWhere(BANS_FILE, $compClause, -1);
	foreach ($bans as $ban) {
		deleteBanByID($ban[BAN_ID]);
	}
}

function deleteBanByID($id) {
	$GLOBALS['db']->deleteWhere(BANS_FILE, new SimpleWhereClause(BAN_ID, '=', $id, INTEGER_COMPARISON));
}

/*
********************************************
********%%%%****%%%%****%%****%%******%%%%**
********%%%%****%%%%****%%***%%*******%%%%**
********%%%%****%%%%*****%%*%%********____**
********%%%%****%%%%******%%%*********%%%%**
********%%%%****%%%%*******%%*********%%%%**
********%%%%%%%%%%%%*******%%*********%%%%**
********%%%%****%%%%*******%%*********%%%%**
********%%%%****%%%%*******%%*********%%%%**
********%%%%****%%%%*******%%*********%%%%**
********************************************
*/
if (MALAIB_TRIPSEED == '' || MALAIB_ADMINPASS == '') {
	fancyDie('MALAIB_TRIPSEED and MALAIB_ADMINPASS must be configured');
}

$redirect = true;
// Check if the request is to make a post
if (isset($_POST['message']) || isset($_POST['file'])) {
	if (MALAIB_DBMIGRATE) {
		fancyDie('Posting is currently disabled.<br>Please try again in a few moments.');
	}

	list($loggedin, $isadmin) = manageCheckLogIn();
	$rawpost = isRawPost();
	if (!$loggedin) {
		checkCAPTCHA();
		checkBanned();
		checkMessageSize();
		checkFlood();
	}

	$post = newPost(setParent());
	$post['ip'] = $_SERVER['REMOTE_ADDR'];

//	list($post['name'], $post['tripcode']) = nameAndTripcode($_POST['name']);

	$post['name'] = cleanString(substr($post['name'], 0, 75));
//	$post['email'] = cleanString(str_replace('"', '&quot;', substr($_POST['email'], 0, 75)));
	$post['subject'] = cleanString(substr($_POST['subject'], 0, 75));
	if ($rawpost) {
		$rawposttext = ($isadmin) ? ' <span style="color: red;">## Admin</span>' : ' <span style="color: purple;">## Mod</span>';
		$post['message'] = $_POST['message']; // Treat message as raw HTML
	} else {
		$rawposttext = '';
		$post['message'] = str_replace("\n", '<br>', colorQuote(postLink(cleanString(rtrim($_POST['message'])))));
	}
	$post['password'] = ($_POST['password'] != '') ? md5(md5($_POST['password'])) : '';
	$post['nameblock'] = nameBlock($post['name'], $post['tripcode'], $post['email'], time(), $rawposttext);

	if (isset($_FILES['file'])) {
		if ($_FILES['file']['name'] != "") {
			validateFileUpload();

			if (!is_file($_FILES['file']['tmp_name']) || !is_readable($_FILES['file']['tmp_name'])) {
				fancyDie("File transfer failure. Please retry the submission.");
			}

			if ((MALAIB_MAXKB > 0) && (filesize($_FILES['file']['tmp_name']) > (MALAIB_MAXKB * 1024))) {
				fancyDie("That file is larger than " . MALAIB_MAXKBDESC . ".");
			}

			$post['file_original'] = trim(htmlentities(substr($_FILES['file']['name'], 0, 50), ENT_QUOTES));
			$post['file_hex'] = md5_file($_FILES['file']['tmp_name']);
			$post['file_size'] = $_FILES['file']['size'];
			$post['file_size_formatted'] = convertBytes($post['file_size']);

			// Uploaded file type
			$file_type = strtolower(preg_replace('/.*(\..+)/', '\1', $_FILES['file']['name']));
			if ($file_type == '.jpeg') {
				$file_type = '.jpg';
			}
			if ($file_type == '.weba') {
				$file_type = '.webm';
			}

			// Thumbnail type
			if ($file_type == '.webm') {
				$thumb_type = '.jpg';
			} else if ($file_type == '.swf') {
				$thumb_type = '.png';
			} else {
				$thumb_type = $file_type;
			}

			$file_name = time() . substr(microtime(), 2, 3);
			$post['file'] = $file_name . $file_type;
			$post['thumb'] = $file_name . "s" . $thumb_type;

			$file_location = "src/" . $post['file'];
			$thumb_location = "thumb/" . $post['thumb'];

			checkDuplicateFile($post['file_hex']);

			if (!move_uploaded_file($_FILES['file']['tmp_name'], $file_location)) {
				fancyDie("Could not copy uploaded file.");
			}

			if ($file_type == '.webm') {
				$file_mime_output = shell_exec('file --mime-type ' . $file_location);
				$file_mime_split = explode(' ', $file_mime_output);
				$file_mime = strtolower(trim(array_pop($file_mime_split)));
			} else {
				if (!@getimagesize($file_location)) {
					@unlink($file_location);
					fancyDie("Failed to read the size of the uploaded file. Please retry the submission.");
				}

				$file_info = getimagesize($file_location);
				$file_mime = $file_info['mime'];
			}

			if (!($file_mime == "image/jpeg" || $file_mime == "image/gif" || $file_mime == "image/png" || (MALAIB_WEBM && ($file_mime == "video/webm" || $file_mime == "audio/webm")) || (MALAIB_SWF && ($file_mime == "application/x-shockwave-flash")))) {
				@unlink($file_location);
				fancyDie(supportedFileTypes());
			}

			if ($_FILES['file']['size'] != filesize($file_location)) {
				@unlink($file_location);
				fancyDie("File transfer failure. Please go back and try again.");
			}

			if ($file_mime == "audio/webm" || $file_mime == "video/webm") {
				$post['image_width'] = intval(shell_exec('mediainfo --Inform="Video;%Width%" ' . $file_location));
				$post['image_height'] = intval(shell_exec('mediainfo --Inform="Video;%Height%" ' . $file_location));

				if ($post['image_width'] <= 0 || $post['image_height'] <= 0) {
					$post['image_width'] = 0;
					$post['image_height'] = 0;

					$file_location_old = $file_location;
					$file_location = substr($file_location, 0, -1) . 'a'; // replace webm with weba
					rename($file_location_old, $file_location);

					$post['file'] = substr($post['file'], 0, -1) . 'a'; // replace webm with weba
				}

				if ($file_mime == "video/webm") {
					list($thumb_maxwidth, $thumb_maxheight) = thumbnailDimensions($post);
					shell_exec("ffmpegthumbnailer -s " . max($thumb_maxwidth, $thumb_maxheight) . " -i $file_location -o $thumb_location") . '!';

					$thumb_info = getimagesize($thumb_location);
					$post['thumb_width'] = $thumb_info[0];
					$post['thumb_height'] = $thumb_info[1];

					if ($post['thumb_width'] <= 0 || $post['thumb_height'] <= 0) {
						@unlink($file_location);
						@unlink($thumb_location);
						fancyDie("Sorry, your video appears to be corrupt.");
					}

					addVideoOverlay($thumb_location);
				}

				$duration = intval(shell_exec('mediainfo --Inform="' . ($file_mime == 'video/webm' ? 'Video' : 'Audio') . ';%Duration%" ' . $file_location));
				$mins = floor(round($duration / 1000) / 60);
				$secs = str_pad(floor(round($duration / 1000) % 60), 2, "0", STR_PAD_LEFT);

				$post['file_original'] = "$mins:$secs" . ($post['file_original'] != '' ? (', ' . $post['file_original']) : '');
			} else {
				$file_info = getimagesize($file_location);

				$post['image_width'] = $file_info[0];
				$post['image_height'] = $file_info[1];

				if ($file_mime == "application/x-shockwave-flash") {
					if (!copy('data:image/png;base64,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', $thumb_location)) {
						@unlink($file_location);
						fancyDie("Could not create thumbnail.");
					}

					addVideoOverlay($thumb_location);
				} else {
					list($thumb_maxwidth, $thumb_maxheight) = thumbnailDimensions($post);

					if (!createThumbnail($file_location, $thumb_location, $thumb_maxwidth, $thumb_maxheight)) {
						@unlink($file_location);
						fancyDie("Could not create thumbnail.");
					}
				}
			}

			$thumb_info = getimagesize($thumb_location);
			$post['thumb_width'] = $thumb_info[0];
			$post['thumb_height'] = $thumb_info[1];
		}
	}

	if ($post['file'] == '') { // No file uploaded
		if ($post['parent'] == MALAIB_NEWTHREAD && (MALAIB_PIC || MALAIB_SWF || MALAIB_WEBM) && !MALAIB_NOFILEOK) {
			fancyDie("A file is required to start a thread.");
		}
		if (str_replace('<br>', '', $post['message']) == "") {
			fancyDie("Please enter a message" . ((MALAIB_PIC || MALAIB_SWF || MALAIB_WEBM) ? " and/or upload a file" : "") . ".");
		}
	} else {
		echo $post['file_original'] . ' uploaded.<br>';
	}

	if (!$loggedin && (($post['file'] != '' && MALAIB_REQMOD == 'files') || MALAIB_REQMOD == 'all')) {
		$post['moderated'] = '0';
		echo 'Your ' . ($post['parent'] == MALAIB_NEWTHREAD ? 'thread' : 'post') . ' will be shown <b>once it has been approved</b>.<br>';
		$slow_redirect = true;
	}

	$post['id'] = insertPost($post);

	if ($post['moderated'] == '1') {
		if (strtolower($post['email']) == 'noko') {
			$redirect = 'res/' . ($post['parent'] == MALAIB_NEWTHREAD ? $post['id'] : $post['parent']) . '.html#' . $post['id'];
		}

		trimThreads();

		echo 'Updating thread...<br>';
		if ($post['parent'] != MALAIB_NEWTHREAD) {
			rebuildThread($post['parent']);

			if (strtolower($post['email']) != 'sage') {
				if (MALAIB_MAXREPLIES == 0 || numRepliesToThreadByID($post['parent']) <= MALAIB_MAXREPLIES) {
					bumpThreadByID($post['parent']);
				}
			}
		} else {
			rebuildThread($post['id']);
		}

		echo 'Updating index...<br>';
		rebuildIndexes();
	}
// Check if the request is to delete a post and/or its associated image
} elseif (isset($_GET['delete']) && !isset($_GET['manage'])) {
	if (!isset($_POST['delete'])) {
		fancyDie('Tick the box next to a post and click "Delete" to delete it.');
	}

	if (MALAIB_DBMIGRATE) {
		fancyDie('Post deletion is currently disabled.<br>Please try again in a few moments.');
	}

	$post = postByID($_POST['delete']);
	if ($post) {
		list($loggedin, $isadmin) = manageCheckLogIn();

		if ($loggedin && $_POST['password'] == '') {
			// Redirect to post moderation page
			echo '--&gt; --&gt; --&gt;<meta http-equiv="refresh" content="0;url=' . basename($_SERVER['PHP_SELF']) . '?manage&moderate=' . $_POST['delete'] . '">';
		} elseif ($post['password'] != '' && md5(md5($_POST['password'])) == $post['password']) {
			deletePostByID($post['id']);
			if ($post['parent'] == MALAIB_NEWTHREAD) {
				threadUpdated($post['id']);
			} else {
				threadUpdated($post['parent']);
			}
			fancyDie('Post deleted.');
		} else {
			fancyDie('Invalid password.');
		}
	} else {
		fancyDie('Sorry, an invalid post identifier was sent.  Please go back, refresh the page, and try again.');
	}

	$redirect = false;
// Check if the request is to access the management area
} elseif (isset($_GET['manage'])) {
	$text = '';
	$onload = '';
	$navbar = '&nbsp;';
	$redirect = false;
	$loggedin = false;
	$isadmin = false;
	$returnlink = basename($_SERVER['PHP_SELF']);

	list($loggedin, $isadmin) = manageCheckLogIn();

	if ($loggedin) {
		if ($isadmin) {
			if (isset($_GET['rebuildall'])) {
				$allthreads = allThreads();
				foreach ($allthreads as $thread) {
					rebuildThread($thread['id']);
				}
				rebuildIndexes();
				$text .= manageInfo('Rebuilt board.');
			} elseif (isset($_GET['bans'])) {
				clearExpiredBans();

				if (isset($_POST['ip'])) {
					if ($_POST['ip'] != '') {
						$banexists = banByIP($_POST['ip']);
						if ($banexists) {
							fancyDie('Sorry, there is already a ban on record for that IP address.');
						}

						$ban = array();
						$ban['ip'] = $_POST['ip'];
						$ban['expire'] = ($_POST['expire'] > 0) ? (time() + $_POST['expire']) : 0;
						$ban['reason'] = $_POST['reason'];

						insertBan($ban);
						$text .= manageInfo('Ban record added for ' . $ban['ip']);
					}
				} elseif (isset($_GET['lift'])) {
					$ban = banByID($_GET['lift']);
					if ($ban) {
						deleteBanByID($_GET['lift']);
						$text .= manageInfo('Ban record lifted for ' . $ban['ip']);
					}
				}

				$onload = manageOnLoad('bans');
				$text .= manageBanForm();
				$text .= manageBansTable();
			} else if (isset($_GET['update'])) {
				if (is_dir('.git')) {
					$git_output = shell_exec('git pull 2>&1');
					$text .= '<blockquote class="reply" style="padding: 7px;font-size: 1.25em;">
					<pre style="margin: 0px;padding: 0px;">Attempting update...' . "\n\n" . $git_output . '</pre>
					</blockquote>
					<p><b>Note:</b> If TinyIB updates and you have made custom modifications, <a href="https://github.com/tslocum/TinyIB/commits/master">review the changes</a> which have been merged into your installation.
					Ensure that your modifications do not interfere with any new/modified files.
					See the <a href="https://github.com/tslocum/TinyIB#readme">README</a> for more information.</p>';
				} else {
					$text .= '<p><b>TinyIB was not installed via Git.</b></p>
					<p>If you installed TinyIB without Git, you must <a href="https://github.com/tslocum/TinyIB">update manually</a>.  If you did install with Git, ensure the script has read and write access to the <b>.git</b> folder.</p>';
				}
			} elseif (isset($_GET['dbmigrate'])) {
				if (MALAIB_DBMIGRATE) {
					if (isset($_GET['go'])) {
						if (MALAIB_DBMODE == 'flatfile') {
							if (function_exists('mysqli_connect')) {
								$link = @mysqli_connect(MALAIB_DBHOST, MALAIB_DBUSERNAME, MALAIB_DBPASSWORD);
								if (!$link) {
									fancyDie("Could not connect to database: " . ((is_object($link)) ? mysqli_error($link) : (($link_error = mysqli_connect_error()) ? $link_error : '(unknown error)')));
								}
								$db_selected = @mysqli_query($link, "USE " . constant('MALAIB_DBNAME'));
								if (!$db_selected) {
									fancyDie("Could not select database: " . ((is_object($link)) ? mysqli_error($link) : (($link_error = mysqli_connect_error()) ? $link_error : '(unknown error')));
								}

								if (mysqli_num_rows(mysqli_query($link, "SHOW TABLES LIKE '" . MALAIB_DBPOSTS . "'")) == 0) {
									if (mysqli_num_rows(mysqli_query($link, "SHOW TABLES LIKE '" . MALAIB_DBBANS . "'")) == 0) {
										mysqli_query($link, $posts_sql);
										mysqli_query($link, $bans_sql);

										$max_id = 0;
										$threads = allThreads();
										foreach ($threads as $thread) {
											$posts = postsInThreadByID($thread['id']);
											foreach ($posts as $post) {
												mysqli_query($link, "INSERT INTO `" . MALAIB_DBPOSTS . "` (`id`, `parent`, `timestamp`, `bumped`, `ip`, `name`, `tripcode`, `email`, `nameblock`, `subject`, `message`, `password`, `file`, `file_hex`, `file_original`, `file_size`, `file_size_formatted`, `image_width`, `image_height`, `thumb`, `thumb_width`, `thumb_height`) VALUES (" . $post['id'] . ", " . $post['parent'] . ", " . time() . ", " . time() . ", '" . $_SERVER['REMOTE_ADDR'] . "', '" . mysqli_real_escape_string($link, $post['name']) . "', '" . mysqli_real_escape_string($link, $post['tripcode']) . "',	'" . mysqli_real_escape_string($link, $post['email']) . "',	'" . mysqli_real_escape_string($link, $post['nameblock']) . "', '" . mysqli_real_escape_string($link, $post['subject']) . "', '" . mysqli_real_escape_string($link, $post['message']) . "', '" . mysqli_real_escape_string($link, $post['password']) . "', '" . $post['file'] . "', '" . $post['file_hex'] . "', '" . mysqli_real_escape_string($link, $post['file_original']) . "', " . $post['file_size'] . ", '" . $post['file_size_formatted'] . "', " . $post['image_width'] . ", " . $post['image_height'] . ", '" . $post['thumb'] . "', " . $post['thumb_width'] . ", " . $post['thumb_height'] . ")");
												$max_id = max($max_id, $post['id']);
											}
										}
										if ($max_id > 0 && !mysqli_query($link, "ALTER TABLE `" . MALAIB_DBPOSTS . "` AUTO_INCREMENT = " . ($max_id + 1))) {
											$text .= '<p><b>Warning:</b> Unable to update the AUTO_INCREMENT value for table ' . MALAIB_DBPOSTS . ', please set it to ' . ($max_id + 1) . '.</p>';
										}

										$max_id = 0;
										$bans = allBans();
										foreach ($bans as $ban) {
											$max_id = max($max_id, $ban['id']);
											mysqli_query($link, "INSERT INTO `" . MALAIB_DBBANS . "` (`id`, `ip`, `timestamp`, `expire`, `reason`) VALUES ('" . mysqli_real_escape_string($link, $ban['id']) . "', '" . mysqli_real_escape_string($link, $ban['ip']) . "', '" . mysqli_real_escape_string($link, $ban['timestamp']) . "', '" . mysqli_real_escape_string($link, $ban['expire']) . "', '" . mysqli_real_escape_string($link, $ban['reason']) . "')");
										}
										if ($max_id > 0 && !mysqli_query($link, "ALTER TABLE `" . MALAIB_DBBANS . "` AUTO_INCREMENT = " . ($max_id + 1))) {
											$text .= '<p><b>Warning:</b> Unable to update the AUTO_INCREMENT value for table ' . MALAIB_DBBANS . ', please set it to ' . ($max_id + 1) . '.</p>';
										}

										$text .= '<p><b>Database migration complete</b>.  Set MALAIB_DBMODE to mysqli and MALAIB_DBMIGRATE to false, then click <b>Rebuild All</b> above and ensure everything looks the way it should.</p>';
									} else {
										fancyDie('Bans table (' . MALAIB_DBBANS . ') already exists!  Please DROP this table and try again.');
									}
								} else {
									fancyDie('Posts table (' . MALAIB_DBPOSTS . ') already exists!  Please DROP this table and try again.');
								}
							} else {
								fancyDie('Please install the <a href="http://php.net/manual/en/book.mysqli.php">MySQLi extension</a> and try again.');
							}
						} else {
							fancyDie('Set MALAIB_DBMODE to flatfile and enter in your MySQL settings in settings.php before migrating.');
						}
					} else {
						$text .= '<p>This tool currently only supports migration from a flat file database to MySQL.  Your original database will not be deleted.  If the migration fails, disable the tool and your board will be unaffected.  See the <a href="https://github.com/tslocum/TinyIB#migrating" target="_blank">README</a> <small>(<a href="README.md" target="_blank">alternate link</a>)</small> for instructions.</a><br><br><a href="?manage&dbmigrate&go"><b>Start the migration</b></a></p>';
					}
				} else {
					fancyDie('Set MALAIB_DBMIGRATE to true in settings.php to use this feature.');
				}
			}
		}

		if (isset($_GET['delete'])) {
			$post = postByID($_GET['delete']);
			if ($post) {
				deletePostByID($post['id']);
				rebuildIndexes();
				if ($post['parent'] != MALAIB_NEWTHREAD) {
					rebuildThread($post['parent']);
				}
				$text .= manageInfo('Post No.' . $post['id'] . ' deleted.');
			} else {
				fancyDie("Sorry, there doesn't appear to be a post with that ID.");
			}
		} elseif (isset($_GET['approve'])) {
			if ($_GET['approve'] > 0) {
				$post = postByID($_GET['approve']);
				if ($post) {
					approvePostByID($post['id']);
					$thread_id = $post['parent'] == MALAIB_NEWTHREAD ? $post['id'] : $post['parent'];

					if (strtolower($post['email']) != 'sage' && (MALAIB_MAXREPLIES == 0 || numRepliesToThreadByID($thread_id) <= MALAIB_MAXREPLIES)) {
						bumpThreadByID($thread_id);
					}
					threadUpdated($thread_id);

					$text .= manageInfo('Post No.' . $post['id'] . ' approved.');
				} else {
					fancyDie("Sorry, there doesn't appear to be a post with that ID.");
				}
			}
		} elseif (isset($_GET['moderate'])) {
			if ($_GET['moderate'] > 0) {
				$post = postByID($_GET['moderate']);
				if ($post) {
					$text .= manageModeratePost($post);
				} else {
					fancyDie("Sorry, there doesn't appear to be a post with that ID.");
				}
			} else {
				$onload = manageOnLoad('moderate');
				$text .= manageModeratePostForm();
			}
		} elseif (isset($_GET["rawpost"])) {
			$onload = manageOnLoad("rawpost");
			$text .= manageRawPostForm();
		} elseif (isset($_GET["logout"])) {
			$_SESSION['tinyib'] = '';
			session_destroy();
			die('--&gt; --&gt; --&gt;<meta http-equiv="refresh" content="0;url=' . $returnlink . '?manage">');
		}
		if ($text == '') {
			$text = manageStatus();
		}
	} else {
		$onload = manageOnLoad('login');
		$text .= manageLogInForm();
	}

	echo managePage($text, $onload);
} elseif (!file_exists('index.html') || countThreads() == 0) {
	rebuildIndexes();
}

if ($redirect) {
	echo '--&gt; --&gt; --&gt;<meta http-equiv="refresh" content="' . (isset($slow_redirect) ? '3' : '0') . ';url=' . (is_string($redirect) ? $redirect : 'index.html') . '">';
}
