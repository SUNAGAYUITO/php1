<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// 初期化（POST取得）

$age = $_POST['age_group'];
$can_express = $_POST['can_express'];
$reason = htmlspecialchars($_POST['reason'], ENT_QUOTES, 'UTF-8');

// ファイル保存
$date = date("Y-m-d H:i:s");
$data = "{$date},{$age},{$can_express},{$reason}\n";

if (!file_exists('./data')) {
    mkdir('./data', 0777, true);
}
$file = fopen("./data/data.txt", "a");
fwrite($file, $data);
fclose($file);

// ここから全件集計処理
$ages = ['10代' => 0, '20代' => 0, '30代' => 0, '40代〜' => 0];
if (file_exists("./data/data.txt")) {
    $lines = file("./data/data.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        [$date_line, $age_line, $can_express_line, $reason_line] = explode(",", $line, 4);
        if (isset($ages[$age_line]) && $can_express_line === 'はい') {
            $ages[$age_line]++;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>アンケート結果</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <script type="text/javascript">
    google.charts.load('current', {packages: ['corechart']}); //最新版のchart読み込んでグラフを使えるようにした
    google.charts.setOnLoadCallback(function() {
   const data = google.visualization.arrayToDataTable([
  ['年代', '表現できている'],
  ['10代', <?= $ages['10代'] ?>],
  ['20代', <?= $ages['20代'] ?>],
  ['30代', <?= $ages['30代'] ?>],
  ['40代〜', <?= $ages['40代〜'] ?>]
]);


      const options = {
        title: '「自分をうまく表現できている」と感じる人の割合',
        hAxis: {title: '年代'},
        vAxis: {title: '人数', minValue: 0},
        legend: {position: 'none'},
        colors: ['#60A5FA']
      };

      const chart = new google.visualization.ColumnChart(document.getElementById('chart_div')); //ColumnChartは縦棒グラフ
      chart.draw(data, options);
    });

    $(function() {
      $('#showMore').on('click', function() {
        $('#followUp').removeClass('hidden');
      });
    });
  </script>
</head>
<body class="bg-gray-50 p-6">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow space-y-4">
    <h2 class="text-xl font-bold">アンケート結果</h2>
    <div id="chart_div" style="width: 100%; height: 400px;"></div>

    <div class="mt-4">
      <p><strong>あなたの回答：</strong></p>
      <ul class="list-disc pl-5 text-gray-700">
        <li>年代：<?= $age ?></li>
        <li>表現できているか：<?= $can_express ?></li>
        <li>理由：<?= nl2br($reason) ?></li> 
     <!-- PHPの組み込み関数で、「文字列の中の \n（改行コード）を <br> に変換」する -->
      </ul>
    </div>

    <button id="showMore" class="bg-gray-500 text-white px-4 py-2 rounded mt-4">追加質問を見る</button>

    <div id="followUp" class="hidden mt-4 border-t pt-4">
      <p class="font-semibold mb-2">追加質問：</p>
      <ul class="list-disc pl-5 text-gray-700">
        <li>どんなときに「自分をうまく表現できた」と感じますか？</li>
        <li>逆に、うまくいかなかったときはどんな場面でしたか？</li>
        <li>自分を表現しやすくなる環境とは何だと思いますか？</li>
      </ul>
    </div>
    
  </div>
</body>

</html>

