# チュートリアル03

## DBチューニング(アクセス数のチューニング)
チュートリアル01で改善した`http://localhost/tutorial?user_id=1000001`に対し`siege -c`の同時実行数を増やしてみましょう(-c 30 -> -c 100 -> …  -> 200)  
同時実行数を増やした上でチュートリアル01と同様に「200 OK」を返せるようチューニングしていきましょう

### 初期検証
チュートリアル01で検証した同時実行数`siege -c 3`で確認しましょう
 
#### [ターミナル1] siegeを実行する(-c 3)
```
$ siege -c 3 http://localhost/tutorial?user_id=1000001
```

#### [ターミナル2] processlistの確認
何度かprocesslistの確認は行いましょう

```
mysql> show full purocesslist;
```

#### [ターミナル3] MySQL スローログの確認
`tail`で出力しているスローログが、siege実行中にスローログが出ているか確認しましょう


#### [ターミナル3] リソース統計情報の確認
siege実行中にどのリソースが利用されているか、siege実行前との違いなども確認しましょう

#### ブラウザ
siege実行中にブラウザで /tutorial?user_id=1000001 を開いてみましょう

### 負荷検証
同時実行数を`-c 30 -> -c 100 -> …  -> 200`と増やして初期検証と同様の確認をし初期検証との違いを確認しましょう
#### [ターミナル1] siegeを実行する(-c 30 -> -c 100 -> …  -> 200)

```
$ siege -c 30 http://localhost/tutorial?user_id=1000001
```

#### [ターミナル2] processlistの確認
何度かprocesslistの確認は行いましょう

```
mysql> show full purocesslist;
```

#### [ターミナル3] MySQL スローログの確認
`tail`で出力しているスローログが、siege実行中にスローログが出ているか確認しましょう


#### [ターミナル3] リソース統計情報の確認
siege実行中にどのリソースが利用されているか、siege実行前との違いなども確認しましょう

#### ブラウザ
siege実行中にブラウザで /1day/tutorial?user_id=1 を開いてみましょう

----
### Mission ボトルネック、もしくは不具合について考察
初期検証、負荷検証を行い「ボトルネック、もしくは不具合」と思われる箇所を洗い出しましょう

#### ヒント
siege実行中、標準出力に500が出力されたら、ブラウザで確認しましょう。確認すると「mysql Too many connections」が出力されると思います  
「mysql Too many connections」について調べてみましょう


----

### Mission ボトルネック、もしくは不具合の解消
洗い出した「ボトルネック、もしくは不具合」を対策しましょう。対策後「負荷検証」シナリオを実施し解消されたか確認しましょう。
解消された場合、各ターミナルから出力される内容がどのように変わったか確認しましょう

#### ヒント
設定ファイル(rootで変更)`/etc/my.cnf`の値の編集が必要です

MySQLの設定後、再起動が必要です(rootで実施)

```
# systemctl restart mysql
```

----

### 追加Mission 接続先の確認と設定
チュートリアルにて呼び出していたDBの接続先の確認、サーバが2台ある場合接続先の変更などを行いましょう。サーバが2台の場合は負荷サーバ、DBサーバと役割を分けて、ここまでのMissionを改めて行いましょう。

以下demouserで実行

```
$ cd ~/web-performance-tuning/src
$ cat config.php
<?php
$host = 'localhost';
$memcachedConfig['port'] = 11211;
$mysqldConfig['database'] = 'groupwork';
$mysqldConfig['user'] = 'demouser';
$mysqldConfig['password'] = 'demopass';

```
サーバが2台用意されてる場合は、config.phpの`$host`を2台目のサーバのローカルIP(192.168.0で始まるIP)に書き換えましょう。
----

### 追加Mission 同時実行数の上限
リクエストに対して快適なレスポンスを返しながら「200 OK」を維持し続けられる同時実行数(-c)の上限を考察(もしくは検証)しましょう

----
