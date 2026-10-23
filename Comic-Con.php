<?php
declare(strict_types=1);

//los errores para ver si falla algo mientras programamos
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// variables para guardar los mensajes y los datos que luego pintamos
$mensajeError = "";
$mensajeExito = "";
$exito = false;
$datosResguardo = [];

// funcion para sacar el precio base segun la entrada
function calcularPrecioBase(string $tipo): int
{
    switch ($tipo) {
        case 'General':
            return 30;
        case 'VIP':
            return 60;
        case 'Infantil':
            return 10;
        default:
            throw new Exception("El tipo de entrada seleccionado no es válido.");
    }
}

// funcion principal para validar los datos y calcular el precio final
function procesarInscripcion(string $nombre, int $edad, string $tipoEntrada, array $talleresSeleccionados): array
{
    // la edad no puede ser 0 ni negativa
    if ($edad <= 0) {
        throw new Exception("La edad debe ser un número entero positivo.");
    }

    // si es menor de 18 no puede pillar VIP
    if ($tipoEntrada === 'VIP' && $edad < 18) {
        throw new Exception("La entrada VIP está restringida a mayores de edad.");
    }

    // los mayores de 18 no pueden pillar infantil
    if ($tipoEntrada === 'Infantil' && $edad >= 18) {
        throw new Exception("La entrada Infantil está restringida a menores de edad.");
    }

    $precioBase = calcularPrecioBase($tipoEntrada);
    $costeTalleres = 0;
    $listaTalleres = [];

    // recorro con foreach los talleres seleccionados para ir sumando
    foreach ($talleresSeleccionados as $taller) {
        switch ($taller) {
            case 'videojuegos':
                $costeTalleres += 10;
                $listaTalleres[] = ['nombre' => 'Torneo de Videojuegos', 'precio' => 10];
                break;
            case 'cosplay':
                $costeTalleres += 15;
                $listaTalleres[] = ['nombre' => 'Taller de Cosplay', 'precio' => 15];
                break;
            case 'rol':
                $costeTalleres += 5;
                $listaTalleres[] = ['nombre' => 'Zona de Rol en Vivo', 'precio' => 5];
                break;
        }
    }

    $precioTotal = $precioBase + $costeTalleres;

    // devuelvo el array con todos los datos para el resguardo
    return [
        'nombre' => $nombre,
        'edad' => $edad,
        'tipo' => $tipoEntrada,
        'precio_base' => $precioBase,
        'talleres' => $listaTalleres,
        'total' => $precioTotal
    ];
}

// miro si se ha enviado el formulario por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $resultadoLog = "Fallido";

    try {
        $nombreInput = trim($_POST['nombre'] ?? '');
        $edadRaw = $_POST['edad'] ?? '';
        $tipoInput = $_POST['tipo_entrada'] ?? '';
        $talleresInput = is_array($_POST['talleres'] ?? null) ? $_POST['talleres'] : [];

        if (empty($nombreInput)) {
            throw new Exception("El nombre completo es obligatorio.");
        }

        // compruebo que la edad sea un numero entero y positivo
        if (!filter_var($edadRaw, FILTER_VALIDATE_INT) || (int)$edadRaw <= 0) {
            throw new Exception("La edad debe ser un número entero positivo.");
        }
        $edadInput = (int)$edadRaw;

        if (empty($tipoInput)) {
            throw new Exception("Debes seleccionar un tipo de entrada.");
        }

        $datosResguardo = procesarInscripcion($nombreInput, $edadInput, $tipoInput, $talleresInput);
        $exito = true;
        $mensajeExito = "¡Inscripción realizada con éxito!";
        $resultadoLog = "Exitoso";

    } catch (Exception $e) {
        $mensajeError = $e->getMessage();
        $resultadoLog = "Fallido";

    } finally {
        // guardo en el log del servidor la fecha y el resultado
        $fechaActual = date('Y-m-d H:i:s');
        error_log("Operación Comic-Con [$fechaActual] - Resultado: $resultadoLog");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comic-Con</title>
</head>
<body>

    <h1>Formulario de Registro - Comic-Con</h1>

    <?php if (!empty($mensajeError)): ?>
        <p style="color: red;"><strong>Error:</strong> <?= htmlspecialchars($mensajeError) ?></p>
    <?php endif; ?>

    <?php if (!empty($mensajeExito)): ?>
        <p style="color: green;"><strong>Éxito:</strong> <?= htmlspecialchars($mensajeExito) ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <p>
            <label for="nombre">Nombre completo:</label><br>
            <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
        </p>

        <p>
            <label for="edad">Edad:</label><br>
            <input type="number" id="edad" name="edad" min="1" required value="<?= htmlspecialchars((string)($_POST['edad'] ?? '')) ?>">
        </p>

        <p>
            <label for="tipo_entrada">Tipo de Entrada:</label><br>
            <?php $tipoSeleccionado = $_POST['tipo_entrada'] ?? ''; ?>
            <select id="tipo_entrada" name="tipo_entrada" required>
                <option value="">-- Selecciona una opción --</option>
                <option value="General" <?= $tipoSeleccionado === 'General' ? 'selected' : '' ?>>General (30 €)</option>
                <option value="VIP" <?= $tipoSeleccionado === 'VIP' ? 'selected' : '' ?>>VIP (60 €)</option>
                <option value="Infantil" <?= $tipoSeleccionado === 'Infantil' ? 'selected' : '' ?>>Infantil (10 €)</option>
            </select>
        </p>

        <p>
            <strong>Talleres Especiales:</strong><br>
            <?php $talleresPrevios = $_POST['talleres'] ?? []; ?>
            <label>
                <input type="checkbox" name="talleres[]" value="videojuegos" <?= in_array('videojuegos', $talleresPrevios) ? 'checked' : '' ?>> Torneo de Videojuegos (10 euros)
            </label><br>

            <label>
                <input type="checkbox" name="talleres[]" value="cosplay" <?= in_array('cosplay', $talleresPrevios) ? 'checked' : '' ?>> Taller de Cosplay (15 euros)
            </label><br>

            <label>
                <input type="checkbox" name="talleres[]" value="rol" <?= in_array('rol', $talleresPrevios) ? 'checked' : '' ?>> Zona de Rol en Vivo (5 euros)
            </label>
        </p>

        <p>
            <input type="submit" value="Enviar">
        </p>
    </form>

    <?php if ($exito): ?>
        <hr>
        <h2>Resguardo de Inscripción</h2>
        <p><strong>Nombre del asistente:</strong> <?= htmlspecialchars($datosResguardo['nombre']) ?> (<?= $datosResguardo['edad'] ?> años)</p>
        <p><strong>Tipo de entrada:</strong> <?= $datosResguardo['tipo'] ?> (Coste base: <?= $datosResguardo['precio_base'] ?> €)</p>

        <p><strong>Talleres seleccionados:</strong></p>
        <?php if (!empty($datosResguardo['talleres'])): ?>
            <ul>
                <?php foreach ($datosResguardo['talleres'] as $taller): ?>
                    <li><?= htmlspecialchars($taller['nombre']) ?> - <?= $taller['precio'] ?> €</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No se ha seleccionado ningún taller.</p>
        <?php endif; ?>

        <p><strong>Precio total final a pagar:</strong> <?= $datosResguardo['total'] ?> €</p>
    <?php endif; ?>

</body>
</html>
