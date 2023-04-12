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

        <?php
      $pdo = conectar();
      $pdo->beginTransaction();
      $pdo->exec('LOCK TABLE empleados IN SHARE MODE');

      $estado = obtener_get('estado');
      
      $where = '';
      $execute = [];
      $estado = obtener_get('estado');
      if ($estado) {
          $where = 'WHERE p.estado = :estado';
          $execute = [':estado' => $estado];
      }
      
      $sent = $pdo->prepare("SELECT COUNT(*)
                             FROM proyectos p JOIN empleados e
                               ON p.empleado_id = e.id
                               JOIN departamentos d ON p.departamento_id = d.id
                             $where");
      $sent->execute($execute);
      $total = $sent->fetchColumn();
      $sent = $pdo->prepare("SELECT p.*, e.nombre as empleado, d.denominacion as departamento
                               FROM proyectos p JOIN empleados e
                                 ON p.empleado_id = e.id
                                 JOIN departamentos d ON p.departamento_id = d.id
                               $where
                               ORDER BY nombre");
      
      $sent->execute($execute);
      $pdo->commit();
      
      $nf = new NumberFormatter('es_ES', NumberFormatter::CURRENCY);
      
      cabecera();
        ?>
        <br>
        <div>
            <form action="" method="get">
                <fieldset>
                    <legend> <b>Crtiterios de búsqueda:</b> </legend> <br>
                    <label class="block mb-2 text-sm font-medium w-1/4 pr-4">
                        Estado proyecto:
                        <select name="estado" class="border text-sm rounded-lg p-2.5">
                            <option value="" <?php if (!$estado) { ?> selected <?php } ?>></option>
                            <option value="sin comenzar" <?php if ($estado == 'sin comenzar') { ?> selected <?php } ?>>Sin comenzar</option>
                            <option value="en curso" <?php if ($estado == 'en curso') { ?> selected <?php } ?>>En curso</option>
                            <option value="finalizado" <?php if ($estado == 'finalizado') { ?> selected <?php } ?>>Finalizado</option>
                        </select>
                    </label>
                    <button type="submit" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Buscar</button>
                </fieldset>
            </form>
        </div>
        <br>
        <div class="overflow-x-auto relative mt-4">
            <table class="mx-auto text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <th scope="col" class="py-3 px-6">Nombre</th>
                    <th scope="col" class="py-3 px-6">Descripción</th>
                    <th scope="col" class="py-3 px-6">Fecha inicio</th>
                    <th scope="col" class="py-3 px-6">Fecha fin</th>
                    <th scope="col" class="py-3 px-6">Estado</th>
                    <th scope="col" class="py-3 px-6">Presupuesto estimado</th>
                    <th scope="col" class="py-3 px-6">Empleado asignado</th>
                    <th scope="col" class="py-3 px-6">Departamento</th>
                    <th scope="col" class="py-3 px-6 text-center">Acciones</th>
                </thead>
                <tbody>
                    <?php foreach ($sent as $fila) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="py-4 px-6"><?= mb_substr($fila['nombre'], 0, 30) ?></td>
                            <td class="py-4 px-6"><?= $fila['descripcion'] ?></td>
                            <td class="py-4 px-6"><?= $fila['fecha_inicio'] ?></td>
                            <td class="py-4 px-6"><?= $fila['fecha_fin_prevista'] ?></td>
                            <td class="py-4 px-6"><?= $fila['estado'] ?></td>
                            <td class="py-4 px-6"><?= $nf->format($fila['presupuesto_estimado']) ?></td>
                            <td class="py-4 px-6"><?= $fila['empleado'] ?></td>
                            <td class="py-4 px-6"><?= $fila['departamento'] ?></td>
                            <td class="py-4 px-6 text-center">
                                <a href="confirmar_borrado.php?id=<?= $fila['id'] ?>" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Borrar</a> <br><br>
                                <a href="modificar.php?id=<?= $fila['id'] ?>" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">Modificar</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
            <p>Número total de filas: <?= $total ?></p>
            <a href="insertar.php" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">Crear un nuevo proyecto</a>
        </div>
    </div>
    <script src="/js/flowbite.js"></script>
</body>

</html>