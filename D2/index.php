<?php
// Fungsi untuk membaca log.json
function readPosts() {
    if (file_exists('log.json')) {
        $json = file_get_contents('log.json');
        $posts = json_decode($json, true);
        return $posts ? $posts : [];
    }
    return [];
}

// Fungsi untuk menulis ke log.json
function writePosts($posts) {
    file_put_contents('log.json', json_encode($posts, JSON_PRETTY_PRINT));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $posts = readPosts();
    
    // Simpan post baru
    if (isset($_POST['save'])) {
        $name = htmlspecialchars($_POST['name'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        
        if (!empty($name) && !empty($content)) {
            $id = uniqid();
            $date = date('d/m/Y');
            
            $posts[$id] = [
                'name' => $name,
                'content' => $content,
                'date' => $date
            ];
            
            writePosts($posts);
        }
    }
    
    // Update post
    elseif (isset($_POST['update_id'])) {
        $id = $_POST['update_id'];
        $name = htmlspecialchars($_POST['name'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        
        if (!empty($name) && !empty($content) && isset($posts[$id])) {
            $posts[$id]['name'] = $name;
            $posts[$id]['content'] = $content;
            // Tanggal tetap sama
            
            writePosts($posts);
        }
    }
    
    // Delete post
    elseif (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        
        if (isset($posts[$id])) {
            unset($posts[$id]);
            writePosts($posts);
        }
    }
    
    // Redirect untuk menghindari resubmission saat refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle menampilkan form update
$editMode = false;
$editId = '';
$editName = '';
$editContent = '';

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $posts = readPosts();
    $editId = $_GET['edit'];
    
    if (isset($posts[$editId])) {
        $editMode = true;
        $editName = $posts[$editId]['name'];
        $editContent = $posts[$editId]['content'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Noticeboard</title>
    <style type="text/css">
        .box{
            border-radius:16px;
            padding:20px;
            border:solid 1px rgba(0,0,0,0.2);
            margin-bottom: 10px;
        }
        .date{
            display:block;
            color:rgba(0,0,0,0.6);
            margin-bottom: 10px;
        }
        h3{
            margin:0;
        }
        textarea {
            width: 300px;
            height: 100px;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 300px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Noticeboard</h1>
    
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php if ($editMode): ?>
            <input type="hidden" name="update_id" value="<?php echo $editId; ?>">
        <?php endif; ?>
        
        <input type="text" name="name" placeholder="Name" value="<?php echo $editMode ? $editName : ''; ?>" required><br>
        <textarea name="content" placeholder="Content" required><?php echo $editMode ? $editContent : ''; ?></textarea><br>
        
        <?php if ($editMode): ?>
            <button type="submit">Update</button>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><button type="button">Cancel</button></a>
        <?php else: ?>
            <button type="submit" name="save">Save</button>
        <?php endif; ?>
    </form>
    
    <hr>
    
    <?php
    $posts = readPosts();
    
    // Urutkan post dari yang terbaru
    if (!empty($posts)) {
        krsort($posts);
        
        foreach ($posts as $id => $post) {
            echo '<div class="box">';
            echo '<h3>' . $post['name'] . '</h3>';
            echo nl2br($post['content']);
            echo '<span class="date">' . $post['date'] . '</span>';
            echo '<form method="get" style="display:inline;">';
            echo '<input type="hidden" name="edit" value="' . $id . '">';
            echo '<button type="submit">Update</button>';
            echo '</form> ';
            echo '<form method="post" style="display:inline;">';
            echo '<input type="hidden" name="delete_id" value="' . $id . '">';
            echo '<button type="submit">Delete</button>';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo '<p>No posts yet.</p>';
    }
    ?>
</body>
</html>