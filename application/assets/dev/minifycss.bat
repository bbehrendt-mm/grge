@ECHO OFF
cd css
copy /b "*.css" "temp.css"
move /Y "temp.css" "../temp.css"
cd ..
java -jar yuicompressor-2.4.8.jar -o zombvival.base.min.css temp.css
del temp.css
move /Y zombvival.base.min.css "../css/zombvival.base.min.css"