<?php
if(!file_exists("../../../../wp-load.php")){
    require_once("../../../../wp-systcon/wp-load.php");
} else {
    require_once("../../../../wp-load.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proweaver Scanner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="./css/style.min.css">
</head>

<body> 
    <div class="wrapper">
        <main>
            <h1><?php echo get_home_url(); ?></h1>
            <section>
                <?php
                    require_once './scan.php';
                    $scan = new MalwareScanner();
                    $scan->setFlagHideOk(true);
                    $scan->run('../../../../');
                ?>
            </section>            
            <small class="wp_version">WordPress version: <?php echo $wp_version; ?></small>
        </main>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script src="./js/script.js"></script>
<script>

</script>
</body>

</html>