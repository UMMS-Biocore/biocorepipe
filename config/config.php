<?php
/** Configuration Variables **/
/** If DOLPHIN_PARAMS_SECTION environment variable set into any parameter section in config.ini file 
The configuration parameters will be read from that section**/
if (!empty($_SERVER["HTTP_HOST"])){
   $http_host=$_SERVER["HTTP_HOST"];
   # CHANGE HERE ACCORDING TO YOUR ENVIRONMENT
   if  ( preg_match("/dolphinnext/", $http_host) )
   {
      $param_section="Dolphinnext";
   } 
   else if  ( preg_match("/localhost/", $http_host) )
   {
      $param_section="Localhost";
   }
   ###########################################
}
if(strpos(getcwd(),'travis/build') > 0){
   $param_section="Travis";
}
 
$ini = parse_ini_file("config.ini", true);
$ini_array = $ini[$param_section];
date_default_timezone_set($ini_array['TIMEZONE']);
define('CONFIG', $ini_array['CONFIG']);
define('RUNPATH', $ini_array['RUNPATH']);

$secRaw = parse_ini_file(".sec", true);
$sec = $secRaw['Dolphinnext'];
define('DB', $sec['DB']);
define('DBUSER', $sec['DBUSER']);
define('DBPASS', $sec['DBPASS']);
define('DBHOST', $sec['DBHOST']);
define('DBPORT', $sec['DBPORT']);
define('SSHPATH', $sec['SSHPATH']);
define('AMAZON', $sec['AMAZON']);
define('AMZPATH', $sec['AMZPATH']);

?>
