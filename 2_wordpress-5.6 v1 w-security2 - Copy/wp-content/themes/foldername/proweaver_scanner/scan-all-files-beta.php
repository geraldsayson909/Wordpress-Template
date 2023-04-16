<?php
require_once './scan.php';
$scan = new MalwareScanner();
$scan->setFlagHideOk(true);
$scan->setFlagHideWhitelist(true);
$scan->setFlagScanEverything(true);
$scan->run('../../../../');