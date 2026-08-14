<?php
$articles = json_decode(file_get_contents('./articles.json'), true);

$dns= 'mysql:host=localhost;dbname=blog_php';
$user = 'blog-php-user';
$pwd = 'password';

$pdo = new PDO($dns, $user, $pwd);

$statement = $pdo->prepare('
INSERT INTO article (title, category, content, image) 
VALUES (:title, :category, :content, :image)');

foreach ($articles as $article) {
    $statement->bindValue(':title', $article['title']);
    $statement->bindValue(':category', $article['category']);
    $statement->bindValue(':image', $article['image']);
    $statement->bindValue(':content', $article['content']);
    $statement->execute();
}