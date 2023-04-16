<?php
set_include_path('controller');
include 'connect.php';
set_include_path('includes');
include 'detect/user_agents.php';
$detect = new UserAgent();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="<?php echo $mkeywords ;?>" />
<meta name="description" content="<?php echo $mdescriptions ;?>" />
	<title><?php echo $dtitle;?></title>


<link href="css/style.css" rel="stylesheet" type="text/css" />
<?php if($detect->is_mobile() || $page == 'Mobile Panel'){?>
<link href="css/mobile-media.css" rel="stylesheet" type="text/css" />
<?php } ?>
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="fonts/Roboto.css">
<link rel="stylesheet" type="text/css" href="fonts/Roboto-300.css">

<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/table-sorter.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.pager.js"></script>
<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript">initAll();</script>
</head>
<body>
<!--wrapper-->
<div id="<?php echo $wrapper ?>">
