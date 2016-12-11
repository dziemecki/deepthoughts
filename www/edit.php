<html>
<head>
<?php
include "./pdo.php"; 
// parse the get string
$url ="http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$parts = parse_url($url);

if(array_key_exists('query',$parts)){
	parse_str($parts['query'], $query);
	if(array_key_exists('tid',$query)){
		$tid = $query['tid'];
	}
}

$db_file = "./totd.sqlite";
PDO_Connect("sqlite:$db_file");
$deepThought = PDO_FetchAll("SELECT * FROM deep_thoughts where tid=".$tid);


?>
<link rel="stylesheet" type="text/css" href="totd.css">
</head>
<body>
<div align="center">
<form action="thoughts.php">
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="tid" value="<? echo $tid ?>" />
Modify values and click "update.
<table>
<tr>
<td class="header">Thought</td>
<td><input type="text" name="thought" value="<? echo $deepThought[0]['thought'] ?>" /></td>
</tr>
<tr>
<td class="header">Thinker</td>
<td><input type="text" name="thinker" value="<? echo $deepThought[0]['thinker'] ?>" /></td>
</tr>
</table>
<input type="submit" value="Update" />
</form>
</div>
</body>
</html?