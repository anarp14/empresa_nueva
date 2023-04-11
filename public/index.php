<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/output.css" rel="stylesheet">
    <script>
        function cambiar(el, id) {
            el.preventDefault();
            const oculto = document.getElementById('oculto');
            oculto.setAttribute('value', id);
        }
    </script>
    <title>Portal admin</title>
</head>

<body>
    <?php require '../vendor/autoload.php';

    $usuario = (\App\Tablas\Usuario::logueado()->usuario);

    $pdo = conectar();

    $sent = $pdo->prepare("SELECT e.nombre as empleado, d.denominacion as departamento, salario, fecha_nac
            FROM empleados e
            JOIN usuarios u ON e.id = u.empleado_id
            JOIN departamentos d ON e.departamento_id = d.id
            WHERE u.usuario = :usuario ");

    $sent->execute([':usuario' => $usuario]);

    if ($usuario = \App\Tablas\Usuario::logueado()) {
    } else {
        return redirigir_login();
    }

    ?>

    <div class="container mx-auto">
        <?php
        require '../src/_menu.php';
        require '../src/_alerts.php';
        ?>

        <div class="overflow-x-auto relative mt-4">
            <table class="mx-auto text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <th scope="col" class="py-3 px-6">Nombre</th>
                    <th scope="col" class="py-3 px-6">Salario</th>
                    <th scope="col" class="py-3 px-6">Fecha de nac.</th>
                    <th scope="col" class="py-3 px-6">Departamento</th>   
                </thead>
                <tbody>
                    <?php foreach ($sent as $fila) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="py-4 px-6"><?= $fila['empleado'] ?></td>
                            <td class="py-4 px-6"><?= $fila['salario'] ?></td>
                            <td class="py-4 px-6"><?= $fila['fecha_nac'] ?></td>
                            <td class="py-4 px-6"><?= $fila['departamento'] ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="/js/flowbite/flowbite.js"></script>
</body>

</html>