<html>
<head>
<?php
// parse the get string
$url ="http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$parts = parse_url($url);

$updateMessage = "";

$xml=simplexml_load_file("totd.xml") or die("Error: Cannot create object");

// Check for action
if(array_key_exists('query',$parts)){
	parse_str($parts['query'], $query);
	$keys = array_keys($query);
	if(array_key_exists('action',$query)){
		if($query['action']=="edit"){
			// load form and update values
			foreach($keys as $key){
				if($key != "action"  && $key != "method"){
					$xml->$query['method']->$key = $query[$key];
				}
			}
		$xml->system->sendmethod = $query['method'];
		$xml->asXML("totd.xml");
		$updateMessage = "Update completed";
		} 
		if($query['action']=="toggle"){
			$xml->system->sendmethod = $query['method'];
			$xml->asXML("totd.xml");
		}
	}
}

$SendMethod = $xml->system->sendmethod;
if($SendMethod=="evernote"){
		$AltSendMethod = "smeggymail";
		$ENChecked = "Checked";
		$SMChecked = "";
} else {
		$AltSendMethod = "evernote";
		$ENChecked = "";
		$SMChecked = "Checked";
}

?>
<link rel="stylesheet" type="text/css" href="totd.css">
</head>
<body>
<div align="center">
<? echo $updateMessage ?><br />
<? echo "Sending via ".$SendMethod;?>
<form action="config.php">
<input type="hidden" name="action" value="edit" />
Modify values and click "Update".
<table>
<?

foreach($xml->children() as $Child){
	echo "<tr>";
	$CName = $Child->getName();
	if($CName==$SendMethod){
			foreach($Child->children() as $GrandChild){
				$GCName = $GrandChild->getName();
				echo "<td class='header'>".$GCName."</td>";
				echo "<td><input type='text' name='".$GCName."' value='".$GrandChild."' /></td>";
				echo "</tr>";
		}
	}
}
?>
</table>
<p>
	Send Method: 
	<input type="radio" name="method" value="evernote" <? echo $ENChecked; ?> onclick="window.location = 'config.php?action=toggle&method=evernote';">
	Evernote
	<input type="radio" name="method" value="smeggymail" <? echo $SMChecked; ?> onclick="window.location = 'config.php?action=toggle&method=smeggymail';">
	SmeggyMail
</p>
<input type="submit" value="Update" />
</form>
</div>
 <p>[<a href="index.php">home</a>][<a href="thoughts.php">thoughts</a>]</p>
</body>
</html>