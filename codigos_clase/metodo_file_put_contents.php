<?php
$guarda_d = "TAW-251";
$nombre_archivo = "ejemplo_TAW.txt";

$resultado = file_put_contents($nombre_archivo, $guarda_d . "\n" . $guarda_d . "\n", FILE_APPEND);
if($resultado !== false){
    echo "Dato guardado correctamente en ".$nombre_archivo.". Se escribieron $resultado bytes.";
}else{
    echo "Error al guardar el dato.";
}
?>