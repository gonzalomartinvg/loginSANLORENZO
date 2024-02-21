<?php 

include("conexion.php");

session_start(); //Si la sesión esta iniciada, lleva al usuario directamente a dentro del sistema
	if (isset($_SESSION['id_usuario'])){ 
		header("Location: admin.php");
	}

    //Login

	if (isset($_POST["login"])){ //Si se clickea en el botón entrar sucede lo siguiente. "Login" es el name del botón.

		//A) Obtener lo que el usuario colocó en los INPUTS de login

		$socio = mysqli_real_escape_string($conexion, $_POST['socio']); //Esto toma la información que el usuario coloco en el input y lo mete en una variable
		$password = mysqli_real_escape_string($conexion, $_POST['pass']); //Esto toma la información que el usuario coloco en el input y lo mete en una variable

		$password_encriptada = sha1($password); //Este no es un dato en si, es la encriptación de la clave para otorgar más seguridad.

		//B) Consultar si socio y la contraseña existen en la base de datos y qué hacer en cada caso.

		$sql = "SELECT idusuarios FROM socios WHERE Socio = '$socio' AND password= '$password_encriptada'"; //Consulta sql

		$resultado = $conexion->query($sql); // ejecuta la consulta en la base de datos a través de la conexion establecida y mediante el método "query" que es un método de consulta
		$rows = $resultado->num_rows;

		if ($rows > 0){
			$row = $resultado->fetch_assoc();
			$_SESSION['id_usuario'] = $row['idusuarios'];
			header("Location: admin.php"); //Indicamos a qué página queremos que acceda luego de hacer el login.
		} else{
			echo "<script>
				alert('El usuario o la contraseña son incorrectos')
				window.location = 'index.php';
			</script>";
		}

	}

    //Registrar usuario: Si se clickea el botón de registrar, se dispara una función que toma los datos a registrar. Cada input tiene un "name" que reconoce el tipo de dato, ver en "GONZALO INDICADOR 2"

	if (isset($_POST["registrar"])){ //Si se clickea en el botón registrar sucede lo siguiente. "Registrar" es el name del botón. Ver donde dice "GONZALO INDICADOR 3"

		//A) Obtener lo que el usuario colocó en los INPUTS de registro

		$nombre = mysqli_real_escape_string($conexion, $_POST['nombre']); //Esto toma la información que el usuario coloco en el input y lo mete en una variable
		$correo = mysqli_real_escape_string($conexion, $_POST['correo']); //Esto toma la información que el usuario coloco en el input y lo mete en una variable
		$socio = mysqli_real_escape_string($conexion, $_POST['socio']); //Esto toma la información que el usuario coloco en el input y lo mete en una variable
		$password = mysqli_real_escape_string($conexion, $_POST['pass']); //Esto toma la información que el usuario coloco en el input y lo mete en una variable

		$password_encriptada = sha1($password); //Este no es un dato en si, es la encriptación de la clave para otorgar más seguridad.

		//B) Consultar si el usuario o el mail ya están registrado en la Base de Datos

		$sqluser = "SELECT idusuarios FROM socios WHERE socio = '$socio'"; //Con esto, hacemos una consulta a la base de datos donde se verifica si ya existe el socio para no crear uno duplicado y seleccionamos el idusuarios.
		$resultadouser = $conexion->query($sqluser);
		$filasusuario = $resultadouser->num_rows;

		$sqlcorreo = "SELECT idusuarios FROM socios WHERE correo = '$correo'"; //Con esto, hacemos una consulta a la base de datos donde se verifica si ya existe el mail para no crear un usuario con mail duplicado.

		$resultadocorreo = $conexion->query($sqlcorreo);
		$filascorreo = $resultadocorreo->num_rows;

		//C) Definir qué hacer si el usuario está o no registrado

		if ($filasusuario > 0 || $filascorreo > 0){
			echo "<script>
				alert('El número de socio o el mail utilizado ya existen')
				window.location = 'index.php';
			</script>";
		} else{
			//Insertar información del usuario en la base de datos
			$sqlusuario = "INSERT INTO socios(Nombre,Correo,Socio,Password) 
			VALUES('$nombre' , '$correo', '$socio' , '$password_encriptada') ";

			$resultadousuario = $conexion->query($sqlusuario);
			if($resultadousuario > 0){
				echo "<script>
					alert('Registro existoso')
					window.location = 'index.php';
				</script>";
			}else{
				echo "<script>
					alert('Error al registrarse')
					window.location = 'index.php';
				</script>";
			}
		}

	}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro - Mi CASLA</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro Mi CASLA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

        <main>

            <div class="contenedor__todo">
                <div class="caja__trasera">
                    <div class="caja__trasera-login">
                        <h3>¿Ya tenés una cuenta?</h3>
                        <p>Iniciá sesión para entrar en la página</p>
                        <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                    </div>
                    <div class="caja__trasera-register">
                        <h3>¿Aún no tenés una cuenta?</h3>
                        <p>Regístrate para que puedas iniciar sesión</p>
                        <button id="btn__registrarse">Registrarse</button>
                    </div>
                </div>

                <!--Formulario de Login y registro-->
                <div class="contenedor__login-register">
                    <!--Login-->
                    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" class="formulario__login"> <!--Importante configurar "action" y "method"-->
                        <h2>Iniciar Sesión</h2>
                        <input type="text" placeholder="Número de socio" name="socio"> <!--Importante configurar el "name" ya que vamos a obtener lo que se coloca en el input por acá-->
                        <input type="password" placeholder="Contraseña" name="pass"> <!--Importante configurar el "name" ya que vamos a obtener lo que se coloca en el input por acá-->
                        <div class="contenedor-boton-escudo">
                            <button type="submit" name="login">Entrar</button>
                            <img class="escudo" src="assets/images/escudo.png" alt="">
                        </div>
                    </form>

                    <!--Register-->
                    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" class="formulario__register"> <!--Importante configurar "action" y "method"-->
                        <h2>Registrarse</h2> 
                        <input type="text" placeholder="Nombre completo" name="nombre">
                        <input type="text" placeholder="Correo Electronico" name="correo">
                        <input type="text" placeholder="Número de socio" name="socio">
                        <input type="password" placeholder="Contraseña" name="pass">
                        <div class="contenedor-boton-escudo">
                            <button type="submit" name="registrar">Regístrate</button> <!--GONZALO INDICADOR 3-->
                            <img class="escudo" src="assets/images/escudo.png" alt="">
                        </div>
                    </form>
                </div>
            </div>

        </main>

        <script src="assets/js/script.js"></script>
</body>
</html>
