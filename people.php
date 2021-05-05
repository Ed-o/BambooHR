<?php
// PHP script to read the list of employees from BambooHR system and process them
// as an example this one works out how many people joined before or after an employee.

// To use the API, you need a company name (the subdomain of the BambooHR instance you use) and an API key.
$companydomain = "company-name";
$apikey = "abcdef1234567890abcdef1234567890abcdef12";

// For this simple example I have set an ID number for the person I am looking for.
$findme = 189;

// We build up the headers for the web request
$headers = array(
	'Authorization: Basic ' . base64_encode($apikey . ":x"),
	'Accept: application/xml'
);

// and pass these into a stream request
$stream = stream_context_create(array('http' => array(
		'header' => $headers,
		'timeout' => 60
		)
));

// We make up the URL based on the company name
$url = "https://api.bamboohr.com/api/gateway.php/" . $companydomain . "/v1/employees/directory" ;

// And call it to get the data back

$content = file_get_contents($url, false, $stream);
$xml = simplexml_load_string($content);

// The page content is a stream. We need this as an array
$array = (array)$xml;

// Then lets extract the data into arrays we can go through
$employees = (array)$array['employees'];
$employee = $employees['employee'];

// For this simple example we will look for IDs that are lower and higher.
$lower = 0;
$higher = 0;
$count = 0;

// Now loop through all the employees and count them
foreach ($employee as $theperson) {
	$person = (array)$theperson ;
	$id = $person['@attributes']['id']; // this is the ID number of the person
	$count = $count + 1; // count ever person we find
	if ( $id < $findme) { $lower = $lower + 1; } // count the ones that have lower IDs
	if ( $id > $findme) { $higher = $higher + 1; } // and count the higher ones too
}

echo "I found $count people and of these, $lower were lower and $higher were higher";
echo " " ;

?>
