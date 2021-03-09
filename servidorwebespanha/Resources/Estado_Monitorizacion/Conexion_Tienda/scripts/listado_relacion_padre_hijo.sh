function LF() { echo "$1"; };
function MySQL() { mysql n2a -e "$1" | grep -v " row " | awk '{ if ($0 ~ /^: /) {print substr($0,3);} else {print $0}}'; };

MySQL "SELECT
	CONCAT('| ', LPAD(ITEM_PARENT_ID,10,' '),
		  ' | ', RPAD((SELECT DESCRIPTION FROM ITEM WHERE ITEM_PARENT_ID=ITEM_ID),20,' '),
		  ' | ', LPAD(ITEM_CHILD_ID,10,' '),
		  ' | ', RPAD((SELECT DESCRIPTION FROM ITEM WHERE ITEM_ID=ITEM_CHILD_ID),20,' '),
		  ' |'
	)
'+------------+----------------------+-----------+-----------------------+
| Cod. Padre | Descripcion Padre    | Cod. Hijo | Descripcion Hijo      |
+------------+----------------------+-----------+-----------------------+' 
	FROM ITEM_ESCANDALLOS_REL";
LF "+------------+----------------------+-----------+-----------------------+";