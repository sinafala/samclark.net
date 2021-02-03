#! /bin/sh
echo "Uploading samclark.net ..."
export SSHPASS=R5Jh5^Bv
# Use a "here" document within '!'
sshpass -e sftp -oBatchMode=no -b - dh_7gfp82@samclark.net << !
   cd samclark.net
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/index.php" index.php
   cd sam-files
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/bibliography.html" bibliography.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/links.html" links.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects.html" projects.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/sam-styles.css" sam-styles.css
   cd projects
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/verbal-autopsy.html" verbal-autopsy.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/indirect-estimates.html" indirect-estimates.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/interviewer-effects.html" interviewer-effects.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/COVID-19.html" COVID-19.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/data-methods.html" data-methods.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/africa-epidemiology-demography.html" africa-epidemiology-demography.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/methods.html" methods.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/orphan-mortality.html" orphan-mortality.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/small-area-estimates.html" small-area-estimates.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/software.html" software.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/projects/misc.html" misc.html
   bye
!
# End of "here" document
echo "samclark.net html uploaded"

