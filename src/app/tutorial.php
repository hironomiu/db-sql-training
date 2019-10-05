<?php

$app->get('/tutorial',function($request,$response,$args) {

    $req = $request->getQueryParams();

    $con = $this->get('pdo');

    // SQL
    $sql = 'select message,created_at from messages where user_id = ?';
    $sth = $con->prepare($sql);
    $sth->bindValue('1', (int)$req['user_id'], PDO::PARAM_INT);
    $sth->execute();
    $results = $sth->fetchAll();

    return $this->view->render($response,'chapter1.twig',['message_line' => $results]);
});

