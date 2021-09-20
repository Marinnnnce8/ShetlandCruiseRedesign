<?php namespace ProcessWire;

/**
 * Data
 *
 */

$cache = $config->paths->assets . "data/cruise.json";

$q = $input->get->selectorValue("q");
$data = [];
$df = "d-m-Y";
$tf = "g:ia";

function getVisitValue($visit, $fields = []) {

	if(!is_array($fields)) $fields = explode("|", $fields);

	$value = "";
	foreach($fields as $field) {
		if(isset($visit[$field])) {
			$value = $visit[$field];
			break;
		}
	}

	return $value;
}

function tQ($t, $q) {
	return strpos(strtolower($t), strtolower($q)) === false;
}

if(file_exists($cache) && filemtime($cache) > time() - 86400) {

	$data = json_decode(file_get_contents($cache), 1);

	if($q) {
		$filtered = [];
		foreach($data as $visit) {
			if(tQ($visit["ship"], $q)) continue;
			$filtered[] = $visit;
		}
		$data = $filtered;
	}

} else {

	foreach($nb->http->getJSON("https://agent.lerwick-harbour.co.uk/WebApi/api/Public/GetAllCruiseShip") as $visit) {

		$from = strtotime(getVisitValue($visit, "All_Fast_Time|ETA|ETM"));
		$to = strtotime(getVisitValue($visit, "Let_Go_Time|ETD"));

		if($to < time() || $from > strtotime((date("Y") + 1) . "-01-01")) continue;

		$title = $visit["Ship_Name"];

		if($q) if(tQ($title, $q)) continue;

		$data[] = [
			"ship" => $title,
			"from" => $from,
			"to" => $to,
			"date_from" => $datetime->date($df, $from),
			"date_to" => $datetime->date($df, $to),
			"time_from" => $datetime->date($tf, $from),
			"time_to" => $datetime->date($tf, $to),
		];
	}

	usort($data, function($a, $b) {
		$b["from"] <=> $a["from"];
	});

	file_put_contents($cache, json_encode($data));
}

echo json_encode($data, $user->isSuperUser());
