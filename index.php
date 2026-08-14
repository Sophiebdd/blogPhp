<?php

$articleDB = require_once './database/models/Article.php';
$articles = $articleDB->fetchAll();

$categories = [];
$articlePerCategories = [];

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$selectedCat = $_GET['cat'] ?? '';

if (count($articles)) {
    
    $cattmp = array_map(fn ($a) => $a['category'], $articles);
    $categories = array_reduce($cattmp, function ($acc, $cat) {
        if (isset($acc[$cat])) {
            $acc[$cat]++;
        } else {
            $acc[$cat] = 1;
        }
        return $acc;
    }, []);

    $articlePerCategories = array_reduce($articles, function($acc, $article) {
        if (isset($acc[$article['category']])) {
            $acc[$article['category']] = [...$acc[$article['category']], $article];
        } else {
            $acc[$article['category']] = [$article];
        }
        return $acc;
    }, []);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <script src="public/js/index.js" defer></script>
    <title>Blog</title>
</head>
<body>
    <div class="container">
        <?php require_once 'includes/header.php'; ?>
        <div class="content">
            <div class="newsfeed-container">
                <ul class="category-container">
                    <li class="<?=  $selectedCat ? '' : 'cat-active' ?>"><a href="/">Tous les articles <span class="small">(<?= count($articles) ?>)</span></a></li>

                    <?php foreach ($categories as $catName => $catNum) : ?>
                        <li class="<?=  $selectedCat === $catName ? 'cat-active' : '' ?>"><a href="/?cat=<?=  $catName ?>"><?= $catName ?><span class="small">(<?=  $catNum ?>)</span> </a></li>
                    <?php endforeach; ?>
                </ul>

                <div class="newsfeed-content">
                    <?php if(!$selectedCat) : ?>
                        <?php foreach ($categories as $cat => $num) : ?>
                            <h2><?= $cat ?></h2>
                            <div class="article-container">
                                <?php foreach($articlePerCategories[$cat] as $a): ?>
                                    <a href="/show-article.php?id=<?= $a['id'] ?>" class="article block">
                                        <div class="overflow">
                                            <div class="img-container" style="background-image:url(<?=  $a['image'] ?>"></div>
                                        </div>
                                        <h3><?= $a['title'] ?></h3>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <h2><?= $selectedCat ?></h2>
                            <div class="article-container">
                                <?php foreach($articlePerCategories[$selectedCat] as $a): ?>
                                    <a href="/show-article.php?id=<?= $a['id'] ?>" class="article block">
                                        <div class="overflow">
                                            <div class="img-container" style="background-image:url(<?=  $a['image'] ?>"></div>
                                        </div>
                                        <h3><?= $a['title'] ?></h3>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                    <?php endif; ?>
                </div>
            </div>

            
            
        </div>
        <?php require_once 'includes/footer.php'; ?>  

    </div>
    
   

</body>
</html>