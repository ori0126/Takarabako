<?php
//DB接続設定

session_start();

require_once 'DB.php';

try {
    $userId = $_SESSION['id'];
} catch (Exception $e) {
    //何かの理由でセッションが切れてる場合、ログイン画面へ遷移する
    header('Location: ../user/login.php');
}
//tagがまず送られている？
if (isset($_POST['tag'])) {
    $hobby_tag = $_POST['tag'];
    //プルダウンの選択が全てなら全部表示
    if ($hobby_tag == '全て') {
        $sql = "SELECT tags.tag,hobbys.memo,hobbys.day_at,hobbys.id,hobbys.URL, hobbys.user_id FROM `hobbys` 
        INNER JOIN hobby_tag ON hobbys.id = hobby_tag.hobby_id 
        INNER JOIN tags ON hobby_tag.tag_id = tags.id WHERE hobbys.user_id = :user_id";

        $stmt =  $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $tasks = $stmt->fetchall();
        //プルダウンの文字列に該当するものだけ送信
    } else {
        //プレースホルダにするべきかも
        //tagを確認
        $sql = "SELECT tags.tag,hobbys.memo,hobbys.day_at,hobbys.id,hobbys.URL, hobbys.user_id FROM `hobbys` 
        INNER JOIN hobby_tag ON hobbys.id = hobby_tag.hobby_id 
        INNER JOIN tags ON hobby_tag.tag_id = tags.id WHERE hobbys.user_id = :user_id AND tags.tag=:tags_tag";
        $stmt =  $pdo->prepare($sql);
        $stmt->bindValue(':tags_tag', $hobby_tag);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $tasks = $stmt->fetchall();
    }
    //$tagに変数がない場合
} else {
    $sql = "SELECT tags.tag,hobbys.memo,hobbys.day_at,hobbys.id,hobbys.URL, hobbys.user_id FROM `hobbys` 
    INNER JOIN hobby_tag ON hobbys.id = hobby_tag.hobby_id 
    INNER JOIN tags ON hobby_tag.tag_id = tags.id WHERE hobbys.user_id = :user_id";

    $stmt =  $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId);
    $stmt->execute();
    $tasks = $stmt->fetchall();
}
//プルダウンにタグを渡す
//ここは本来はユーザーが持っているタグのみを表示するべき
//1. tagsにユーザIDの外部キーを持たせる - sqlを修正してタグにユーザーIDを持たせるようにする
$sql = "SELECT DISTINCT * FROM tags WHERE user_id = :user_id and tag != ' '";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $userId);
$stmt->execute();
$tags = $stmt->fetchall();