<?php

require_once("/home/soporteweb/config.php");

myQUERY("alter table tiendas add tipoEtiquetadora varchar(20), add frescos varchar(2)",true);

myQUERY("CREATE TABLE Elementos IF NOT EXISTS (Tienda int(11) NOT NULL, Elemento varchar(20) NOT NULL, IP varchar(18) NOT NULL, Conexion tinyint(1) NOT NULL, Comentario varchar(100) NOT NULL, PRIMARY KEY (Tienda,Elemento) USING BTREE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",true);

myQUERY("create or replace view tmpTiendas as SELECT a.numerotienda, b.conexion, if(b.version is null,'NO DISPON.',b.version) as version,
a.centro, a.tipo, a.subtipo, a.direccion, a.poblacion, a.provincia, a.telefono, a.IP, a.tipoConexion, a.tipoEtiquetadora, a.frescos, IF(a.centro='SEDE',1,0) 'Pruebas', a.Pais FROM tiendas a LEFT JOIN Checks$Pais b ON a.numerotienda=b.tienda and b.caja=1",true);

myQUERY("CREATE OR REPLACE VIEW tmpTiendas_Total AS SELECT a.* ,SUM(case when b.Elemento like 'b%'  and b.conexion=1 then 1 else 0 end) as Balanzas ,SUM(case when b.Elemento like 'i%'  and b.conexion=1 then 1 else 0 end) as PCs ,SUM(case when b.Elemento like 'pc%' and b.conexion=1 then 1 else 0 end) as Impresoras FROM tmpTiendas a LEFT JOIN Elementos b ON a.numerotienda=b.tienda GROUP BY a.numerotienda",true);

?>