<?php

session_start();
require('../dbconnect.php');

if(!empty($_POST)){
	if($_POST['name']===''){
		//print('名前が入力されていません');
		$error['name']='blank';
	}


	if($_POST['email']===''){
		//print('名前が入力されていません');
		$error['email']='blank';
	}

	if(strlen($_POST['password'])<4){
		//print('名前が入力されていません');
		$error['password']='length';
	}


	if($_POST['password']===''){
		//print('名前が入力されていません');
		$error['password']='blank';
	}
	$fileName = $_FILES['image']['name'];
	//	$fileNameという変数に下記のインプットから得たファイルを保存


	//--------------------------------------
	//アカウント重複チェック
	//--------------------------------------
	if(empty($error)){
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		$member->execute(array($_POST['email']));
		$record = $member->fetch();
		if($record['cnt'] > 0 ){
			$error['email'] = 'duplicate';
		}
	}
	
	


	if(!empty($fileName)){
		//	画像がアップロードされていれば

		$ext = substr($fileName,-3);
		//	後ろ3文字を切り取る→拡張子を得る

		if($ext != 'JPG' && $ext != 'gif' && $ext != 'png' && $ext != 'jpg'){
			//	if()内に指定されて「いない」ファイルの場合は、

			$error['image']	= 'type';
			//	拡張子をtypeとして$errorの配列に挿入する
		}
	}
	//		

	if(empty($error)){
		$image = date('YmdHis') . $_FILES['image']['name'];
		//	ファイルのアップロード
		//	$_FILES以下でファイル名(日付)つけている

		move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/' .$image);

		//	$_FILESはグローバル変数
		//	下記の<input>のところから得られた画像ファイル
		//	配列になっている
		//	['temp_name']は一時的に保存している場所
		//	move_uploaded_fileは二つのパラメータをとる
		//	move_uploaded_file[①,②]
		//	①は今、保存している場所
		//	②は移動先


		$_SESSION['join'] = $_POST;
		$_SESSION['join']['image'] = $image;
		//	セッションに保管する
		header('Location: check.php');
		exit();
	}
}

if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
	$_POST = $_SESSION['join'];
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>次のフォームに必要事項をご記入ください。</p>
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
					<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>" />
					<?php if($error['name']==='blank'):?>
						<p>ニックネームを入力してください</p>
					<?php endif; ?>


		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
					<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['email'],ENT_QUOTES)); ?>" />
					<?php if($error['email']==='blank'):?>
						<p>メールアドレスを入力してください</p>
					<?php endif; ?>
					<?php if($error['email']==='duplicate'):?>
						<p>指定されたメールアドレスは既に登録されています</p>
					<?php endif; ?>

		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
					<input type="password" name="password" size="10" maxlength="20" value="<?php print(htmlspecialchars($_POST['password'],ENT_QUOTES)); ?>" />
					<?php if($error['password']==='blank'):?>
						<p>パスワードを入力してください</p>
					<?php endif; ?>
					<?php if($error['password']==='length'):?>
						<p>パスワードは4文字以上で入力してください</p>
					<?php endif; ?>
					
					
        </dd>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
					<?php if($error['image'] === 'type'): ?>
					<p class ="error">*写真は拡張子が「.gif」、「.jpg」、「.png」の画像を指定してください</p>
					<?php endif; ?>

					<?php if(!empty($error)):?>
					<p class="error">恐れ入りますが、画像を再度指定してください</p>
					<?php endif;?>
					<!-- 後でページ推移後に写真ファイルを設定する -->
    </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
