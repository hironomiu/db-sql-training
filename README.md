# サンプルアプリ
このアプリはデータベースに対して負荷検証を行いデータベースチューニングなどを学習するためのサンプルアプリです。セッションなど実装しておらず、CSRFなど一般的なセキュリティ対策も未対応なためWebアプリのサンプルとしては利用しないでください。

# SetUp

## deploy & DB 
事前にddlディレクトリの作成、サンプルデータを取り込むこと
```
$ make install
```

## トレーニング
[documents](https://github.com/hironomiu/db-sql-training/tree/master/documents)配下にDB-SQLのトレーニングを行うMDがあります。こちらのMDを挑戦してみましょう。

## tips
### DB周りの接続設定
app/config.phpにDB接続(MySQL,Memcached)の設定をすること
```
$ vi app/config.php
```

### BuiltInServerを利用する場合
任意のHOST、PORTを指定して起動
```
$ HOST=xxx.xxx.xxx.xxx PORT=xxxx make server
```

### cacheディレクトリのパーミッション
src/cacheはWebサーバが書き込み可能な状態にすること


