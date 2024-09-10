<?php
$website = fopen("https://basketball.realgm.com/nba/players", "r");

$txt = "";
$dbh = new PDO('mysql:host=localhost;dbname=nations', 'nations', 'nations!');
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);


while (!feof($website)){
	$txt .= fread($website,999999);
}
fclose($website);

preg_match_all('/href="\/player\/\w*-\w*\/Summary\/\d*">(\w* \w*)<\/a><\/td>
<td data-th="Pos" class="nowrap" rel="\d*">\w*<\/td>
<td data-th="Height" class="nowrap" rel="\d*">\d*-\d*<\/td>
<td data-th="Weight" class="nowrap" rel="(\d*)/', $txt, $player);
$playerarray = array();
$playerweight = array();
#var_dump($player);

for($i=0; $i<463; $i++){
    $player[1][$i] = preg_replace('/_/', ' ', $player[1][$i]);
    array_push($playerarray, strtolower(trim($player[1][$i])));
    array_push($playerweight, $player[2][$i]);
}

// for($i=0; $i<count($playerarray); $i++){
//     print($playerarray[$i]);
//     print($playerweight[$i]);
// }

// for($i=0; $i<count($playerarray); $i++){
//     $stmt = $dbh->prepare("insert into nbaplayers(name, weight) values (?, ?)");
//     $stmt->execute(array($playerarray[$i], $playerweight[$i]));
// }


$website2 = fopen("https://hoopshype.com/salaries/players/", "r");

$txt2 = "";

while (!feof($website2)){
	$txt2 .= fread($website2,999999);
}
fclose($website2);

preg_match_all('/<td class="name">\s*<a href="https:\/\/hoopshype\.com\/player\/[\w-]+\/salary\/">\s*([\w\s]+)\s*<\/a>\s*<\/td>\s*<td style="color:black" class="hh-salaries-sorted" data-value="(\d+)"/', $txt2, $salary);

$salaryarray = array();
$salarytestnames = array();

for($j=0; $j<451; $j++){
    $salary[1][$j] = preg_replace('/_/', ' ', $salary[1][$j]);
    array_push($salarytestnames, strtolower(trim($salary[1][$j])));
    array_push($salaryarray, $salary[2][$j]);
}

for($i=0; $i<count($salaryarray); $i++){
    print($salaryarray[$i]);
    print($salarytestnames[$i]);
    print($playerarray[$i]);
    print($playerweight[$i]);
}


for($i=0; $i<count($salaryarray); $i++){
    if(in_array($salarytestnames[$i], $playerarray)){
        $id = array_search($salarytestnames[$i], $playerarray);
        print($id);
        $id = $id+1;
        $stmt = $dbh->prepare("UPDATE nbaplayers SET salary = ? WHERE id = ?");
        $stmt->execute(array($salaryarray[$i], $id));
    }
}

?>
