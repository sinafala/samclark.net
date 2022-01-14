<!DOCTYPE html>

<html lang="en">

<head>
  <meta name="author" content="Samuel Clark">
  <meta name="description" content="Samuel Clark is a demographer, epidemiologist, and data scientist who develops new methods and investigates population dynamics and epidemiology.">
  <meta name="keywords" content="Samuel Clark, demography, demographer, epidemiology, epidemiologistc, statistics, statistician, africa, methods, verbal autopsy, Ohio State University, University of Washington, Penn, Caltech, Ohio">
  <? include "./sam-files/includes/header.html"; ?>
  <title>Samuel Clark - Demographer</title>
</head>

<body>

<br>

<div class="sam-title">
  Samuel J. Clark
</div>

<br>

<? include "./sam-files/includes/main-links.html"; ?>

<br>

<div>
<a href="mailto:sam@samclark.net"><img title="Sam Clark" src="./sam-files/sam.jpeg"></a>
</div>

<br>

<div class="sam-block">

<p>
  I am a demographer, epidemiologist, and data scientist who develops new methods and investigates population dynamics and epidemiology, primarily in low- and middle-income countries, with a focus in Africa. 
</p>

<p>
  I work as a <a href="https://sociology.osu.edu/people/clark.2962" target="_blank">professor</a> in the <a href="https://sociology.osu.edu" target="_blank">Department of Sociology</a> at <a href="https://www.osu.edu" target="_blank">The Ohio State University</a> (OSU), and I am a faculty affiliate of the <a href="https://ipr.osu.edu" target="_blank">Institute for Population Research</a> and the <a href="https://tdai.osu.edu" target="_blank">Translational Data Analytics Institute</a>, both at OSU. I am affiliated with the <a href="https://stat.uw.edu/" target="_blank">Department of Statistics</a> at the University of Washington and the <a href="https://www.wits.ac.za/publichealth/" target="_blank">School of Public Health</a> at the University of the Witwatersrand. 
</p>

<p>
  During 2020 I worked on a survey to estimate the prevalence of the coronavirus and an excess deaths study to characterize the total burden of mortality associated with the COVID-19 epidemic. Both projects are in the state of Ohio, USA and were conducted by large teams at The Ohio State University in close coordination with the Ohio State Department of Health.
</p>

<p>
  On an ongoing basis I work to improve: 1) verbal autopsy as a tool to measure the burden of disease, 2) mathematical models of human mortality, 3) indirect estimates of child mortality, and 4) small-area estimates of mortality. I also coordinate a small team developing software to implement new methods - mostly for verbal autopsy and mathematical models of human mortality.
</p>

</div>

<br>

<? include "./sam-files/includes/main-links.html"; ?>

<br>

<div class="sam-note">
Page loads since 2020-05-06: 
<strong>
<?php
  $hits = intval(file_get_contents('counter.txt'));
  file_put_contents('counter.txt',$hits+1);
  echo ($hits+1);
  // append current hit counter to log
  $file = fopen('hit_log.txt', 'a'); // open hit log in append mode 
  // append timestamp and new hit value 
  fwrite($file, date('Y-m-d H:i:s').': '.($hits+1)."\n");
  fclose($file);
?>
</strong> 
</div>

<div class="sam-note">
  Copyright 1999-2022 by Samuel J. Clark; all rights reserved.
</div>

<div class="sam-note">
<!-- UPDATE THIS -->
Updated 2022-01-12
<!-- UPDATE THIS -->
</div>

<br>

</body>

</html>

<!-- 
Check links with https://www.deadlinkchecker.com/website-dead-link-checker.asp
Check HTML with https://validator.w3.org/nu/?doc=http%3A%2F%2Fsamclark.net%2F
 -->