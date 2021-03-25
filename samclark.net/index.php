<!DOCTYPE html>

<html lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="./sam-files/sam-styles.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Samuel Clark - Demographer</title>
</head>

<body>

<br>

<div class="sam-title">
Samuel Clark - Demographer
</div>

<br>

<? include "./sam-files/includes/main-links.html"; ?>

<br>

<div>
<img src="./sam-files/sam.jpeg" alt="Sam">
</div>

<br>

<div class="sam-center-block">

<p>
  I am a demographer, epidemiologist, and aspiring statistician who develops new methods and investigates population dynamics and epidemiology in low- and middle-income countries, with a focus in Africa. 
</p>

<p>
  I work as a <a href="https://sociology.osu.edu/people/clark.2962" target="_blank">professor</a> in the <a href="https://sociology.osu.edu" target="_blank">Department of Sociology</a> at <a href="https://www.osu.edu" target="_blank">The Ohio State University</a> (OSU), and I am a faculty affiliate of the <a href="https://ipr.osu.edu" target="_blank">Institute for Population Research</a> and the <a href="https://tdai.osu.edu" target="_blank">Translational Data Analytics Institute</a>, both at OSU. I am affiliated with the <a href="https://stat.uw.edu/" target="_blank">Department of Statistics</a> at the University of Washington and the <a href="https://www.wits.ac.za/publichealth/" target="_blank">School of Public Health</a> at the University of the Witwatersrand. 
</p>

<p>
  Most recently I have been working on a survey to estimate the prevalence of the coronavirus and an excess deaths study to characterize the total burden of mortality associated with the COVID-19 epidemic - both in the state of Ohio, USA.
</p>

<p>
  On an ongoing basis I develop mortality models and work to improve: 1) verbal autopsy as a tool to measure burden of disease, 2) indirect estimates of child mortality, and 3) small-area estimates of mortality. I also coordinate a small team developing software to implement new methods - mostly for verbal autopsy and mathematical models of human mortality.
</p>

</div>

<br>

<? include "./sam-files/includes/main-links.html"; ?>

<br>
   
<div class="sam-note" style="line-height: 20px">
Page loads since 2020-05-06: 
<strong>
<?php
    $hits = intval(file_get_contents('counter.txt'));
    file_put_contents('counter.txt',$hits+1);
    echo $hits;
?>
</strong> 
</div>

<div class="sam-note">
<!-- UPDATE THIS -->
Updated 2021-03-10
<!-- UPDATE THIS -->
</div>

<br>

</body>

</html>

<!-- 
Check links with https://www.deadlinkchecker.com/website-dead-link-checker.asp
Check HTML with https://validator.w3.org/nu/?doc=http%3A%2F%2Fsamclark.net%2F
 -->