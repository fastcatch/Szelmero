c:\Downloads\UnxUtils\usr\local\wbin\gawk.exe -F, "{ print substr($1,1,4) \"-\" substr($1,5,2) \"-\" substr($1,7,2) \" \" substr($1,9,2) \":\" substr($1,11,2) \",\" $2 \",\" $3 }" pelda_valasz_ver_B.txt
pause
