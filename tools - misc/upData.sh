#! /bin/sh
echo "Uploading samclark.net ..."
export SSHPASS=R5Jh5^Bv
# for -oIPQoS=none see https://bbs.archlinux.org/viewtopic.php?id=251156
# Use a "here" document within '!'
sshpass -e sftp -oIPQoS=none -oBatchMode=no -b - dh_7gfp82@samclark.net << !
   cd samclark.net
   #used to upload to samclark.net root because OSU Soc site linked there; doesn't now
   #put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/Samuel-Clark_CV.pdf" Samuel-Clark_CV.pdf
   cd sam-files
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/Sam-Clark-Publications.zip" Sam-Clark-Publications.zip
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/Samuel-Clark_CV.pdf" Samuel-Clark_CV.pdf
   put "/Users/samueljclark/Documents/GitHub/samclark.net/samclark.net/sam-files/sam.jpeg" sam.jpeg
   bye
!
# End of "here" document
echo "samclark.net html uploaded"


