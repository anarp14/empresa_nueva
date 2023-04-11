<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/output.css" rel="stylesheet">
    <title>Departamentos</title>
</head>

<body>
    <?php
    require '../../../src/auxiliar.php';
    require 'auxiliar.php';

    $desde_codigo = obtener_get('desde_codigo');
    $hasta_codigo = obtener_get('hasta_codigo');
    $denominacion = obtener_get('denominacion');

    cabecera();
    ?>
    <div class="container mx-auto">
        <div>
            <form action="" method="get">
                <fieldset>
                    <legend> <b>Criterios de búsqueda </b></legend><br>
                    <div class="flex mb-3 font-normal text-gray-700 dark:text-gray-400">
                        <label>
                            Desde código:
                            <input type="text" name="desde_codigo" value="<?= $desde_codigo ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </label>
                    </div>
                    <div class="flex mb-3 font-normal text-gray-700 dark:text-gray-400">
                        <label>
                            Hasta código:
                            <input type="text" name="hasta_codigo" value="<?= $hasta_codigo ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </label>
                    </div>
                    <div class="flex mb-3 font-normal text-gray-700 dark:text-gray-400">
                        <label>
                            Denominación:
                            <input type="text" name="denominacion" value="<?= $denominacion ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </label>
                    </div>
                    <button type="submit" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Buscar</button>
                </fieldset>
            </form>
        </div>
        <?php
        $pdo = conectar();
        $pdo->beginTransaction();
        $pdo->exec('LOCK TABLE departamentos IN SHARE MODE');
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
        $sent = $pdo->prepare("SELECT COUNT(*) FROM departamentos $where");
        $sent->execute($execute);
        $total = $sent->fetchColumn();
        $sent = $pdo->prepare("SELECT * FROM departamentos $where ORDER BY codigo");
        $sent->execute($execute);
        $pdo->commit();
        ?>
        <br>
        <div class="overflow-x-auto relative mt-4">
            <table class="mx-auto text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <th scope="col" class="py-3 px-6">Código</th>
                    <th scope="col" class="py-3 px-6">Denominación</th>
                    <th scope="col" class="py-3 px-6 text-center">Acciones</th>
                </thead>
                <tbody>
                    <?php foreach ($sent as $fila) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="py-4 px-6"><?= $fila['codigo'] ?></td>
                            <td class="py-4 px-6"><?= $fila['denominacion'] ?></td>
                            <td class="py-4 px-6 text-center">
                                <a href="confirmar_borrado.php?id=<?= $fila['id'] ?>" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Borrar</a>
                                <a href="modificar.php?id=<?= $fila['id'] ?>" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">Modificar</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <p>Número total de filas: <?= $total ?></p>
            <div class="flex mb-3 font-normal text-gray-700 dark:text-gray-400">
                <a href="insertar.php" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">Insertar un nuevo departamento</a>
            </div>
        </div>
    </div>
    <script src="/js/flowbite.js"></script>
</body>

</html>