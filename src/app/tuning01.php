<?php

$app->get('/tuning01',function($request,$response,$args) {

    $con = $this->get('pdo');

    $user_id = mt_rand(1,1000000);

    // SQL
    $sql = 'select count(*) as count from follows where follow_user_id = ?';
    $sth = $con->prepare($sql);
    $sth->bindValue('1',$user_id, PDO::PARAM_INT);
    $sth->execute();
    $follow_count = $sth->fetch(PDO::FETCH_BOTH);

    return $this->view->render($response,'chapter1.twig',
        [
        'follower' => $follow_count['count'],
        ]);
});

