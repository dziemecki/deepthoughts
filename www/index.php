<html>
<head>
<?php
include "./pdo.php"; 
$db_file = "./totd.sqlite";
PDO_Connect("sqlite:$db_file");
$deepThoughts = PDO_FetchAll("SELECT * FROM deep_thoughts");
$totd = $deepThoughts[array_rand($deepThoughts)];
?>
<link rel="stylesheet" type="text/css" href="totd.css">
    <title>Thought of the Day</title>
</head>
<body>
<?

//print_r($totd ); 

?>
    <h1>Consider this...</h1>

    <div style="background-color:gray; color:white; padding:2em; font-size:3em">
        <? echo $totd['thought'] ?>
    </div>
 - <i><? echo $totd['thinker'] ?></i>
 <p>[<a href="config.php">config</a>][<a href="index.php">reload</a>][<a href="thoughts.php">thoughts</a>]</p>
</body>
</html>