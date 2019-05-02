<?php
//INPUTS

$syllableFile = file('syllables.txt');
$json = file('example_json.txt'); //Currently needs a file in the same directory

//POPULATE NAME START AND END ARRAYS
function init($syllableFile) {
    $starts = array();
    $ends = array();
    $out = array($starts, $ends);
    foreach ($syllableFile as $i => $syl) {
        $firstLetter = substr($syllableFile[$i], 0, 1);
        if (ctype_upper($firstLetter) == true) {
            array_push($out[0], preg_replace("/\r|\n/", "", $syl));
        } else {
            array_push($out[1], preg_replace("/\r|\n/", "", $syl));
        }
    }
    return $out;
}

//EXTRACT UIDS FROM RECEIVED JSON, DUMPS INTO ARRAY
function getUids($json) {
    $json = json_decode($json[0], true);
    $gpus = $json['gpu'];
    $uids = [];
    foreach ($gpus as $i => $gpu) {
        $uids[$i] = $gpu['GpuId'];
    }
    return $uids;
}

//SOME EXTRA RANDOMNESS
function coinflip() {
    $result = mt_rand(0, 1);
    return $result;
}

function hyphenOrSpace() {
    if (coinflip() == 0) {
        return " ";
    } else {
        return "-";
    }
}

function nameLength($minSyllables, $coinflips) {
    $extraSyl = 0; 
    for ($i = 0; $i < $coinflips; $i++) {
        if (coinflip() == 0) {
            $extraSyl += 0;
        } else {
            $extraSyl += 1;
        }
    }
    return $minSyllables + $extraSyl;
}

//NAME GENERATION
function generateFirst($starts, $ends) {
    $startsLen = count($starts);
    $endsLen = count($ends);
    $syllableCount = nameLength(1, 1);
    $firstName = $starts[mt_rand(0, ($startsLen - 1))];
    if ($syllableCount != 1) {
        $spacing = hyphenOrSpace();
        $firstName = $firstName . $spacing . ucfirst($ends[mt_rand(0, ($endsLen - 1))]);
    }
    return $firstName;
}

function generateLast($ends) {
    $endsLen = count($ends);
    $syllableCount = nameLength(0, 3);
    $lastName = "";
    for ($i = 0; $i < $syllableCount; $i++) {
        if ($i == 0) {
            $lastName = $lastName . ucfirst($ends[mt_rand(0, ($endsLen - 1))]);
        } else {
            $lastName = $lastName . $ends[mt_rand(0, ($endsLen - 1))];
        }
    }
    return $lastName;
}

function generateName($starts, $ends) {
    $firstName = generateFirst($starts, $ends);
    $lastName = generateLast($ends);
    return $firstName . " " . $lastName;
}
/*
function updateNameList($nameList, $nameToPush) {
    array_push($nameList, $nameToPush);
}
*/

//MAIN FUNC
function main($syllableFile, $json) {
    $syllables = init($syllableFile);
    $uids = getUids($json);
    $starts = $syllables[0];
    $ends = $syllables[1];
    $out = array();
    foreach ($uids as $uid) {
        $out[$uid] = generateName($starts, $ends);
        //updateNameList($names, $name);
    }
    return $out;
}

?>
