# DBパフォーマンスチューニング 1Dayインターン(0)

## 事前準備
ここではこれから行うTutorialでの事前準備を行います

### サーバログイン
ここでは公開鍵、秘密鍵を用いたサーバログインを行います

#### 秘密鍵の設定
事前に配布した秘密鍵をローカルPCに配置しましょう。パーミッションは`600`で設定します。

```
$ vi id_rsa_vg_intern
$ chmod 600 id_rsa_vg_intern
```

#### ログイン
割り振られたサーバに`root`でログインしましょう。rootでログインできれば成功です。ログイン後LANGの設定を行いましょう

```
$ ssh -i id_rsa_vg_intern root@xxx.xxx.xxx.xxx
# export LANG=ja_JP.UTF-8
```
サーバのターミナルは4つ用意しましょう(sshログインを4つ、`screen`でターミナルを増やすなど各自の方法でも可です)

以降、ターミナル[1-4]と呼びます

----

## 環境説明
ここでは今回インターンで利用するミドルウェアについて説明します
### Webサーバ
Webサーバはapache(httpd)を利用しています

#### 起動停止
起動停止はrootユーザにて行います

```
# systemctl [start|stop|restart] httpd
```
#### 設定ファイル
設定ファイルを編集する場合はrootにて行います

```
/etc/httpd/conf/httpd.conf
/etc/sysconfig/httpd
```

##### 利用ポート
80番をLISTENしています

```
# lsof -i:80
```

### PHP(アプリケーション)
今回インターン用のサンプルアプリケーションはPHPで制作しました。その都合でPHPをインストールしてあります。

#### 設定ファイル
設定ファイルを編集する場合はrootにて行います

```
/etc/php.ini
/etc/php.d/*.ini
```
#### サンプルアプリケーション
ターミナル1はdemouserに遷移し以下のディレクトリが存在することを確認しましょう、講義ではこのディレクトリ配下のPHPファイルを用います

```
# su - demouser
$ export LANG=ja_JP.UTF-8
$ ll
$ cd web-performance-tuning
$ ll /src/app
$ ll /public_html
$ ll /src/views
```

### DB(今回のインターンのターゲット)
DBはMySQLを利用します。

#### 起動停止
起動停止はrootユーザにて行います。

```
# systemctl [start|stop|restart] mysql
```

#### 設定ファイル
設定ファイルを編集する場合はrootにて行います。

```
/etc/my.cnf
```

#### アカウント
MySQLにログインするアカウントの情報です

|ユーザ|パスワード|用途|
|:-|:-|:-|
|root|vagrant|管理者|
|demouser|demopass|アプリケーション|

接続例

```
$ mysql -u demouser -p demopass
```

##### 利用ポート
3306番をLISTENしています

```
# lsof -i:3306
```

#### Mission1
実際に起動停止と各ユーザで接続を行ってみましょう

```
# systemctl stop mysql
# systemctl start mysql
# mysql -u root -p
# mysql -u demouser -p
```

#### Mission2
MySQLにログインしDB groupwokにあるテーブルの一覧、テーブル構造、各テーブルのデータ件数を把握しましょう

```
$ mysql -u root -p

mysql> use groupwork;
mysql> show tables;
mysql> show create table ~;
mysql> select count(*) from ~;
```

----

## 利用ツール、コマンド
ここではインターンにて利用するツール、コマンドについて説明します

### siege(負荷ツール)
継続的に負荷を与える事も可能なベンチマークツール

使用例
```
$ siege -c 10 http://localhost/
```

オプション説明（一部）
```
-c 同時実行数
-r リクエスト数
-f URLを記載したファイル
siege -c 10 -f hoge.txt
POSTを指定した例(ファイルに記述)
http://localhost POST key1=hoge&key2=fuga
-C 設定の確認
-h ヘルプ
```

### 情報採取

#### 主にロードアベレージの取得
にロードアベレージの取得
uptime
システムが起動してからの時間、システムを利用しているユーザ数左から1、5、15分のロードアベレージを表示

top
リアルタイムにロードアベレージ等の表示
プロセス情報の表示

ロードアベレージ(load average)とは?
CPUの空きを待つプロセス数を指す

#### プロセス、スレッドの情報
psコマンドを用いて確認します

例

```
# ps auxw
```

出力内容

```
%CPU	psコマンドを実行した際のそのプロセスのCPU使用率
%MEM	プロセスがどの程度物理メモリを消費しているかを百分率で表示する
VSZ、RSS	それぞれ、そのプロセスが確保している仮想メモリ領域のサイズ、物理メモリ領域のサイズ
STAT	プロセスの状態を示す。
TIME	CPUを使った時間を表示する
```

-L を付けるとスレッド単位で表示されます。以下はMySQLで使用されているスレッド数を数えています

```
# ps aux -L | grep mysql | wc -l
```

#### サーバの情報
利用可能なCPUの数を調べる

```
# cat /proc/cpuinfo
```

利用可能なメモリ量を調べる

```
# free
```

利用可能なDiskを調べる

```
# df
```

#### dstat
サーバ稼働情報全般

使用例

```
# dstat -tclmdrn
```

オプション

```
-c --cpu CPU使用率に関する情報表示
-d --disk ディスクのreadとwriteに関する情報表示
-g --page ページイン、ページアウトの情報表示
-i --int 割り込み回数に関する情報表示
-l --load ロードアベレージ
-m --mem メモリ使用状況に関する情報表示
-n --net ネットワークのreceiveとsendに関する情報表示
-p --proc プロセスの状態に関する情報表示
-s --swap スワップ使用状況に関する情報を表示
-y --sys 割り込みとコンテキストスイッチの回数
--socket TCPやUDPなどのソケットに関する情報表示
--tcp TCPコネクションの状態
--udp UDPコネクションの状態
--vm 仮想メモリ（ページフォルトなど）に関する情報表示
-a --all -cdngyを指定したときと同じ。表示オプション無指定の場合
--top-cpu 最もCPUを使っているプロセス表示
--top-bio 最もブロップI/Oをしているプロセス表示
--top-io 最もI/Oをしているプロセス表示 
```

----

## Tutorial
Tutorialではサーバのログイン、今回使う負荷ツール、各種情報採取コマンド、簡単なボトルネック箇所の解消を行い以降のChapterを行う上での練習を行います

### 各ターミナル設定
ターミナル[1-4]で各々が行う監視やログインを行う、尚ターミナルを増やし、この監視項目以外にも監視を行うことは可(例 `top`,`ps`,その他)

#### [ターミナル1] アプリケーションユーザ(demouser)に遷移
demouserに遷移し、ホームディレクトリ配下にアプリケーションが配置されていることを確認する。LANGの設定も行う

```
# su - demouser
$ export LANG=ja_JP.UTF-8
$ ll
```
アプリケーションの確認

```
$ curl http://localhost/tutorial?user_id=1000001
```

ブラウザでも同様に http://xxx.xxx.xxx.xxx/tutorial?user_id=1000001 を開いてみる(xxx.xxx.xxx.xxxは割り振られたサーバのIPアドレス)


#### [ターミナル2] MySQLに接続(processlist監視、実行計画確認用)
ログインはroot、demouserのどちらでも可、ログイン後アプリケーションが利用するDB`groupwork`に遷移する

```
# mysql -u root -p
mysql > user groupwork
mysql > show full processlist;
```
----
##### Mission！(2分)
[ターミナル1]で`curl http://localhost/tutorial?user_id=1000001`を実行中の裏で`show full processlist;`を行いましょう

```
mysql> show processlist;
+-----+----------+-----------+-----------+---------+------+--------------+-----------------------------------------------------------------+
| Id  | User     | Host      | db        | Command | Time | State        | Info                                                            |
+-----+----------+-----------+-----------+---------+------+--------------+-----------------------------------------------------------------+
| 913 | root     | localhost | NULL      | Query   |    0 | init         | show processlist                                                |
| 914 | demouser | localhost | groupwork | Execute |    2 | Sending data | select message,created_at from messages where user_id = 1000001 |
+-----+----------+-----------+-----------+---------+------+--------------+-----------------------------------------------------------------+
2 rows in set (0.00 sec)
```

----


#### [ターミナル3] MySQL スローログの監視
`tail`で常時監視する。停止は`CTRL+C`

```
# tail -f /var/lib/mysql/mysql-slow.log
```
----
##### Mission！(5分)
[ターミナル1]で`curl http://localhost/tutorial?user_id=1000001`を実行中の裏でスローログがどのように出力されているか確認しましょう。出力されている内容について調べましょう

```
# Query_time: 5.810426  Lock_time: 0.000045 Rows_sent: 16  Rows_examined: 1754073
SET timestamp=1485246826;
select message,created_at from messages where user_id = 1000001
```

----

#### [ターミナル4] リソース統計情報の監視
`dstat`を用いてサーバのリソースの利用状況を監視する

```
# dstat -tclmdrn
----system---- ----total-cpu-usage---- ---load-avg--- ------memory-usage----- -dsk/total- --io/total- -net/total-
     time     |usr sys idl wai hiq siq| 1m   5m  15m | used  buff  cach  free| read  writ| read  writ| recv  send
24-01 17:26:08|  2   0  97   1   0   0|   0 0.01 0.05| 551M 72.5M 1231M  147M| 628k 2602k|38.6  56.4 |   0     0
24-01 17:26:09|  1   0 100   0   0   0|   0 0.01 0.05| 551M 72.5M 1231M  147M|   0     0 |   0     0 | 280B  102B
24-01 17:26:10|  0   0 100   0   0   0|   0 0.01 0.05| 551M 72.5M 1231M  147M|   0     0 |   0     0 | 126B 1126B
24-01 17:26:11|  0   1 100   0   0   0|   0 0.01 0.05| 551M 72.5M 1231M  147M|   0     0 |   0     0 | 340B  406B
```

----
##### Mission！(5分)
`dstat -tclmdrn`で表示されている内容(どのような項目)について調べましょう

----

### ボトルネックSQLの特定
今回は`curl http://localhost/tutorial?user_id=1000001`の実行時間を改善する必要がある課題として見立てます。

#### [ターミナル1]負荷を掛ける
負荷ツール`siege`を用いて負荷をかけて行きます

```
$ siege -c 3 http://153.120.82.23/1day/tutorial?user_id=1
```
----
##### Mission!(3分)
siegeを実行中にターミナル[2-4]を確認しましょう

----

負荷ツール`siege`を`CTRL+C｀で止める

----
##### Mission!(3分)
siegeを停止後のターミナル[2-4]を確認しましょう。siege停止時に標準出力に出力される内容についても確認しましょう。

----

### ボトルネックSQLのボトルネック理由の調査
[ターミナル2]でスローログで検出したSQLで`explain`をしましょう

```
mysql> explain select message,created_at from messages where user_id = 1000001\G
*************************** 1. row ***************************
           id: 1
  select_type: SIMPLE
        table: messages
         type: ALL
possible_keys: NULL
          key: NULL
      key_len: NULL
          ref: NULL
         rows: 1675948
        Extra: Using where
1 row in set (0.00 sec)
```

### ボトルネック解消
ボトルネックのケースや状況により解消方法は多様にありますが今回はINDEXの作成で解消していきます。

#### [ターミナル1] INDEXの作成と実行計画の確認
INDEXを作成しexplainを実行し、作成前に確認したexplain(実行計画)と比較してみましょう。


```
mysql> alter table messages add index user_id(user_id);
Query OK, 0 rows affected (8.86 sec)
Records: 0  Duplicates: 0  Warnings: 0

mysql> explain select message,created_at from messages where user_id = 1000001\G
*************************** 1. row ***************************
           id: 1
  select_type: SIMPLE
        table: messages
         type: ref
possible_keys: user_id
          key: user_id
      key_len: 4
          ref: const
         rows: 16
        Extra: NULL
1 row in set (0.00 sec)
```

----
##### Mission!(1分)
INDEX作成前後でexplain(実行計画)の違いについて調べましょう

----

#### [ターミナル1]負荷を掛ける
負荷ツール`siege`を用いて負荷をかけて行きます

```
$ siege -c 3 http://localhost/tutorial?user_id=1000001
```
----
##### Mission!(3分)
siegeを実行中にターミナル[2-4]を確認しましょう

----

負荷ツール`siege`を`CTRL+C｀で止める

----
##### Mission!(3分)
siegeを停止後のターミナル[2-4]を確認しましょう。siege停止時に標準出力に出力される内容についても確認しましょう。**性能が改善したかについても確認しましょう(何故？改善できたのかも考えられるとベターです)**

----

ここまでがTutorialでした。午後はこのオペレーションを各Missionで行いながら実際に皆さんにチューニング(負荷、観測、解析、対策)をしてもらいます

----

## 参考
### 環境構築
今回のサーバ環境は以下のリポジトリのpuppetマニフェストを適用しています。

https://github.com/hironomiu/MySQL-Hands-On

### サンプルアプリのFork
このサンプルアプリをGitHub管理したい場合はこのリポジトリをForkし、作業するサーバのgitリポジトリのpush先をForkしたリポジトリに変更しましょう

```
$ git remote remove origin
$ git remote add origin <Forkしたリポジトリ名>
```

## その他
講義を進めるにあたって、使い慣れたパッケージを入れたい場合(yum等で可能なパッケージ)、サーバの動作に支障が無いものは自己責任でインストールして構いません
