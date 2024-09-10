<?php
$dbh = new PDO('mysql:host=localhost;dbname=nations', 'nations', 'nations!');
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
$stmt = $dbh->prepare("select weight, salary from nbaplayers");
$stmt->execute();

$dict = array();
$temp = array();
$final = array();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if(is_null($row['salary'])){
        continue;
    }
    if(array_key_exists($row['weight'], $dict) and !array_key_exists($row['weight'], $temp)){
        $temp[$row['weight']] = 1;
        $dict[$row['weight']] += $row['salary'];
    } elseif (array_key_exists($row['weight'], $dict) and array_key_exists($row['weight'], $temp)){
        $temp[$row['weight']]++;
        $dict[$row['weight']] += $row['salary'];
    }
    $dict[$row['weight']] = $row['salary'];
}

ksort($dict);

var_dump($dict);
print('<br>' . '<br>');
var_dump($temp);


foreach($temp as $tweight => $tnum){
    $dict[$tweight] = $dict[$tweight] / $tnum;
}
ksort($dict);

print('<br>' . '<br>');
var_dump($dict);

foreach($dict as $finalweight => $avgsalary){
    $stmt = $dbh->prepare("insert into nbamapdata (weight, salary) values (?, ?)");
    $stmt->execute(array($finalweight, $avgsalary));
}

?>
