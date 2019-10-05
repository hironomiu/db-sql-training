# チュートリアル01

## 事前準備
ここではこれから行うチュートリアルの事前準備と環境理解を行います

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

