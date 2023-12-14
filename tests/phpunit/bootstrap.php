<?php

use tad\FunctionMocker\FunctionMocker;

require_once dirname(__DIR__) . '/../vendor/autoload.php';

// Bootstrap Patchwork
WP_Mock::setUsePatchwork(true);

// Bootstrap WP_Mock to initialize built-in features
WP_Mock::bootstrap();
