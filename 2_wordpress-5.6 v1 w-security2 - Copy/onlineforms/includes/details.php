<?php
@session_start();

$a = $b = $c = $d = $e = $f = $g = $h = '';
$la = $lb = $lc = $ld = $le = $lf = $lg = $lh = $li = '';

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_name = basename($url, ".php");
$page = ($base_name == 'index' || strpos(basename($_SERVER['REQUEST_URI']), ".php") === false) ? 'Homepage' : ucwords(str_replace('-',' ',$base_name));

$mkeywords = '';
$mdescriptions = '';
$compname = 'Form Database';
 if($page == 'Homepage'):
	$dtitle = $compname;
 else:
	$dtitle = $compname.' - '.$page;
 endif;


 $container = ($page == 'Mobile Panel')? 'mobile-container' : 'container';

 $wrapper = ($page == 'Mobile Panel' || substr($page, 0, 16) == 'View Form Mobile')? 'mobile-wrapper' : 'wrapper';

?>
