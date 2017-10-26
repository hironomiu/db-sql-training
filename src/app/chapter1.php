<?php

$app->get('/',function ($request, $response, $args) {
    return "hello!";
});


$app->get('/chapter1/read',function($request,$response,$args) {

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
    $sql = 'select message from messages where user_id = ? order by created_at limit 20';
    $sth = $con->prepare($sql);
    $sth->bindValue('1',$user_id, PDO::PARAM_INT);
    $sth->execute();
    $results = $sth->fetchAll();

    return $this->view->render($response,'chapter1.twig',
        [
        'user' => $result['name'],
        'message_count' => $message_count['count'],
        'follow' => $follow_count['count'],
        'message_line' => $results
        ]);
});

$app->post('/chapter1/write',function($request,$response,$args) {
    // SQL
    $sql = 'insert into messages values(null,?,?,?,now(),now())';

    $con = $this->get('pdo');
    $sth = $con->prepare($sql);
    $id = mt_rand(1,1000000);
    $sth->execute(array($id,'title',$_POST['message'].'by '.$id));
    return $response->withStatus(301)->withHeader('Location', '/chapter1/read');
});

$app->get('/chapter1/follow',function($request,$response,$args) {

    $con = $this->get('pdo');

    $user_id = mt_rand(1,1000000);
    $follow_user_id = mt_rand(1,1000000);

    // SQL
    $sql = 'insert into follows(id,user_id,follow_user_id,created_at,updated_at)  values(null,?,?,now(),now())';

    $sth = $con->prepare($sql);
    $sth->bindValue(1,$user_id, PDO::PARAM_INT);
    $sth->bindValue(2,$follow_user_id, PDO::PARAM_INT);
    $sth->execute();
    return "success user_id:".$user_id . " follow_user_id:".$follow_user_id;
});

