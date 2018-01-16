# tuning04
## JOIN SQLチューニング (作業時間目安40分)

この章ではJOINしたSQLのチューニングを行います

### 準備
#### [ターミナル1]
curlでサンプルアプリの確認をする。実行時間も確認する。

```
$ curl http://localhost/tuning04
$ time curl http://localhost/tuning04

real    0m41.670s
user    0m0.003s
sys     0m0.006s
```


### 負荷検証
同時実行数を`-c 10`で動作確認をしましょう

#### [ターミナル1] siegeを実行する(-c 10)
同時実行数10で負荷をかけましょう

```
$ siege -c 10 http://localhost/tuning04

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

### 実行計画確認
ターミナル2,3で出力されたSQLをターミナル2で`explain`を使い確認しましょう

```
mysql> explain <SQL> \G
```

----

### Mission ボトルネックSQLの改善
実行計画確認で洗い出したボトルネックと思われるSQLを性能改善しましょう。改善後「/tuning045」を実行しどのように改善されたか確認しましょう。explainの改善前後の確認は必ずしましょう。各ターミナルから出力される内容がどのように変わったか確認しましょう

#### SQLの改善
explainの改善前後の確認

#### 負荷ツールによる1秒あたりの処理量
siegeの「-c」オプジョンを変更し、siege終了時に標準出力で出力される「Transaction rate」がどのくらい向上するか

#### ヒント
インデックスの作成、テーブルの作成、APの変更(SQLの作成、SQLの修正)などを総動員して改善しましょう

**目標 1秒を切り、Transaction rateを最大化しましょう(100以上)**
----
