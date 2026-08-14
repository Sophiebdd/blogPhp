<?php
$pdo = require_once __DIR__ . '/../database.php';
class Article {

    private PDOStatement $statementCreateOne;
    private PDOStatement $statementUpdateOne;
    private PDOStatement $statementDeleteOne;
    private PDOStatement $statementReadOne;
    private PDOStatement $statementReadAll;

    function __construct(private PDO $pdo) {
        $this->statementCreateOne = $pdo->prepare('INSERT INTO article (title, category, content, image) VALUES (:title, :category, :content, :image)');
        $this->statementUpdateOne = $pdo->prepare('UPDATE article SET title=:title, category=:category, content=:content, image=:image WHERE id=:id');
        $this->statementReadOne = $pdo->prepare('SELECT * FROM article WHERE id=:id');
        $this->statementReadAll = $pdo->prepare('SELECT * FROM article');
        $this->statementDeleteOne = $pdo->prepare('DELETE FROM article WHERE id=:id');
    }

    public function fetchAll(){
        $this->statementReadAll->execute();
        return $this->statementReadAll->fetchAll();
    }

    public function fetchOne(int $id){
        $this->statementReadOne->bindValue(':id', $id);
        $this->statementReadOne->execute();
        return $this->statementReadOne->fetch();
    }

    public function deleteOne(int $id){
        $this->statementDeleteOne->bindValue(':id', $id);
        $this->statementDeleteOne->execute();
        return $id;
    }

    public function createOne($article){
        $this->statementCreateOne->bindValue(':title', $article['title']);
        $this->statementCreateOne->bindValue(':content', $article['content']);
        $this->statementCreateOne->bindValue(':category', $article['category']);
        $this->statementCreateOne->bindValue(':image', $article['image']);
        $this->statementCreateOne->execute();
        return $this->fetchOne($this->pdo->lastInsertId());
    }

    public function updateOne($article){
        $this->statementUpdateOne->bindValue(':title', $article['title']);
        $this->statementUpdateOne->bindValue(':content', $article['content']);
        $this->statementUpdateOne->bindValue(':category', $article['category']);
        $this->statementUpdateOne->bindValue(':image', $article['image']);
        $this->statementUpdateOne->bindValue(':id', $article['id']);
        $this->statementUpdateOne->execute();
        return $article;
    }

}

return new Article($pdo);