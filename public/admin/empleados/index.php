<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/output.css" rel="stylesheet">
    <title>Empleados</title>
</head>

<body>
    <?php
    require 'auxiliar.php';

    const FMT_FECHA = 'Y-m-d H:i:s';
    ?>
    <div class="container mx-auto">
        <!--
    <div>
        <form action="" method="get">
            <fieldset>
                <legend>Criterios de búsqueda</legend>
                <p>
                    <label>
                        Desde código:
                        <input type="text" name="desde_codigo" size="8" value="<?= $desde_codigo ?>">
                    </label>
                </p>
                <p>
                    <label>
                        Hasta código:
                        <input type="text" name="hasta_codigo" size="8" value="<?= $hasta_codigo ?>">
                    </label>
                </p>
                <p>
                    <label>
                        Denominación:
                        <input type="text" name="denominacion" value="<?= $denominacion ?>">
                    </label>
                </p>
                <button type="submit">Buscar</button>
            </fieldset>
        </form>
    </div>
    -->
        <?php
        $pdo = conectar();
        $pdo->beginTransaction();
        $pdo->exec('LOCK TABLE empleados IN SHARE MODE');
        /*
    $where = [];
    $execute = [];
    if (isset($desde_codigo) && $desde_codigo != '') {
        $where[] = 'codigo >= :desde_codigo';
        $execute[':desde_codigo'] = $desde_codigo;
    }
    if (isset($hasta_codigo) && $hasta_codigo != '') {
        $where[] = 'codigo <= :hasta_codigo';
        $execute[':hasta_codigo'] = $hasta_codigo;
    }
    if (isset($denominacion) && $denominacion != '') {
        $where[] = 'lower(denominacion) LIKE lower(:denominacion)';
        $execute[':denominacion'] = "%$denominacion%";
    }
    $where = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    */
        $where = '';
        $execute = [];
        $sent = $pdo->prepare("SELECT COUNT(*)
                             FROM empleados e JOIN departamentos d
                               ON e.departamento_id = d.id
                           $where");
        $sent->execute($execute);
        $total = $sent->fetchColumn();
        $sent = $pdo->prepare("SELECT e.*, denominacion
                             FROM empleados e JOIN departamentos d
                               ON e.departamento_id = d.id
                           $where
                         ORDER BY numero");
        $sent->execute($execute);
        $pdo->commit();
        $nf = new NumberFormatter('es_ES', NumberFormatter::CURRENCY);
        // $df = new IntlDateFormatter(
        //     'es_ES',
        //     IntlDateFormatter::LONG,
        //     IntlDateFormatter::NONE,
        //     'Europe/Madrid'
        // );
        cabecera();
        ?>
        <br>
        <div>
            <a href="../proyectos/../index.php" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">Volver</a> <br> <br>
        </div>
        <div class="overflow-x-auto relative mt-4">
            <table class="mx-auto text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <th scope="col" class="py-3 px-6">Número</th>
                    <th scope="col" class="py-3 px-6">Nombre</th>
                    <th scope="col" class="py-3 px-6">Salario</th>
                    <th scope="col" class="py-3 px-6">Fecha de nac.</th>
                    <th scope="col" class="py-3 px-6">Departamento</th>
                    <th scope="col" class="py-3 px-6 text-center">Acciones</th>
                </thead>
                <tbody>
                    <?php foreach ($sent as $fila) : ?>
                        <tr>
                            <td class="py-4 px-6"><?= $fila['numero'] ?></td>
                            <td class="py-4 px-6"><?= mb_substr($fila['nombre'], 0, 30) ?></td>
                            <td class="py-4 px-6"><?= $nf->format($fila['salario']) ?></td>
                            <td class="py-4 px-6"><?= DateTime::createFromFormat(
                                                        FMT_FECHA,
                                                        $fila['fecha_nac'],
                                                        new DateTimeZone('Europe/Madrid')
                                                    )->format('d-m-Y') ?></td>
                            <td class="py-4 px-6"><?= mb_substr($fila['denominacion'], 0, 30) ?></td>
                            <td class="py-4 px-6 text-center">
                                <a href="confirmar_borrado.php?id=<?= $fila['id'] ?>" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Borrar</a>
                                <a href="modificar.php?id=<?= $fila['id'] ?>" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">Modificar</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <p>Número total de filas: <?= $total ?></p>
            <a href="insertar.php" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">Insertar un nuevo empleado</a>
        </div>
    </div>
    <script src="/js/flowbite.js"></script>
</body>

</html>