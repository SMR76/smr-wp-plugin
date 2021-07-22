echo off
:: set argumants as domain and locale if argumaint exist
if "%1" == "" ( set domain=smr-plugin) else ( set domain=%1 )
if "%2" == "" ( set locale=fa_IR)      else ( set locale=%2 )
:: create .pot file
xgettext --from-code=UTF-8 --add-comments -d "%domain%"  *.php  -o %domain%.pot
:: create local directory if needed
if not exist locale/%locale%/LC_MESSAGES/ ( mkdir "locale/%locale%/LC_MESSAGES/" && echo "-- created (locale/%locale%/LC_MESSAGES/) --" )
:: create .po files
msginit --locale %locale% --input %domain%.pot --output locale/%locale%/LC_MESSAGES/%domain%.po
:: remove .pot file in the base directory.
del %domain%.pot

echo "-- please translate .po files and continue --"
pause

:: convert .po files into .mo files
msgfmt "locale/%locale%/LC_MESSAGES/%domain%.po" --output-file="locale/%locale%/LC_MESSAGES/%domain%.mo"

echo "-- done --"