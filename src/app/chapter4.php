<?php

$app->get('/chapter4/db',function($request,$response,$args) {

    $con = $this->get('pdo');

    $id = mt_rand(1,1000000);

    // SQL
    $sql = 'select name from  users where id = ?';
    $sth = $con->prepare($sql);
    $sth->bindValue('1',$id, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetch(PDO::FETCH_BOTH);
    return $this->view->render($response,'chapter1.twig',['user' => $result['name']]);
});

$app->get('/chapter4/cache',function($request,$response,$args) {

    $pass = null;
    $mem = $this->get('memcached');
    $name = $mem->get(mt_rand(1,100000));
    return $this->view->render($response,'chapter1.twig',['user' => $name]);
});
