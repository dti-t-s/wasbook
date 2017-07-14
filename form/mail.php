<?php header("Content-Type:text/html;charset=utf-8"); ?>
<?php //error_reporting(E_ALL | E_STRICT);
##-----------------------------------------------------------------------------------------------------------------##
#
#  PHPメールプログラム　フリー版 最終更新日2014/12/12　
#  //----------------------------------------------------------------------
#  // セッションによる戻るボタンでのデータリセット対策版 ※PC、スマホのみ対応。ガラケーは不可です。
#  //----------------------------------------------------------------------
#　改造や改変は自己責任で行ってください。
#	
#  今のところ特に問題点はありませんが、不具合等がありましたら下記までご連絡ください。
#  MailAddress: info@php-factory.net
#  name: K.Numata
#  HP: http://www.php-factory.net/
#
#  重要！！サイトでチェックボックスを使用する場合のみですが。。。
#  チェックボックスを使用する場合はinputタグに記述するname属性の値を必ず配列の形にしてください。
#  例　name="当サイトをしったきっかけ[]"  として下さい。
#  nameの値の最後に[と]を付ける。じゃないと複数の値を取得できません！
#
##-----------------------------------------------------------------------------------------------------------------##
if (version_compare(PHP_VERSION, '5.1.0', '>=')) {//PHP5.1.0以上の場合のみタイムゾーンを定義
	date_default_timezone_set('Asia/Tokyo');//タイムゾーンの設定（日本以外の場合には適宜設定ください）
}
/*-------------------------------------------------------------------------------------------------------------------
* ★以下設定時の注意点　
* ・値（=の後）は数字以外の文字列（一部を除く）はダブルクオーテーション「"」、または「'」で囲んでいます。
* ・これをを外したり削除したりしないでください。後ろのセミコロン「;」も削除しないください。
* ・また先頭に「$」が付いた文字列は変更しないでください。数字の1または0で設定しているものは必ず半角数字で設定下さい。
* ・メールアドレスのname属性の値が「Email」ではない場合、以下必須設定箇所の「$Email」の値も変更下さい。
* ・name属性の値に半角スペースは使用できません。
*以上のことを間違えてしまうとプログラムが動作しなくなりますので注意下さい。
-------------------------------------------------------------------------------------------------------------------*/


//既存のプログラムファイルに「データリセット対策」を反映されたい場合には、
//「データリセット対策用変更箇所」の文字列でこのファイル内を検索して下さい
//下記を合わせて2箇所ありますのでそれぞれ同じ場所に追記下さい。（このファイルはそれだけですが、実際にはフォーム側も修正する必要があります）

//----------------------------------------------------------------------
//【データリセット対策用変更箇所1　※以下2行を追記（コピペ）下さい】
//----------------------------------------------------------------------
session_start();
$_SESSION = $_POST;//POSTデータをセッションに保存する（戻るボタンで戻った際にデータを反映するため）


//---------------------------　必須設定　必ず設定してください　-----------------------

//サイトのトップページのURL　※デフォルトでは送信完了後に「トップページへ戻る」ボタンが表示されますので
$site_top = "http://localhost:8080/";

// 管理者メールアドレス ※メールを受け取るメールアドレス(複数指定する場合は「,」で区切ってください 例 $to = "aa@aa.aa,bb@bb.bb";)
$to = "xxxxxxxxxx@xxx.xxx";

//フォームのメールアドレス入力箇所のname属性の値（name="○○"　の○○部分）
$Email = "Email";

/*------------------------------------------------------------------------------------------------
以下スパム防止のための設定　
※有効にするにはこのファイルとフォームページが同一ドメイン内にある必要があります
------------------------------------------------------------------------------------------------*/

//スパム防止のためのリファラチェック（フォームページが同一ドメインであるかどうかのチェック）(する=1, しない=0)
$Referer_check = 0;

//リファラチェックを「する」場合のドメイン ※以下例を参考に設置するサイトのドメインを指定して下さい。
$Referer_check_domain = "php-factory.net";

//---------------------------　必須設定　ここまで　------------------------------------


//---------------------- 任意設定　以下は必要に応じて設定してください ------------------------


// 管理者宛のメールで差出人を送信者のメールアドレスにする(する=1, しない=0)
// する場合は、メール入力欄のname属性の値を「$Email」で指定した値にしてください。
//メーラーなどで返信する場合に便利なので「する」がおすすめです。
$userMail = 1;

// Bccで送るメールアドレス(複数指定する場合は「,」で区切ってください 例 $BccMail = "aa@aa.aa,bb@bb.bb";)
$BccMail = "";

// 管理者宛に送信されるメールのタイトル（件名）
$subject = "ホームページのお問い合わせ";

// 送信確認画面の表示(する=1, しない=0)
$confirmDsp = 1;

// 送信完了後に自動的に指定のページ(サンクスページなど)に移動する(する=1, しない=0)
// CV率を解析したい場合などはサンクスページを別途用意し、URLをこの下の項目で指定してください。
// 0にすると、デフォルトの送信完了画面が表示されます。
$jumpPage = 0;

// 送信完了後に表示するページURL（上記で1を設定した場合のみ）※httpから始まるURLで指定ください。
$thanksPage = "http://xxx.xxxxxxxxx/thanks.html";

// 必須入力項目を設定する(する=1, しない=0)
$requireCheck = 0;

/* 必須入力項目(入力フォームで指定したname属性の値を指定してください。（上記で1を設定した場合のみ）
値はシングルクォーテーションで囲み、複数の場合はカンマで区切ってください。フォーム側と順番を合わせると良いです。 
配列の形「name="○○[]"」の場合には必ず後ろの[]を取ったものを指定して下さい。*/
$require = array('last_name','first_name');


//----------------------------------------------------------------------
//  自動返信メール設定(START)
//----------------------------------------------------------------------

// 差出人に送信内容確認メール（自動返信メール）を送る(送る=1, 送らない=0)
// 送る場合は、フォーム側のメール入力欄のname属性の値が上記「$Email」で指定した値と同じである必要があります
$remail = 0;

//自動返信メールの送信者欄に表示される名前　※あなたの名前や会社名など（もし自動返信メールの送信者名が文字化けする場合ここは空にしてください）
$refrom_name = "";

// 差出人に送信確認メールを送る場合のメールのタイトル（上記で1を設定した場合のみ）
$re_subject = "送信ありがとうございました";

//フォーム側の「名前」箇所のname属性の値　※自動返信メールの「○○様」の表示で使用します。
//指定しない、または存在しない場合は、○○様と表示されないだけです。あえて無効にしてもOK
$dsp_name = 'お名前';

//自動返信メールの冒頭の文言 ※日本語部分のみ変更可
$remail_text = <<< TEXT

お問い合わせありがとうございました。
早急にご返信致しますので今しばらくお待ちください。

送信内容は以下になります。

TEXT;


//自動返信メールに署名（フッター）を表示(する=1, しない=0)※管理者宛にも表示されます。
$mailFooterDsp = 0;

//上記で「1」を選択時に表示する署名（フッター）（FOOTER～FOOTER;の間に記述してください）
$mailSignature = <<< FOOTER

──────────────────────
株式会社○○○○　佐藤太郎
〒150-XXXX 東京都○○区○○ 　○○ビル○F　
TEL：03- XXXX - XXXX 　FAX：03- XXXX - XXXX
携帯：090- XXXX - XXXX 　
E-mail:xxxx@xxxx.com
URL: http://www.php-factory.net/
──────────────────────

FOOTER;


//----------------------------------------------------------------------
//  自動返信メール設定(END)
//----------------------------------------------------------------------

//メールアドレスの形式チェックを行うかどうか。(する=1, しない=0)
//※デフォルトは「する」。特に理由がなければ変更しないで下さい。メール入力欄のname属性の値が上記「$Email」で指定した値である必要があります。
$mail_check = 1;

//全角英数字→半角変換を行うかどうか。(する=1, しない=0)
$hankaku = 0;

//全角英数字→半角変換を行う項目のname属性の値（name="○○"の「○○」部分）
//※複数の場合にはカンマで区切って下さい。（上記で「1」を指定した場合のみ有効）
//配列の形「name="○○[]"」の場合には必ず後ろの[]を取ったものを指定して下さい。
$hankaku_array = array('電話番号','金額');


//------------------------------- 任意設定ここまで ---------------------------------------------


// 以下の変更は知識のある方のみ自己責任でお願いします。


//----------------------------------------------------------------------
//  関数実行、変数初期化
//----------------------------------------------------------------------
$encode = "UTF-8";//このファイルの文字コード定義（変更不可）

if(isset($_GET)) $_GET = sanitize($_GET);//NULLバイト除去//
if(isset($_POST)) $_POST = sanitize($_POST);//NULLバイト除去//
if(isset($_COOKIE)) $_COOKIE = sanitize($_COOKIE);//NULLバイト除去//
if($encode == 'SJIS') $_POST = sjisReplace($_POST,$encode);//Shift-JISの場合に誤変換文字の置換実行
$funcRefererCheck = refererCheck($Referer_check,$Referer_check_domain);//リファラチェック実行

//変数初期化
$sendmail = 0;
$empty_flag = 0;
$post_mail = '';
$errm ='';
$header ='';

if($requireCheck == 1) {
	$requireResArray = requireCheck($require);//必須チェック実行し返り値を受け取る
	$errm = $requireResArray['errm'];
	$empty_flag = $requireResArray['empty_flag'];
}
//メールアドレスチェック
if(empty($errm)){
	foreach($_POST as $key=>$val) {
		if($val == "confirm_submit") $sendmail = 1;
		if($key == $Email) $post_mail = h($val);
		if($key == $Email && $mail_check == 1 && !empty($val)){
			if(!checkMail($val)){
				$errm .= "<p class=\"error_messe\">【".$key."】はメールアドレスの形式が正しくありません。</p>\n";
				$empty_flag = 1;
			}
		}
	}
}
  
if(($confirmDsp == 0 || $sendmail == 1) && $empty_flag != 1){
	
	//差出人に届くメールをセット
	if($remail == 1) {
		$userBody = mailToUser($_POST,$dsp_name,$remail_text,$mailFooterDsp,$mailSignature,$encode);
		$reheader = userHeader($refrom_name,$to,$encode);
		$re_subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($re_subject,"JIS",$encode))."?=";
	}
	//管理者宛に届くメールをセット
	$adminBody = mailToAdmin($_POST,$subject,$mailFooterDsp,$mailSignature,$encode,$confirmDsp);
	$header = adminHeader($userMail,$post_mail,$BccMail,$to);
	$subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject,"JIS",$encode))."?=";
	
	mail($to,$subject,$adminBody,$header);
	if($remail == 1) mail($post_mail,$re_subject,$userBody,$reheader);
	
	//----------------------------------------------------------------------
	// 【データリセット対策用変更箇所2】以下1行を追記下さい
	//----------------------------------------------------------------------
	//$_SESSION = array();//送信後にセッションをクリアしておく
}
else if($confirmDsp == 1){ 

	/*　▼▼▼送信確認画面のレイアウト※編集可　オリジナルのデザインも適用可能▼▼▼　*/
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	<title>確認画面</title>
	<style type="text/css">
	/* 自由に編集下さい */
	#formWrap {
		width:700px;
		margin:0 auto;
		color:#555;
		line-height:120%;
		font-size:90%;
	}
	table.formTable{
		width:100%;
		margin:0 auto;
		border-collapse:collapse;
	}
	table.formTable td,table.formTable th{
		border:1px solid #ccc;
		padding:10px;
	}
	table.formTable th{
		width:30%;
		font-weight:normal;
		background:#efefef;
		text-align:left;
	}
	p.error_messe{
		margin:5px 0;
		color:red;
	}


	@media screen and (max-width:572px) {
	/*　画面サイズが572px以下はここを読み込む　*/

		#formWrap {
			width:90%;
			margin:0 auto;
		}
		table.formTable th, table.formTable td {
			width:auto;
			display:block;
		}
		table.formTable th {
			margin-top:5px;
			border-bottom:0;
		}
		input[type="text"], textarea {
			width:80%;
			padding:5px;
			font-size:110%;
			display:block;
		}
		input[type="submit"], input[type="reset"], input[type="button"] {
			display:block;
			width:100%;
			height:40px;
		}

	}

	</style>
	</head>
	<body>

	<!-- ▲ Headerやその他コンテンツなど　※自由に編集可 ▲-->

	<!-- ▼************ 送信内容表示部　※編集は自己責任で ************ ▼-->
	<div id="formWrap">
	<?php if($empty_flag == 1){ ?>
		<div align="center">
		<h4>入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</h4>
		<?php echo $errm; ?><br /><br /><input type="button" value=" 前画面に戻る " onClick="history.back()">
		</div>
	<?php }else{ ?>
		<h3>確認画面</h3>
		<p align="center">以下の内容で間違いがなければ、「送信する」ボタンを押してください。</p>
		<form action="<?php echo h($_SERVER['SCRIPT_NAME']); ?>" method="POST">
			<table class="formTable">
			<!--<?php echo confirmOutput($_POST);//入力内容を表示?>-->
			<?php echo confirmOutput_suru($_POST);//入力内容を表示?>
			</table>
			<p align="center"><input type="hidden" name="mail_set" value="confirm_submit">
			<input type="hidden" name="httpReferer" value="<?php echo h($_SERVER['HTTP_REFERER']);?>">
			<input type="submit" value="　送信する　">
			<input type="button" value="前画面に戻る" onClick="history.back()"></p>
		</form>
	<?php } ?>
	</div><!-- /formWrap -->
	<!-- ▲ *********** 送信内容確認部　※編集は自己責任で ************ ▲-->

	<!-- ▼ Footerその他コンテンツなど　※編集可 ▼-->
	</body>
	</html>

	<?php
	/* ▲▲▲送信確認画面のレイアウト　※オリジナルのデザインも適用可能▲▲▲　*/
}

if(($jumpPage == 0 && $sendmail == 1) || ($jumpPage == 0 && ($confirmDsp == 0 && $sendmail == 0))) { 

	/* ▼▼▼送信完了画面のレイアウト　編集可 ※送信完了後に指定のページに移動しない場合のみ表示▼▼▼　*/
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>完了画面</title>
	</head>
	<body>
	<div align="center">
	<?php if($empty_flag == 1){ ?>
		<h4>入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</h4>
		<div style="color:red"><?php echo $errm; ?></div>
		<br /><br /><input type="button" value=" 前画面に戻る " onClick="history.back()">
		</div>
		</body>
		</html>
	<?php }else{ ?>
	<?php
		// フォームから送信されたデータを各変数に格納
		$last_name = $_SESSION["last_name"];
		$first_name = $_SESSION["first_name"];
		$last_name_kana = $_SESSION["last_name_kana"];
		$first_name_kana = $_SESSION["first_name_kana"];
		$tel = $_SESSION["tel"];
		$mail = $_SESSION["mail"];
		$sex = $_SESSION["sex"];
		$year = $_SESSION["year"];
		$month = $_SESSION["month"];
		$day = $_SESSION["day"];
		$zip1 = $_SESSION["zip1"];
		$zip2 = $_SESSION["zip2"];
		$pref_cd = $_SESSION["pref_cd"];
		$address = $_SESSION["address"];

		$dsn = 'mysql:dbname=testdb;host=localhost';
		$user = 'testdb';
		$password = '123456789';

		try{
			$pdo = new PDO($dsn, $user, $password);
			// 送信ボタンが押された時に動作する処理をここに記述する
			$stmt = $pdo -> prepare("INSERT INTO applicant (last_name, first_name, last_name_kana, first_name_kana, tel1, mail, sex, birthday1, birthday2, birthday3, zip1, zip2, pref_cd, address1) VALUES (:last_name, :first_name, :last_name_kana, :first_name_kana, :tel, :mail, :sex, :birthday1, :birthday2, :birthday3, :zip1, :zip2, :pref_cd, :address)");
			$stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
			$stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
			$stmt->bindParam(':last_name_kana', $last_name_kana, PDO::PARAM_STR);
			$stmt->bindParam(':first_name_kana', $first_name_kana, PDO::PARAM_STR);
			$stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
			$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
			$stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
			$stmt->bindParam(':birthday1', $year, PDO::PARAM_STR);
			$stmt->bindParam(':birthday2', $month, PDO::PARAM_STR);
			$stmt->bindParam(':birthday3', $day, PDO::PARAM_STR);
			$stmt->bindParam(':zip1', $zip1, PDO::PARAM_STR);
			$stmt->bindParam(':zip2', $zip2, PDO::PARAM_STR);
			$stmt->bindParam(':pref_cd', $pref_cd, PDO::PARAM_STR);
			$stmt->bindParam(':address', $address, PDO::PARAM_STR);
			$stmt->execute();
		}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			die();
		}

		//----------------------------------------------------------------------
		// 【データリセット対策用変更箇所2】以下1行を追記下さい
		//----------------------------------------------------------------------
		$_SESSION = array();//送信後にセッションをクリアしておく

	?>
		送信ありがとうございました。<br />
		送信は正常に完了しました。<br /><br />
		<a href="<?php echo $site_top ;?>">トップページへ戻る&raquo;</a>
		</div>
		<?php copyright(); ?>
		<!--  CV率を計測する場合ここにAnalyticsコードを貼り付け -->
		</body>
		</html>
	<?php 
	/* ▲▲▲送信完了画面のレイアウト 編集可 ※送信完了後に指定のページに移動しない場合のみ表示▲▲▲　*/
	}
}
//確認画面無しの場合の表示、指定のページに移動する設定の場合、エラーチェックで問題が無ければ指定ページヘリダイレクト
else if(($jumpPage == 1 && $sendmail == 1) || $confirmDsp == 0) { 
	if($empty_flag == 1){ ?>
<div align="center"><h4>入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</h4><div style="color:red"><?php echo $errm; ?></div><br /><br /><input type="button" value=" 前画面に戻る " onClick="history.back()"></div>
<?php 
	}else{ header("Location: ".$thanksPage); }
}

// 以下の変更は知識のある方のみ自己責任でお願いします。

//----------------------------------------------------------------------
//  関数定義(START)
//----------------------------------------------------------------------
function checkMail($str){
	$mailaddress_array = explode('@',$str);
	if(preg_match("/^[\.!#%&\-_0-9a-zA-Z\?\/\+]+\@[!#%&\-_0-9a-z]+(\.[!#%&\-_0-9a-z]+)+$/", "$str") && count($mailaddress_array) ==2){
		return true;
	}else{
		return false;
	}
}
function h($string) {
	global $encode;
	return htmlspecialchars($string, ENT_QUOTES,$encode);
}
function sanitize($arr){
	if(is_array($arr)){
		return array_map('sanitize',$arr);
	}
	return str_replace("\0","",$arr);
}
//Shift-JISの場合に誤変換文字の置換関数
function sjisReplace($arr,$encode){
	foreach($arr as $key => $val){
		$key = str_replace('＼','ー',$key);
		$resArray[$key] = $val;
	}
	return $resArray;
}
//送信メールにPOSTデータをセットする関数
function postToMail($arr){
	global $hankaku,$hankaku_array;
	$resArray = '';
	foreach($arr as $key => $val) {
		$out = '';
		if(is_array($val)){
			foreach($val as $key02 => $item){ 
				//連結項目の処理
				if(is_array($item)){
					$out .= connect2val($item);
				}else{
					$out .= $item . ', ';
				}
			}
			$out = rtrim($out,', ');
			
		}else{ $out = $val; }//チェックボックス（配列）追記ここまで
		if(get_magic_quotes_gpc()) { $out = stripslashes($out); }
		
		//全角→半角変換
		if($hankaku == 1){
			$out = zenkaku2hankaku($key,$out,$hankaku_array);
		}
		if($out != "confirm_submit" && $key != "httpReferer") {
			$resArray .= "【 ".h($key)." 】 ".h($out)."\n";
		}
	}
	return $resArray;
}
//確認画面の入力内容出力用関数
function confirmOutput($arr){
	global $hankaku,$hankaku_array;
	$html = '';
	foreach($arr as $key => $val) {
		$out = '';
		if(is_array($val)){
			foreach($val as $key02 => $item){ 
				//連結項目の処理
				if(is_array($item)){
					$out .= connect2val($item);
				}else{
					$out .= $item . ', ';
				}
			}
			$out = rtrim($out,', ');
			
		}else{ $out = $val; }//チェックボックス（配列）追記ここまで
		if(get_magic_quotes_gpc()) { $out = stripslashes($out); }
		$out = nl2br(h($out));//※追記 改行コードを<br>タグに変換
		$key = h($key);
		
		//全角→半角変換
		if($hankaku == 1){
			$out = zenkaku2hankaku($key,$out,$hankaku_array);
		}
		
		$html .= "<tr><th>".$key."</th><td>".$out;
		$html .= '<input type="hidden" name="'.$key.'" value="'.str_replace(array("<br />","<br>"),"",$out).'" />';
		$html .= "</td></tr>\n";
	}
	return $html;
}


//確認画面の入力内容出力用関数
function confirmOutput_suru($arr){
	global $hankaku,$hankaku_array;
	$html = '';

	// フォームから送信されたデータを各変数に格納
	$last_name = $_POST["last_name"];
	$first_name = $_POST["first_name"];
	$name = $last_name . " " . $first_name;

	$html .= "<tr><th>お名前</th><td>".$name;
	$html .= '<input type="hidden" name="last_name" value="'.str_replace(array("<br />","<br>"),"",$last_name).'" />';
	$html .= '<input type="hidden" name="first_name" value="'.str_replace(array("<br />","<br>"),"",$first_name).'" />';
	$html .= "</td></tr>\n";

	$last_name_kana = $_POST["last_name_kana"];
	$first_name_kana = $_POST["first_name_kana"];
	$kana = $last_name_kana . " " . $first_name_kana;

	$html .= "<tr><th>カタカナ</th><td>".$kana;
	$html .= '<input type="hidden" name="last_name_kana" value="'.str_replace(array("<br />","<br>"),"",$last_name_kana).'" />';
	$html .= '<input type="hidden" name="first_name_kana" value="'.str_replace(array("<br />","<br>"),"",$first_name_kana).'" />';
	$html .= "</td></tr>\n";

	$tel1 = $_POST["tel1"];
	$tel2 = $_POST["tel2"];
	$tel3 = $_POST["tel3"];
	$tel = $tel1 . " - " . $tel2. " - " . $tel3;

	$html .= "<tr><th>電話番号</th><td>".$tel;
	$html .= '<input type="hidden" name="tel1" value="'.str_replace(array("<br />","<br>"),"",$tel1).'" />';
	$html .= '<input type="hidden" name="tel2" value="'.str_replace(array("<br />","<br>"),"",$tel2).'" />';
	$html .= '<input type="hidden" name="tel3" value="'.str_replace(array("<br />","<br>"),"",$tel3).'" />';
	$html .= "</td></tr>\n";

	$zip1 = $_POST["zip1"];
	$zip2 = $_POST["zip2"];
	$zip = $zip1 . " - " . $zip2;

	$html .= "<tr><th>郵便番号</th><td>".$zip;
	$html .= '<input type="hidden" name="zip1" value="'.str_replace(array("<br />","<br>"),"",$zip1).'" />';
	$html .= '<input type="hidden" name="zip2" value="'.str_replace(array("<br />","<br>"),"",$zip2).'" />';
	$html .= "</td></tr>\n";

	$pref_cd = $_POST["pref_cd"];
	$pref_nm = getPrefName($pref_cd);
	
	$html .= "<tr><th>都道府県</th><td>".$pref_nm;
	$html .= '<input type="hidden" name="pref_cd" value="'.str_replace(array("<br />","<br>"),"",$pref_cd).'" />';
	$html .= "</td></tr>\n";

	$address1 = $_POST["address1"];
	$address2 = $_POST["address2"];
	$address3 = $_POST["address3"];

	$html .= "<tr><th>市区郡町村</th><td>".$address1;
	$html .= '<input type="hidden" name="address1" value="'.str_replace(array("<br />","<br>"),"",$address1).'" />';
	$html .= "</td></tr>\n";
	$html .= "<tr><th>丁目・番地</th><td>".$address2;
	$html .= '<input type="hidden" name="address2" value="'.str_replace(array("<br />","<br>"),"",$address2).'" />';
	$html .= "</td></tr>\n";
	$html .= "<tr><th>ビル・マンション名</th><td>".$address3;
	$html .= '<input type="hidden" name="address3" value="'.str_replace(array("<br />","<br>"),"",$address3).'" />';
	$html .= "</td></tr>\n";

	$mail = $_POST["mail"];

	$html .= "<tr><th>メールアドレス</th><td>".$mail;
	$html .= '<input type="hidden" name="mail" value="'.str_replace(array("<br />","<br>"),"",$mail).'" />';
	$html .= "</td></tr>\n";

	$sex = $_POST["sex"];
	$html .= "<tr><th>性別</th><td>".$sex;
	$html .= '<input type="hidden" name="sex" value="'.str_replace(array("<br />","<br>"),"",$sex).'" />';
	$html .= "</td></tr>\n";

	$year = $_POST["year"];
	$month = $_POST["month"];
	$day = $_POST["day"];
	$birth = $year . " 年 " . $month. " 月 " . $day. " 日 ";

	$html .= "<tr><th>生年月日</th><td>".$birth;
	$html .= '<input type="hidden" name="year" value="'.str_replace(array("<br />","<br>"),"",$year).'" />';
	$html .= '<input type="hidden" name="month" value="'.str_replace(array("<br />","<br>"),"",$month).'" />';
	$html .= '<input type="hidden" name="day" value="'.str_replace(array("<br />","<br>"),"",$day).'" />';
	$html .= "</td></tr>\n";

	$qustion_arr = $_POST["サイトを知ったきっかけ"];
	$qustion = connect2val($qustion_arr);
	$html .= "<tr><th>サイトを知ったきっかけ</th><td>".$qustion;
	$html .= '<input type="hidden" name="qustion" value="'.str_replace(array("<br />","<br>"),"",$qustion).'" />';
	$html .= "</td></tr>\n";

	return $html;
}

//全角→半角変換
function zenkaku2hankaku($key,$out,$hankaku_array){
	global $encode;
	if(is_array($hankaku_array) && function_exists('mb_convert_kana')){
		foreach($hankaku_array as $hankaku_array_val){
			if($key == $hankaku_array_val){
				$out = mb_convert_kana($out,'a',$encode);
			}
		}
	}
	return $out;
}
//配列連結の処理
function connect2val($arr){
	$out = '';
	foreach($arr as $key => $val){
		if($key === 0 || $val == ''){//配列が未記入（0）、または内容が空のの場合には連結文字を付加しない（型まで調べる必要あり）
			$key = '';
		}elseif(strpos($key,"円") !== false && $val != '' && preg_match("/^[0-9]+$/",$val)){
			$val = number_format($val);//金額の場合には3桁ごとにカンマを追加
		}
		$out .= $val . $key;
	}
	return $out;
}

//全角→半角変換
function getPrefName($pref_cd){
	switch( $pref_cd ) {
		case '01': $pref_nm = '北海道'  ; break;
		case '02': $pref_nm = '青森県'  ; break;
		case '03': $pref_nm = '岩手県'  ; break;
		case '04': $pref_nm = '宮城県'  ; break;
		case '05': $pref_nm = '秋田県'  ; break;
		case '06': $pref_nm = '山形県'  ; break;
		case '07': $pref_nm = '福島県'  ; break;
		case '08': $pref_nm = '茨城県'  ; break;
		case '09': $pref_nm = '栃木県'  ; break;
		case '10': $pref_nm = '群馬県'  ; break;
		case '11': $pref_nm = '埼玉県'  ; break;
		case '12': $pref_nm = '千葉県'  ; break;
		case '13': $pref_nm = '東京都'  ; break;
		case '14': $pref_nm = '神奈川県'; break;
		case '15': $pref_nm = '新潟県'  ; break;
		case '16': $pref_nm = '富山県'  ; break;
		case '17': $pref_nm = '石川県'  ; break;
		case '18': $pref_nm = '福井県'  ; break;
		case '19': $pref_nm = '山梨県'  ; break;
		case '20': $pref_nm = '長野県'  ; break;
		case '21': $pref_nm = '岐阜県'  ; break;
		case '22': $pref_nm = '静岡県'  ; break;
		case '23': $pref_nm = '愛知県'  ; break;
		case '24': $pref_nm = '三重県'  ; break;
		case '25': $pref_nm = '滋賀県'  ; break;
		case '26': $pref_nm = '京都府'  ; break;
		case '27': $pref_nm = '大阪府'  ; break;
		case '28': $pref_nm = '兵庫県'  ; break;
		case '29': $pref_nm = '奈良県'  ; break;
		case '30': $pref_nm = '和歌山県'; break;
		case '31': $pref_nm = '鳥取県'  ; break;
		case '32': $pref_nm = '島根県'  ; break;
		case '33': $pref_nm = '岡山県'  ; break;
		case '34': $pref_nm = '広島県'  ; break;
		case '35': $pref_nm = '山口県'  ; break;
		case '36': $pref_nm = '徳島県'  ; break;
		case '37': $pref_nm = '香川県'  ; break;
		case '38': $pref_nm = '愛媛県'  ; break;
		case '39': $pref_nm = '高知県'  ; break;
		case '40': $pref_nm = '福岡県'  ; break;
		case '41': $pref_nm = '佐賀県'  ; break;
		case '42': $pref_nm = '長崎県'  ; break;
		case '43': $pref_nm = '熊本県'  ; break;
		case '44': $pref_nm = '大分県'  ; break;
		case '45': $pref_nm = '宮崎県'  ; break;
		case '46': $pref_nm = '鹿児島県'; break;
		case '47': $pref_nm = '沖縄県'  ; break;
		default:
			$pref_nm = '';
	}
	return $pref_nm;
}

//管理者宛送信メールヘッダ
function adminHeader($userMail,$post_mail,$BccMail,$to){
	$header = '';
	if($userMail == 1 && !empty($post_mail)) {
		$header="From: $post_mail\n";
		if($BccMail != '') {
		  $header.="Bcc: $BccMail\n";
		}
		$header.="Reply-To: ".$post_mail."\n";
	}else {
		if($BccMail != '') {
		  $header="Bcc: $BccMail\n";
		}
		$header.="Reply-To: ".$to."\n";
	}
		$header.="Content-Type:text/plain;charset=iso-2022-jp\nX-Mailer: PHP/".phpversion();
		return $header;
}
//管理者宛送信メールボディ
function mailToAdmin($arr,$subject,$mailFooterDsp,$mailSignature,$encode,$confirmDsp){
	$adminBody="「".$subject."」からメールが届きました\n\n";
	$adminBody .="＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
	$adminBody.= postToMail($arr);//POSTデータを関数からセット
	$adminBody.="\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n";
	$adminBody.="送信された日時：".date( "Y/m/d (D) H:i:s", time() )."\n";
	$adminBody.="送信者のIPアドレス：".@$_SERVER["REMOTE_ADDR"]."\n";
	$adminBody.="送信者のホスト名：".getHostByAddr(getenv('REMOTE_ADDR'))."\n";
	if($confirmDsp != 1){
		$adminBody.="問い合わせのページURL：".@$_SERVER['HTTP_REFERER']."\n";
	}else{
		$adminBody.="問い合わせのページURL：".@$arr['httpReferer']."\n";
	}
	if($mailFooterDsp == 1) $adminBody.= $mailSignature;
	return mb_convert_encoding($adminBody,"JIS",$encode);
}

//ユーザ宛送信メールヘッダ
function userHeader($refrom_name,$to,$encode){
	$reheader = "From: ";
	if(!empty($refrom_name)){
		$default_internal_encode = mb_internal_encoding();
		if($default_internal_encode != $encode){
			mb_internal_encoding($encode);
		}
		$reheader .= mb_encode_mimeheader($refrom_name)." <".$to.">\nReply-To: ".$to;
	}else{
		$reheader .= "$to\nReply-To: ".$to;
	}
	$reheader .= "\nContent-Type: text/plain;charset=iso-2022-jp\nX-Mailer: PHP/".phpversion();
	return $reheader;
}
//ユーザ宛送信メールボディ
function mailToUser($arr,$dsp_name,$remail_text,$mailFooterDsp,$mailSignature,$encode){
	$userBody = '';
	if(isset($arr[$dsp_name])) $userBody = h($arr[$dsp_name]). " 様\n";
	$userBody.= $remail_text;
	$userBody.="\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
	$userBody.= postToMail($arr);//POSTデータを関数からセット
	$userBody.="\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
	$userBody.="送信日時：".date( "Y/m/d (D) H:i:s", time() )."\n";
	if($mailFooterDsp == 1) $userBody.= $mailSignature;
	return mb_convert_encoding($userBody,"JIS",$encode);
}
//必須チェック関数
function requireCheck($require){
	$res['errm'] = '';
	$res['empty_flag'] = 0;
	foreach($require as $requireVal){
		$existsFalg = '';
		foreach($_POST as $key => $val) {
			if($key == $requireVal) {
				
				//連結指定の項目（配列）のための必須チェック
				if(is_array($val)){
					$connectEmpty = 0;
					foreach($val as $kk => $vv){
						if(is_array($vv)){
							foreach($vv as $kk02 => $vv02){
								if($vv02 == ''){
									$connectEmpty++;
								}
							}
						}
						
					}
					if($connectEmpty > 0){
						$res['errm'] .= "<p class=\"error_messe\">【".h($key)."】は必須項目です。</p>\n";
						$res['empty_flag'] = 1;
					}
				}
				//デフォルト必須チェック
				elseif($val == ''){
					$res['errm'] .= "<p class=\"error_messe\">【".h($key)."】は必須項目です。</p>\n";
					$res['empty_flag'] = 1;
				}
				
				$existsFalg = 1;
				break;
			}
			
		}
		if($existsFalg != 1){
				$res['errm'] .= "<p class=\"error_messe\">【".$requireVal."】が未選択です。</p>\n";
				$res['empty_flag'] = 1;
		}
	}
	
	return $res;
}
//リファラチェック
function refererCheck($Referer_check,$Referer_check_domain){
	if($Referer_check == 1 && !empty($Referer_check_domain)){
		if(strpos($_SERVER['HTTP_REFERER'],$Referer_check_domain) === false){
			return exit('<p align="center">リファラチェックエラー。フォームページのドメインとこのファイルのドメインが一致しません</p>');
		}
	}
}
function copyright(){
	echo '<a style="display:block;text-align:center;margin:15px 0;font-size:11px;color:#aaa;text-decoration:none" href="http://www.php-factory.net/" target="_blank">- PHP工房 -</a>';
}
//----------------------------------------------------------------------
//  関数定義(END)
//----------------------------------------------------------------------
?>