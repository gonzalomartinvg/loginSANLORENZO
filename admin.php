<?php 

//COSAS IMPORTANTES:

	//1) El script que modifica las clases de los elementos que cambian dependiendo de si el usuario habilitó o no el carnet, se ejecuta al final, ya que se da un proceso asíncrono que si se ejecuta al principio, no llega a modificarase. Es necesario ejecutarlo al final del html y con la función: "DOMContentLoaded" que quiere decir que se ejecuta cada vez que se termina de cargar y analizar el html.
    
    //2) SALIR DE SESIÓN ESTÁ CONFIGURADO EN salir.php y en el botón de salir, hay un link que lleva a salir.php que cierra sesión y te lleva a index.php Ver donde dice "SALIR GONZA"
	
	include("conexion.php");

	//Si no existe la sesión, devuelve al index.php
	session_start();
	if (!isset($_SESSION['id_usuario'])){
		header("Location: index.php");
	}
	$iduser = $_SESSION['id_usuario']; //La variable $_SESSION almacena el número de usuario (la clave primaria).
	$sql = "SELECT idusuarios, Nombre, Habilitado FROM socios WHERE idusuarios = '$iduser'"; //Con el número de usuario que almacenamos del inicio de sesión, hacemos una consulta para obtener otros datos, como el nombre, el idusuarios y si esta habilitado o no.
	$resultado = $conexion->query($sql); //Ejecutamos la consulta y metemos el resultado en la variable $resultado
	$row = $resultado->fetch_assoc(); //Introducimos en row la información a la aque accedimos. Luego se podrá imprimir y utilizar con la siguiente estructura: Si queremos acceder a la habilitación: "$row['Habilitado']" / Si queremos acceder al nombre: "$row['Nombre']", etc.

    //CLICK EN HABILITAR

        // Definimos la variable nuevoEstado fuera del bloque if para que sea accesible en el JavaScript del final.
    $nuevoEstado = $row['Habilitado']; //El $nuevoEstado, toma la información del usuario que se recupero con la consulta que hizo $sql y que se metió dentro de $row mediante fetch_assoc().

    if (isset($_POST["habilitar"])) {
        // Verificar el estado actual de habilitación del usuario
        if ($row['Habilitado'] == "0") {
            $nuevoEstado = '1';
            $mensaje = 'El usuario fue habilitado';
        } else {
            $nuevoEstado = '0';
            $mensaje = 'El usuario fue deshabilitado';
        }

        // Actualizar el estado de habilitación del usuario en la base de datos
        $sqlusuario = "UPDATE socios SET Habilitado = '$nuevoEstado' WHERE idusuarios = '$iduser'";

        // Ejecutar la consulta
        $resultadousuario = $conexion->query($sqlusuario);

        if (!$resultadousuario) {
            echo "<script>
                    alert('Error al actualizar el estado del usuario');
                    window.location = 'admin.php';
                    </script>";
        }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro - Mi CASLA</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
        <main>

            <div class="contenedor__todo">

                <!--Info partido-->
                <div class="caja__trasera">
                    <div class="caja__trasera-register">
                        <div id="contenedor-estado" class="contenedor-estado">
                            <h2 id="estado" class="estado">Deshabilitado</h2>
                        </div>
        
                        <div class="contenedor-partido">
                            <img class="escudo" src="assets/images/escudo.png" alt="">
                            <h3>VS</h3>
                            <img class="escudo escudo2" src="assets/images/escudo-huracan.png" alt="">
                        </div>

                        <div class="contenedor-fecha">
                            <h2>15 de febrero</h2>
                        </div>
                    </div>
                </div>

                <!--Bienvenida y habilitación-->
                <div class="contenedor-bienvenida">
                    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST"> <!--IMPORTANTÍSIMO SETEAR ESTO, SI NO, NO FUNCIONA-->
                        <h2>Bienvenid@ <?php echo utf8_decode($row['Nombre']);	?></h2>
                        <h3>¿List@ para ir a alentar al Ciclón?</h3>
                        <div class="contenedor-boton-escudo">
                            <button class="button-habilitar" type="submit" name="habilitar" id="botonhabilitar">Habilitar carnet</button> <!-- "GONZALO INDICADOR 1" IMPORTANTE el name en el button para luego llamar el evento y que cuando se clickee se modifique la base de datos-->
                        </div>
                        <a class="cerrar-sesion" href="salir.php">Cerrar sesión</a>
                    </form>
                </div>
            </div>

        </main>

        <script>
            // Este script se ejecutará siempre luego de imprimir el html y analizarlo. Se coloca acá para que funcione.
            document.addEventListener('DOMContentLoaded', function() {
                var contenedorEstado = document.getElementById('contenedor-estado');
                var estadoElement = document.getElementById('estado');
                var estadoBoton = document.getElementById('botonhabilitar');
                
                if (contenedorEstado && estadoElement && '<?php echo $nuevoEstado; ?>' !== '') {
                    contenedorEstado.classList.remove('contenedor-estado', 'contenedor-estado-habilitado');
                    contenedorEstado.classList.add('<?php echo $nuevoEstado; ?>' === '1' ? 'contenedor-estado-habilitado' : 'contenedor-estado');
                    
                    // Cambiar el contenido del h2 con id "estado"
                    estadoElement.innerText = '<?php echo $nuevoEstado; ?>' === '1' ? 'Habilitado' : 'Deshabilitado';

                    // Cambiar el contenido del button con id "botonhabilitar"
                    estadoBoton.innerText = '<?php echo $nuevoEstado; ?>' === '1' ? 'Deshabilitar carnet' : 'Habilitar carnet';
                }
            });
        </script>
</body>
</html>
