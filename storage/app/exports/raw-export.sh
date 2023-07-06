#!/bin/bash
#
# sudo chown -R "$USER:" /path/to/the/directory


MONGO_MATCH_JSON=$1
MONGO_EXPORT_FIELDS=$2
CSV_FILE_PATH=$3
EXPORT_FILE_PATH=$4
EXPORT_FILE=$5
dbusername=$6
dbpassword=$7
dbdatabase=$8
dbhost=${9}
dbcollection=${10}

WDIR=$(pwd)

FILE_PREFIX=$(date +%Y_%m_%d)
EDATE=$(date +%Y-%m-%d)


echo "Export PARAMS Recieved\n"
echo "MONGO_MATCH_JSON : $MONGO_MATCH_JSON\n"
echo "MONGO_EXPORT_FIELDS : $MONGO_EXPORT_FIELDS\n"
echo "EXPORT_FILE_PATH : $EXPORT_FILE_PATH\n"
echo "CSV_FILE_PATH : $CSV_FILE_PATH\n"
echo "EXPORT_FILE : $EXPORT_FILE\n"
echo "dbusername : $dbusername\n"
echo "dbpassword : $dbpassword\n"
echo "dbdatabase : $dbdatabase\n"
echo "dbhost : $dbhost\n"
echo "dbcollection : $dbcollection\n"
echo "\n"



cond="$MONGO_MATCH_JSON"




#JSON EXPORT
#CMD="/home/vijith/mongodb/mongodb-linux-x86_64-2.6.10/bin/mongoexport -h '${dbhost}' -u $dbusername --db $dbdatabase -p '${dbpassword}'  --collection ${dbcollection} --query '${cond}' --sort '{"_id": -1}' --fields  '${MONGO_EXPORT_FIELDS}' --csv --out '${CSV_FILE_PATH}'  " 
CMD="mongoexport -h '${dbhost}' -u $dbusername --db $dbdatabase -p '${dbpassword}'  --collection ${dbcollection} --query '${cond}' --sort '{"_id": -1}' --fields  '${MONGO_EXPORT_FIELDS}' --csv --out '${CSV_FILE_PATH}'  " 
#CSV
# CMD="mongoexport -h $dbhost -u $dbuser --db $dbname -p '${dbpassword}'  --collection ${dbcollection} --query '${cond}' --sort '{"_id": -1}'  --fields  '${dbfields}' --csv --out 'RAW.csv'  " 
echo $CMD;
eval $CMD;


cat "$EXPORT_FILE_PATH/$EXPORT_FILE.csv" | wc -l > "$EXPORT_FILE_PATH/COUNT_$EXPORT_FILE.txt"

chmod 777 -R "$EXPORT_FILE_PATH/$EXPORT_FILE.csv"
chmod 777 -R "$EXPORT_FILE_PATH/"


# sed -i "s/ActualStoreId/DealerCode/g" "$EXPORT_FILE_PATH/$EXPORT_FILE.csv"
sed -i "s/CallType/Call_T_y_p_e/g" "$EXPORT_FILE_PATH/$EXPORT_FILE.csv"
sed -i "s/TrackingNumber/VirtualNumber/g" "$EXPORT_FILE_PATH/$EXPORT_FILE.csv"
sed -i "s/CallerId/CustomerNumber/g" "$EXPORT_FILE_PATH/$EXPORT_FILE.csv"
# sed -i "s/Store/Dealer/g" "$EXPORT_FILE_PATH/$EXPORT_FILE.csv"

sed -i "s/Call_T_y_p_e/CallType/g" "$EXPORT_FILE_PATH/$EXPORT_FILE.csv"

echo "\n Zipping csv records ....\n"

echo "\n zip -r $EXPORT_FILE.zip $EXPORT_FILE.csv \n"

zip -r "$EXPORT_FILE.zip" "$EXPORT_FILE.csv"

chmod 755 -R "$EXPORT_FILE_PATH/$EXPORT_FILE.zip"


echo "\n Removing tmp records ....\n"

rm -rf "$EXPORT_FILE_PATH/$EXPORT_FILE.csv"

rm -rf "$EXPORT_FILE_PATH/COUNT_${EXPORT_FILE}.txt"
rm -rf "$EXPORT_FILE_PATH/${EXPORT_FILE}.txt"

echo "\n Export process completed \n"