<?php
$app->get('/tuning05',function($request,$response,$args) {

    $con = $this->get('pdo');

    // SQL
    $sql = 'select name,birthday from users order by rand() limit 1';
    $sth = $con->prepare($sql);
    $sth->execute();
    $username = $sth->fetch(PDO::FETCH_BOTH);
    $message_line["message"] = "あなたにオススメユーザ：" . $username["name"];
    $message_line["created_at"] = date("Y/m/d G:i:s",time());

    return $this->view->render($response,'chapter1.twig',
        [
        'message_line' => [$message_line]
        ]);
});

