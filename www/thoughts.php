<html>
<head>
<?php
include "./pdo.php"; 
// parse the get string
$url ="http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$parts = parse_url($url);

$db_file = "./totd.sqlite";
PDO_Connect("sqlite:$db_file");

if(array_key_exists('query',$parts)){
	parse_str($parts['query'], $query);
	if(array_key_exists('action',$query)){
		if($query['action']=="delete"){
			PDO_Execute("DELETE FROM deep_thoughts WHERE tid = '".$query['tid']."'");
			$totd = PDO_FetchAll("SELECT * FROM deep_thoughts");
		}
		if($query['action']=="submit"){
			 PDO_Execute("INSERT INTO deep_thoughts (thought, thinker, dateadded, dateupdated) VALUES (:thought, :thinker, :dateadded, :dateupdated)", array("thought"=>$query['thought'], "thinker"=>$query['thinker'], "dateadded"=>time(), "dateupdated"=>time())); 
			 $totd = PDO_FetchAll("SELECT * FROM deep_thoughts");
		}
		if($query['action']=="edit"){
			 PDO_Execute("UPDATE deep_thoughts SET thought = \"" . $query['thought'] . "\", thinker = \"" . $query['thinker'] . "\", dateupdated = " . time() . " WHERE tid = " . $query['tid']); 
			 $totd = PDO_FetchAll("SELECT * FROM deep_thoughts");
		}
		if($query['action']=="filter"){
			 $totdfull = PDO_FetchAll("SELECT * FROM deep_thoughts"); 
			 $totd  = array();
			 $searchstr = strtolower($query['string']);
			 If(strlen($searchstr)<1){$searchstr = " ";}
			 foreach($totdfull as $node){
				$strThought = strtolower($node['thought']);
				$strThinker = strtolower($node['thinker']);
				if((strpos($strThought,$searchstr) !== false) or (strpos($strThinker,$searchstr) !== false)){
					$totd[] = $node; 
				}
			 }
		}		
}
} else {
	$totd = PDO_FetchAll("SELECT * FROM deep_thoughts");
}
if(count($totd)>0){
	foreach ($totd as $val){
	 $tmp[] = strtolower($val['thinker']);
	}
	array_multisort($tmp, SORT_ASC, $totd);
}
?>
<link rel="stylesheet" type="text/css" href="totd.css">
<title>Deep Thoughts</title>
</head>
<body>
<h1>Edit Deep Thoughts Table</h1>
 <p>[<a href="index.php">home</a>][<a href="config.php">config</a>]</p>
<center>
 <div align="center" style="width:400px;">
 <form action="thoughts.php">
 <input type="text" name="string" /><br />
 <input type="submit" value="Filter" />
 <input type="hidden" name="action" value="filter" />[<a href="thoughts.php">clear</a>]
 </form>
 </div>
</center>
<center>
<form action="thoughts.php">
<table>
<tr>
<td class="header">Action</td>
<td class="header">Thought</td>
<td class="header">Thinker</td>
</tr>
<tr>
<td style="text-align: center;"><input type="submit" value="Insert" /><input type="hidden" name="action" value="submit" /></td>
<td><input type="text" name="thought" /></td>
<td><input type="text" name="thinker" /></td>
</tr>
<? 
//loop through array 
foreach ($totd as $thought){
echo "<tr>";
echo "<td style=\"white-space: nowrap\">[<a href=\"thoughts.php?action=delete&tid=".$thought['tid']."\">delete</a>][<a href=\"edit.php?tid=".$thought['tid']."\">edit</a>]</td>";
echo "<td>".$thought['thought']."</td>";
echo "<td>".$thought['thinker']."</td>";
echo "<tr>";
}
?>
</table>
</form>
</center>
</body>
</html>