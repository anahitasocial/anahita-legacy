<?php 

require_once 'class.php';

array_shift($argv);
$target = array_shift($argv);
$query  = str_replace('-','_',implode('&', $argv));
parse_str($query, $_GET);
function getopts($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

if ( empty($target) ) {exit(0);}
$src    = realpath(dirname(__FILE__).'/../code');

if  ( !file_exists($target) ) {
    Installer\ln("Creating folder $target");    
    mkdir($target,0755);
}

$mapper = new Installer\Mapper($src, $target);
$mapper->addMap('vendors/mc/rt_missioncontrol_j15','administrator/templates/rt_missioncontrol_j15');

$patterns = array(
    '#^(site|administrator)/(components|modules|templates|media)/([^/]+)/.+#' => '\1/\2/\3',
    '#^(components|modules|templates|libraries|media)/([^/]+)/.+#' => '\1/\2',
    '#^plugins/([^/]+)/([^/]+)/.+#' => 'plugins/\1/\2',
    '#^(administrator/)?(images)/.+#' => '\1\2',    
    '#^(site|administrator)/includes/.+#' => '\1/includes',                            
    '#^(vendors|migration)/.+#'    => '',
);

if ( file_exists($target.'/configuration.php') ) 
{
    $patterns['#^installation/.+#'] = '';
}

IO\write("Symlinking files...");
$mapper->addCrawlMap('vendors/joomla', $patterns);
$mapper->addCrawlMap('vendors/nooku',  $patterns);
$mapper->addCrawlMap('',  $patterns);
$mapper->symlink();

$mapper->getMap('vendors/joomla/index.php','index.php')->copy();

if ( file_exists($target.'/installation') ) {
    $mapper->getMap('vendors/joomla/installation/index.php','installation/index.php')->copy();    
}
$mapper->getMap('vendors/joomla/administrator/index.php','administrator/index.php')->copy();

if ( file_exists($target.'/configuration.php') ) 
{
    IO\write('configuration file exists');
    IO\write('done');
    exit(0);
}

IO\write('configuration file doesn\'t exists');

$value = IO\read('Do you want to create a configuration file ? ', array('key'=>'configure','boolean'=>true,'default'=>'y'));

if ( $value ) 
{    
    IO\write('Creating configuration file...');
    define('DS', DIRECTORY_SEPARATOR);
    define( '_JEXEC', 1 );
    define('JPATH_BASE',           $target);    
    define('JPATH_ROOT',           JPATH_BASE );
    define('JPATH_SITE',           JPATH_ROOT );
    define('JPATH_CONFIGURATION',  JPATH_ROOT );
    define('JPATH_ADMINISTRATOR',  JPATH_ROOT.'/administrator');
    define('JPATH_XMLRPC',         JPATH_ROOT.'/xmlrpc');
    define('JPATH_LIBRARIES',      JPATH_ROOT.'/libraries');
    define('JPATH_PLUGINS',        JPATH_ROOT.'/plugins');
    define('JPATH_INSTALLATION',   JPATH_ROOT.'/installation');
    define('JPATH_THEMES',         JPATH_BASE.'/templates');
    define('JPATH_CACHE',		   JPATH_BASE.'/cache' );    
    include_once (JPATH_LIBRARIES . '/joomla/import.php');
    include_once (JPATH_BASE."/installation/installer/helper.php");
    $database['name'] = IO\read('Enter Database Name: ', array('key'=>'db_name'));
    $database['host'] = IO\read('Enter Database Host: ', array('key'=>'db_host','default'=>'localhost')); 
    $database['port'] = IO\read('Enter Database Port: ', array('key'=>'db_port','default'=>3306));  
    $database['user'] = IO\read('Enter Database User: ', array('key'=>'db_user'));
    $database['password'] = IO\read('Enter Database Password: ', array('key'=>'db_password'));
    $database['prefix']   = IO\read('Enter Database Prefix: ', array('key'=>'db_prefix','default'=>'jos_')).'_';
    IO\write('connecting to database...');
    IO\write($database);
    $errors		 = array();
    $db = JInstallationHelper::getDBO('mysqli',$database['host'],$database['user'],$database['password'],$database['name'],$database['prefix'],false);
    if ( $db instanceof JException )
    {
        IO\write(str_repeat('*', 80));
        IO\write($db->toString());
        IO\write(str_repeat('*', 80));
        exit(1);
    }
    if ( $db->select($database['name']) ) {
        IO\write('dropping existing database...');
        JInstallationHelper::deleteDatabase($db, $database['name'], $database['prefix'], $errors);
    }
    IO\write('creating new database...');
    JInstallationHelper::createDatabase($db, $database['name'],true);    
    $db->select($database['name']);
    
    $sql_files = array(JPATH_ROOT."/installation/sql/mysql/schema.sql",JPATH_ROOT."/installation/sql/mysql/install.sql");
    IO\write('populating database...');
    array_walk($sql_files, function($file) use($db) {
        JInstallationHelper::populateDatabase(&$db, $file, $errors);
    });
    $admin_name = IO\read('Enter admin name: ', array('key'=>'admin_name','required'=>true));    
    $admin_passwd = IO\read('Enter admin password: ', array('key'=>'admin_password','required'=>true));
    $admin_email  = IO\read('Enter admin email: ', array('key'=>'admin_email','required'=>true));
    date_default_timezone_set('GMT');
    $vars = array(
        'DBhostname' => $database['host'],
        'DBuserName' => $database['user'],
        'DBpassword' => $database['password'],
        'DBname' 	 => $database['name'],
        'DBPrefix'   => $database['prefix'],
        'adminName'  => $admin_name,
        'adminEmail' => $admin_email,
        'adminPassword' => $admin_passwd
    );   
    if ( !JInstallationHelper::createAdminUser($vars) )
    {
        IO\write(str_repeat('*', 80));
        IO\write("Counldn't create an admin user. Make sure you have entered a correct email");
        IO\write(str_repeat('*', 80));
        exit(1);
    }     
    write_config(array(
        'db_host'       => $database['host'],
        'db_username'   => $database['user'],
        'db_password'   => $database['password'],
        'db_name' 	    => $database['name'],
        'db_prefix'     => $database['prefix'],
        'secret'		=> JUserHelper::genRandomPassword(32)
    ));
    exec("rm -rf ".JPATH_ROOT."/installation");
    IO\write(str_repeat('*', 80));
    IO\write("Congradulation you're done. Try logging with the following credentials");
    IO\write(array(
        'username' => 'admin',
        'password' => $admin_passwd
    ));   
}

function write_config($config)
{
    extract($config);
    $cache 			 = 'file';
    $session_handler = 'database';
    if ( function_exists('apc_exists') )
    {
        $cache = 'apc';
        $session_handler = 'apc';
    }
    $config = <<<EOF
<?php
class JConfig {

	/* Site Settings */
	var \$offline = '0';
	var \$offline_message = 'This site is down for maintenance.<br /> Please check back again soon.';
	var \$sitename = 'Anahita';			// Name of Anahita site
	var \$editor = 'tinymce';
	var \$list_limit = '20';
	var \$legacy = '0';

	/* Database Settings */
	var \$dbtype = 'mysqli';					// Normally mysql
	var \$host = '$db_host';				// This is normally set to localhost
	var \$user = '$db_username';							// MySQL username
	var \$password = '$db_password';						// MySQL password
	var \$db = '$db_name';							// MySQL database name
	var \$dbprefix = '$db_prefix';				// Do not change unless you need to!

	/* Server Settings */
	var \$secret = '$secret'; 		//Change this to something more secure
	var \$gzip = '0';
	var \$error_reporting = '633';
	var \$helpurl = 'http://help.joomla.org';
	var \$xmlrpc_server = '1';
	var \$ftp_host = '';
	var \$ftp_port = '';
	var \$ftp_user = '';
	var \$ftp_pass = '';
	var \$ftp_root = '';
	var \$ftp_enable = '';
	var \$tmp_path	= '/tmp';
	var \$log_path	= '/var/logs';
	var \$offset = '0';
	var \$live_site = ''; 					// Optional, Full url to Joomla install.
	var \$force_ssl = 0;		//Force areas of the site to be SSL ONLY.  0 = None, 1 = Administrator, 2 = Both Site and Administrator

	/* Session settings */
	var \$lifetime = '15';					// Session time
	var \$session_handler = '$session_handler';

	/* Mail Settings */
	var \$mailer = 'mail';
	var \$mailfrom = '';
	var \$fromname = '';
	var \$sendmail = '/usr/sbin/sendmail';
	var \$smtpauth = '0';
	var \$smtpuser = '';
	var \$smtppass = '';
	var \$smtphost = 'localhost';

	/* Cache Settings */
	var \$caching = '1';
	var \$cachetime = '15';
	var \$cache_handler = '$cache';

	/* Debug Settings */
	var \$debug      = '0';
	var \$debug_db 	= '0';
	var \$debug_lang = '0';

	/* Meta Settings */
	var \$MetaDesc = 'Anahitaª is a remarkable open source platform and framework for developing various social networking services';
	var \$MetaKeys = 'Anahita';
	var \$MetaTitle = '1';
	var \$MetaAuthor = '1';

	/* SEO Settings */
	var \$sef = '0';
	var \$sef_rewrite = '0';
	var \$sef_suffix = '';

	/* Feed Settings */
	var \$feed_limit   = 10;
	var \$feed_email   = 'author';
}
?>
EOF;
    file_put_contents(JPATH_BASE.'/configuration.php', $config);
}

?>