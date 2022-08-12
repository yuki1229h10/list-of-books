<?php

// POST を受け取る変数を初期化
$dinosaur = '';

// セレクトボックスの値を格納する配列
$dinosaursList = array(
  "選択してください",
  "トリケラトプス",
  "ステゴサウルス",
  "パラサウロロフス",
  "プテラノドン",
  "ティラノサウルス"
);

// 戻ってきた場合
if (isset($_POST['dinosaur'])) {
  $dinosaur = $_POST['dinosaur'];
}

?>

<body>
  <form method="POST" action="afterSubmit.php">
    <table>
      <tbody>
        <tr>
          <th>好きな恐竜を選んでね</th>
          <td>
            <select name="dinosaur">
              <?php
              foreach ($dinosaursList as $value) {
                if ($value === $dinosaur) {
                  // ① POST データが存在する場合はこちらの分岐に入る
                  echo "<option value='$value' selected>" . $value . "</option>";
                } else {
                  // ② POST データが存在しない場合はこちらの分岐に入る
                  echo "<option value='$value'>" . $value . "</option>";
                }
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <th></th>
          <td>
            <input type="submit" value="送信する">
          </td>
        </tr>
      </tbody>
    </table>
  </form>
</body>
