<?php
    session_start();    // セッション開始（$_SESSION変数を使うために必要）
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    // テーブル作成処理
    $sql = "CREATE TABLE IF NOT EXISTS tbposts"  // テーブルの作成
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment text,"
    . "date text,"
    . "pass text"
    .");";
    $stmt = $pdo->query($sql);

    /*
    // テーブル一覧の表示
    $sql ='SHOW TABLES';
    $result = $pdo -> query($sql);
    foreach ($result as $row){
        echo $row[0];
        echo '<br>';
    }
    echo '表示終了<br>';
    */

    /*
    // テーブルの構成詳細表示
    $sql ='SHOW CREATE TABLE tbposts';
    $result = $pdo -> query($sql);
    foreach ($result as $row){
        echo $row[1];
        echo '<br>';
    }
    */

    /*
    // テーブルの削除
    $sql = 'DROP TABLE IF EXISTS tbposts';
    $stmt = $pdo->query($sql);
    echo '削除完了<br>';
    */
?>
<?php
    // 編集が押されたときの処理 
    // $name_editと$comment_editと先に定義しておくためにHTMLより先に記述
    if(isset($_POST["edit"])) {
        if(strlen($_POST["edit_num"])) {    // 編集番号が入力されていたら
            $edit_num = $_POST["edit_num"]; // 編集番号をPOSTから取得
            $edit_pass = $_POST["edit_pass"];

            $sql = 'SELECT * FROM tbposts WHERE id=:id'; // sql命令定義
            $stmt = $pdo->prepare($sql);                  // sql命令セット
            $stmt->bindParam(':id', $edit_num, PDO::PARAM_INT); // $edit_numを:idにバインド
            $stmt->execute();                             // sql命令実行
            $results = $stmt->fetchAll();

            // 投稿のパスワードを取得
            foreach($results as $row) {
                $row_pass = $row['pass'];
            }

            if($edit_pass == $row_pass) {   // 入力されたパスワードと実際のパスワードを比較
                foreach ($results as $row) {
                    //$rowの中にはテーブルのカラム名が入る
                    $name_edit = $row['name'];
                    $comment_edit = $row['comment'];
                    $pass_edit = $row['pass'];
                }
            } else {
                $_SESSION['pass_error'] = "<b>パスワードが違います</b><br>";
                header("Location: [掲示板のURL]");
                exit;
            }
            
        }
    }
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
    <style>
        body {
            background-color: #f5fffa;
        }
        ul li {
            list-style: none;
        }
        label {
            width: 145px;
            float: left;
        }
        div.forms {
            margin-bottom: 30px;
            padding: 10px;
            padding-right: 50px;
            border: 3px solid #4dffff;
            border-radius: 15px;
            display: inline-block;
            background-color: #e0ffff;
        }
        hr {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2 style="margin-left: 40px;">行ってみたい県、国</h2>
    
    <!-- 投稿エラーメッセージ -->
    <?php if(!empty($_SESSION['post_error'])){ ?>
            <p class="post_error">
                <?= $_SESSION['post_error'] ?>
            </p>
            
            <?php unset($_SESSION['post_error']); ?>
    <?php } ?>

    <!-- 削除完了メッセージ -->
    <?php if(!empty($_SESSION['delete_message'])){ ?>
            <p class="delete-success">
                <?= $_SESSION['delete_message'] ?>
            </p>
            
            <?php unset($_SESSION['delete_message']); ?>
    <?php } ?>

    <!-- 削除番号未入力メッセージ -->
    <?php if(!empty($_SESSION['delete_error'])){ ?>
            <p class="delete-error">
                <?= $_SESSION['delete_error'] ?>
            </p>
            
            <?php unset($_SESSION['delete_error']); ?>
    <?php } ?>
    
    <!-- 編集完了メッセージ -->
    <?php if(!empty($_SESSION['edit_message'])){ ?>
            <p class="edit-message">
                <?= $_SESSION['edit_message'] ?>
            </p>
            
            <?php unset($_SESSION['edit_message']); ?>
    <?php } ?>

    <!-- 編集番号未入力メッセージ -->
    <?php if(!empty($_SESSION['edit_error'])){ ?>
            <p class="edit-error">
                <?= $_SESSION['edit_error'] ?>
            </p>
            
            <?php unset($_SESSION['edit_error']); ?>
    <?php } ?>

    <!-- パスワードエラーメッセージ -->
    <?php if(!empty($_SESSION['pass_error'])){ ?>
            <p class="pass-error">
                <?= $_SESSION['pass_error'] ?>
            </p>
            
            <?php unset($_SESSION['pass_error']); ?>
    <?php } ?>

    <!-- POST方式のフォームの作成 -->
    <div class="forms">
        <form action="" method="post">
            <ul>
                <li>
                    <b>【  投稿フォー ム  】</b><br>
                    <label for="name">名前:</label>
                    <input type="text" name="name" value="<?php 
                    if(isset($name_edit)) {
                        echo $name_edit;
                    }
                    ?>"><br>
                </li>
                <li>
                    <label for="comment">コメント:</label>
                    <input type="text" name="comment" value="<?php 
                    if(isset($comment_edit)) {
                        echo $comment_edit;
                    }
                    ?>"><br>
                </li>
                <li>
                    <label for="pass">パスワード：</label>
                    <input type="password" name="pass" value="<?php 
                    if(isset($pass_edit)) {
                        echo $pass_edit;
                    }
                    ?>"><br>
                </li>
                <input type="submit" name="submit"><br>
                <hr widh="100">
                <br>
                <b>【  削除フォー ム  】</b><br>
                <li>
                    <label for="delete_num">削除する投稿番号：</label>
                    <input type="number" name="delete_num"><br>
                </li>
                <li>
                    <label for="delete_pass">パスワード：</label>
                    <input type="password" name="delete_pass"><br>
                </li>
                <input type="submit" name="delete" value="削除"><br>
                <hr widh="100">
                <br>
                <b>【  編集フォー ム  】</b><br>
                <li>
                    <label for="edit_num">編集する投稿番号：</label>
                    <input type="number" name="edit_num"><br>
                </li>
                <li>
                    <label for="edit_pass">パスワード：</label>
                    <input type="password" name="edit_pass"><br>
                </li>
                <input type="submit" name="edit" value="編集"><br>
                <!-- 編集番号取得用隠しフォーム -->
                <input type="hidden" name="edit_num0" value="<?php 
                if(isset($edit_num)) {
                    echo $edit_num;
                }
                ?>">
            </ul>
        </form>
    </div><br>
    <b>【  投稿一覧  】</b><br>
    <?php
        if((!isset($_POST["name"]) && !isset($_POST["comment"]))) { // ページを最初に開いたときも投稿内容を表示する
            $sql = 'SELECT * FROM tbposts';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].'. ';
                echo $row['name'].': ';
                echo $row['comment'].' ';
                echo $row['date'].'<br>';
            }
        }
        // ---- 新規投稿及び編集処理 ----
        if(isset($_POST['submit']) && !empty($_POST['edit_num0'])) { // 編集モード時の処理
            $edit_num0 = $_POST['edit_num0'];   // 編集番号
            $name = $_POST['name']; // 編集後の名前
            $comment = $_POST['comment'];   // 編集後のコメント
            $pass = $_POST["pass"]; // 編集後のパスワード

            // ---- 編集処理 ----
            $sql = 'UPDATE tbposts SET name=:name,comment=:comment,pass=:pass WHERE id=:id';   // sql命令定義
            $stmt = $pdo->prepare($sql);    // sql命令セット
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);   // $nameを:nameにバインド
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR); // $commentを:commentにバインド
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);    // $passを:passにバインド
            $stmt->bindParam(':id', $edit_num0, PDO::PARAM_INT);    // $edit_num0を:idにバインド
            $stmt->execute();

            $_SESSION['edit_message'] = '<b>'.$edit_num0.'番目の投稿を編集しました</b><br>';
            header("Location: [掲示板のURL]");
            exit;
        } elseif(isset($_POST['submit']) && empty($_POST['edit_num0'])){    // 新規投稿モード時の処理
            if(isset($_POST["name"]) && isset($_POST["comment"])) {
                if(strlen($_POST["name"]) && strlen($_POST["comment"])) {   // 名前とコメント両方入力されていれば処理を行う
                    // 投稿データ用変数の定義
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $pass = $_POST["pass"];
                    $date = date("(Y/m/d H:i:s)");
                    // $pass = $_POST["pass"];         // パスワード受け取り
                    
                    // データベースへの書き込み
                    $sql = $pdo -> prepare("INSERT INTO tbposts (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)"); // sql命令セット
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);  // $nameを:nameにバインド
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);    // $commentを:commentにバインド
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);  // $dateを:dateにバインド
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);    // $passを:passにバインド
                    $sql -> execute();  // sql命令実行
                    
                    // テーブルレコードの表示
                    $sql = 'SELECT * FROM tbposts';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        //$rowの中にはテーブルのカラム名が入る
                        echo $row['id'].'. ';
                        echo $row['name'].': ';
                        echo $row['comment'].' ';
                        echo $row['date'].'<br>';
                    }

                    // リロードによる再投稿防止処理
                    header("Location: [掲示板のURL]");
                    exit;
                } elseif(strlen($_POST["name"]) && !strlen($_POST["comment"])) {    // コメントが入力されていなかったら
                    $_SESSION['post_error'] = "<b>コメントを入力してください</b><br>";
                    header("Location: [掲示板のURL]");
                    exit;
                } elseif(!strlen($_POST["name"]) && strlen($_POST["comment"])) {    // 名前が入力されていなかったら
                    $_SESSION['post_error'] = "<b>名前を入力してください</b><br>";
                    header("Location: [掲示板のURL]");
                    exit;
                } else {    // 両方入力されていなかったら
                    $_SESSION['post_error'] = "<b>名前とコメントを入力してください</b><br>";
                    header("Location: [掲示板のURL]");
                    exit;
                }
            }
        } elseif(isset($_POST["delete"])) {
            // ---- 削除ボタンが押されたときの動作 ----
            
            if(strlen($_POST["delete_num"])) {      // 削除番号が入力されていたら処理を実行
                $delete_num = $_POST["delete_num"];         // 削除番号の定義
                $delete_pass = $_POST["delete_pass"];   // パスワード受け取り

                $sql = 'SELECT * FROM tbposts WHERE id=:id'; // sql命令定義
                $stmt = $pdo->prepare($sql);                  // sql命令セット
                $stmt->bindParam(':id', $delete_num, PDO::PARAM_INT); // $delete_numを:idにバインド
                $stmt->execute();                             // sql命令実行
                $results = $stmt->fetchAll();

                // 投稿のパスワードを取得
                foreach($results as $row) {
                    $row_pass = $row['pass'];
                }
                 
                if($delete_pass == $row_pass) { // 入力されたパスワードと実際のパスワードを比較
                    
                    $sql = 'delete from tbposts where id=:id';  // sql命令定義
                    $stmt = $pdo->prepare($sql);    // sql命令セット
                    $stmt->bindParam(':id', $delete_num, PDO::PARAM_INT);   // $delete_numを:idにバインド
                    $stmt->execute();   // sql命令実行

                    $_SESSION['delete_message'] = '<b>'.$delete_num.'番目の投稿を削除しました</b><br>';    // 削除完了メッセージ
                } else {
                    $_SESSION['pass_error'] = "<b>パスワードが違います</b><br>";
                }
                header("Location: [掲示板のURL]");
                exit;
            } else {    // 削除番号が入力されていなかったら
                $_SESSION['delete_error'] = "<b>削除番号を入力してください</b><br>";
                header("Location: [掲示板のURL]");
                exit;
            }
        } elseif(isset($_POST['edit']) && strlen($_POST['edit_num'])) {
            // 編集作業中のテーブルレコード表示
            $sql = 'SELECT * FROM tbposts';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].'. ';
                echo $row['name'].': ';
                echo $row['comment'].' ';
                echo $row['date'].'<br>';
            }
        } elseif(isset($_POST['edit']) && empty($_POST['edit_num'])) {
            $_SESSION['edit_error'] = "<b>編集番号を入力してください</b><br>";
            header("Location: [掲示板のURL]");
            exit;
        }
    ?>
</body>
</html>