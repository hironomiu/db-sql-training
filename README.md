# SetUp

## deploy & DB 
事前にddlディレクトリの作成、サンプルデータを取り込むこと
```
$ make install
```

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
