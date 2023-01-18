<?php

function shutdown()
{
    // これがシャットダウン関数で、
    // スクリプトの処理が完了する前に
    // ここで何らかの操作をすることができます
		$lastError = error_get_last();
		//echo "stop:".$lastError;
}
register_shutdown_function('shutdown');

$time=date('Ymd-His');
?>
<!DOCTYPE html>
<html lang='ja'>

<head>
  <meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'>
  <META http-equiv='Content-Type' content='text/html; charset=UTF-8'>
  <link rel='apple-touch-icon' href='apple-touch-icon.png'>
  <!-- Bootstrap5 CSS/js -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c&family=Reggae+One&family=Rowdies&display=swap" rel="stylesheet">
  <!--サイト共通-->
  <link rel='stylesheet' href='css/style.css?<?php echo $time; ?>' >
  <!--Vue.js-->
  <script src="https://unpkg.com/vue@next"></script>
  <script src="https://unpkg.com/vue-cookies@1.8.2/vue-cookies.js"></script>
  <!--ajaxライブラリ-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
  <!--<script>axios.defaults.baseURL = <?php //echo "'".ROOT_URL."'" ?>;</script>-->
  <link rel='manifest' href='manifest.webmanifest'>
  <script>
      if('serviceWorker' in navigator){
      	navigator.serviceWorker.register('serviceworker.js').then(function(){
      		console.log("Service Worker is registered!!");
      	});
      }
      //スマフォで:active :hover を有効に
      document.getElementsByTagName('html')[0].setAttribute('ontouchstart', '');
  </script>    <!--ページ専用CSS-->
  <TITLE>タイピング、やろ～よ</TITLE>
</head>
<body>
  <div  id='app'>
  <header><h1>タイピング、やろ～よ</h1></header>
  <main>
    <div style='border: solid;border-width: thin;'>
      <div style='text-align:center;min-width:400px;border: solid;border-width: thin;'>
        <div style='height:100px;margin:0px;'>
          <p>これがうてるかな？</p>
          <p style='font-size:20px;'>{{mondai_disp}}</p>
          <span class='mondai'>『{{mondai}}』</span>
        </div>
        <div style='display:flex;margin:0 100px;padding-top:10px;font-size:20px;'>
          <div :class='hit' style="width:100px;margin-left: auto;margin-right: auto;">あ た り！</div>
          <div :class='miss' style="width:100px;margin-left: auto;margin-right: auto;">は ず れ！</div>
        </div>
      </div>
      <div style='width:min-400px;height:150px;border: solid;border-width: thin;'>
        <p>タイピング：{{typing}} => {{typingJP}}</p>
        <p>答え：{{answer}}</p>
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

            }
          }
        }

        const get_moji = (key) => {//ローマ文字変換
          console.log('get_moji [' + key + ']')
          let moji = ([])
          let loc_key = key
          let ttu = ''  //っ
          //小さい「つ」の判定
          if(loc_key.length>=2){
            if(loc_key.substr(0,1)===loc_key.substr(1,1)){
              loc_key=loc_key.substr(1,loc_key.length-1)
              ttu = 'っ'
            }
          }
          moji = henkanhyou.value.filter((record) => {
            return (record.eng.toUpperCase() === loc_key.toUpperCase());
          })
          if(moji.length===0){
            return [false,key]
          }else{
            return [true,ttu + moji[0].jp]
          }
        }

        const mondai_disp=ref('河童の川流れ')
        const mondai=ref('かっぱのかわながれ')
        const check_answer = (moji) =>{
        let mojisu = moji.length
        if(mondai.value.substr(0,mojisu) === moji){
          console.log('hit!')
          if(mondai.value.length-mojisu === 0){
            mondai.value = ''
          }else{
            mondai.value = mondai.value.slice((mondai.value.length - mojisu) * (-1))
          }
          return true
        }else{
          console.log('miss!')
          return false
        }
        }
        const get_next_task=()=>{
          typingJP.value=''
          mondai.value = 'いけ！たんじろう'
          return 'finish'
        }


        onMounted(()=>{
          document.addEventListener('keydown', onKeyPress)
        })
        return{
          typing,
          onKeyPress,
          typingJP,
          mondai,
          answer,
          hit,
          miss,
          mondai_disp,
        }
      }
    }).mount('#app');
  </script>
  <script>
	// Enterキーが押された時にSubmitされるのを抑制する
  /*
	document.onkeypress = (e) => {
		// form1に入力されたキーを取得
    console.log('js onKeyPress')
    console.log(e)
		const key = e.keyCode || e.charCode || 0;
		// 13はEnterキーのキーコード
		if (key == 13) {
			// アクションを行わない
			e.preventDefault();
		}
	}
  */
  </script>
</body>
