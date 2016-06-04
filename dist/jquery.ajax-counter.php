<?php
new AjaxCounter(
  $_GET['dat_dir'], // ユーザーが設定しJSから送られてくる、datフォルダへのパス
  'count.dat',      // 訪問数を記録するファイル名 
  'log.dat'         // 日別に記録し続けるファイル名
);

/**
 * 訪問者数を1つ増加させてファイルに記録するクラス
 */
class AjaxCounter {
  /**
   * 使用するパラメータ
   */
  private $count_file;
  private $log_file;
  /**
   * コンストラクタ
   * @param {string} [$dat_dir] - datフォルダへのパス
   * @param {string} [$count_file] - 訪問数を記録するファイル名
   * @param {string} [$log_file] - 日別に記録し続けるファイル名
   */
  public function __construct ($dat_dir, $count_file, $log_file) {
    $this->isAjax();
    $this->setParam($dat_dir, $count_file, $log_file);
    $this->setFile();
    $this->doCount();
  }
  /**
   * Ajaxでなければ終了する
   */
  private function isAjax () {
    if (
      isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
      return true;
    }
    exit('only allow access via Ajax');
  }
  /**
   * 訪問数保管ファイルをパラメータに格納する
   * @param {string} [$dat_dir] - datフォルダへのパス
   * @param {string} [$count_file] - 訪問数を記録するファイル名
   * @param {string} [$log_file] - 日別に記録し続けるファイル名
   */
  private function setParam ($dat_dir, $count_file, $log_file) {
    $this->count_file = $dat_dir . $count_file;
    $this->log_file = $dat_dir . $log_file;
  }
  /**
   * ファイルが存在しなければ、作成して初期化する
   */
  private function setFile () {
    if (!file_exists($this->count_file)) {
      file_put_contents($this->count_file, '19000101,0,0,0', LOCK_EX);
    }
    if (!file_exists($this->log_file)) {
      file_put_contents($this->log_file, '', LOCK_EX);
    }
  }
  /**
   * カウント処理を行い，アクセス数を json 形式で出力する
   */
  private function doCount () {
    $count_fp = fopen($this->count_file, 'r+'); // アクセス数ファイルをopen
    if (flock($count_fp, LOCK_EX)) { // アクセス数ファイルをLock
      $countData = fgets($count_fp); // アクセス数データを$countに読み込む
      $count = explode(',', $countData); // $countDataを,で区切って [0]日付 [1]累計 [2]今日 [3]昨日

      // 24時間期限のクッキーがまだ生きている場合はカウントしない
      if (!isset($_COOKIE['visit_counter'])) {
        setcookie('visit_counter', 'true', time() + 60 * 60 * 24 * 1);

        $count[1] += 1; // 累計アクセス数を1増やす

        // タイムゾーンを日本標準時に（WordPress対策）
        date_default_timezone_set('Asia/Tokyo');
        $now = date('Ymd'); //今日の日付を8桁で取得
        date_default_timezone_set('UTC');

        if ($now === $count[0]) { // 日付が一致したら，今日アクセス数を1増やす
          $count[2] += 1;
        } else { // 日付が変わった場合
          $this->writeLog($count[0], $count[2]); // ログに書き込む
          $count[3] = $count[2]; // 今日を昨日に
          $count[2] = 1; // 今日をリセット
        }
        ftruncate($count_fp, 0); // 中身をリセット
        rewind($count_fp); // アクセス数ファイルのファイルポインタを先頭へ
        fwrite($count_fp, $now . ',' . $count[1] . ',' . $count[2] . ',' . $count[3]); // アクセス数ファイルに新たな値を書き込む
        flock($count_fp, LOCK_UN); // アクセス数ファイルをunLock
      }
    }
    fclose($count_fp); // アクセス数ファイルをclose
    // アクセス数を json 形式にして出力
    $counts = array('total' => $count[1], 'today' => $count[2], 'yesterday' => $count[3]);
    echo json_encode($counts);
  }
  /**
   * ログにアクセス数を書き込む
   */
  public function writeLog($day, $count) {
    $fp = fopen($this->log_file, 'a+'); // ログファイルをopen
    if (flock($fp, LOCK_EX)) { // ログファイルをLock
      fwrite($fp, "\n"); // ログファイルの最後尾に書き込む
      fwrite($fp, $day . ',' . $count); // ログファイルの最後尾に書き込む
      flock($fp, LOCK_UN); // ログファイルをunLock
    }
    fclose($fp); // ログファイルをclose
  }
}
