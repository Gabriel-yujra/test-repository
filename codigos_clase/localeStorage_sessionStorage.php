<?php
$dato_servidor = "Mensaje generado por PHP el " . date("H:i:s");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ejemplo de Almacenamiento Web</title>
</head>
<body>
    <h1>Almacenamiento Local (localStorage)</h1>
    <p>Trabajado con JavaScript y no asi con PHP</p>
    <input type="text" id="nuevoDato" placeholder="Escribe algo aqui">
    <button onclick="guardarDato()">Guardar en LocalStorage</button>
    <hr>
    <p>Dato del Servidor (PHP): <b id="phpDato"><?php echo $dato_servidor;?></b></p>
    <p>Dato Guardado en LocalStorage: <b id="localStorageDato"></b></p>
    <p>Dato Guardado en SessionStorage: <b id="datoSessionStorage"></b></p>
    <script>
        const CLAVE_LOCAL = "nombre_usuario";
        const CLAVE_SESSION = "ultimaVisita";
        function guardarDato(){
            const valor = document.getElementById("nuevoDato").value;
            if(valor){
                localStorage.setItem(CLAVE_LOCAL, valor);
                alert('Dato '${valor}' guardado en localStorage.');
                mostrarDatos();
            }
        }
        function mostrarDatos(){
            const localData = localStorage.getItem(CLAVE_LOCAL);
            document.getElementById('datoLocalStorage').textContent = localData ? localData : "Aun no hay datos guardados.";
            const sessionData = sessionStorage.getItem(CLAVE_SESSION);
            document.getElementById('datoSessionStorage').textContent = sessionData ? sessionData : "No hay datos de sesion.";
        }

        sessionStorage.setItem(CLAVE_SESSION, "Pagina visitada a las: " + new Date().toLocaleTimeString());
        mostrarDatos();
    </script>
</body>
</html>