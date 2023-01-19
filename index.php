<?php
require "php_header.php";
if(EXEC_MODE=="Trial"){
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: menu.php");
        exit();
}
//$_SESSION['csrf_token'] = get_token(); // CSRFのトークンを取得する

//自動ログイン情報の取得
$login_type = (!empty($_COOKIE["login_type"])?$_COOKIE["login_type"]:"normal");

//if (isset($_COOKIE['webrez_token'])) {
if ($login_type==="auto") {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: logincheck.php");
}

$errmsg = "";
if(isset($_SESSION["EMSG"])){
    $errmsg="<div style='color:red'>".$_SESSION["EMSG"]."</div>";
    //一度エラーを表示したらクリアする
    $_SESSION["EMSG"]="";
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head.html" 
    ?>
    <!--ページ専用CSS-->
    <link rel="stylesheet" href="css/style_index.css?<?php echo $time; ?>" >
    <script src="script/index.js"></script>
    <TITLE><?php echo secho($title)." ようこそ";?></TITLE>
</head>
<body style='width:900px;'>
  <div  id='app'>
  <header><h1>タイピング、やろ～よ</h1></header>
  <main>
    <div style='border: solid;border-width: thin;'>
      <div style='text-align:center;border: solid;border-width: thin;'>
        <div style='height:100px;margin:0px;'>
          <p>これがうてるかな？</p>
          <p style='font-size:20px;'>{{mondai_disp}}</p>
          <span class='mondai'>『{{mondai}}』</span>
        </div>
        <div style='display:flex;margin:0 200px;padding-top:10px;font-size:20px;'>
          <div :class='hit' style="width:100px;margin-left: auto;margin-right: auto;">あ た り！</div>
          <div :class='miss' style="width:100px;margin-left: auto;margin-right: auto;">は ず れ！</div>
        </div>
      </div>
      <div style='height:150px;border: solid;border-width: thin;'>
        <p>タイピング：{{typing}} => {{typingJP}}</p>
        <p>答え：{{answer}}</p>
      </div>
      <div style='text-align:center;padding:5px 0px'>
        <button @click='get_next_task()' class='btn btn-primary' style='width:150px;height:40px;font-size:20px;'>スタート！</button>
      </div>
    </div>
  </main>
  <footer></footer>
  </div>
  <script>
		const { createApp, ref, onMounted, computed, VueCookies } = Vue;
		createApp({
			setup(){
				const typing=ref('')
				const typingJP=ref('')
				const answer=ref('')
				const hit=ref('')
				const miss=ref('')
				let chk_flg=false
				//ローマ字変換表
				const henkanhyou = ref([
					{'eng':'A', 'jp':'あ'},
					{'eng':'I', 'jp':'い'},
					{'eng':'U', 'jp':'う'},
					{'eng':'E', 'jp':'え'},
					{'eng':'O', 'jp':'お'},
					{'eng':'KA', 'jp':'か'},
					{'eng':'KI', 'jp':'き'},
					{'eng':'KU', 'jp':'く'},
					{'eng':'KE', 'jp':'け'},
					{'eng':'KO', 'jp':'こ'},
					{'eng':'GA', 'jp':'が'},
					{'eng':'GI', 'jp':'ぎ'},
					{'eng':'GU', 'jp':'ぐ'},
					{'eng':'GE', 'jp':'げ'},
					{'eng':'GO', 'jp':'ご'},
					{'eng':'SA', 'jp':'さ'},
					{'eng':'SI', 'jp':'し'},
					{'eng':'SU', 'jp':'す'},
					{'eng':'SE', 'jp':'せ'},
					{'eng':'SO', 'jp':'そ'},
					{'eng':'ZA', 'jp':'ざ'},
					{'eng':'ZI', 'jp':'じ'},
					{'eng':'ZU', 'jp':'ず'},
					{'eng':'ZE', 'jp':'ぜ'},
					{'eng':'ZO', 'jp':'ぞ'},
					{'eng':'JI', 'jp':'じ'},
					{'eng':'TA', 'jp':'た'},
					{'eng':'TI', 'jp':'ち'},
					{'eng':'TU', 'jp':'つ'},
					{'eng':'TE', 'jp':'て'},
					{'eng':'TO', 'jp':'と'},
					{'eng':'CHI', 'jp':'ち'},
					{'eng':'TSU', 'jp':'つ'},
					{'eng':'DA', 'jp':'だ'},
					{'eng':'DI', 'jp':'ぢ'},
					{'eng':'DU', 'jp':'づ'},
					{'eng':'DE', 'jp':'で'},
					{'eng':'DO', 'jp':'ど'},
					{'eng':'NA', 'jp':'な'},
					{'eng':'NI', 'jp':'に'},
					{'eng':'NU', 'jp':'ぬ'},
					{'eng':'NE', 'jp':'ね'},
					{'eng':'NO', 'jp':'の'},
					{'eng':'HA', 'jp':'は'},
					{'eng':'HI', 'jp':'ひ'},
					{'eng':'HU', 'jp':'ふ'},
					{'eng':'HE', 'jp':'へ'},
					{'eng':'HO', 'jp':'ほ'},
					{'eng':'FU', 'jp':'ふ'},
					{'eng':'BA', 'jp':'ば'},
					{'eng':'BI', 'jp':'び'},
					{'eng':'BU', 'jp':'ぶ'},
					{'eng':'BE', 'jp':'べ'},
					{'eng':'BO', 'jp':'ぼ'},
					{'eng':'PA', 'jp':'ぱ'},
					{'eng':'PI', 'jp':'ぴ'},
					{'eng':'PU', 'jp':'ぷ'},
					{'eng':'PE', 'jp':'ぺ'},
					{'eng':'PO', 'jp':'ぽ'},
					{'eng':'MA', 'jp':'ま'},
					{'eng':'MI', 'jp':'み'},
					{'eng':'MU', 'jp':'む'},
					{'eng':'ME', 'jp':'め'},
					{'eng':'MO', 'jp':'も'},
					{'eng':'YA', 'jp':'や'},
					{'eng':'YU', 'jp':'ゆ'},
					{'eng':'YO', 'jp':'よ'},
					{'eng':'RA', 'jp':'ら'},
					{'eng':'RI', 'jp':'り'},
					{'eng':'RU', 'jp':'る'},
					{'eng':'RE', 'jp':'れ'},
					{'eng':'RO', 'jp':'ろ'},
					{'eng':'WA', 'jp':'わ'},
					{'eng':'WO', 'jp':'を'},
					{'eng':'NN', 'jp':'ん'},
					{'eng':'XA', 'jp':'ぁ'},
					{'eng':'XI', 'jp':'ぃ'},
					{'eng':'XU', 'jp':'ぅ'},
					{'eng':'XE', 'jp':'ぇ'},
					{'eng':'XO', 'jp':'ぉ'},
					{'eng':'LA', 'jp':'ぁ'},
					{'eng':'LI', 'jp':'ぃ'},
					{'eng':'LU', 'jp':'ぅ'},
					{'eng':'LE', 'jp':'ぇ'},
					{'eng':'LO', 'jp':'ぉ'},
					{'eng':'KYA', 'jp':'きゃ'},
					{'eng':'KYI', 'jp':'きぃ'},
					{'eng':'KYU', 'jp':'きゅ'},
					{'eng':'KYE', 'jp':'きぇ'},
					{'eng':'KYO', 'jp':'きょ'},
					{'eng':'QA', 'jp':'くぁ'},
					{'eng':'QI', 'jp':'くぃ'},
					{'eng':'QWU', 'jp':'くぅ'},
					{'eng':'QE', 'jp':'くぇ'},
					{'eng':'QO', 'jp':'くぉ'},
					{'eng':'GYA', 'jp':'ぎゃ'},
					{'eng':'GYI', 'jp':'ぎぃ'},
					{'eng':'GYU', 'jp':'ぎゅ'},
					{'eng':'GYE', 'jp':'ぎぇ'},
					{'eng':'GYO', 'jp':'ぎょ'},
					{'eng':'GWA', 'jp':'ぐぁ'},
					{'eng':'GWI', 'jp':'ぐぃ'},
					{'eng':'GWU', 'jp':'ぐぅ'},
					{'eng':'GWE', 'jp':'ぐぇ'},
					{'eng':'GWO', 'jp':'ぐぉ'},
					{'eng':'SYA', 'jp':'しゃ'},
					{'eng':'SYI', 'jp':'しぃ'},
					{'eng':'SYU', 'jp':'しゅ'},
					{'eng':'SYE', 'jp':'しぇ'},
					{'eng':'SYO', 'jp':'しょ'},
					{'eng':'SHA', 'jp':'しゃ'},
					{'eng':'SHU', 'jp':'しゅ'},
					{'eng':'SHE', 'jp':'しぇ'},
					{'eng':'SHO', 'jp':'しょ'},
					{'eng':'SWA', 'jp':'すぁ'},
					{'eng':'SWI', 'jp':'すぃ'},
					{'eng':'SWU', 'jp':'すぅ'},
					{'eng':'SWE', 'jp':'すぇ'},
					{'eng':'SWO', 'jp':'すぉ'},
					{'eng':'JA', 'jp':'じゃ'},
					{'eng':'ZYI', 'jp':'じぃ'},
					{'eng':'JU', 'jp':'じゅ'},
					{'eng':'JE', 'jp':'じぇ'},
					{'eng':'JO', 'jp':'じょ'},
					{'eng':'ZYA', 'jp':'じゃ'},
					{'eng':'ZYU', 'jp':'じゅ'},
					{'eng':'ZYE', 'jp':'じぇ'},
					{'eng':'ZYO', 'jp':'じょ'},
					{'eng':'TYA', 'jp':'ちゃ'},
					{'eng':'TYI', 'jp':'ちぃ'},
					{'eng':'TYU', 'jp':'ちゅ'},
					{'eng':'TYE', 'jp':'ちぇ'},
					{'eng':'TYO', 'jp':'ちょ'},
					{'eng':'CHA', 'jp':'ちゃ'},
					{'eng':'CHU', 'jp':'ちゅ'},
					{'eng':'CHE', 'jp':'ちぇ'},
					{'eng':'CHO', 'jp':'ちょ'},
					{'eng':'THA', 'jp':'てゃ'},
					{'eng':'THI', 'jp':'てぃ'},
					{'eng':'THU', 'jp':'てゅ'},
					{'eng':'THE', 'jp':'てぇ'},
					{'eng':'THO', 'jp':'てょ'},
					{'eng':'TWA', 'jp':'とぁ'},
					{'eng':'TWI', 'jp':'とぃ'},
					{'eng':'TWO', 'jp':'とぅ'},
					{'eng':'TWE', 'jp':'とぇ'},
					{'eng':'TWO', 'jp':'とぉ'},
					{'eng':'DYA', 'jp':'ぢゃ'},
					{'eng':'DYI', 'jp':'ぢぃ'},
					{'eng':'DYU', 'jp':'ぢゅ'},
					{'eng':'DYE', 'jp':'ぢぇ'},
					{'eng':'DYO', 'jp':'ぢょ'},
					{'eng':'DHA', 'jp':'でゃ'},
					{'eng':'DHI', 'jp':'でぃ'},
					{'eng':'DHU', 'jp':'でゅ'},
					{'eng':'DHE', 'jp':'でぇ'},
					{'eng':'DHO', 'jp':'でょ'},
					{'eng':'DWA', 'jp':'どぁ'},
					{'eng':'DWI', 'jp':'どぃ'},
					{'eng':'DWU', 'jp':'どぅ'},
					{'eng':'DWE', 'jp':'どぇ'},
					{'eng':'DWO', 'jp':'どぉ'},
					{'eng':'NYA', 'jp':'にゃ'},
					{'eng':'NYI', 'jp':'にぃ'},
					{'eng':'NYU', 'jp':'にゅ'},
					{'eng':'NYE', 'jp':'にぇ'},
					{'eng':'NYO', 'jp':'にょ'},
					{'eng':'HYA', 'jp':'ひゃ'},
					{'eng':'HYI', 'jp':'ひぃ'},
					{'eng':'HYU', 'jp':'ひゅ'},
					{'eng':'HYE', 'jp':'ひぇ'},
					{'eng':'HYO', 'jp':'ひょ'},
					{'eng':'FA', 'jp':'ふぁ'},
					{'eng':'FI', 'jp':'ふぃ'},
					{'eng':'FWU', 'jp':'ふぅ'},
					{'eng':'FE', 'jp':'ふぇ'},
					{'eng':'FO', 'jp':'ふぉ'},
					{'eng':'BYA', 'jp':'びゃ'},
					{'eng':'BYI', 'jp':'びぃ'},
					{'eng':'BYU', 'jp':'びゅ'},
					{'eng':'BYE', 'jp':'びぇ'},
					{'eng':'BYO', 'jp':'びょ'},
					{'eng':'PYA', 'jp':'ぴゃ'},
					{'eng':'PYI', 'jp':'ぴぃ'},
					{'eng':'PYU', 'jp':'ぴゅ'},
					{'eng':'PYE', 'jp':'ぴぇ'},
					{'eng':'PYO', 'jp':'ぴょ'},
					{'eng':'MYA', 'jp':'みゃ'},
					{'eng':'MYI', 'jp':'みぃ'},
					{'eng':'MYU', 'jp':'みゅ'},
					{'eng':'MYE', 'jp':'みぇ'},
					{'eng':'MYO', 'jp':'みょ'},
					{'eng':'RYA', 'jp':'りゃ'},
					{'eng':'RYI', 'jp':'りぃ'},
					{'eng':'RYU', 'jp':'りゅ'},
					{'eng':'RYE', 'jp':'りぇ'},
					{'eng':'RYO', 'jp':'りょ'},
					{'eng':'WHA', 'jp':'うぁ'},
					{'eng':'WI', 'jp':'うぃ'},
					{'eng':'WE', 'jp':'うぇ'},
					{'eng':'WHO', 'jp':'うぉ'},
					{'eng':'-', 'jp':'ー'}
				])
				const hitmiss_cleare=()=>{
					miss.value=''
					hit.value=''
				}
				const onKeyPress = (e) =>{
					console.log('onKeyPress start')
					console.log(e)
          if(e.shiftKey){
            console.log('shift key press')
            return 0
          }else if(e.key==='Backspace'){
            console.log('Backspace key press')
            if(typing.value.length>0){
              typing.value = typing.value.substr(0,typing.value.length-1)
              typingJP.value=''
            }
            return 0
          }else if(e.key.length>1){
            console.log('文字以外' + e.key)
            return 0
          }

          if(chk_flg){
            typing.value=''
            typingJP.value=''
            chk_flg=false
          }
          //アルファベット判定
          if(check_answer(e.key)){
            typingJP.value = e.key
            answer.value = answer.value + e.key
            hit.value='buruburu'
            setTimeout(hitmiss_cleare, 200);
            chk_flg=true
            return 0
          }
          typing.value = typing.value + event.key

          //ひらがな判定
          let jp = get_moji(typing.value)
          console.log(jp)
          if(jp[0]===true){
            typingJP.value = jp[1]
            if(check_answer(jp[1])){
              answer.value = answer.value + jp[1]
              chk_flg=true
              hit.value='buruburu'
            }else{
              miss.value='buruburu'
              typing.value = ''
            }
            setTimeout(hitmiss_cleare, 200);
          }
          //アルファベット4文字以上はミス
          if(typing.value.length>=4){
              miss.value='buruburu'
              typing.value = ''
              setTimeout(hitmiss_cleare, 200);
          }
          //次の問題
          if(mondai.value===''){
            if(get_next_task()==='finish'){
 
<header  class="header-color common_header" style="flex-wrap:wrap">
    <div class="title" style="width: 100%;"><a href="index.php" ><?php echo secho($title);?></a></div>
    <div style="font-size:1rem;"> ようこそWEBREZへ</div>
</header>

<body class='common_body'>
    <div class="container">
        <div class="card card-container">
            <!-- 
            <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" />
            <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />　class="form-check-input"
            <p id="profile-name" class="profile-name-card"></p>
            -->
            <?php echo $errmsg; ?>
            <form class="form-signin" id="form1" method="post" action="logincheck.php">
                <span id="reauth-email" class="reauth-email"></span>
                <input type="email" id="inputEmail" class="form-control" placeholder="Email address" name="LOGIN_EMAIL" required autofocus>
                <input type="password" id="inputPassword" class="form-control" name="LOGIN_PASS" placeholder="Password" required>
                <div id="remember" class="checkbox">
                    <label>
                        <input type="checkbox" name="AUTOLOGIN" checked> Remember 
                    </label>
                </div>

                <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit"  >ロ グ イ ン</button>
                <input type="hidden" name="csrf_token" value="<?php echo csrf_create() /*secho($_SESSION['csrf_token'])*/ ?>">
            </form><!-- /form -->
            <a href="forget_pass_sendurl.php" class="forgot-password">
                ﾊﾟｽﾜｰﾄﾞを忘れたらｸﾘｯｸ
            </a>
            <hr>
            <!--<a href="account_create.php?mode=0" class="btn btn-lg btn-primary btn-block btn-signin" style="padding-top:8px" >新 規 登 録</a>-->
            <a href="pre_account.php" class="btn btn-lg btn-primary btn-block btn-signin" style="padding-top:8px" >新 規 登 録</a>
        </div><!-- /card-container -->
    </div><!-- /container -->    
</body>
<script>
window.onload = function() {
    // Enterキーが押された時にSubmitされるのを抑制する
    document.getElementById("form1").onkeypress = (e) => {
        // form1に入力されたキーを取得
        const key = e.keyCode || e.charCode || 0;
        // 13はEnterキーのキーコード
        if (key == 13) {
            // アクションを行わない
            e.preventDefault();
        }
    }    
    
};    

</script>
</html>
<?php
log_writer("php_header.php _SESSION values ",$_SESSION);
?>