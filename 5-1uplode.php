<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>5-1.php</title>
    </head>
    <body>
        <?php
            //DBに接続
        $dsn='データベース名;';
        $user='ユーザー名';
        $db_pass='パスワード';
        $pdo=new PDO(
            $dsn,$user,$db_pass,
            array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)
        );

            //テーブルを作成
        $sql = <<<EOD
            CREATE TABLE IF NOT EXISTS tb1
            (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name varchar(64),
            comment TEXT,
            password varchar(64),
            created_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
        EOD;
            $stmt = $pdo->query($sql);

            $EDIT_name="";
            $EDIT_comment="";
            $EDIT_No="";

            if (!empty($_POST["num_edit"]) && !empty($_POST["pass_edit"])) {
                $enum=$_POST["num_edit"];
                $epass=$_POST["pass_edit"];
                $sql='SELECT*FROM tb1 WHERE id=:enum AND password=:epass';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':enum',$enum,PDO::PARAM_INT);
                $stmt->bindParam(':epass',$epass,PDO::PARAM_STR);
                $stmt->execute();
                $result=$stmt->fetch();
                $EDIT_name=$result['name'];
                $EDIT_comment=$result['comment'];
                $EDIT_No=$result['id'];
            }
        ?>

        <form action="" method="POST">
            name:<input type="text" name="name"><br>
            comment:<input type="text" name="comment"><br>
            password:<input type="text" name="password" placeholder="password"><br>
            <input type="submit" name="sub1" value='post'>
        <hr>
            number:<input type="number" name="num_edit" placeholder="id"><br>
            password:<input type="text" name="pass_edit" placeholder="password"><br>
            <br>
            name:<input type="text" name="EDIT_name"
                value=<?php echo $EDIT_name; ?>><br>
            comment:<input type="text" name="EDIT_comment"
                value=<?php echo $EDIT_comment; ?>><br>
            <input type="hidden" name="EDIT_No"
                value=<?php echo $EDIT_No; ?>>
            <input type="submit" name="sub2" value="edit">
        <hr>
            number:<input type="number" name="num_delete" placeholder="id"><br>
            password:<input type="text" name="pass_delete" placeholder="password"><br>
            <input type="submit" name="sub3" value="delete">
        </form>

        <?php

            //新規投稿
        if(!empty($_POST["name"]) && !empty($_POST["comment"])){
            $name=$_POST["name"];
            $comment=$_POST["comment"];
            $password=$_POST["password"];
            $sql=$pdo->prepare("INSERT INTO tb1(name,comment,password) VALUES(:name,:comment,:password)");
            $sql->bindParam(':name',$name,PDO::PARAM_STR);
            $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
            $sql->bindParam(':password',$password,PDO::PARAM_STR);
            $sql->execute();
        }

            //編集機能
        if (!empty($_POST["EDIT_No"])) {
            $Edit_No=$_POST["EDIT_No"];
            $sql='SELECT*FROM tb1';
            $stmt=$pdo->query($sql);
            $Results=$stmt->fetchAll();
            foreach ($Results as $row) {
                if ($Edit_No == $row['id']) {
                    $ID=$row['id'];
                    $sql='UPDATE tb1 SET name=:name,comment=:comment WHERE id=:id';
                    $stmt=$pdo->prepare($sql);
                    $Ename=$_POST["EDIT_name"];
                    $Ecomment=$_POST["EDIT_comment"];
                    $stmt->bindParam(':name',$Ename,PDO::PARAM_STR);
                    $stmt->bindParam(':comment',$Ecomment,PDO::PARAM_STR);
                    $stmt->bindParam(':id',$ID,PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }

            //削除機能
        if (!empty($_POST["num_delete"]) && !empty($_POST["pass_delete"])) {
            $dnum=$_POST["num_delete"];
            $dpass=$_POST["pass_delete"];
            $sql='DELETE from tb1 where id=:dnum AND password=:dpass';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':dnum',$dnum,PDO::PARAM_INT);
            $stmt->bindParam(':dpass',$dpass,PDO::PARAM_STR);
            $stmt->execute();
        }

            //投稿の表示
        $sql='SELECT * from tb1';
        $stmt=$pdo->query($sql);
        $results=$stmt->fetchAll();
        foreach ($results as $row) {
            echo $row['id']." ";
            echo $row['name']." ";
            echo $row['comment']." ";
            echo $row['created_date'];
            echo "<hr>";
        }
        ?>
    </body>
</html>