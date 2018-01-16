<?php
$app->get('/tuning04',function($request,$response,$args) {
    $id = mt_rand(1,100000);
    $con = $this->get('pdo');
    $message_line["message"] = "キャンペーン中!!";
    $message_line["created_at"] = date("Y/m/d G:i:s",time());

    // SQL
    $sql = '
            select count(*) as cnt
            from users a
            where TIMESTAMPDIFF(YEAR,a.birthday,CURDATE()) >
              (select avg(TIMESTAMPDIFF(YEAR,b.birthday,CURDATE())) AS age
               from users b
               where a.sex = b.sex)
            and a.id = :id';

    $sth = $con->prepare($sql);
    $sth->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $sth->execute();
    $result = $sth->fetch(PDO::FETCH_BOTH);
    $cnt = $result['cnt'];
    if ($cnt === 0){
        $message_line["message"] = "キャンペーン期間外";
    } 
    return $this->view->render($response,'chapter1.twig',
        [
        'message_line' => [$message_line]
        ]);
});
