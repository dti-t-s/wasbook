<?php

//セッションによる確認画面からの「戻る」時にフォームデータを維持する（上級者向け）※ガラケーはセッションが使えないため不可です。
//iPhoneのChromeでは戻った際の入力データがリセットされてしまうための対策です。現状その他のブラウザでは確認されていません。

/*注意事項
1，このページの拡張子は必ず.phpである必要があります。（またはhtmlをPHPとして動作させるものでもOK）
2，またセッションが使えることが大前提です。（設置サーバーをご確認下さい→一般的なレンタルサーバーであればほぼ問題ありません）
3，既存のフォームに反映する場合には下記の各項目の記述を参考にして下さい。特にフォームの種類によって記述が異なりますのでご注意下さい。
4，これに関するサポートは行なっておりませんのでご了承下さい。ご自身での設置が難しい場合はすべて有償での作業依頼として承りますのでご了承下さい。
5，最終的に入力→確認画面→戻るのページ遷移で入力（または選択）したデータが正常に反映されていることを確認して下さい。
*/

//セッションを使用するため必須の記述（必ずページの最上部に記述する必要があります。またこの上に空白行、スペース等があってもいけません（PHPのものを除く））
session_start();
function h($string) {
	return htmlspecialchars($string, ENT_QUOTES,'utf-8');//設置ページの文字コードに合わせて下さい
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>サンプル</title>
<meta name="Description" content="" />
<meta name="Keywords" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<script type="text/javascript" src="contact.js"></script>
<script type="text/javascript" src="createbox.js"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>

<style type="text/css">
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

/*　画面サイズが572px以下はここを読み込む　*/
@media screen and (max-width:572px) {
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
<div id="formWrap">
  <h3>お問い合わせ</h3>
  <p>下記フォームに必要事項を入力後、確認ボタンを押してください。</p>
  <form method="post" action="mail.php" name="form" onsubmit="return validate()">
    <table class="formTable">
      <tr>
        <th>お名前　※</th>
        <td>
        <input size="10" type="text" name="last_name" value="<?php if(isset($_SESSION['last_name'])) echo h($_SESSION['last_name']);?>" />
        <input size="10" type="text" name="first_name" value="<?php if(isset($_SESSION['first_name'])) echo h($_SESSION['first_name']);?>" />
        </td>
      </tr>
      <tr>
        <th>カタカナ　※</th>
        <td>
        <input size="10" type="text" name="last_name_kana" value="<?php if(isset($_SESSION['last_name_kana'])) echo h($_SESSION['last_name_kana']);?>" />
        <input size="10" type="text" name="first_name_kana" value="<?php if(isset($_SESSION['first_name_kana'])) echo h($_SESSION['first_name_kana']);?>" />
        </td>
      </tr>
      
      <!-- 連結項目の場合ここから（かなり特殊なため間違いにご注意下さい）※連結項目が無い場合には無視して下さい　-->
<!--
      <tr>
        <th>電話番号（半角）　※</th>
        <td>
        <input size="5" type="text" name="tel[][-]" value="<?php if(isset($_SESSION['tel'][0]['-'])) echo h($_SESSION['tel'][0]['-']);?>" /> - 
        <input size="5" type="text" name="tel[][-]" value="<?php if(isset($_SESSION['tel'][1]['-'])) echo h($_SESSION['tel'][1]['-']);?>" /> - 
        <input size="5" type="text" name="tel[][]" value="<?php if(isset($_SESSION['tel'][2][0])) echo h($_SESSION['tel'][2][0]);?>" />
        
        </td>
      </tr>
      <tr>
        <th>郵便番号（半角）　※</th>
        <td>
        <input size="4" type="text" name="zip1[][-]" value="<?php if(isset($_SESSION['zip1'][0]['-'])) echo h($_SESSION['zip1'][0]['-']);?>" /> 
        - <input size="5" type="text" name="zip2[][]" value="<?php if(isset($_SESSION['zip2'][1][0])) echo h($_SESSION['zip2'][1][0]);?>" onKeyUp="AjaxZip3.zip2addr('zip1[][-]','zip2[][]','pref_cd','address1','address2');"/>
        </td>
      </tr>
-->
      <!-- 連結項目の場合ここまで　-->
      <tr>
        <th>電話番号（半角）　※</th>
        <td>
        <input size="5" type="text" name="tel1" value="<?php if(isset($_SESSION['tel1'])) echo h($_SESSION['tel1']);?>" /> - 
        <input size="5" type="text" name="tel2" value="<?php if(isset($_SESSION['tel2'])) echo h($_SESSION['tel2']);?>" /> - 
        <input size="5" type="text" name="tel3" value="<?php if(isset($_SESSION['tel3'])) echo h($_SESSION['tel3']);?>" />
        
        </td>
      </tr>
      <tr>
        <th>郵便番号（半角）　※</th>
        <td>
        <input size="4" type="text" name="zip1" value="<?php if(isset($_SESSION['zip1'])) echo h($_SESSION['zip1']);?>" /> - 
        <input size="5" type="text" name="zip2" value="<?php if(isset($_SESSION['zip2'])) echo h($_SESSION['zip2']);?>" onKeyUp="AjaxZip3.zip2addr('zip1','zip2','pref_cd','address1','address2');"/>
        </td>
      </tr>

      <tr>
        <th>都道府県　※</th>
        <td>
          <select name="pref_cd">
            <option value=""<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '') echo ' selected="selected"';?>>お選びください</option>
            <option value="01"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '01') echo ' selected="selected"';?>>北海道  </option>
            <option value="02"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '02') echo ' selected="selected"';?>>青森県  </option>
            <option value="03"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '03') echo ' selected="selected"';?>>岩手県  </option>
            <option value="04"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '04') echo ' selected="selected"';?>>宮城県  </option>
            <option value="05"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '05') echo ' selected="selected"';?>>秋田県  </option>
            <option value="06"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '06') echo ' selected="selected"';?>>山形県  </option>
            <option value="07"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '07') echo ' selected="selected"';?>>福島県  </option>
            <option value="08"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '08') echo ' selected="selected"';?>>茨城県  </option>
            <option value="09"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '09') echo ' selected="selected"';?>>栃木県  </option>
            <option value="10"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '10') echo ' selected="selected"';?>>群馬県  </option>
            <option value="11"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '11') echo ' selected="selected"';?>>埼玉県  </option>
            <option value="12"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '12') echo ' selected="selected"';?>>千葉県  </option>
            <option value="13"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '13') echo ' selected="selected"';?>>東京都  </option>
            <option value="14"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '14') echo ' selected="selected"';?>>神奈川県</option>
            <option value="15"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '15') echo ' selected="selected"';?>>新潟県  </option>
            <option value="16"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '16') echo ' selected="selected"';?>>富山県  </option>
            <option value="17"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '17') echo ' selected="selected"';?>>石川県  </option>
            <option value="18"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '18') echo ' selected="selected"';?>>福井県  </option>
            <option value="19"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '19') echo ' selected="selected"';?>>山梨県  </option>
            <option value="20"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '20') echo ' selected="selected"';?>>長野県  </option>
            <option value="21"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '21') echo ' selected="selected"';?>>岐阜県  </option>
            <option value="22"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '22') echo ' selected="selected"';?>>静岡県  </option>
            <option value="23"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '23') echo ' selected="selected"';?>>愛知県  </option>
            <option value="24"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '24') echo ' selected="selected"';?>>三重県  </option>
            <option value="25"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '25') echo ' selected="selected"';?>>滋賀県  </option>
            <option value="26"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '26') echo ' selected="selected"';?>>京都府  </option>
            <option value="27"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '27') echo ' selected="selected"';?>>大阪府  </option>
            <option value="28"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '28') echo ' selected="selected"';?>>兵庫県  </option>
            <option value="29"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '29') echo ' selected="selected"';?>>奈良県  </option>
            <option value="30"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '30') echo ' selected="selected"';?>>和歌山県</option>
            <option value="31"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '31') echo ' selected="selected"';?>>鳥取県  </option>
            <option value="32"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '32') echo ' selected="selected"';?>>島根県  </option>
            <option value="33"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '33') echo ' selected="selected"';?>>岡山県  </option>
            <option value="34"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '34') echo ' selected="selected"';?>>広島県  </option>
            <option value="35"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '35') echo ' selected="selected"';?>>山口県  </option>
            <option value="36"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '36') echo ' selected="selected"';?>>徳島県  </option>
            <option value="37"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '37') echo ' selected="selected"';?>>香川県  </option>
            <option value="38"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '38') echo ' selected="selected"';?>>愛媛県  </option>
            <option value="39"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '39') echo ' selected="selected"';?>>高知県  </option>
            <option value="40"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '40') echo ' selected="selected"';?>>福岡県  </option>
            <option value="41"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '41') echo ' selected="selected"';?>>佐賀県  </option>
            <option value="42"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '42') echo ' selected="selected"';?>>長崎県  </option>
            <option value="43"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '43') echo ' selected="selected"';?>>熊本県  </option>
            <option value="44"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '44') echo ' selected="selected"';?>>大分県  </option>
            <option value="45"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '45') echo ' selected="selected"';?>>宮崎県  </option>
            <option value="46"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '46') echo ' selected="selected"';?>>鹿児島県</option>
            <option value="47"<?php if(isset($_SESSION['pref_cd']) && $_SESSION['pref_cd'] == '47') echo ' selected="selected"';?>>沖縄県  </option>
          </select>
        </td>
      </tr>

      <tr>
        <th>市区郡町村　※</th>
        <td><input size="30" type="text" name="address1" value="<?php if(isset($_SESSION['address1'])) echo h($_SESSION['address1']);?>" /></td>
      </tr>
      <tr>
        <th>丁目・番地　※</th>
        <td><input size="30" type="text" name="address2" value="<?php if(isset($_SESSION['address2'])) echo h($_SESSION['address2']);?>" /></td>
      </tr>
      <tr>
        <th>ビル・マンション名</th>
        <td><input size="30" type="text" name="address3" value="<?php if(isset($_SESSION['address3'])) echo h($_SESSION['address3']);?>" /></td>
      </tr>

      
      <tr>
        <th>メールアドレス（半角）　※</th>
        <td><input size="30" type="text" name="mail" value="<?php if(isset($_SESSION['mail'])) echo h($_SESSION['mail']);?>" /></td>
      </tr>
      <tr>
        <th>性別　※</th>
        <td>
          <input type="radio" name="sex" id="sex1" value="男"<?php if(isset($_SESSION['sex']) && $_SESSION['sex'] == '男') echo ' checked="checked"';?> /> <label for="sex1">男</label>　
          <input type="radio" name="sex" id="sex2" value="女"<?php if(isset($_SESSION['sex']) && $_SESSION['sex'] == '女') echo ' checked="checked"';?> /> <label for="sex2">女</label>　
        </td>
      </tr>

      <tr>
        <th>生年月日　※</th>
        <td>
          <select name="year" id="year">
            <option value=""<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '') echo ' selected="selected"';?>>お選びください</option>
            <option value="2017"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2017') echo ' selected="selected"';?>>2017</option>
            <option value="2016"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2016') echo ' selected="selected"';?>>2016</option>
            <option value="2015"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2015') echo ' selected="selected"';?>>2015</option>
            <option value="2014"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2014') echo ' selected="selected"';?>>2014</option>
            <option value="2013"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2013') echo ' selected="selected"';?>>2013</option>
            <option value="2012"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2012') echo ' selected="selected"';?>>2012</option>
            <option value="2011"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2011') echo ' selected="selected"';?>>2011</option>
            <option value="2010"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2010') echo ' selected="selected"';?>>2010</option>
            <option value="2009"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2009') echo ' selected="selected"';?>>2009</option>
            <option value="2008"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2008') echo ' selected="selected"';?>>2008</option>
            <option value="2007"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2007') echo ' selected="selected"';?>>2007</option>
            <option value="2006"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2006') echo ' selected="selected"';?>>2006</option>
            <option value="2005"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2005') echo ' selected="selected"';?>>2005</option>
            <option value="2004"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2004') echo ' selected="selected"';?>>2004</option>
            <option value="2003"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2003') echo ' selected="selected"';?>>2003</option>
            <option value="2002"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2002') echo ' selected="selected"';?>>2002</option>
            <option value="2001"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2001') echo ' selected="selected"';?>>2001</option>
            <option value="2000"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '2000') echo ' selected="selected"';?>>2000</option>
            <option value="1999"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1999') echo ' selected="selected"';?>>1999</option>
            <option value="1998"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1998') echo ' selected="selected"';?>>1998</option>
            <option value="1997"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1997') echo ' selected="selected"';?>>1997</option>
            <option value="1996"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1996') echo ' selected="selected"';?>>1996</option>
            <option value="1995"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1995') echo ' selected="selected"';?>>1995</option>
            <option value="1994"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1994') echo ' selected="selected"';?>>1994</option>
            <option value="1993"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1993') echo ' selected="selected"';?>>1993</option>
            <option value="1992"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1992') echo ' selected="selected"';?>>1992</option>
            <option value="1991"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1991') echo ' selected="selected"';?>>1991</option>
            <option value="1990"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1990') echo ' selected="selected"';?>>1990</option>
            <option value="1989"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1989') echo ' selected="selected"';?>>1989</option>
            <option value="1988"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1988') echo ' selected="selected"';?>>1988</option>
            <option value="1987"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1987') echo ' selected="selected"';?>>1987</option>
            <option value="1986"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1986') echo ' selected="selected"';?>>1986</option>
            <option value="1985"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1985') echo ' selected="selected"';?>>1985</option>
            <option value="1984"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1984') echo ' selected="selected"';?>>1984</option>
            <option value="1983"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1983') echo ' selected="selected"';?>>1983</option>
            <option value="1982"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1982') echo ' selected="selected"';?>>1982</option>
            <option value="1981"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1981') echo ' selected="selected"';?>>1981</option>
            <option value="1980"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1980') echo ' selected="selected"';?>>1980</option>
            <option value="1979"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1979') echo ' selected="selected"';?>>1979</option>
            <option value="1978"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1978') echo ' selected="selected"';?>>1978</option>
            <option value="1977"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1977') echo ' selected="selected"';?>>1977</option>
            <option value="1976"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1976') echo ' selected="selected"';?>>1976</option>
            <option value="1975"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1975') echo ' selected="selected"';?>>1975</option>
            <option value="1974"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1974') echo ' selected="selected"';?>>1974</option>
            <option value="1973"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1973') echo ' selected="selected"';?>>1973</option>
            <option value="1972"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1972') echo ' selected="selected"';?>>1972</option>
            <option value="1971"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1971') echo ' selected="selected"';?>>1971</option>
            <option value="1970"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1970') echo ' selected="selected"';?>>1970</option>
            <option value="1969"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1969') echo ' selected="selected"';?>>1969</option>
            <option value="1968"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1968') echo ' selected="selected"';?>>1968</option>
            <option value="1967"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1967') echo ' selected="selected"';?>>1967</option>
            <option value="1966"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1966') echo ' selected="selected"';?>>1966</option>
            <option value="1965"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1965') echo ' selected="selected"';?>>1965</option>
            <option value="1964"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1964') echo ' selected="selected"';?>>1964</option>
            <option value="1963"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1963') echo ' selected="selected"';?>>1963</option>
            <option value="1962"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1962') echo ' selected="selected"';?>>1962</option>
            <option value="1961"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1961') echo ' selected="selected"';?>>1961</option>
            <option value="1960"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1960') echo ' selected="selected"';?>>1960</option>
            <option value="1959"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1959') echo ' selected="selected"';?>>1959</option>
            <option value="1958"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1958') echo ' selected="selected"';?>>1958</option>
            <option value="1957"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1957') echo ' selected="selected"';?>>1957</option>
            <option value="1956"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1956') echo ' selected="selected"';?>>1956</option>
            <option value="1955"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1955') echo ' selected="selected"';?>>1955</option>
            <option value="1954"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1954') echo ' selected="selected"';?>>1954</option>
            <option value="1953"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1953') echo ' selected="selected"';?>>1953</option>
            <option value="1952"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1952') echo ' selected="selected"';?>>1952</option>
            <option value="1951"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1951') echo ' selected="selected"';?>>1951</option>
            <option value="1950"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1950') echo ' selected="selected"';?>>1950</option>
            <option value="1949"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1949') echo ' selected="selected"';?>>1949</option>
            <option value="1948"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1948') echo ' selected="selected"';?>>1948</option>
            <option value="1947"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1947') echo ' selected="selected"';?>>1947</option>
            <option value="1946"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1946') echo ' selected="selected"';?>>1946</option>
            <option value="1945"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1945') echo ' selected="selected"';?>>1945</option>
            <option value="1944"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1944') echo ' selected="selected"';?>>1944</option>
            <option value="1943"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1943') echo ' selected="selected"';?>>1943</option>
            <option value="1942"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1942') echo ' selected="selected"';?>>1942</option>
            <option value="1941"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1941') echo ' selected="selected"';?>>1941</option>
            <option value="1940"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1940') echo ' selected="selected"';?>>1940</option>
            <option value="1939"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1939') echo ' selected="selected"';?>>1939</option>
            <option value="1938"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1938') echo ' selected="selected"';?>>1938</option>
            <option value="1937"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1937') echo ' selected="selected"';?>>1937</option>
            <option value="1936"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1936') echo ' selected="selected"';?>>1936</option>
            <option value="1935"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1935') echo ' selected="selected"';?>>1935</option>
            <option value="1934"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1934') echo ' selected="selected"';?>>1934</option>
            <option value="1933"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1933') echo ' selected="selected"';?>>1933</option>
            <option value="1932"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1932') echo ' selected="selected"';?>>1932</option>
            <option value="1931"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1931') echo ' selected="selected"';?>>1931</option>
            <option value="1930"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1930') echo ' selected="selected"';?>>1930</option>
            <option value="1929"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1929') echo ' selected="selected"';?>>1929</option>
            <option value="1928"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1928') echo ' selected="selected"';?>>1928</option>
            <option value="1927"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1927') echo ' selected="selected"';?>>1927</option>
            <option value="1926"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1926') echo ' selected="selected"';?>>1926</option>
            <option value="1925"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1925') echo ' selected="selected"';?>>1925</option>
            <option value="1924"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1924') echo ' selected="selected"';?>>1924</option>
            <option value="1923"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1923') echo ' selected="selected"';?>>1923</option>
            <option value="1922"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1922') echo ' selected="selected"';?>>1922</option>
            <option value="1921"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1921') echo ' selected="selected"';?>>1921</option>
            <option value="1920"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1920') echo ' selected="selected"';?>>1920</option>
            <option value="1919"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1919') echo ' selected="selected"';?>>1919</option>
            <option value="1918"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1918') echo ' selected="selected"';?>>1918</option>
            <option value="1917"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1917') echo ' selected="selected"';?>>1917</option>
            <option value="1916"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1916') echo ' selected="selected"';?>>1916</option>
            <option value="1915"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1915') echo ' selected="selected"';?>>1915</option>
            <option value="1914"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1914') echo ' selected="selected"';?>>1914</option>
            <option value="1913"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1913') echo ' selected="selected"';?>>1913</option>
            <option value="1912"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1912') echo ' selected="selected"';?>>1912</option>
            <option value="1911"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1911') echo ' selected="selected"';?>>1911</option>
            <option value="1910"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1910') echo ' selected="selected"';?>>1910</option>
            <option value="1909"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1909') echo ' selected="selected"';?>>1909</option>
            <option value="1908"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1908') echo ' selected="selected"';?>>1908</option>
            <option value="1907"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1907') echo ' selected="selected"';?>>1907</option>
            <option value="1906"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1906') echo ' selected="selected"';?>>1906</option>
            <option value="1905"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1905') echo ' selected="selected"';?>>1905</option>
            <option value="1904"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1904') echo ' selected="selected"';?>>1904</option>
            <option value="1903"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1903') echo ' selected="selected"';?>>1903</option>
            <option value="1902"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1902') echo ' selected="selected"';?>>1902</option>
            <option value="1901"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1901') echo ' selected="selected"';?>>1901</option>
            <option value="1900"<?php if(isset($_SESSION['year']) && $_SESSION['year'] == '1900') echo ' selected="selected"';?>>1900</option>
          </select><label for="year">年</label>

          <select name="month" id="month">
            <option value=""<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '') echo ' selected="selected"';?>>お選びください</option>
            <option value="01"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '01') echo ' selected="selected"';?>>1</option>
            <option value="02"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '02') echo ' selected="selected"';?>>2</option>
            <option value="03"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '03') echo ' selected="selected"';?>>3</option>
            <option value="04"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '04') echo ' selected="selected"';?>>4</option>
            <option value="05"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '05') echo ' selected="selected"';?>>5</option>
            <option value="06"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '06') echo ' selected="selected"';?>>6</option>
            <option value="07"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '07') echo ' selected="selected"';?>>7</option>
            <option value="08"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '08') echo ' selected="selected"';?>>8</option>
            <option value="09"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '09') echo ' selected="selected"';?>>9</option>
            <option value="10"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '10') echo ' selected="selected"';?>>10</option>
            <option value="11"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '11') echo ' selected="selected"';?>>11</option>
            <option value="12"<?php if(isset($_SESSION['month']) && $_SESSION['month'] == '12') echo ' selected="selected"';?>>12</option>
          </select><label for="month">月</label>

          <select name="day" id="day">
            <option value=""<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '') echo ' selected="selected"';?>>お選びください</option>
            <option value="01"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '01') echo ' selected="selected"';?>>01</option>
            <option value="02"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '02') echo ' selected="selected"';?>>02</option>
            <option value="03"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '03') echo ' selected="selected"';?>>03</option>
            <option value="04"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '04') echo ' selected="selected"';?>>04</option>
            <option value="05"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '05') echo ' selected="selected"';?>>05</option>
            <option value="06"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '06') echo ' selected="selected"';?>>06</option>
            <option value="07"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '07') echo ' selected="selected"';?>>07</option>
            <option value="08"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '08') echo ' selected="selected"';?>>08</option>
            <option value="09"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '09') echo ' selected="selected"';?>>09</option>
            <option value="10"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '10') echo ' selected="selected"';?>>10</option>
            <option value="11"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '11') echo ' selected="selected"';?>>11</option>
            <option value="12"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '12') echo ' selected="selected"';?>>12</option>
            <option value="13"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '13') echo ' selected="selected"';?>>13</option>
            <option value="14"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '14') echo ' selected="selected"';?>>14</option>
            <option value="15"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '15') echo ' selected="selected"';?>>15</option>
            <option value="16"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '16') echo ' selected="selected"';?>>16</option>
            <option value="17"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '17') echo ' selected="selected"';?>>17</option>
            <option value="18"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '18') echo ' selected="selected"';?>>18</option>
            <option value="19"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '19') echo ' selected="selected"';?>>19</option>
            <option value="20"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '20') echo ' selected="selected"';?>>20</option>
            <option value="21"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '21') echo ' selected="selected"';?>>21</option>
            <option value="22"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '22') echo ' selected="selected"';?>>22</option>
            <option value="23"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '23') echo ' selected="selected"';?>>23</option>
            <option value="24"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '24') echo ' selected="selected"';?>>24</option>
            <option value="25"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '25') echo ' selected="selected"';?>>25</option>
            <option value="26"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '26') echo ' selected="selected"';?>>26</option>
            <option value="27"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '27') echo ' selected="selected"';?>>27</option>
            <option value="28"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '28') echo ' selected="selected"';?>>28</option>
            <option value="29"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '29') echo ' selected="selected"';?>>29</option>
            <option value="30"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '30') echo ' selected="selected"';?>>30</option>
            <option value="31"<?php if(isset($_SESSION['day']) && $_SESSION['day'] == '31') echo ' selected="selected"';?>>31</option>
          </select><label for="day">日</label>
          <!--<span id="birth_error" class="error_m"></span>-->
        </td>
      </tr>

      <tr>
        <th>サイトを知ったきっかけ</th>
        <td><input name="サイトを知ったきっかけ[]" type="checkbox" value="友人・知人"<?php if(isset($_SESSION['サイトを知ったきっかけ']) && in_array('友人・知人',$_SESSION['サイトを知ったきっかけ'])) echo ' checked="checked"';?> /> 友人・知人　
          <input name="サイトを知ったきっかけ[]" type="checkbox" value="検索エンジン"<?php if(isset($_SESSION['サイトを知ったきっかけ']) && in_array('検索エンジン',$_SESSION['サイトを知ったきっかけ'])) echo ' checked="checked"';?> /> 検索エンジン</td>
      </tr>
    </table>
    <p align="center">
      <input type="submit" value="　 確認 　" />
    </p>
  </form>
  <p>※IPアドレスを記録しております。いたずらや嫌がらせ等はご遠慮ください</p>
</div>
</body>
</html>