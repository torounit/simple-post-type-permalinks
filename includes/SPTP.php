<?php

namespace SPTP;

require SPTP_PATH . '/includes/Psr4AutoloaderClass.php';

$loader = new Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace( 'SPTP', SPTP_PATH . '/includes' );

new Bootstrap();
