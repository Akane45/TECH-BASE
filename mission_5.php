<?php
//4-1：データベースへ接続
$dsn =  'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//4-2：テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS tbmission5"
." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
  . "comment TEXT,"
  . "time DATETIME,"
  . "pass char(32)"
	.");";
  $stmt = $pdo->query($sql);

   //4-3：テーブル一覧
$sql ='SHOW TABLES';
$result = $pdo -> query($sql);
	foreach ($result as $row){
  }

//4-4：テーブル中身を確認
$sql ='SHOW CREATE TABLE tbmission5';
$result = $pdo -> query($sql);
foreach ($result as $row){
  }

//新規投稿
if(isset($_POST["name"],$_POST["comment"],$_POST["password"])){
$name = $_POST["name"];
$comment = $_POST["comment"];
$time = date('Y/m/d G:i:s');
$pass = $_POST["password"];
  // editNoがないときは新規投稿、ある場合は編集を判断
	if (empty($_POST['edit_No'])) {
  //テーブルにinsertを使ってデータを入力
  $sql = $pdo -> prepare("INSERT INTO tbmission5 (name, comment,  time, pass) VALUES (:name, :comment, :time, :pass)");
  $sql -> bindParam(':name', $name, PDO::PARAM_STR);
  $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
  $sql -> bindParam(':time', $time, PDO::PARAM_STR);
  $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
    if(empty($name)){
      echo "名前が入力されていません。<br><br>";
    }elseif(empty($comment)){
      echo  "コメントが入力されていません。<br><br>";
    }elseif(empty($pass)){
      echo "パスワードが入力されていません。<br><br>";
    }elseif(!empty($pass)){
    $sql -> execute();
    }
  }else{
  //編集書き込み機能
  $id = $_POST["edit_No"];
  $name = $_POST["name"];
  $comment = $_POST["comment"];
  $pass = $_POST["password"];
  // UPDATE文を変数に格納
  $sql = 'SELECT * FROM tbmission5 WHERE id = id';
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
  if(empty($pass)){
    echo "パスワードが入力されていません。<br><br>";
  }else if(!empty($pass)){
    foreach ($results as $row){
        //投稿番号と編集対象番号を比較
      if($row['id'] == $id){ //もしexplodeされた投稿番号とポストされた投稿番号が一致したら
        if($row['pass'] == $pass){
          $sql = 'update tbmission5 set name=:name,comment=:comment where id=:id';
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':name', $name, PDO::PARAM_STR);
          $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
          $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        //編集のフォームから送信された値と差し替えて上書き
          $stmt->execute();
        }elseif($row['pass'] !== $_POST["password"]){
          echo "パスワードが違います";
        }
      }else{
        //一致しなかったところはそのまま書き込む
        $stmt->execute();
      }
    }
  }
}
}//投稿終了

//削除機能
if(isset($_POST["delete"])){
$id = $_POST["delete"];//削除したい番号
$pass = $_POST["del_password"];
// UPDATE文を変数に格納
$sql = 'SELECT * FROM tbmission5 WHERE id = id';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
  if(empty($_POST["delete"])){
    echo "削除対象番号が入力されていません。<br><br>";
  }elseif(empty($pass)){
    echo "パスワードが入力されていません。<br><br>";
  }elseif(!empty($_POST["delete"])){
    foreach ($results as $row){
    //それぞれの番号が削除対象番号か照合
      if ($row['id'] !== $id){
        //入力データのファイル書き込み
        $stmt->execute();
      }
      if ($row['id'] == $id){
        if($row['pass'] != $pass){
          echo "パスワードが違います。<br><br>";
        }elseif($row['pass'] = $pass){
          $sql = 'delete from tbmission5 where id=:id'; //where以降をなくすと全部消える
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          //入力データのファイル書き込み
          $stmt->execute();
				}
      }
    }
  }
}//削除終了

//【編集】編集番号から投稿フォームへ表示
if(isset($_POST["edit"])){
	$edit_pw = ($_POST["edit_password"]);
	if(empty($_POST["edit"])){
		echo "編集対象番号が入力されていません。<br><br>";
	}elseif(empty($edit_pw)){
		echo "パスワードが入力されていません。<br><br>";
	}elseif(!empty($_POST["edit"])){
  $edit = $_POST["edit"];//編集したい番号
  $sql = 'SELECT * FROM tbmission5';
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
    foreach ($results as $row){
    //それぞれの番号が$delete番号と同じか照合
      if ($row['id'] == $edit){
        //パスワードがあっているか
        if($row['pass']  != $edit_pw){
          echo "パスワードが違います。<br><br>";
        }else if($row['pass'] == $edit_pw){
        //投稿番号と編集対象番号が一致したらその投稿の「名前」と「コメント」を取得
        $editnumber = $row['id'];
        $username = $row['name'];
        $text = $row['comment'];
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>掲示板5-1</title>
	</head>
	<body>
	<html>
		<head>
			<meta charset="utf-8">
		</head>
	<body>
		<form method = "post">
		<p>【 投稿フォーム 】</p>
    <p>
			<input type="text" name="name" placeholder="名前" value= "<?php if(isset($username)) {echo $username;}?>">
    </p>
    <p>
			<input type="text" name="comment" placeholder="コメント" value= "<?php if(isset($text)) {echo $text;}?>">
    </p>
    <p>
      <input type="password" v-show-password-input name="password" placeholder="パスワード"	>
    	<input type="text" name="edit_No" placeholder="編集番号を反映" value= "<?php if(isset($editnumber)) {echo $editnumber;}?>">
			<input type="submit" value="送信">
    </p>
		</form>
		<p>【 削除フォーム 】</p>
		<p>
		<form method = "post">
			<input type="text" name="delete" placeholder="削除対象番号">
    </p>
    <p>
      <input type="password" name="del_password" placeholder="パスワード">
			<input type="submit" value="削除">
		</form>
		</p>
    <form method = "post">
		<p>【 編集フォーム 】</p>
    <p>
			<input type="text" name="edit" placeholder="編集対象番号">
    </p>
    <p>
      <input type="password" name="edit_password" placeholder="パスワード">
			<input type="submit" value="編集">
    </p>
		</form>
    <br>
    <br>

	</body>
</html>
<?php
//4-6:入力したデータをselectによって表示
$sql = 'SELECT * FROM tbmission5';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
	//$rowの中にはテーブルのカラム名が入る
	echo $row['id'].',';
	echo $row['name'].',';
  echo $row['comment'].',';
  echo $row['time'].'<br>';
  echo "<hr>";
}
?>