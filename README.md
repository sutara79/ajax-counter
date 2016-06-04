# jquery.ajax-counter
jQuery plugin for generating a simple hit-counter using jQuery + PHP.

- Webページのアクセス数をカウントできます。
- クッキーによって、24時間以内の同一のブラウザからのアクセスはカウントしません。
- 累計、今日、昨日のアクセス数を表示することができます。
- 過去の日計アクセス数の保存機能があります。
- Ajax を利用しているので、ページがキャッシュされていてもちゃんとカウントしてくれます。

## Forked from
- [shun91/ajax-counter](//github.com/shun91/ajax-counter)

上記のプロジェクトをフォークし、独自に編集しました。  
変更点は下記のとおりです。

- ページ更新による連続カウントをクッキーによって防ぐようにした。
- jQueryプラグインとして記述を整えた。
- HTMLから呼び出す際にオプションを渡せるようにした。
- PHPの処理をクラス化した。

## Demo
http://usamimi.info/~sutara/sample2/ajax-counter/

## Usage
###### Set file permission
- `dat/` (707 or 777)
    - `count.dat` (606 or 666)
    - `log.dat` (606 or 666)

###### Load plugin
```html
<script src="//code.jquery.com/jquery-2.2.3.min.js"></script>
<script src="jquery.ajax-counter.js"></script>
```

###### JavaScript
```javascript
$(function () {
  $("#counter").ajaxCounter("jquery.ajax-counter.php");
});
```

###### HTML
```html
<div id="counter">Total: <span class="count-total"></span></div>
```

## Options
- **`dat_dir`**  
  Path to "dat" directory that contains "count.dat" and "log.dat".  
  In relative path, the place with "jquery.ajax-counter.php" is the current directory.  
  (datフォルダへのパス。相対パスの場合は、PHPファイルを基準とする)
    - default: `dat/`
- **`total`**  
  CSS selectors for an element that displays total count.  
  (総計を表示する要素のセレクタ)
    - default: `.count-total`
- **`today`**  
  CSS selectors for an element that displays today's count.  
  (今日の訪問者数を表示する要素のセレクタ)
    - default: `.count-today`
- **`yesterday`**  
  CSS selectors for an element that displays yesterday's count.  
  (昨日の訪問者数を表示する要素のセレクタ)
    - default: `.count-yesterday`

###### Example
```javascript
$(function () {
  $("#counter").ajaxCounter(
    "jquery.ajax-counter.php",
    {
      dat_dir: "ajax-counter-dat/",
      total: ".total-area",
      today: ".today-area",
      yesterday: ".yesterday-area"
    }
  );
});
```

## Author
宮崎 雄策 (Yuusaku Miyazaki) <toumin.m7@gmail.com>

## License
[MIT License](//www.opensource.org/licenses/mit-license.php)
