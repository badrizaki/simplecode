<?php
/*
|--------------------------------------------------------------------------
| DEVELOPMENT MODE
|--------------------------------------------------------------------------
|
| FOR DEVELOPMENT MODE
| Set $development_mode = 1;
| for get all errors
|
*/
$development_mode = 1;

/*
|--------------------------------------------------------------------------
| SETTING DEFAULT TIMEZONE & IP VISITOR
|--------------------------------------------------------------------------
|
| if date_default_timezone_get is empty then set timezone manually
| if $_SERVER['REMOTE_ADDR'] is ::1 or localhost then set ip visitor manually
|
*/
if (!date_default_timezone_get()) date_default_timezone_set('Asia/Jakarta');
if ($_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') $config['ipVisitor'] = "202.53.250.218";
else $config['ipVisitor']	= $_SERVER['REMOTE_ADDR'];

/*
|--------------------------------------------------------------------------
| SETTING DATABASE
|--------------------------------------------------------------------------
|
| IF USE MYSQL SET IS EMPTY
| $config['database']['connection'] = '';
| IF USE SQL SERVER SET sqlserver
| $config['database']['connection'] = 'sqlserver';
|
*/
$config['database']['connection'] = '';

/*
|--------------------------------------------------------------------------
| SETTING DIRECTORY
|--------------------------------------------------------------------------
|
| Setting your directory, you can add new variable config for new directory
| And create directory
|
*/
$config['publicDir'] 	= BASE_PATH;
$config['apiDir'] 		= BASE_PATH.'/api';
$config['dataDir']		= BASE_PATH."/data";
$config['cacheDir']		= $config['dataDir']."/cache";
$config['passwordDir']	= $config['dataDir']."/password";
$config['resetDir']		= $config['dataDir']."/reset-password";
$config['reportDir']	= $config['dataDir']."/report";

$config['assetsDir']	= BASE_PATH."/assets";
$config['imgDir']		= BASE_PATH."/assets/img";
$config['imgPath']['user'] 		= $config['imgDir']."/user";
$config['imgPath']['setting']	= $config['imgDir']."/setting";

$config['logDir']		= BASE_PATH."/log";
$config['logWeb']		= $config['logDir']."/web";
$config['logAdmin']		= $config['logDir']."/admin";
$config['logApi']		= $config['logDir']."/api";

$config['cacheFilename']['setting'] = 'setting';
$config['cacheFilename']['access'] 	= 'access';

if (!is_dir($config['cacheDir'])) mkdir($config['cacheDir'], 0777, true);
if (!is_dir($config['passwordDir'])) mkdir($config['passwordDir'], 0777, true);
if (!is_dir($config['resetDir'])) mkdir($config['resetDir'], 0777, true);
if (!is_dir($config['reportDir'])) mkdir($config['reportDir'], 0777, true);

/*
|--------------------------------------------------------------------------
| SETTING URL
|--------------------------------------------------------------------------
|
| URL to your root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| If this is not set then CodeIgniter will try guess the protocol, domain
| and path to your installation. However, you should always configure this
| explicitly and never rely on auto-guessing, especially in production
| environments.
|
*/
// $config['publicUrl']		= "http://simplecode.atommediastudio.com/";
$publicUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
if (!isset($config['publicUrl'])) $config['publicUrl']	= "http://$_SERVER[SERVER_NAME]$publicUrl";
$config['publicUrlAsset']	= $config['publicUrl']."assets/";
$config['publicUrlAdmin']	= $config['publicUrl']."admin/";
$config['publicUrlData']	= $config['publicUrl']."data/";
$config['imgUrl']			= "img/";
$config['imgSettingUrl']	= $config['imgUrl']."setting/";
$config['imgUserUrl']		= $config['imgUrl']."user/";
$config['imagesUrl']		= $config['publicUrlAsset']."images/";

/*
|--------------------------------------------------------------------------
| SETTING DESCRIPTION
|--------------------------------------------------------------------------
|
| For title website, meta, logo, icon
|
*/
$config['applicationName']			= "Simple Code";
$config['applicationAddress']		= "";
$config['applicationContact']		= "";
$config['applicationAuthor']		= "";
$config['applicationDescription']	= "";
$config['applicationIcon']			= $config['publicUrlAsset']."img/logo/icon.png";
$config['applicationLogo']			= $config['publicUrlAsset']."img/logo/logo.png";

/*
|--------------------------------------------------------------------------
| EMAIL CONFIGURATION
|--------------------------------------------------------------------------
|
| SEND EMAIL, DESIGN AND TEMPLATE EMAIL
|
*/
$config["mail"]["email"]       	= ""; ## Email for sender
$config["mail"]["name"]			= "";
$config["mail"]["mode"] 		= "smtp"; ## smtp, mail, customMail
$config["mail"]["smtpSecure"] 	= "tls"; ## do not change this, only for phpmailer ssl/tls
$config["mail"]["emailHost"]  	= ""; ## SMTP smtp.gmail.com
$config["mail"]["emailPort"]  	= 587; ## SMTP 587/465/26
$config["mail"]["user"]       	= ""; ## Email for auth
$config["mail"]["password"]   	= ""; ## Password email

$config['message']['registered'] = "<p>Kepada <b>[NAME] - [EMAIL]</b>,</p><p>Anda telah terdaftar pada [NAMEWEBSITE]. <br>Berikut adalah password Anda <br><br><b>[PASSWORD]</b><br><br>segera ganti password Anda<p> <p><b>Terima Kasih</b><br>[NAMEWEBSITE]</p>";

$config['message']['resetPassword'] = '<p>Halo <b>[NAME]</b>,</p> <p>Seseorang meminta untuk mengatur ulang password di akun [NAMEWEBSITE] Anda belum lama ini, abaikan email ini jika Anda tidak meminta untuk mengatur ulang kata sandi Anda.<br><a href="[URL]">Klik di sini untuk mengganti password Anda.</a> <br><br><b>[KODE]</b></p> <p><b>Terima Kasih</b><br>[NAMEWEBSITE]</p>';