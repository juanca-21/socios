


<<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>crear usuario</title>
</head>
<body>

<?php
include ('includes/config.php'); // Tu archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // 1. Cifrar la contraseña (Algoritmo BCRYPT es el estándar actual)
    //$passwordHash = password_hash($pass, PASSWORD_DEFAULT);
    $passwordHash = md5($pass);

    // 2. Preparar la consulta para evitar Inyección SQL
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $passwordHash);

    if ($stmt->execute()) {
        echo "Usuario registrado con éxito.";
    } else {
        if ($conn->errno == 1062) {
            echo "Error: El nombre de usuario ya existe.";
        } else {
            echo "Error al registrar: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<form method="POST" action="crearusuarios.php">
    <input type="text" name="username" placeholder="Nombre de usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Registrar Usuario</button>
</form>
</body>
</html>

<!--
// Fragmento del proceso de Login
$sql = "SELECT id, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_digitado);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Comparamos la clave escrita con el hash de la DB
    if (password_verify($pass_digitado, $row['password'])) {
        // ¡ÉXITO! Iniciamos sesión
        session_start();
        $_SESSION['user_id'] = $row['id'];
        header("Location: dashboard.php");
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "El usuario no existe.";
}

-->