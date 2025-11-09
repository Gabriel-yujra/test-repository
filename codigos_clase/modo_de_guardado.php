<?php
$dato = "Ejemplificado TAW-251 .\n";

$nombre_archivo = "registro_datos.txt";
$manejador_archivo = fopen($nombre_archivo, "a");
if($manejador_archivo){
    $bytes_escritos = fwrite($manejador_archivo, $dato);
    fclose($manejador_archivo);
    echo "Dato guardado correctamente en ".$nombre_archivo.". Se escribieron $bytes_escritos bytes.";
}else{
    echo "Error al abrir el archivo '$nombre_archivo'. verifica los permisos de escritura.";
}
?>