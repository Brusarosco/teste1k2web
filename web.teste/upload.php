<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$targetDir = "uploads";
$targetFile = $targetDir . '/' . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// Verifica se o arquivo é uma imagem ou um vídeo
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if($check !== false || $imageFileType == 'mp4') {
        $uploadOk = 1;
    } else {
        echo "O arquivo não é uma imagem ou vídeo.";
        $uploadOk = 0;
    }
}

// Verifica se o arquivo já existe
if (file_exists($targetFile)) {
    echo "Desculpe, o arquivo já existe.";
    $uploadOk = 0;
}

// Verifica o tamanho do arquivo
if ($_FILES["file"]["size"] > 5000000) {
    echo "Desculpe, seu arquivo é muito grande.";
    $uploadOk = 0;
}

// Permite certos formatos de arquivos
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "mp4") {
    echo "Desculpe, apenas arquivos JPG, JPEG, PNG, GIF, & MP4 são permitidos.";
    $uploadOk = 0;
}

// Verifica se $uploadOk é 0 por causa de um erro
if ($uploadOk == 0) {
    echo "Desculpe, seu arquivo não foi enviado.";
// se tudo estiver ok, tenta fazer o upload do arquivo
} else {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        echo "O arquivo ". htmlspecialchars( basename( $_FILES["file"]["name"])). " foi enviado.";

        // Conecte ao banco de dados
        $conn = new mysqli('sql211.infinityfree.com', 'if0_36737559', 'testepro123', 'if0_36737559_upload_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $filename = basename($_FILES["file"]["name"]);
        $description = $_POST["description"];
        $type = strpos($imageFileType, 'mp4') !== false ? 'video' : 'image';

        // Inserir o upload no banco de dados
        $stmt = $conn->prepare("INSERT INTO uploads (filename, description, type) VALUES (?, ?, ?)");
        if (!$stmt) {
            echo "Erro ao preparar statement: " . $conn->error;
        } else {
            $stmt->bind_param("sss", $filename, $description, $type);
            if ($stmt->execute()) {
                echo "Upload salvo no banco de dados.";
            } else {
                echo "Erro ao executar statement: " . $stmt->error;
            }
            $stmt->close();
        }
        $conn->close();
    } else {
        echo "Desculpe, houve um erro ao enviar seu arquivo.";
    }
}
header("Location: index.php");
exit();
?>
