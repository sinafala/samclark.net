#! /bin/sh
echo Uploading samclark.net ...
export SSHPASS=R5Jh5^Bv
# Use a "here" document within '!'
sshpass -e sftp -oBatchMode=no -b - dh_7gfp82@carousel.dreamhost.com << !
   cd samclark.net
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/ Development/samclark.net/index.html" index.html
   cd sam-files
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/ Development/samclark.net/sam-files/bibliography.html" bibliography.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/ Development/samclark.net/sam-files/links.html" links.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/ Development/samclark.net/sam-files/projects.html" projects.html
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/ Development/samclark.net/sam-files/sam-styles.css" sam-styles.css
   cd projects
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/ Development/samclark.net/sam-files/projects/verbal-autopsy.html" verbal-autopsy.html
   bye
!
# End of "here" document
echo samclark.net html uploaded
