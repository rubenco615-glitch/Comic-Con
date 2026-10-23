<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comic-Con</title>
</head>
<body>

    <h1>Formulario de Registro - Comic-Con</h1>

    <form action="" method="POST">
        <p>
            <label for="nombre">Nombre completo:</label><br>
            <input type="text" id="nombre" name="nombre" required>
        </p>

        <p>
            <label for="edad">Edad:</label><br>
            <input type="number" id="edad" name="edad" min="0" required>
        </p>

        <p>
            <label for="tipo_entrada">Tipo de Entrada:</label><br>
            <select id="tipo_entrada" name="tipo_entrada" required>
                <option value="">-- Selecciona una opción --</option>
                <option value="General">General (disponible)</option>
                <option value="VIP">VIP (disponible)</option>
                <option value="Infantil">Infantil (disponible)</option>
            </select>
        </p>

        <p>
            <strong>Talleres Especiales:</strong><br>
            <input type="checkbox" id="videojuegos" name="talleres[]" value="Torneo de Videojuegos">
            <label for="videojuegos">Torneo de Videojuegos (10 euros)</label><br>

            <input type="checkbox" id="cosplay" name="talleres[]" value="Taller de Cosplay">
            <label for="cosplay">Taller de Cosplay (15 euros)</label><br>

            <input type="checkbox" id="rol" name="talleres[]" value="Zona de Rol en Vivo">
            <label for="rol">Zona de Rol en Vivo (5 euros)</label>
        </p>
        <p>
            <input type="submit" value="Enviar">
        </p>
    </form>

</body>
</html>
