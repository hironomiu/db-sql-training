# tuning02

## 重たいSQLの特定と改善(作業時間目安40分)
この章では重たいSQLの特定、検証、改善を行います

### [ターミナル1] コンテンツの確認
curlで確認後ブラウザでも確認しましょう

```
$ curl http://localhost/tuning02-1
$ curl http://localhost/tuning02-2?title=Hello!!
```

### 負荷検証
同時実行数を`-c 10`で動作確認をしましょう

#### [ターミナル1] siegeを実行する(-c 10)
-fオプションでファイルを指定して実行すること

```
$ siege -c 10 -f ~/web-performance-tuning/load-test/siege_tuning02.txt

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
siege実行中にブラウザで 「/tuning02-1」「/tuning02-2?title=Hello!!」 を開いてみましょう

### 実行計画確認
ターミナル2,3で出力されたSQLをターミナル2で`explain`を使い確認しましょう

```
mysql> explain <SQL> \G
```

----

### Mission ボトルネックSQLの改善
実行計画確認で洗い出したボトルネックと思われるSQLを性能改善しましょう。改善後「負荷検証」シナリオを実施しどのように改善されたか確認しましょう。**explainの改善前後の確認は必ずしましょう**。各ターミナルから出力される内容がどのように変わったか確認しましょう

----
### Mission Chapter3の性能限界
「Mission ボトルネックSQLの解消」が解消された場合、ターミナル1のsiege -cオプションの同時実行数の上限はいくつあたりか確認しましょう。

----
