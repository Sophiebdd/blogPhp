<?php

$articleDB = require_once './database/models/Article.php';


const ERROR_REQUIRED = 'Ce champ est requis';
const ERROR_TITLE_TOO_SHORT = 'Le titre doit contenir au moins 5 caractères';
const ERROR_CONTENT_TOO_SHORT = 'Le contenu doit contenir au moins 50 caractères';
const ERROR_IMAGE_URL = 'L\'URL de l\'image n\'est pas valide';

$errors = [
    'title' => '',
    'image' => '',
    'category' => '',
    'content' => ''
];

// Logique de préremplissage des champs lorsque l'id existe pour l'édition
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;

if ($id) {
    
    $article = $articleDB->fetchOne($id);

    if (!$article) {
        header('Location: /');
        exit;
    }

    $title = $article['title'];
    $image = $article['image'];
    $category = $article['category'];
    $content = $article['content'];
} else {
    $title = '';
    $image = '';
    $category = '';
    $content = '';
}


// Si on est en POST alors on nettoie les données saisies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $POST = filter_input_array(INPUT_POST, [
        'title' => FILTER_SANITIZE_SPECIAL_CHARS,
        'image' => FILTER_SANITIZE_URL,
        'category' => FILTER_SANITIZE_SPECIAL_CHARS,
        'content' => [
            'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
            'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
        ]
    ]);
    // pour repréremplir le form si une erreur survient
    $title = ($POST['title'] ?? '');
    $image = ($POST['image'] ?? '');
    $category = ($POST['category'] ?? '');
    $content = ($POST['content'] ?? '');

    // logique des erreurs de validation
    if (!$title) {
        $errors['title'] = ERROR_REQUIRED;
    } elseif (mb_strlen($title) < 5) {
        $errors['title'] = ERROR_TITLE_TOO_SHORT;
    }

    if (!$image) {
        $errors['image'] = ERROR_REQUIRED;
    } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
        $errors['image'] = ERROR_IMAGE_URL;
    }

    if (!$category) {
        $errors['category'] = ERROR_REQUIRED;
    }

    if (!$content) {
        $errors['content'] = ERROR_REQUIRED;
    } elseif (mb_strlen($content) < 50) {
        $errors['content'] = ERROR_CONTENT_TOO_SHORT;
    }

    // si le tableau d'erreur est vide alors on traite:
    if (empty(array_filter($errors, fn($e) => $e !== ''))) {
        // Si on est en édition on remplace les données des champs
        if ($id) {
            $article['title'] = $title;
            $article['image'] = $image;
            $article['category'] = $category;
            $article['content'] = $content;
            
            $articleDB->updateOne($article);

        // Sinon on crée l'article
        } else {
            $articleDB->createOne([
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'image' => $image
            ]);
        }

        
        header('Location: /');
        exit;
    } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/form-article.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <script src="public/js/index.js" defer></script>
    <title><?=  $id ? 'Modifier' : 'Créer' ?> un article</title>
</head>
<body>
    <div class="container">
        <?php require_once 'includes/header.php'; ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1><?=  $id ? 'Modifier' : 'Écrire' ?> un article</h1>
                <form action="/form-article.php<?= $id ? "?id=$id" : '' ?>" method="POST">
                    <div class="form-control">
                        <label for="title">Titre de l'article</label>
                        <input type="text" id="title" name="title" value="<?= $title ?? '' ?>" required>
                        <?php if ($errors['title']) : ?>
                            <p class="text-danger"><?= $errors['title']?></p> 
                        <?php endif; ?>
                    </div>
                    <div class="form-control">
                        <label for="image">Image</label>
                        <input type="text" id="image" name="image" value="<?= $image ?? '' ?>" required>
                        <?php if ($errors['image']) : ?>
                            <p class="text-danger"><?= $errors['image']?></p> 
                        <?php endif; ?>
                    </div>
                    <div class="form-control">
                        <label for="category">Catégorie</label>
                        <select name="category" id="category" required>
                            <option <?= !$category ? 'selected' : '' ?> value="">Sélectionner une catégorie</option>
                            <option <?= $category === 'actualites' ? 'selected' : '' ?> value="actualites">Actualités</option>
                            <option <?= $category === 'technologie' ? 'selected' : '' ?> value="technologie">Technologie</option>
                            <option <?= $category === 'sport' ? 'selected' : '' ?> value="sport">Sport</option>
                            <option <?= $category === 'culture' ? 'selected' : '' ?> value="culture">Culture</option>
                        </select>
                        <?php if ($errors['category']) : ?>
                            <p class="text-danger"><?= $errors['category']?></p> 
                        <?php endif; ?>
                    </div>
                    <div class="form-control">
                        <label for="content">Contenu de l'article</label>
                        <textarea id="content" name="content" required><?=  $content ?? '' ?></textarea>
                        <?php if ($errors['content']) : ?>
                            <p class="text-danger"><?= $errors['content']?></p> 
                        <?php endif; ?>
                    </div>
                    <div class="form-action">
                        <a href="/" type="button" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary"><?=  $id ? 'Modifier l\'article' : 'Publier l\'article' ?></button>

                    </div>
                </form>
            </div>
        </div>
        <?php require_once 'includes/footer.php'; ?>  

    </div>
    
   

</body>
</html>