<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require '../vendor/autoload.php';

// Hey!
// Did you know?
// The final release of me was written in 13 days. Documentation, tests, and all.
// Not bad.

$app = \csm2020\PatientApp\PatientApp::init();

$app->run();
