<?php 

// define a default path constant
define('PATH', getenv('DOCUMENT_ROOT') . '/libs/');
define('ROOT', getenv('DOCUMENT_ROOT') . '/'); 
 $rui = explode('/',$_SERVER['REQUEST_URI'] )[1] ?? 'dashboard';
 $rui = explode('?',$rui)[0];      
        
//define('DASHBOARD', getenv('DOCUMENT_ROOT') . '/views/dashboard/');
define('ADMIN', getenv('DOCUMENT_ROOT') . "/views/{$rui}/");
define('PROFILE_NAV', "{$rui}" );

define('SERVER', "localhost");
define('USERNAME', "root");
define('PASSWORD',"password");
define('DBNAME', "myblog");
define('CODE_VERSION', '1.0.0.0');
define('SYSTEM', "public/assets/system");
define('UPLOADS', "public/assets/uploads");
define('CLASS_NAME', $rui);
