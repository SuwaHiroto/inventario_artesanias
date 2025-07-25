<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inicio de Sesión</title>
  <link rel="stylesheet" href="/public/css/login.css" />
</head>
<body>
  <main class="contenido-login">
  <div class="cuadro">
      <h2></h2>
      <br />
      <h2>INICIO DE SESIÓN</h2>
        <div id="mensajeError" class="error"></div>
<script>
  const params = new URLSearchParams(window.location.search);
  if (params.get("error") === "1") {
    document.getElementById("mensajeError").innerHTML = "<h4 style='color:red; text-align:center;'>Usuario o contraseña incorrectos.</h4>";
  }
</script>
      <form action="/app/Controllers/ValidacionController.php" method="post">
        <div class="cuadro-peque">
          <div class="input">
            <input type="text" name="usuario" required />
            <label>
              <h3>Usuario</h3>
            </label>
          </div>
          <div class="input">
            <input type="password" name="contrasena" required />
            <label>
              <h3>Contraseña</h3>
            </label>
          </div>
          <button type="submit">
            <h3>Iniciar Sesión</h3>
          </button>
        </div>
      </form>
  </div>
  </main>
</body>
</html>