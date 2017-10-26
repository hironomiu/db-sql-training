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


$app->get('/chapter3/db',function($request,$response,$args) {
    $sql = 'select name from  users where id = ?';
    $con = $this->get('pdo');
    $sth = $con->prepare($sql);
    $sth->execute(array(mt_rand(1,100000)));
    $result = $sth->fetch(PDO::FETCH_BOTH);
    return $this->view->render($response,'chapter1.twig',['user' => $result['name']]);
});

$app->get('/chapter3/cache',function($request,$response,$args) {
    $pass = null;
    $mem = $this->get('memcached');
    $name = $mem->get(mt_rand(1,100000));
    return $this->view->render($response,'chapter1.twig',['user' => $name]);
});
