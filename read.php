<?php
$dsn = 'mysql:dbname=php_db_app;host=localhost;charset=utf8mb4';
$user = 'root';
// MAMPを利用しているMacユーザーの方は、''ではなく'root'を代入してください
$password = '';

try {
    $pdo = new PDO($dsn, $user, $password);

    // orderパラメータの値が存在すれば(並び替えボタンを押したとき)その値を変数$orderに代入する
    if (isset($_GET['order'])) {
        $order = $_GET['order'];
    } else {
        $order = NULL;
    }

    // パラメータの値が存在すれば(商品名を検索した時)
    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
    } else {
        $keyword = NULL;
    }
    

    // orderパラメータの値によってSQL文を変更する
    if ($order === 'desc') {
        $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at DESC';
    } else {
        $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ASC';
    }

    // SQL文をする用意する
    $stmt_select = $pdo->prepare($sql_select);


    // LIKE句で使うため,$keywordの前後を%で囲む
    $partial_match = "%{$keyword}%";

    $stmt_select->bindValue(':keyword', $partial_match, PDO::PARAM_STR);


    $stmt_select->execute();    

    //  sql文の実行結果を配列で取得する
     $products = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
       exit($e->getMessage());
   }
?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧</title>
    <link rel="stylesheet" href="css/style.css">

    <!-- google fontsの読込 -->
    <link rel="preconnecct" href="https://fonts.googleapis.com">
    <link rel="preconnecct" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav>
            <a href="index.php">商品管理アプリ</a>
        </nav>
    </header>

    <main>
        <article class="products">
            <h1>商品一覧</h1>
            <div class="products-ui">
                <div>
                    <a href="read.php?order=desc&keyword=<?= $keyword ?>">
                        <img src="images/arrow-down.png" alt="降順に並び替え" class="sort-img">
                    </a>
                    <a href="read.php?order=asc&keyword=<?= $keyword ?>">
                        <img src="images/arrow-up.png" alt="昇順に並び替え" class="sort-img">
                    </a>
                    <form action="read.php" method="get" class="search-form">
                        <input type="hidden" name="order" name="order" value="<?= $order ?>">
                        <input type="text" class="search_box" placeholder="商品名で検索" name="keyword" value="<?= $keyword ?>">
                    </form>
                </div>
                <!-- "＃"トップへーじへ遷移 -->
                <a href="#" class="btn">商品登録</a>
            </div>
            <table class="products-table">
                <tr>
                    <th>商品コード</th>
                    <th>商品名</th>
                    <th>単価</th>
                    <th>在庫数</th>
                    <th>仕入先コード</th>
                </tr>
                
                <?php
                // 配列の中身を順番に取り出し表形式で出力する
                    // URLの末尾にパラメータとその値をつけることで、フォームを使わなくてもGETメソッドで値を渡すことができる
                foreach ($products as $product) {
                    $table_row = "
                    <tr>
                    <td>{$product['product_code']}</td>
                    <td>{$product['product_name']}</td>
                    <td>{$product['price']}</td>
                    <td>{$product['stock_quantity']}</td>
                    <td>{$product['vendor_code']}</td>
                    </tr>
                "; 
                // 出力する文字列が長いため、一度$table_rowに代入している
                echo $table_row;
                }
                ?>
            <table>
        </article>
    </main>
    <footer>
        <p class="copyright">&copy; 商品管理アプリ ALL rights reserved.</p>
    </footer>
</body>
</html>