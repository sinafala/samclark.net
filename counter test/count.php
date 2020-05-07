<!DOCTYPE html>

<html lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="./sam-files/sam-styles.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Samuel Clark - Demographer</title>
</head>

<body>


<?php
$hits = intval(file_get_contents('counter.txt'));
file_put_contents('counter.txt',$hits+1);
echo $hits;
?>


</body>

</html>