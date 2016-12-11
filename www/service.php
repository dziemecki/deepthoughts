<?php
include "./pdo.php"; 
$currWorkingDir = getcwd();
$cgiExeFile = dirname($currWorkingDir) . "\\php\php-cgi.exe";
$xml=simplexml_load_file("totd.xml") or die("Error: Cannot create object");

// connect to DB and grab thought
$db_file = "./totd.sqlite";
PDO_Connect("sqlite:$db_file");
$deepThoughts = PDO_FetchAll("SELECT * FROM deep_thoughts");
shuffle($deepThoughts);
$totd = $deepThoughts[array_rand($deepThoughts)];

// build text output
$thought = $totd['thought'];
$thinker = $totd['thinker'];

$SendMethod = $xml->system->sendmethod;
// if sending via SmeggyMail
if($SendMethod == "smeggymail"){
	// Write email data to SmeggyMail XML file
	$smxml=simplexml_load_file("sm.xml") or die("Error: Cannot create object");
	$smxml->toaddress=$xml->smeggymail->toaddress;
	$smxml->toname=$xml->smeggymail->toname;
	$smxml->subject=$xml->smeggymail->subject;
	$smxml->message=$currWorkingDir . "\\" . $xml->smeggymail->message;
	$smxmlmailer=$xml->smeggymail->mailer;
	$smxml->asXML("sm.xml");
	// Create message body
	$template = "./template.html";
	$message = file_get_contents($template);
	$message = str_replace("<!TOTD!>",$thought,$message);
	$message = str_replace("<!ATTR!>",$thinker,$message);
	// write output to file
	$myfile = fopen($smxml->message, "w") or die("Unable to open file!");
	fwrite($myfile, $message);
	fclose($myfile);
	// Call SmeggyMail and pass required fields in XML
	//echo $cgiExeFile . " " . $smxmlmailer . " config=". $currWorkingDir . "\\sm.xml";
	exec($cgiExeFile . " " . $smxmlmailer . " config=". $currWorkingDir . "\\sm.xml");
}

// if sending via Evernote
if($SendMethod == "evernote"){
	//create message body
	$template = "./template.enex";
	$message = file_get_contents($template);
	$message = str_replace("<!TOTD!>",$thought,$message);
	$message = str_replace("<!ATTR!>",$thinker,$message);

	// write output to file
	$myfile = fopen("totd.enex", "w") or die("Unable to open file!");
	fwrite($myfile, $message);
	fclose($myfile);

	// load config values
	$EvernoteDatabase=$xml->evernote->evernotedatabase;
	$ENScript=$xml->evernote->enscript;
	$Notebook=$xml->evernote->notebook;

	// Insert new note
	exec($ENScript . " ImportNotes /s " . $currWorkingDir."\\totd.enex /n " . $Notebook . " /d " . $EvernoteDatabase);
}
?>