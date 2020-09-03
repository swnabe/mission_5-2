<?php
    
     error_reporting(E_ALL & ~E_NOTICE); //Noticeを表示させない

    //接続
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
    //変数
    $name=$_POST["name"];
    $str=$_POST["str"];
    $pass=$_POST["pass"];
    $delpass=$_POST["delpass"];
    $editpass=$_POST["editpass"];
    $deletenum = $_POST["deletenum"];
    $date=date("Y/m/d H:i:s");
    $editnum=$_POST["editnum"];
    $editNO = $_POST['editNO'];
    if(file_exists($filename)){
        $postnum=count(file($filename))+1;
    }else{
        $postnum=1;
    }
    
    //投稿
    if(empty($editNO) &&!empty($_POST["submit"])){//編集対象番号がなく送信ボタンが押されているとき
        if(!empty($name) && !empty($str) && !empty($pass)){//名前・コメント・パスワードが入力されているとき
            //データ入力
        	$sql = $pdo -> prepare("INSERT INTO aaa (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
        	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
        	$sql -> bindParam(':comment', $str, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $pass, PDO::PARAM_STR);
        	$sql -> execute();
            $success="書き込み成功！<br>";
        }elseif(empty($name) && !empty($str) && !empty($pass)) {
            $namenot="名前が入力されていません";
        }elseif(!empty($name) && empty($str) && !empty($pass)){
            $strnot="コメントが入力されていません";
        }elseif(!empty($name) && !empty($str) && empty($pass)){
            $passnot="パスワードが入力されていません";
        }else{
            $miss="入力されていません";}
    }
    
    //削除
    if(!empty($deletenum) && !empty($_POST["delete"]) && !empty($delpass)){//もし削除対象番号、削除ボタン、削除パスワードが押されているとき
    	$sql = 'SELECT * FROM aaa';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
	        if($row['password']==$delpass){
	            $sql = 'delete from aaa where id=:id';
	            $stmt = $pdo->prepare($sql);
               	$stmt->bindParam(':id', $deletenum, PDO::PARAM_INT);
               	$stmt->execute();
                $del="削除しました";
            }else{
                $delpass_not="パスワードが違います";}
	    }
    }elseif(empty($delpass) && !empty($_POST["delete"])){
        $delpass_not="パスワードを入力してください";}
    
    
    //編集選択
        if(!empty($editnum) && !empty($_POST["edit"]) && !empty($editpass)){
        	$sql = 'SELECT * FROM aaa';
	        $stmt = $pdo->query($sql);
	        $results = $stmt->fetchAll();
	        foreach ($results as $row){
	            if($row['password']==$editpass && $editnum==$row["id"]){
	                $editnumber=$editnum;
                    $editname=$row["name"];
                    $editcomment=$row["comment"];
                    $passedit=$row["password"];
                    $editnow="編集中です";
                    $resetpass="※パスワードを再設定してください";
                }elseif($row['password']!=$editpass && $editnum==$row["id"]){
                    $editpass_not="パスワードが違います";}
	        }
        }elseif(!empty($_POST["edit"]) && empty($editpass)){
            $editpass_not="パスワードを入力してください";
        }

    // 編集実行
    if (!empty($editNO)){
        if(!empty($name) && !empty($str) && !empty($pass)) {
            $id=$editNO;
            $sql = 'UPDATE aaa SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
        	$stmt = $pdo->prepare($sql);
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
         	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
     	    $stmt->bindParam(':comment', $str, PDO::PARAM_STR);
      	    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
    	    $stmt->execute();
            $editok="編集しました";
        }else{
            $editnot="入力されていない項目があります";
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_3-5</title>
</head>
<body>
    <style>
        .class1{color:red;}
        .class2{color:blue;}
    </style>
    <h2>入力フォーム</h2>
    <form action="" method="post">
        <p><input type="text" name="name" placeholder="名前" value= "<?php echo $editname; ?>"> 
           <input type="text" name="str" placeholder="コメント" value="<?php echo $editcomment; ?>"> </p>
        <p><input type="password" name="pass" placeholder="パスワード" value="<?php echo $passedit; ?>">
           <input type="submit" name="submit" value="送信"></p>
        <p><input type="hidden" name="editNO" value="<?php if(isset($editnumber)) {echo $editnumber;} ?>"></p>
        <p><?php echo $success?></p>
        <div class="class1">
            <p><?php echo $namenot?></p>
            <p><?php echo $strnot?></p>
            <p><?php echo $passnot?></p>
            <p><?php echo $miss?></p>
            <p><?php echo $editnot ?></p>
        </div>
        <div class="class2">
            <p><?php echo $editnow?></p>
            <p><?php echo $resetpass ?></p>
        </div>
        <p><?php echo $editok ?></p>
        <br>
    <h2>削除</h2>
        <p><input type="number" name="deletenum" placeholder="削除対象番号"></p>
        <p><input type="password" name="delpass" placeholder="パスワード">
           <input type="submit" name="delete" value="削除"></p>
        <div><?php echo $del?></div>
        <div class="class1">
            <p><?php echo $delpass_not?></p>
        </div>
        <br>
    <h2>編集</h2>
        <p><input type="number" name= "editnum" placeholder="編集対象番号"></p>
        <p><input type="password" name="editpass" placeholder="パスワード">
           <input type="submit" name="edit" value="編集"></p>
        <div class="class1"><?php echo $editpass_not?></div>
        <br>
    </form>
        <h2>投稿一覧</h2>
        <?php       
            //表示
            $sql='SELECT * FROM aaa';
            $stmt=$pdo->query($sql);
            $results=$stmt->fetchAll();
            foreach($results as $row){
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date']."<br>";
                echo "<hr>";
            }
        ?>
        <br>
</body>
</html>