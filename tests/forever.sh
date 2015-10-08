#Could move over previous data file but let's not as other processes could be reading the data file
#d=$(date +%y%m%d%H%M%S)
#mv -f data.txt "data-$d.txt" 2>/dev/null
while :
do
	date
	./gomidi >> data.txt
done
