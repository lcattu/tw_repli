<?php

session_start();
require('dbconnect.php');
if(isset($_SESSION['id'])&& $_SESSION['time'] + 3600>time()){
//現在の時刻よりも1時間大きい場合

$_SESSION['time'] = time();
//現在の時刻に更新

$members = $db->prepare('SELECT * FROM members WHERE id = ?');
$members->execute(array($_SESSION['id']));
$member = $members->fetch();

}else{
  //ログインしていない時の処理
  header('Location:login.php');
  exit();
}

if(!empty($_POST)){
  if($_POST['message']!==''){
    $message = $db->prepare('INSERT INTO posts SET member_id=? , message=?, reply_message_id=?,created=NOW()');

    //上の二つの"？"
    //つまりmessage=?,created=?,に対して
    $message->execute(array(
      $member['id'],
      $_POST['message'],
      $_POST['reply_post_id']
    ));
      
    header('Location: index.php');
    exit();

  }
}

$posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC');


if(isset($_REQUEST['res'])){
  // 返信機能
  $response = $db ->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
  $response ->execute(array($_REQUEST['res']));

  $table = $response->fetch();
  $message = '@'. $table['name'] .' ' .$table['message'];
}

$p_id = ''; //  投稿ID
$dbPostData = ''; //  投稿内容
$dbPostGoodNum = ''; // いいねの数

$n = empty($dbPostGoodNum);
echo $n;

//  get送信がある場合
if(!empty($_GET['p_id'])){
  //  投稿IDのGETパラメータを取得
  $p_id = $_GET['p_id'];

  //  DBから投稿データを取得
  $dbPostData = getPostData($p_id);

  //  DBからいいねの数を取得
  $dbPostGoodNum = count(getGood($p_id));

}




?>



<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

  <script type="text/javascript" src="js/script.js"></script>


  <!-- Bootstrap4.3.1 -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <!-- font-awesome -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
    integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- --------- -->
  <!-- style.css -->
  <!-- --------- -->

  
  <link rel="stylesheet" href="css/mobile.css" >
  
  <title>レプリ</title>
</head>
<body>
  <div class="wrapper">
    <!-- ------------------------------- -->
    <!-- aside-------------------------- -->
    <!-- ------------------------------- -->
    <div class="aside-container d-none d-md-block">
      <aside class="aside">

        <ul class="aside-menu">
          <a href=""> <img class="logo" src="img/9243.png" alt=""> </a>

          <li></li>
          <li class="profile"><a href=""><img src="img/0">プロフィール</a></li>
          <div class="post-btn"><a href="">投稿</a></div>

        </ul>

      </aside>

    </div>

    <!-- ------------------------------- -->
    <!-- main -->
    <!-- ------------------------------- -->
    <div class ="main">
      
      <div class="main-header">
        <ul>
          <li ><a class=" d-lg-none" href=""><img class ="self-image"  src="img/20200202060100kC_1DO-u_400x400.jpg" alt="" width="32" height="32"></a>
          </li>

          <li class="title-section">
            <a href="">ホーム</a>
          </li>
        </ul>
      </div>

      <!-- main => self-post-area -->
      <!-- 投稿画面 -->
       <div div class="self-post-area">
        
          <img class="self-icon" src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES));?>">
        

          <form action="" method="post" enctype="multipart/form-data">
          <!-- enctype="multipart/form-data は動画をアップロードする際に、必要な決まり文句 -->
              <div class="input-area">
                <div class="txt-area">
                  <textarea name="message" class="txt" placeholder="Hello <?php print(htmlspecialchars($member['name'],ENT_QUOTES)); ?>, What's up now">
                    <?php print(htmlspecialchars($message, ENT_QUOTES)); ?>  
                  </textarea>


                  <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>"/>
                </div>
                <div class="add-file-area">
                  <div class="add"><a href="">a</a></div>
                  <div class="add"><a href="">b</a></div>

                  <input type="file" name="image" size="35">
                  <input type="submit" value="投稿" class="post-btn">


                </div>
              </div>
          </form>
      


        </div>
        
        <!-- main-content -->
        <div class="contents">

        
            <?php foreach($posts as $post): ?>
              <div class="msg">

                <div class="self-icon">
                  <a href="#">
                    <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" width="53" height="53" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" />
                  </a>
                </div>
                

                <div>
                  <p>
                    <span class="name">（<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>
                    ）</span>
                    <br>
                    <br>
                    <?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?>

                    [<a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>">Re</a>]
                    
                  </p>
                    
                    <p class="day">
                      <?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?>

                      <a href="view.php?id="></a>
                      <a href="view.php?id=">返信元のメッセージ</a>
                      [<a href="delete.php?id="style="color: #F33;">削除</a>]
                      
                    </p>

                <section class="post" data-postid="<?php echo sanitize($p_id); ?>" >
                
                <div class="btn-good <?php if(isGood(isset($_SESSION['member_id']), $dbPostList[$key]['id'])) echo 'active'; ?>">
                  <!-- 自分がいいねした投稿にはハートのスタイルを常に保持する -->
                  <p class="fa-heart fa-lg px-16<?php if(!empty($_SESSION['member_id'])){
                      if(isGood($_SESSION['member_id'],$dbPostList[$key]['id'])){
                        echo ' active fas';
                      }else{
                        echo ' far';
                      }
                    }else{
                      echo ' far';
                    }; ?>"></p><span><?php echo $dbPostGoodNum; ?></span>
                </div>
                </section>
                </div>
              </div>
            <?php endforeach ?>
            
        </div>
        
      </div>
    </div>
    
  </div>
  <div class="mobile-menu  d-md-none" >
    <ul>
      <li><a href=""><img src="img/Human icon.png" alt="" width="25.9" height="23.63"></a></li>
      <li><a href=""><img src="img/Human icon.png" alt="" width="25.9" height="23.63"></a></li>
      <li><a href=""><img src="img/Human icon.png" alt="" width="25.9" height="23.63"></a></li>

      <li><a href=""></a></li>
      <li><a href=""></a></li>

    </ul>
    
  </div>

  
</body>
</html>