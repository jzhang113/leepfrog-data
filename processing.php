<?php
date_default_timezone_set("UTC");

echo "<head>";
echo "<script src = \"https://d3js.org/d3.v4.min.js\"></script>";
echo "<script src = \"leepfrog.js\"></script>";
echo "</head>";

$file = fopen ("majorcourses.txt", "r");
$data = fread ($file, filesize ("majorcourses.txt"));
fclose ($file);

$classlist = explode ("\n", $data);
$courses = [];

foreach ($classlist as $class) {
    if ($class != null) {
        $parts = explode ("|", $class);
        $courses[] = [substr ($parts[0], 0, 9), $parts[1], $parts[2]]; // Remove section labels, if any
    }
}

$courseCount = count ($courses);
$majorCount = [];

for ($i = 0; $i < $courseCount; $i++) {
    $subCount = 0;
    
    while ($i + 1 < $courseCount && $courses[$i][1] == $courses[$i + 1][1]) {
        $i++;
        $subCount++;
    }
    
    $majorCount[$courses[$i][1]] = $subCount + 1;
}

usort ($courses, function ($a, $b) {
    $cmp = strcmp ($a[0], $b[0]);
    
    if ($cmp == 0) {
        return strcmp ($a[1], $b[1]);
    } else
        return $cmp;
});

for ($i = 0; $i < $courseCount - 1; $i++) {
    if ($courses[$i][0] == $courses[$i + 1][0] && $courses[$i][1] == $courses[$i + 1][1]) {
        unset ($courses[$i]);
    }
}

$courses = array_values($courses);

$courseCount = count ($courses);
$countArray = [];
$row = [];

while ($test = current ($majorCount)) {
    $row[key ($majorCount)] = 0;
    next ($majorCount);
}

reset ($majorCount);

while ($test = current ($majorCount)) {
    $countArray[key ($majorCount)] = $row;
    next ($majorCount);
}

for ($i = 0; $i < $courseCount - 1; $i++) {
    $tempList = [];
    
    while ($i + 1 < $courseCount && $courses[$i][0] == $courses[$i + 1][0]) {
        $tempList[] = $courses[$i][1];
        $i++;
    }

    if ($courses[$i][0] == $courses[$i - 1][0])
        $tempList[] = $courses[$i][1];

    if (count ($tempList) == 0) // unique classes
        $countArray[$courses[$i][1]][$courses[$i][1]]++;

    for ($j = 0; $j < count ($tempList) - 1; $j++) {
        for ($k = $j + 1; $k < count ($tempList); $k++) {
            $countArray[$tempList[$j]][$tempList[$k]]++;
            $countArray[$tempList[$k]][$tempList[$j]]++;
        }
    }
}

$pass = json_encode ($countArray);

echo "<svg></svg>";
?>