var validate = function() {

	var flag = true;

	removeElementsByClass("error");
	removeClass("error-form");

	// お名前の入力をチェック
	if(document.form.last_name.value == "" || document.form.first_name.value == ""){
		errorElement(document.form.first_name, "お名前が入力されていません");
		flag = false;
	}

	// カタカナの入力をチェック
	if(document.form.last_name_kana.value == "" || document.form.first_name_kana.value == ""){
		errorElement(document.form.first_name_kana, "カタカナ入力されていません");
		flag = false;
	} else {
		// カタカナの形式をチェック
		if(validateKana(document.form.last_name_kana.value) || validateKana(document.form.first_name_kana.value)){
			errorElement(document.form.first_name_kana, "カタカナ以外の文字が入っています");
			flag = false;
		}
	}

	// 電話番号の入力をチェック
	if(document.form.tel1.value == "" || document.form.tel2.value == "" || document.form.tel3.value == ""){
		errorElement(document.form.tel3, "電話番号が入力されていません");
		flag = false;
	} else {
		// 電話番号の形式をチェック
		if(!validateNumber(document.form.tel1.value) || !validateNumber(document.form.tel2.value) || !validateNumber(document.form.tel3.value)){
			errorElement(document.form.tel3, "半角数字のみを入力してください");
			flag = false;
		} 
		/*
		else {
			if(!validateTel(document.form.tel1.value) || !validateTel(document.form.tel2.value) || !validateTel(document.form.tel3.value)){
				errorElement(document.form.tel3, "電話番号が正しくありません");
				flag = false;
			}
		}
		*/
	}
	
	// 郵便番号の入力をチェック
	if(document.form.zip1.value == "" || document.form.zip2.value == ""){
		errorElement(document.form.zip2, "郵便番号が入力されていません");
		flag = false;
	} else {
		// 郵便番号の形式をチェック
		if(!validateNumber(document.form.zip1.value) || !validateNumber(document.form.zip2.value)){
			errorElement(document.form.zip2, "半角数字のみを入力してください");
			flag = false;
		}
	}

	// 都道府県項目の選択をチェック
	if(document.form.pref_cd.value == ""){
		errorElement(document.form.pref_cd, "都道府県を選択してください");
		flag = false;
	}
	// 市区郡町村項目の選択をチェック
	if(document.form.address1.value == ""){
		errorElement(document.form.address1, "市区郡町村が入力されていません");
		flag = false;
	}
	// 丁目・番地項目の選択をチェック
	if(document.form.address2.value == ""){
		errorElement(document.form.address2, "丁目・番地が入力されていません");
		flag = false;
	}
	
	// メールアドレスの入力をチェック
	if(document.form.mail.value == ""){
		errorElement(document.form.mail, "メールアドレスが入力されていません");
		flag = false;
	} else {
		// メールアドレスの形式をチェック
		if(!validateMail(document.form.mail.value)){
			errorElement(document.form.mail, "メールアドレスが正しくありません");
			flag = false;
		}
	}


	// 生年月日項目の選択をチェック
	if(document.form.year.value == "" || document.form.month.value == "" || document.form.day.value == ""){
		errorElement(document.form.day, "生年月日を選択してください");
		//$('#birth_error').html("<div class='error'>生年月日を選択してください</div> 正しい電話番号を入力してください。");
		flag = false;
	}

	return flag;
}



var errorElement = function(form, msg) {
	form.className = "error-form";
	var newElement = document.createElement("div");
	newElement.className = "error";
	var newText = document.createTextNode(msg);
	newElement.appendChild(newText);
	form.parentNode.insertBefore(newElement, form.nextSibling);
}


var removeElementsByClass = function(className){
	var elements = document.getElementsByClassName(className);
	while (elements.length > 0){ 
		elements[0].parentNode.removeChild(elements[0]);
	}
}

var removeClass = function(className){
	var elements = document.getElementsByClassName(className);
	while (elements.length > 0) {
		elements[0].className = "";
	}
}

var validateMail = function (val){
	if (val.match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)==null) {
		return false;
	} else {
		return true;
	}
}


var validateNumber = function (val){
	if (val.match(/[^0-9]+/)) {
		return false;
	} else {
		return true;
	}
}


var validateTel = function (val){
	if (val.match(/^[0-9-]{6,13}$/) == null) {
		return false;
	} else {
		return true;
	}
}


var validateKana = function (val){
	if (val.match(/^[ぁ-んー　]*$/) == null) {
		return false;
	} else {
		return true;
	}
}
