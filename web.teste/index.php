<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload de Imagens e Vídeos</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .upload-container { margin: 20px; }
        .uploads { margin-top: 20px; }
        .upload-item { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="upload-container">
        <h1>Upload de Imagens e Vídeos</h1>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" accept="image/*,video/*" required>
            <br><br>
            <textarea name="description" placeholder="Descrição" required></textarea>
            <br><br>
            <input type="submit" value="Upload">
        </form>
    </div>
    <div class="uploads">
        <h2>Uploads Anteriores</h2>
        <?php
        // Conecte ao banco de dados
        $conn = new mysqli('sql211.infinityfree.com', 'if0_36737559', 'testepro123', 'if0_36737559_upload_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Selecionar os uploads
        $sql = "SELECT * FROM uploads ORDER BY uploaded_at DESC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="upload-item">';
                if ($row['type'] == 'image') {
                    echo '<img src="uploads/' . $row['filename'] . '" alt="Image" style="max-width: 300px;">';
                } else {
                    echo '<video controls style="max-width: 300px;">
                            <source src="uploads/' . $row['filename'] . '" type="video/mp4">
                          Seu navegador não suporta a tag de vídeo.
                          </video>';
                }
                echo '<p>' . $row['description'] . '</p>';
                echo '<p><em>Uploaded at: ' . $row['uploaded_at'] . '</em></p>';
                echo '<form action="delete.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <input type="hidden" name="filename" value="' . $row['filename'] . '">
                        <input type="submit" value="Delete">
                      </form>';
                echo '</div>';
            }
        } else {
            echo "No uploads found.";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
