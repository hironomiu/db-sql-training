<?php

$app->get('/chapter3/follower',function($request,$response,$args) {

    $con = $this->get('pdo');

    $user_id = mt_rand(1,1000000);

    // SQL
    $sql = 'select count(*) as count from follows where follow_user_id = ?';
    $sth = $con->prepare($sql);
    $sth->bindValue('1',$user_id, PDO::PARAM_INT);
    $sth->execute();
    $follower_count = $sth->fetch(PDO::FETCH_BOTH);

    return "user_id :" . $user_id . " follower is :".$follower_count[0];
});


$app->get('/chapter3/new-timeline',function($request,$response,$args) {

    $con = $this->get('pdo');

    $user_id = mt_rand(1,1000000);

    // SQL
    $sql = 'select name from users where id = ?';
    $sth = $con->prepare($sql);
    $sth->bindValue('1',$user_id, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetch(PDO::FETCH_BOTH);

    // SQL
    $sql = 'select count(*) as count from follows where user_id = ?';
    $sth = $con->prepare($sql);
    $sth->bindValue('1',$user_id, PDO::PARAM_INT);
    $sth->execute();
    $follow_count = $sth->fetch(PDO::FETCH_BOTH);

    // SQL
    $sql = 'select count(*) as count from messages where user_id = ?';
    $sth = $con->prepare($sql);
    $sth->bindValue('1',$user_id, PDO::PARAM_INT);
    $sth->execute();
    $message_count = $sth->fetch(PDO::FETCH_BOTH);

    // SQL
    $sql = 'select user_id,message,created_at from messages where user_id in ( select follow_user_id from follows where user_id = ?) order by created_at desc limit 10';
    //$sql = 'select user_id,message,created_at from (select user_id,message,created_at from messages where user_id in ( select follow_user_id from follows where user_id = ?) union select user_id,message,created_at from messages where user_id = ? ) a order by created_at desc limit 20';
    $sth = $con->prepare($sql);
    $sth->bindValue(1,$user_id, PDO::PARAM_INT);
    //$sth->bindValue(2,$user_id, PDO::PARAM_INT);
    $sth->execute();
    $results = $sth->fetchAll();

    // SQL
    $sql = 'select count(*) as count from follows where follow_user_id = ?';
    $sth = $con->prepare($sql);
    $sth->bindValue('1',$user_id, PDO::PARAM_INT);
    $sth->execute();
    $follower_count = $sth->fetch(PDO::FETCH_BOTH);

    return $this->view->render($response,'chapter1.twig',
        [
        'user' => $result['name'],
        'message_count' => $message_count['count'],
        'follow' => $follow_count['count'],
        'follower' => $follower_count['count'],
        'message_line' => $results
        ]);
});

