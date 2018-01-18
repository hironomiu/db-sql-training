# チュートリアル02

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
