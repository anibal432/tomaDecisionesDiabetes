<?php
require 'conexion.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $descripcion = $_POST['descripcion'];
    $tipoDiabetes = $_POST['tipoDiabetes'];
    $query = "INSERT INTO TipoDiabetes (DESCRIPCION, TipoDiabetes) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $descripcion, $tipoDiabetes);
    $stmt->execute();
    header("Location: TipoDiabetes.php");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conn->query("DELETE FROM TipoDiabetes WHERE IdDiabetes = $id");
    header("Location: TipoDiabetes.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $tipoDiabetes = $_POST['tipoDiabetes'];
    $query = "UPDATE TipoDiabetes SET DESCRIPCION=?, TipoDiabetes=? WHERE IdDiabetes=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $descripcion, $tipoDiabetes, $id);
    $stmt->execute();
    header("Location: TipoDiabetes.php");
    exit();
}


$result = $conn->query("SELECT * FROM TipoDiabetes");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tipos de Diabetes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Gestión de Tipos de Diabetes</h2>
        <div class="card p-4 shadow">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Tipo de Diabetes:</label>
                    <input type="text" class="form-control" name="tipoDiabetes" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción:</label>
                    <input type="text" class="form-control" name="descripcion" required maxlength="200">
                </div>
                <button type="submit" name="agregar" class="btn btn-primary w-100">Agregar</button>
            </form>
        </div>
        
        <div class="mt-4">
            <h4>Lista de Tipos de Diabetes</h4>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Tipo de Diabetes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['IdDiabetes'] ?></td>
                            <td><?= $row['DESCRIPCION'] ?></td>
                            <td><?= $row['TipoDiabetes'] ?></td>
                            <td>
                                <a href="TipoDiabetes.php?eliminar=<?= $row['IdDiabetes'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
                                <i class="bi bi-trash"></i>
                                </a>
                                <button class="btn btn-warning btn-sm" onclick="editar(<?= $row['IdDiabetes'] ?>, '<?= $row['DESCRIPCION'] ?>', '<?= $row['TipoDiabetes'] ?>')"><i class="bi bi-pencil"></i></button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tipo de Diabetes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Diabetes:</label>
                            <input type="text" class="form-control" name="tipoDiabetes" id="editTipo" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción:</label>
                            <input type="text" class="form-control" name="descripcion" id="editDescripcion" required maxlength="200">
                        </div>
                        <button type="submit" name="editar" class="btn btn-success w-100">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editar(id, descripcion, tipoDiabetes) {
            document.getElementById('editId').value = id;
            document.getElementById('editDescripcion').value = descripcion;
            document.getElementById('editTipo').value = tipoDiabetes;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html>
