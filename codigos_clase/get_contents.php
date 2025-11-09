<?php
$nombre_archivo = "ejemplo_TAW.txt";

if(file_exists($nombre_archivo)){
    $contenido = file_get_contents($nombre_archivo);
    echo "<h2>leyendo el archivo' . $nombre_archivo . ' </h2>";
    echo "<pre>".htmlspecialchars($contenido)."</pre>";
}else{
    echo "error: el archivo ' . $nombre_archivo . ' no existe.";
}
?>