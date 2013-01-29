<?php
$loader = __DIR__ . '/vendor/autoload.php';
 
// local testing
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
	$loader = __DIR__ . '/../../vendor/autoload.php';
}
 
if (!$loader = @include($loader)) {
	echo <<<EOM
You must set up the project dependencies by running the following commands:
 
    curl -s http://getcomposer.org/installer | php
    php composer.phar install
 
EOM;
 
	exit(1);
}
