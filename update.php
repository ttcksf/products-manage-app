<?php
  $dsn = "mysql:dbname=php_db_app;host=localhost;charset=utf8mb4";
  $user = "root";
  $password = "root";

  if(isset($_POST['submit'])){
    try{
      $pdo = new PDO($dsn, $user, $password);

      $sql_update = "UPDATE products SET product_code = :product_code, product_name = :product_name, price = :price, stock_quantity = :stock_quantity, vendor_code = :vendor_code WHERE id = :id";

      $stmt_update = $pdo->prepare($sql_update);

      $stmt_update->bindValue(':product_code', $_POST['product_code'], PDO::PARAM_INT);
      $stmt_update->bindValue(':product_name', $_POST['product_name'], PDO::PARAM_STR);
      $stmt_update->bindValue(':price', $_POST['price'], PDO::PARAM_INT);
      $stmt_update->bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
      $stmt_update->bindValue(':vendor_code', $_POST['vendor_code'], PDO::PARAM_INT);
      $stmt_update->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

      $stmt_update->execute();
      $count = $stmt->rowCount();
      $message = "商品を{$count}件編集しました。";
      header("Location: read.php?message={$message}");
    }catch(PDOException $e){
      exit($e->getMessage());
    }
  }

  if(isset($_GET["id"])){
    try{
      $pdo = new PDO($dsn, $user, $password);
      //idカラムの値をプレースホルダ（:id）に置き換えたSQL文を事前に用意。更新対象となるSELECT文
      $sql_select_product = "SELECT * FROM products WHERE id = :id";
      $stmt_select_product = $pdo->prepare($sql_select_product);
      //実際の値をプレースホルダに割り当てる
      $stmt_select_product->bindValue(":id", $_GET["id"], PDO::PARAM_INT);
    
      $stmt_select_product->execute();
      //SQLの実行結果を配列で受け取る
      $product = $stmt_select_product->fetch(PDO::FETCH_ASSOC);
      //idパラメータの値と同じidのデータが存在しない場合はエラーとする。fetchメソッドは実行結果が取得できなかった場合はFALSEを返す
      if($product === FALSE){
        exit("idパラメーターの値が不正です。");
      }
      //vendorsテーブルからvendor_codeカラムのデータを取得するSQL文を事前に用意。仕入先コードを取得するSELECT文
      $sql_select_vendor_codes = "SELECT vendor_code FROM vendors";
      
      $stmt_select_vendor_codes = $pdo->query($sql_select_vendor_codes);
      //SQLの実行結果を配列で取得する
      $vendor_codes = $stmt_select_vendor_codes->fetchAll(PDO::FETCH_COLUMN);
    }catch(PDOException $e){
      exit($e->getMessage());
    }
  }

  try{
    $pdo = new PDO($dsn, $user, $password);
    $sql_select = "SELECT vendor_code FROM vendors";
    $stmt_select = $pdo->query($sql_select);
    $vendor_codes = $stmt_select->fetchAll(PDO::FETCH_COLUMN);
  }catch(PDOException $e){
    exit($e->getMessage());
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>商品一覧</title>
  <link rel="stylesheet" href="./css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <nav><a href="index.php">商品管理アプリ</a></nav>
  </header>
  <main>
    <article class="registration">
      <h1>商品登録</h1>
      <div class="back">
        <a href="read.php" class="btn">&lt; 戻る</a>
      </div>
      
      <form action="create.php" method="post" class="registration-form">
        <div>
          <label for="product_code">商品コード</label>
          <input type="number" name="product_code" value="<?= $product['product_code'] ?>"  min="0" max="100000000" required>

          <label for="product_name">商品名</label>
          <input type="text" name="product_name" value="<?= $product['product_name'] ?>" min="50" required>

          <label for="price">単価</label>
          <input type="number" name="price" value="<?= $product['price'] ?>" min="0" max="100000000" required>
          
          <label for="stock_quantity">在庫数</label>
          <input type="number" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" min="0" max="100000000" required>

          <label for="vendor_code">仕入先コード</label>
          <select name="vendor_code" required>
            <option disabled selected value>選択してください</option>
            <?php
              foreach($vendor_codes as $vendor_code){
                if($vendor_code === $product["vendor_code"]){
                  echo "<option value='{$vendor_code}' selected>{$vendor_code}</option>";
                }else{
                  echo "<option value='{$vendor_code}'>{$vendor_code}</option>";
                }
              }
            ?>
          </select>
        </div>
        <button class="submit-btn" type="submit" name="submit" value="update">更新</button>
      </form>
    </article>
  </main>
  <footer>
    <p class="copyright">&copy; 商品管理アプリ All rights reserved.</p>
  </footer>
</body>
</html>
