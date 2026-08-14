<?php
    $articleDB = require_once './database/models/Article.php';
    $_GET = filter_input_array(INPUT_GET, FILTER_VALIDATE_INT);
    $id = $_GET['id'] ?? '';


    if (!$id) {
        header('Location: /');
        exit;
    } else {
        $article = $articleDB ->fetchOne($id);
    }

    if (!$article) {
        header('Location: /');
        exit;
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/show-article.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <script src="public/js/index.js" defer></script>
    <title>Article</title>
</head>
<body>
    <div class="container">
        <?php require_once 'includes/header.php'; ?>
    
        <div class="content">
            <div class="article-container">
                <a class="article-back" href="/">Retour à la liste des articles</a>
                <div class="article-cover-img" style="background-image:url('<?= $article['image'] ?>')"></div>
                <h1 class="article-title"><?= $article['title'] ?></h1>
                <div class="separator"></div>
                <p class="article-content"><?=  $article['content'] ?></p>
                <div class="action">
                    <a class="btn btn-secondary" href="/delete-article.php?id=<?= $article['id'] ?>">Supprimer</a>
                    <a class="btn btn-primary" href="/form-article.php?id=<?= $article['id'] ?>">Editer l'article</a>
                </div>
            </div>
            
        </div>
        <?php require_once 'includes/footer.php'; ?>  

    </div>
    
   

</body>
</html>