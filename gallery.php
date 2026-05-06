<?php 
include 'functions.php';

getCookies();

    $category = isset($_COOKIE['category']) ? $_COOKIE['category'] : 'nudes';
    $model = isset($_COOKIE['model']) ? $_COOKIE['model'] : 'all';
    $qtd = isset($_COOKIE['qtd']) ?  $_COOKIE['qtd'] : '20';
    $time = isset($_COOKIE['time']) ?  $_COOKIE['time'] : '180';
    $pause = isset($_COOKIE['pause']) ?  $_COOKIE['pause'] : '5';
    $timer = isset($_COOKIE['timer']) ?  $_COOKIE['timer'] : 'yes'; 

$arrayQtd = array(
    '1' => 1, 
    '2' => 2, 
    '5' => 5, 
    '10' => 10, 
    '20' => 20, 
    '30' => 30, 
    '90' => 90, 
);

$arrTime = array(
    '15 sec' => 15, 
    '30 sec' => 30, 
    '1 min' => 60, 
    '2 min' => 120, 
    '3 min' => 180, 
    '5 min' => 300, 
    '10 min' => 600, 
    '30 min' => 1800, 
);

$arrPause = array(
    '3 seg' => 3, 
    '5 seg' => 5, 
    '10 seg' => 10, 
);

$arrTimer = array(
    'Sim' => 'yes',
    'Não' => 'no',
);



function getFolders(){
    $dirs = array_filter(glob('images/*'), 'is_dir');
    $dirs = str_replace('images/', "", $dirs);

    // usort($dirs, "cmp");

    foreach ($dirs as $folder) {
        // echo $folder;
        $dirs2 = array_filter(glob('images/'.$folder.'/*'), 'is_dir');
        // só exibe se tiver subdiretórios
        if (count($dirs2) >= 1){
            echo "<h3 style='text-transform:uppercase;'>". cleanFolderName($folder)."</h3>";
echo "<h4>images/". $folder."</h4>";
            foreach ($dirs2 as $folder2) {    
                $folder2 = str_replace('images/'.$folder.'/', '', $folder2);
                echo '<button data-category="'. $folder .'" data-value="'.$folder2.'" type="button" class="btn btn-warning btn-model"> '.str_replace("-", " ", $folder2).'</button> ';
            }    
            if (count($dirs2) > 1){
                echo '<BR><button data-category="'. $folder .'" data-value="all" type="button" class="btn btn-info btn-model">TODAS</button> ';
            }
        }
    }
}

function getButtons($a,$s,$class=""){
    $arr = $a;
    $active = '';

    if ($class){
        $nameclass = $class;
    }

    foreach ($arr as $key => $value) {
        if ($s == $value){
            $active = 'active';
        }else{  
            $active = '';
        };
        echo '<button data-value="' . $value . '" type="button" class="btn btn-sm btn-info '. $nameclass .' ' . $active . '"> ' . $key . '</button> ';
    }
}




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

    </head>
    <body>
        <div class="container">


            <h1 style="text-align:center;">DRAWING SLIDESHOW</h1>


                <?php 
                    // lista folders
                    getFolders();
                 ?>
            <br/>
 

            <hr>

                <h4> A quantidade de imagens:</h4>
                <?php getButtons($arrayQtd,$qtd,"btn-qtd"); ?>

            <br/>
            <br/>

                <h4> A duração de cada slide:</h4>
                <?php getButtons($arrTime,$time,"btn-time"); ?>

            <br/>
            <br/>

                <h4> A pausa entre as imagens:</h4>
                <?php getButtons($arrPause,$pause,"btn-pause"); ?>

            <br/>
            <br/>
                <h4>Exibir o relógio?</h4>
                <?php getButtons($arrTimer,$timer,"btn-timer"); ?>

            <br/>
            <br/>
            
            <div style="text-align:center;">
                <form method="post" action="slideshow.php">
                    <input id="submit" type="submit" value="INICIAR" class="btn btn-lg btn-danger">
                </form>
            </div>

        </div>

        <div style="text-align:center; padding: 20px 0 10px;">
            <a href="logout.php">Logout</a>
        </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">

    function sformat(s) {
          var fm = [
                // Math.floor(s / 60 / 60 / 24), // DAYS
                Math.floor(s / 60 / 60) % 24, // HOURS
                Math.floor(s / 60) % 60, // MINUTES
                s % 60 // SECONDS
          ];
          return $.map(fm, function(v, i) { return ((v < 10) ? '0' : '') + v; }).join(':');
    }

$(document).ready( function(){

    $('.btn-time').click(function(){   
        $('.btn-time').removeClass('active'); 
        $(this).toggleClass('active'); 
    
        $.ajax({
            type : 'GET',
            url : 'session.php',
            data: {
                time :  $(this).attr('data-value')
            },
            success : function(data){
                //alert(data);
            },
            error : function(XMLHttpRequest, textStatus, errorThrown){
                alert ("Error Occured");
            }
        });
    });

    $('.btn-model').click(function(){
        var category = $(this).attr('data-category');
        var value = $(this).attr('data-value');

        if (value === 'all') {
            $('.btn-model[data-category="' + category + '"]').removeClass('active');
            $(this).addClass('active');
        } else {
            $('.btn-info.btn-model[data-category="' + category + '"]').removeClass('active');
            $(this).toggleClass('active');
        }

        var selected = [];
        $('.btn-model.active').each(function(){
            selected.push($(this).attr('data-category') + '/' + $(this).attr('data-value'));
        });

        $.ajax({
            type : 'GET',
            url : 'session.php',
            data: { models: selected.join(',') },
            success : function(data){},
            error : function(){ alert("Error Occured"); }
        });
    });
    
    $('.btn-qtd').click(function(){   
        $('.btn-qtd').removeClass('active'); 
        $(this).addClass('active'); 
    
        $.ajax({
            type : 'GET',
            url : 'session.php',
            data: {
                qtd :  $(this).attr('data-value')
            },
            success : function(data){
                //alert(data);
            },
            error : function(XMLHttpRequest, textStatus, errorThrown){
                alert ("Error Occured");
            }
        });
    });    

    $('.btn-pause').click(function(){   
        $('.btn-pause').removeClass('active'); 
        $(this).addClass('active'); 
    
        $.ajax({
            type : 'GET',
            url : 'session.php',
            data: {
                pause :  $(this).attr('data-value')
            },
            success : function(data){
                //alert(data);
            },
            error : function(XMLHttpRequest, textStatus, errorThrown){
                alert ("Error Occured");
            }
        });
    });

    $('.btn-timer').click(function(){   
        $('.btn-timer').removeClass('active'); 
        $(this).addClass('active'); 
    
        $.ajax({
            type : 'GET',
            url : 'session.php',
            data: {
                timer :  $(this).attr('data-value')
            },
            success : function(data){
                //alert(data);
            },
            error : function(XMLHttpRequest, textStatus, errorThrown){
                alert ("Error Occured");
            }
        });
    });
    

});
</script>
<br/>

    </body>
</html>