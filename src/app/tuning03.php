<?php

$app->get('/tuning03',function($request,$response,$args) {

    $start_time = date( "G:i:s" , time());
    $con = $this->get('pdo');

    // SQL
    $sql = 'truncate table user_birth_month_count';
    $sth = $con->prepare($sql);
    $sth->execute();

    // SQL
    $sql = '
            insert into user_birth_month_count(sex,month,count)
            select 0,1,count(*) from users where sex =0 and month(birthday) = 1
            union
            select 0,2,count(*) from users where sex =0 and month(birthday) = 2
            union
            select 0,3,count(*) from users where sex =0 and month(birthday) = 3
            union
            select 0,4,count(*) from users where sex =0 and month(birthday) = 4
            union
            select 0,5,count(*) from users where sex =0 and month(birthday) = 5
            union
            select 0,6,count(*) from users where sex =0 and month(birthday) = 6
            union
            select 0,7,count(*) from users where sex =0 and month(birthday) = 7
            union
            select 0,8,count(*) from users where sex =0 and month(birthday) = 8
            union
            select 0,9,count(*) from users where sex =0 and month(birthday) = 9
            union
            select 0,10,count(*) from users where sex =0 and month(birthday) = 10
            union
            select 0,11,count(*) from users where sex =0 and month(birthday) = 11
            union
            select 0,12,count(*) from users where sex =0 and month(birthday) = 12
            union
            select 1,1,count(*) from users where sex =1 and month(birthday) = 1
            union
            select 1,2,count(*) from users where sex =1 and month(birthday) = 2
            union
            select 1,3,count(*) from users where sex =1 and month(birthday) = 3
            union
            select 1,4,count(*) from users where sex =1 and month(birthday) = 4
            union
            select 1,5,count(*) from users where sex =1 and month(birthday) = 5
            union
            select 1,6,count(*) from users where sex =1 and month(birthday) = 6
            union
            select 1,7,count(*) from users where sex =1 and month(birthday) = 7
            union
            select 1,8,count(*) from users where sex =1 and month(birthday) = 8
            union
            select 1,9,count(*) from users where sex =1 and month(birthday) = 9
            union
            select 1,10,count(*) from users where sex =1 and month(birthday) = 10
            union
            select 1,11,count(*) from users where sex =1 and month(birthday) = 11
            union
            select 1,12,count(*) from users where sex =1 and month(birthday) = 12';
    $sth = $con->prepare($sql);
    $sth->execute();
    $end_time = date( "G:i:s" , time());
    echo "バッチ処理insert 成功! 開始：" . $start_time . "、終了：" . $end_time;
});

