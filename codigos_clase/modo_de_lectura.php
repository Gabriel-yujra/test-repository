<?php
$nombre_archivo = "registro.txt";
$lineas = [];
$manejador_archivo = fopen($nombre_archivo, "r");
if($manejador_archivo){
    echo "<h2>Lectura linea por linea de '$nombre_archivo'</h2>";
    echo "<ul>";
    while (!feof($manejador_archivo)){
        $linea = trin(fgets($manejador_archivo));

        if(!empty($linea)){
            $lineas[] = $linea;
            echo "<li>{$linea}</li>";
        }
    }
echo "</ul>";
fclose($manejador_archivo);
echo "<p>Total de lineas leidas y guardadas en array: ".count($lineas)."</p>";
}else{
    echo "Error: No se pudo abrir el archivo '$nombre_archivo'.";
}
?>