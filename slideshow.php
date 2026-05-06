<?php 
include 'functions.php';

    $models_cookie = isset($_COOKIE['models']) ? $_COOKIE['models'] : '';
    $qtd = isset($_COOKIE['qtd']) ?  $_COOKIE['qtd'] : '20';
    $time = isset($_COOKIE['time']) ?  $_COOKIE['time'] : '180';
    $pause = isset($_COOKIE['pause']) ?  $_COOKIE['pause'] : '5';
    $timer = isset($_COOKIE['timer']) ?  $_COOKIE['timer'] : 'yes'; 


// echo $model . " " . $qtd . " " . $time . " " . $pause . " " . $timer;
// echo "</br>";

function showInfo($time, $qtd){

    echo '<a href="gallery.php">Back</a>';

    if ($time < 60) {
        echo " | ". $time . " sec ";  
    }else{
        echo " | ". $time/60 . " min ";  
    }
    
    echo " | ". $qtd . " images ";

    echo " | Session: ";

    if($time > '600'){
        echo gmdate("H\hi", $time * $qtd);
    }else{
        echo ($time * $qtd)/60 ." mins";
    };
    
    echo " | File: ";
    echo "<span id='file_name'></span>"; 


}

// directory

$dirs = array();
$images = array();

function getModel($models_str){
    $dirs = [];
    if (empty($models_str)) {
        return array_filter(glob('images/*/*'), 'is_dir');
    }
    foreach (explode(',', $models_str) as $m) {
        $m = trim($m);
        if (empty($m)) continue;
        if (substr($m, -4) === '/all') {
            $cat = substr($m, 0, -4);
            $subdirs = array_filter(glob('images/' . $cat . '/*'), 'is_dir');
            $dirs = array_merge($dirs, array_values($subdirs));
        } else {
            $dirs[] = 'images/' . $m;
        }
    }
    return $dirs;
}

function getImages($arrdir,$category){

    foreach ($arrdir as $modeldir) {

        if ($handle = opendir($modeldir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && $entry != ".DS_Store") {
                    $images[] = $modeldir .'/'. $entry;
                }
            }
            closedir($handle);
        }
    }
    // randomiza
    shuffle($images);

    // var_dump($images);
    return $images;
}

function displayImages($images, $qtd){
    $total = count($images);

    if ($qtd === 'all'){
        $total = 100;
    }
    
    if ($qtd < $total){
        $total = $qtd;
    }

        $count = 0;
        foreach ($images as $image) {
            $count++;
            if ($count == $total){
                break;
            }
            echo '<li><img src="' . $image . '" title="'. $image .'"/></li>';
        }

        // echo $image;
}

function showTimer(){
    if($timer === "no") echo 'display:none;';
}


$arrModel = getModel($models_cookie);
$arrImages = getImages($arrModel, '');


?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Drawing Slideshow</title>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="author" content="" />
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script src="js/modernizr.custom.js"></script>
    </head>
    <body>
        <div id="box-info">
            <?php showInfo($time, $qtd); ?>
        </div>
        <div class="container">
<!--            <header class="clearfix">
                <span>Blueprint</span>
                <h1>Background Slideshow</h1>
                <nav>
                    <a href="http://tympanus.net/Blueprints/ResponsiveFullWidthGrid/" class="icon-arrow-left" data-info="previous Blueprint">Previous Blueprint</a>
                    <a href="http://tympanus.net/codrops/?p=14667" class="icon-drop" data-info="back to the Codrops article">back to the Codrops article</a>
                </nav>
            </header>    -->
            <div class="main">
                <ul id="cbp-bislideshow" class="cbp-bislideshow">
                    <?php 
                    echo displayImages($arrImages,$qtd);
                    ?>
                </ul>
                <div id="cbp-bicontrols" class="cbp-bicontrols">
                    <span class="cbp-biprev"></span>
                    <span class="cbp-bipause"></span>
                    <span class="cbp-binext"></span>
                </div>

               
               <div id="box-timer" style="<?php showTimer(); ?>"></div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

        <script src="js/jquery.imagesloaded.min.js"></script>
        <script src="js/cbpBGSlideshow.js"></script>

        <script type="text/javascript">
            $(function() {
                cbpBGSlideshow.init(<?php echo $pause; ?>, <?php echo $time; ?>);
            });

            $("document").ready(function() {
                $('#box-timer').click(function(){   
                    $(this).hide(); 
                }); 
            });
        </script>

           
        </script>
    </body>
</html>