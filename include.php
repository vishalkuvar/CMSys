<?php
// Load All Configs
require_once dirname(__FILE__).'/config/main.php';
// Load All Traits
foreach (glob(dirname(__FILE__).'/modules/traits/*.php') as $filename) {
    require_once $filename;
}
// Load All Modules
require_once dirname(__FILE__).'/modules/errorHandler.php';	// Priority
foreach (glob(dirname(__FILE__).'/modules/*.php') as $filename) {
    require_once $filename;
}
// Load the FontAwesome css.
echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/font-awesome.min.css">';
echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/roboto.css">';
?>