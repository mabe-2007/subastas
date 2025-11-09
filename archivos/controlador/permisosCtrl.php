<?php
header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Bogota');

include('../../include/conex.php');
$conn = Conectarse();
$fecha = date("Y-m-d");

// Asegurar que venga 'action'
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// ================================
// SWITCH PRINCIPAL
// ================================
switch ($action) {

    // ===================================
    // 1. ROLES: presentarRoles
    // ===================================
    case 'presentarRoles':
        $jTableResult = array();
        $jTableResult['listaRoles'] = "<div class='list-group'>";

        $sqlRoles = "SELECT idrol, rol FROM rol ORDER BY rol ASC";
        if ($result = mysqli_query($conn, $sqlRoles)) {
            while ($r = mysqli_fetch_assoc($result)) {
                $jTableResult['listaRoles'] .= "
                    <button type='button' 
                            class='list-group-item list-group-item-action btn-rol'
                            data-idrol='{$r['idrol']}'
                            data-nombrerol='" . htmlspecialchars($r['rol'], ENT_QUOTES, 'UTF-8') . "'>
                        " . htmlspecialchars($r['rol'], ENT_QUOTES, 'UTF-8') . "
                    </button>";
            }
        } else {
            $jTableResult['listaRoles'] = "<div class='alert alert-danger'>Error al cargar roles: " . mysqli_error($conn) . "</div>";
        }
        $jTableResult['listaRoles'] .= "</div>";
        $jTableResult['listaAsignarPermisos'] = "<div class='accordion' id='acordionPermiso'></div>";
        echo json_encode($jTableResult, JSON_UNESCAPED_UNICODE);
        exit();

    // ===================================
    // 2. PERMISOS: adicionar / suspender
    // ===================================
    case 'adicionar':
        $jTableResult = array();
        $idrol = mysqli_real_escape_string($conn, $_POST['idrolpermiso']);
        $idmenu = mysqli_real_escape_string($conn, $_POST['idmenupermiso']);
        $idsubmenu = mysqli_real_escape_string($conn, $_POST['idsubmenupermiso']);

        $checkQuery = "SELECT * FROM permisos WHERE idrol='$idrol' AND idmenu='$idmenu' AND idsubmenu='$idsubmenu'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) == 0) {
            $insertQuery = "INSERT INTO permisos (idrol, idmenu, idsubmenu) VALUES ('$idrol', '$idmenu', '$idsubmenu')";
            if (mysqli_query($conn, $insertQuery)) {
                $jTableResult['estado'] = "OK";
            } else {
                $jTableResult['estado'] = "ERROR: " . mysqli_error($conn);
            }
        } else {
            $jTableResult['estado'] = "YA_EXISTE";
        }
        echo json_encode($jTableResult);
        exit();

    case 'suspender':
        $jTableResult = array();
        $idrol = mysqli_real_escape_string($conn, $_POST['idrolpermiso']);
        $idmenu = mysqli_real_escape_string($conn, $_POST['idmenupermiso']);
        $idsubmenu = mysqli_real_escape_string($conn, $_POST['idsubmenupermiso']);

        $deleteQuery = "DELETE FROM permisos WHERE idrol='$idrol' AND idmenu='$idmenu' AND idsubmenu='$idsubmenu'";
        if (mysqli_query($conn, $deleteQuery)) {
            $jTableResult['estado'] = "OK";
        } else {
            $jTableResult['estado'] = "ERROR: " . mysqli_error($conn);
        }
        echo json_encode($jTableResult);
        exit();

    // ===================================
    // 3. PRESENTAR PERMISOS (para inicio.php)
    // ===================================
    case 'presentarpermisos':
        $permisos = [];
        $idrol = isset($_POST['idrol']) ? (int)$_POST['idrol'] : 0;

        if ($idrol > 0) {
            $query3 = "
                SELECT m.nombremenu
                FROM permisos p
                INNER JOIN menu m ON m.idmenu = p.idmenu
                WHERE p.idrol = $idrol
                GROUP BY m.idmenu, m.nombremenu
                ORDER BY m.nombremenu ASC
            ";
            if ($res = mysqli_query($conn, $query3)) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $permisos[] = "<a class='nav-link' href='#'>" . htmlspecialchars($row['nombremenu'], ENT_QUOTES, 'UTF-8') . "</a>";
                }
            }
        }
        echo json_encode(['permisos' => $permisos], JSON_UNESCAPED_UNICODE);
        exit();

    // ===================================
    // 4. ARMAR ACCORDION DE MENÚS POR ROL
    // ===================================
    case 'presentarmenuid':
        $idrol = isset($_POST['idrol']) ? (int)$_POST['idrol'] : 0;
        $menus = [];

        if ($idrol <= 0) {
            echo json_encode(['error' => 'idrol inválido'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $sqlMenus = "
            SELECT DISTINCT m.idmenu, m.nombremenu
            FROM menu m
            INNER JOIN submenu s ON s.idmenu = m.idmenu
            ORDER BY m.nombremenu ASC
        ";

        if ($result = mysqli_query($conn, $sqlMenus)) {
            while ($menu = mysqli_fetch_assoc($result)) {
                $idmenu = (int)$menu['idmenu'];
                $submenusHTML = "";

                $sqlSubs = "
                    SELECT 
                        s.idsubmenu, 
                        s.nombresubmenu,
                        (
                            SELECT COUNT(*) 
                            FROM permisos 
                            WHERE idrol = $idrol 
                              AND idmenu = $idmenu 
                              AND idsubmenu = s.idsubmenu
                        ) AS tiene_permiso
                    FROM submenu s
                    WHERE s.idmenu = $idmenu
                    ORDER BY s.ordenSubMenu ASC
                ";

                if ($res2 = mysqli_query($conn, $sqlSubs)) {
                    while ($sub = mysqli_fetch_assoc($res2)) {
                        $checked = ((int)$sub['tiene_permiso'] > 0) ? "checked" : "";
                        $submenusHTML .= "
                            <div class='form-check'>
                                <input 
                                    class='form-check-input toggle-submenu' 
                                    type='checkbox'
                                    data-idrol='{$idrol}'
                                    data-idmenu='{$idmenu}' 
                                    data-idsubmenu='{$sub['idsubmenu']}' 
                                    $checked
                                >
                                <label class='form-check-label'>" . htmlspecialchars($sub['nombresubmenu'], ENT_QUOTES, 'UTF-8') . "</label>
                            </div>";
                    }
                }

                $menuHTML = "
                    <div class='accordion-item'>
                        <h2 class='accordion-header' id='heading{$idmenu}'>
                            <button class='accordion-button collapsed' type='button'
                                    data-bs-toggle='collapse' data-bs-target='#collapse{$idmenu}'
                                    aria-expanded='false' aria-controls='collapse{$idmenu}'>
                                " . htmlspecialchars($menu['nombremenu'], ENT_QUOTES, 'UTF-8') . "
                            </button>
                        </h2>
                        <div id='collapse{$idmenu}' class='accordion-collapse collapse' 
                             data-bs-parent='#acordionPermiso' aria-labelledby='heading{$idmenu}'>
                            <div class='accordion-body'>
                                " . ($submenusHTML ?: "<em>No hay submenús</em>") . "
                            </div>
                        </div>
                    </div>";

                $menus[] = $menuHTML;
            }
        } else {
            echo json_encode(['error' => mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
            exit();
        }

        echo json_encode(['menus' => $menus], JSON_UNESCAPED_UNICODE);
        exit();

    // ===================================
    // 5. MENU POR ROL (inicio.php)
    // ===================================
    case 'menuPorRol':
        $idrol = isset($_POST['idrol']) ? (int)$_POST['idrol'] : 0;
        $menuHtml = '';

        if ($idrol > 0) {
            $sqlMenu = "SELECT DISTINCT m.idmenu, m.nombremenu 
                        FROM permisos p
                        INNER JOIN menu m ON p.idmenu = m.idmenu
                        WHERE p.idrol = $idrol
                        ORDER BY m.ordenmenu ASC";

            $resMenu = mysqli_query($conn, $sqlMenu);
            while ($menu = mysqli_fetch_assoc($resMenu)) {
                $idmenu = (int)$menu['idmenu'];
                $nombremenu = htmlspecialchars($menu['nombremenu'], ENT_QUOTES, 'UTF-8');

                $menuHtml .= "<div class='nav-item'>";
                $menuHtml .= "<a class='nav-link d-flex align-items-center' data-bs-toggle='collapse' href='#submenu{$idmenu}' role='button'>";
                $menuHtml .= "<i class='bi bi-list me-2'></i> {$nombremenu}</a>";

                $sqlSub = "SELECT s.idsubmenu, s.nombresubmenu 
                           FROM permisos p
                           INNER JOIN submenu s ON p.idsubmenu = s.idsubmenu
                           WHERE p.idrol = $idrol AND p.idmenu = $idmenu
                           ORDER BY s.ordenSubMenu ASC";

                $resSub = mysqli_query($conn, $sqlSub);
                if ($resSub && mysqli_num_rows($resSub) > 0) {
                    $menuHtml .= "<div class='collapse ps-3' id='submenu{$idmenu}'>";
                    while ($sub = mysqli_fetch_assoc($resSub)) {
                        $nombresubmenu = htmlspecialchars($sub['nombresubmenu'], ENT_QUOTES, 'UTF-8');

                        if ($nombresubmenu == 'Asignar rol') {
                            $menuHtml .= "<a class='nav-link' href='#' data-bs-toggle='modal' data-bs-target='#seleccionarRolModal'>{$nombresubmenu}</a>";
                        } elseif ($nombresubmenu == 'Categoria') {
                            $menuHtml .= "<a class='nav-link listar-categoria' href='#'>{$nombresubmenu}</a>";
                        }elseif ($nombresubmenu == 'Lista de usuarios') {
                            $menuHtml .= "<a class='nav-link listar-usuario' href='#'>{$nombresubmenu}</a>";
                        }
                         else {
                            $menuHtml .= "<a class='nav-link' href='#'>{$nombresubmenu}</a>";
                        }
                    }
                    $menuHtml .= "</div>";
                }
                $menuHtml .= "</div>";
            }
        }
        echo json_encode(['menuHtml' => $menuHtml], JSON_UNESCAPED_UNICODE);
        exit();

    // ===================================
    // 6. BUSCAR USUARIOS
    // ===================================
    case 'buscarUsuarios':
        $termino = trim($_POST['termino']);
        $pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
        $porPagina = 10;
        $offset = ($pagina - 1) * $porPagina;

        if (empty($termino)) {
            echo json_encode(['filas' => '', 'paginacion' => '']);
            exit();
        }

        $termino = mysqli_real_escape_string($conn, $termino);

        $sqlCount = "SELECT COUNT(*) as total FROM usuario u
                    INNER JOIN rol r ON u.idrol = r.idrol 
                    INNER JOIN departamentos d ON u.iddepartamento = d.iddepartamento
                    WHERE u.nombre LIKE '%$termino%'
                        OR u.apellidos LIKE '%$termino%'
                        OR u.identificacion LIKE '%$termino%'
                        OR u.correo LIKE '%$termino%'
                        OR u.telefono LIKE '%$termino%'";

        $resCount = mysqli_query($conn, $sqlCount);
        $totalFilas = mysqli_fetch_assoc($resCount)['total'];
        $totalPaginas = ceil($totalFilas / $porPagina);

        $sql = "SELECT 
                    u.idusuario, 
                    u.nombre, 
                    u.apellidos, 
                    u.direccion, 
                    u.idTipoDocumento, 
                    u.identificacion, 
                    u.telefono, 
                    u.correo, 
                    r.rol AS rol, 
                    d.departamento AS departamentos
                FROM usuario u
                INNER JOIN rol r ON u.idrol = r.idrol 
                INNER JOIN departamentos d ON u.iddepartamento = d.iddepartamento
                WHERE u.nombre LIKE '%$termino%'
                OR u.apellidos LIKE '%$termino%'
                OR u.identificacion LIKE '%$termino%'
                OR u.correo LIKE '%$termino%'
                OR u.telefono LIKE '%$termino%'
                LIMIT $porPagina OFFSET $offset";

        $result = mysqli_query($conn, $sql);
        $filasHtml = '';

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $filasHtml .= "<tr>
                                <td>{$row['nombre']}</td>
                                <td>{$row['apellidos']}</td>
                                <td>{$row['direccion']}</td>
                                <td>{$row['identificacion']}</td>
                                <td>{$row['telefono']}</td>
                                <td>{$row['correo']}</td>
                                <td>{$row['rol']}</td>
                                <td>{$row['departamentos']}</td>
                                <td>
                                    <button class='btn btn-sm btn-editar' data-id='{$row['idusuario']}'>✏️ Editar</button>
                                    <button class='btn btn-sm btn-borrar' data-id='{$row['idusuario']}'>🗑️ Borrar</button>
                                </td>
                            </tr>";
            }
        } else {
            $filasHtml = "<tr><td colspan='9' class='text-center'>No se encontraron usuarios.</td></tr>";
        }

        $paginacionHtml = '';
        if ($totalPaginas > 1) {
            $paginacionHtml = '<ul class="pagination">';
            if ($pagina > 1) {
                $paginacionHtml .= "<li class='page-item'><a class='page-link' href='#' data-pagina='" . ($pagina - 1) . "'>Anterior</a></li>";
            } else {
                $paginacionHtml .= "<li class='page-item disabled'><span class='page-link'>Anterior</span></li>";
            }
            $inicio = max(1, $pagina - 2);
            $fin = min($totalPaginas, $pagina + 2);
            for ($i = $inicio; $i <= $fin; $i++) {
                $activo = ($i == $pagina) ? 'active' : '';
                $paginacionHtml .= "<li class='page-item $activo'><a class='page-link' href='#' data-pagina='$i'>$i</a></li>";
            }
            if ($pagina < $totalPaginas) {
                $paginacionHtml .= "<li class='page-item'><a class='page-link' href='#' data-pagina='" . ($pagina + 1) . "'>Siguiente</a></li>";
            } else {
                $paginacionHtml .= "<li class='page-item disabled'><span class='page-link'>Siguiente</span></li>";
            }
            $paginacionHtml .= '</ul>';
        }

        echo json_encode([
            'filas' => $filasHtml,
            'paginacion' => $paginacionHtml,
            'total' => $totalFilas
        ], JSON_UNESCAPED_UNICODE);
        exit();

    // ===================================
    // 7. BORRAR USUARIO
    // ===================================
    case 'borrarUsuario':
        $idusuario = isset($_POST['idusuario']) ? (int)$_POST['idusuario'] : 0;
        $sql_check = "SELECT 1 FROM usuario WHERE idusuario = $idusuario";
        if (mysqli_num_rows(mysqli_query($conn, $sql_check)) == 0) {
            echo json_encode(['estado' => 'ERROR', 'mensaje' => 'Usuario no encontrado']);
            exit();
        }
        $sql_delete = "DELETE FROM usuario WHERE idusuario = $idusuario";
        $eliminado = mysqli_query($conn, $sql_delete);
        echo json_encode([
            'estado' => $eliminado ? 'OK' : 'ERROR',
            'mensaje' => $eliminado ? 'Usuario eliminado' : 'No se pudo eliminar'
        ]);
        exit();

    // ===================================
    // 8. OBTENER USUARIO
    // ===================================
    case 'obtenerUsuario':
        $idusuario = isset($_POST['idusuario']) ? (int)$_POST['idusuario'] : 0;
        if ($idusuario <= 0) {
            echo json_encode(['estado' => 'ERROR', 'mensaje' => 'ID inválido']);
            exit();
        }
        $sql = "SELECT u.*, r.rol, d.departamento 
                FROM usuario u
                INNER JOIN rol r ON u.idrol = r.idrol
                INNER JOIN departamentos d ON u.iddepartamento = d.iddepartamento
                WHERE u.idusuario = $idusuario";
        $result = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['estado' => 'OK', 'usuario' => $row], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['estado' => 'ERROR', 'mensaje' => 'Usuario no encontrado']);
        }
        exit();

    // ===================================
    // 9. CARGAR ROLES Y DEPARTAMENTOS
    // ===================================
    case 'cargarRoles':
        $roles = [];
        $result = mysqli_query($conn, "SELECT idrol, rol FROM rol ORDER BY rol");
        while ($row = mysqli_fetch_assoc($result)) $roles[] = $row;
        echo json_encode(['roles' => $roles]);
        exit();

    case 'cargarDepartamentos':
        $deps = [];
        $result = mysqli_query($conn, "SELECT iddepartamento, departamento FROM departamentos ORDER BY departamento");
        while ($row = mysqli_fetch_assoc($result)) $deps[] = $row;
        echo json_encode(['departamentos' => $deps]);
        exit();

    // ===================================
    // 10. ACTUALIZAR USUARIO
    // ===================================
       case 'actualizarUsuario':

        $idusuario = 0;
        $idrol = 0;
        $iddepartamento = 0;
        $nombre = '';
        $apellidos = '';
        $direccion = '';
        $identificacion = '';
        $telefono = '';
        $correo = '';

        if (isset($_POST['idusuario'])) {
            $idusuario = (int)$_POST['idusuario'];
        }
        if (isset($_POST['idrol'])) {
            $idrol = (int)$_POST['idrol'];
        }
        if (isset($_POST['iddepartamento'])) {
            $iddepartamento = (int)$_POST['iddepartamento'];
        }
        if (isset($_POST['nombre'])) {
            $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
        }
        if (isset($_POST['apellidos'])) {
            $apellidos = mysqli_real_escape_string($conn, $_POST['apellidos']);
        }
        if (isset($_POST['direccion'])) {
            $direccion = mysqli_real_escape_string($conn, $_POST['direccion']);
        }
        if (isset($_POST['identificacion'])) {
            $identificacion = mysqli_real_escape_string($conn, $_POST['identificacion']);
        }
        if (isset($_POST['telefono'])) {
            $telefono = mysqli_real_escape_string($conn, $_POST['telefono']);
        }
        if (isset($_POST['correo'])) {
            $correo = mysqli_real_escape_string($conn, $_POST['correo']);
        }

        // Validar datos obligatorios
        if ($idusuario <= 0 || $nombre == '' || $correo == '') {
            echo json_encode([
                'estado'  => 'ERROR',
                'mensaje' => 'Datos inválidos'
            ]);
            exit();
        }

    // Armar consulta
    $sql = "UPDATE usuario SET 
                nombre = '$nombre',
                apellidos = '$apellidos',
                direccion = '$direccion',
                identificacion = '$identificacion',
                telefono = '$telefono',
                correo = '$correo',
                idrol = $idrol,
                iddepartamento = $iddepartamento
            WHERE idusuario = $idusuario";

    $success = mysqli_query($conn, $sql);

    if ($success) {
        echo json_encode([
            'estado'  => 'OK',
            'mensaje' => 'Usuario actualizado'
        ]);
    } else {
        echo json_encode([
            'estado'  => 'ERROR',
            'mensaje' => 'Error al actualizar'
        ]);
    }

    exit();


    // ===================================
    // 11. CRUD CATEGORÍAS
    // ===================================

    
    case 'listarCategorias':
        $categorias= [];
        $result= mysqli_query($conn, "SELECT * FROM categoria ORDER BY nombre");
        while ($row = mysqli_fetch_assoc($result)) {
            $categorias[] = $row;
        }
        echo json_encode(['categorias' => $categorias]);
        exit();

    case 'crearCategoria':
        $nombre= mysqli_real_escape_string($conn, $_POST['nombre']);
        $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);


        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Nombre es obligatorio']);
            exit();
        }

        $sql = "INSERT INTO categoria (idcategoria, nombre, descripcion) VALUES (NULL, '$nombre', '$descripcion')";
        $success = mysqli_query($conn, $sql);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Creada' : 'Error: ' . mysqli_error($conn)
        ]);
        exit();

    case 'editarCategoria':
        $id          = (int)$_POST['idcategoria'];
        $nombre      = mysqli_real_escape_string($conn, $_POST['nombre']);
        $descripcion  = mysqli_real_escape_string($conn, $_POST['descripcion']);

        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Nombre es obligatorio']);
            exit();
        }

        $sql = "UPDATE categoria SET descripcion = $descripcion WHERE categoria.idcategoria = $id";
        $success = mysqli_query($conn, $sql);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Actualizada' : 'Error'
        ]);
        exit();

    default:
        echo json_encode(['error' => 'Acción no válida: ' . $action]);
        exit();
}

mysqli_close($conn);
?>