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
	<meta name="google-site-verification" content="Ti3a9WFQOPYTR_HHzZ2jtft_iw_RN3xK_ydPCS0EpfQ" />
	<meta name="description" content="webで遊べる！すぐに遊べる！無料で無制限タイピングゲーム。こどもから大人まで、気軽にどうぞ～">
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
	<script src="https://cdn.jsdelivr.net/npm/vue@3.4.4"></script>
	<script src="https://unpkg.com/vue-cookies@1.8.2/vue-cookies.js"></script>
	<!--ajaxライブラリ-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
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
	<TITLE>【無料タイピングゲーム】タイピング、やろ～よ</TITLE>
</head>
<body style='width:100%;min-width:900px;text-align:center;'>
	<div  id='app'>
	<header> 
		<h1>タイピング、やろ～よ</h1>
		<select v-model='level' @change='get_mondai_List()' class='form-select form-select-lg' style='text-align:center;width:150px;margin-right:auto; margin-left:auto;'>
			<option value=1>れべる１</option>
			<option value=2>れべる２</option>
			<option value=3>れべる３</option>
			<option value=4>れべる４</option>
			<option value=5>れべる５</option>
		</select>
	</header>
	<main>
		<div v-if='hit' class='wrap' style='text-align:center;width:300px;font-size:200px;z-index:99;color:blue;'>〇</div>
		<div v-if='miss' class='buruburu wrap' style='text-align:center;width:300px;font-size:100px;z-index:99;'>ＸＸＸ</div>
		<div v-if=ture class='wrap' style='text-align:center;width:600px;font-size:100px;z-index:99;color:blue;'>おしまいっ！</div>

		<div style='border: solid;border-width: thin;'>
			<div style='text-align:center;border: solid;border-width: thin;padding:10px 0'>
				<div style='height:160px;margin:0px;'>
					<p>これがうてるかな？</p>
					<p style='font-size:20px;'>{{mondai_disp}}</p><!--漢字-->
					<p class='mondai kana' style='font-size:16px;'>『{{mondai}}』</p><!--ひらがな-->
					<br>
					<p v-if='mondai_roma!==""' class='romaji' style='font-size:23px;'>{{mondai_roma}}</p><!--アルファベット-->
					<template v-if='mondai_roma===""' >
					<span class='romaji' style='font-size:23px;color:red;'>{{get_romaji[1]}}</span><!--アルファベット-->
						　　>>>　　
						<span class='kana' style='font-size:23px;'>{{get_romaji[0]}}</span>
					</template>
				</div>
				<div>
					<img :src = '`img/${player_img}`' height='100'>
					<img :src = '`img/${enemy_img}`' height='100'>
				</div>
				<div style='height:50px;padding-top:5px;'>
					<span>のこりじかん：</span><input v-model='timer_viewer' style='width:70px;height:35px;font-size:20px;text-align:center;' type='number'>
					　
					<span style=''>とくてん：<span style='font-size:20px;color:blue'>{{score}}</span></span>
				</div>
			</div>
			<div style='min-height:50px;border: solid;border-width: thin;padding:5px 200px;'>
				<div style='width:100%;margin:0;text-align:center;padding-top:10px;'>タイピング：</div>
				<div style='display:flex; justify-content:center;'>
					<div class='romaji' style='width:70px;margin:0;font-size:20px;text-align:center;'>{{typing}}</div> 
					<div style='width:50px;margin:0;text-align:center;color:blue;padding-top:10px;'> >>> </div>
					<div style='width:70px;margin:0;font-size:20px;text-align:center;'>{{typingJP}}</div>
				</div>
			</div>
			<div style='text-align:center;border:solid;border-width:thin;padding:5px 20px;min-height:50px;font-size:20px;background-color:#fff;'>{{answer}}</div>
			<div style='text-align:center;padding:15px 0px'>
				<button @click='start_btn()' class='btn btn-primary' style='width:150px;height:40px;font-size:20px;'>{{btn_name}}</button>
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
				const hit=ref(false)
				const miss=ref(false)
				
				let chk_flg=false			//文字チェック、もしくはローマ字変換失敗時にtrueとなり、次回キータイプ時にタイピング内容をクリアする処理が走る
				let finish_flg=true	//カウントダウンタイマーが０になったらtrueとなり、keydownイベントをスキップする。スタートボタンが押されるとfalseとなる
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
					{'eng':'TWU', 'jp':'とぅ'},
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
					{'eng':'lya', 'jp':'ゃ'},
					{'eng':'lyu', 'jp':'ゅ'},
					{'eng':'lyo', 'jp':'ょ'},
					{'eng':'-', 'jp':'ー'}
				])

				//タイピング判定関連
				const score = ref(0)
				const hitmiss_cleare=()=>{
					miss.value=false
					hit.value=false
				}
				const onKeyPress = (e) =>{
					console.log('onKeyPress start')
					if(finish_flg===true){
						console.log('onKeyPress スタートボタンで有効になります')
						return 0
					}
					console.log(e)
					if(chk_flg){
						typing.value=''
						typingJP.value=''
						chk_flg=false
					}
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

					typing.value = typing.value + e.key
					//アルファベット判定
					if(check_answer(e.key)){
						typingJP.value = e.key
						answer.value = answer.value + e.key
						score.value++
						chk_flg=true
					    //次の問題
					    if(mondai.value===''){
					    	hit.value=true
					    	if(get_next_task()==='finish'){
    
					    	}
					    }
						return 0
					}

					//ひらがな判定
					let jp = get_moji(typing.value)
					console.log(jp)
					if(jp[0]===true){
						typingJP.value = jp[1]
						if(check_answer(jp[1])){
							answer.value = answer.value + jp[1]
							chk_flg=true
							score.value++
							get_ramd_index()
						}else{
							miss.value=true
							score.value--
							chk_flg=true
						}
						setTimeout(hitmiss_cleare, 200);
					}
					//アルファベット4文字以上はミス
					if(typing.value.length>=4){
						miss.value=true
						score.value--
						chk_flg=true
						setTimeout(hitmiss_cleare, 200);
					}
					//次の問題
					if(mondai.value===''){
						hit.value=true
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
						if(loc_key.substr(0,1)===loc_key.substr(1,1) && loc_key.toUpperCase().substr(0,1) !== 'N'){
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
				const check_answer = (moji) =>{//タイピング内容の判定
						let mojisu = moji.toString().length
						let mondai_char = zenk2hank(mondai.value.substr(0,mojisu).toUpperCase())
						if(mondai_char === moji.toUpperCase()){
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
				const zenk2hank =(str)=>{//全角数字の半角変換
				    return str.replace(/[０-９]/g,(s)=>{
				        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0)
				    })
				}
				const get_romaji = computed(()=>{
					let moji = ([])
					let start = 0
					if(mondai.value.substr(0,1)==="っ"){
						start = 1
					}
					//きゃ等の2文字をチェック
					moji = henkanhyou.value.filter((record) => {
						return (record.jp === mondai.value.substr(start,2))
					})

					if(moji.length===0){//きゃ等の2文字がなかったら一文字をチェック
						moji = henkanhyou.value.filter((record) => {
							return (record.jp === mondai.value.substr(start,1))
						})
					}
					if(moji.length===0){
						return [mondai.value.substr(0,1),mondai.value.substr(0,1)]	//問題自体がアルファベット
					}else{
						if(start===0){
							return [moji[0].jp,moji[0].eng]
						}else{
							return ["っ"+moji[0].jp,moji[0].eng.substr(0,1) + moji[0].eng]
						}
						
					}
				})

				//格闘
				const player_actions = ['p_kick01.png','p_kick02.png','p_kick03.png','p_panchi.png']	//idx[0:静止,1:ダメージ　,2～攻撃]
				const player_img=ref('p_kamae.png')
				const enemy_actions = ['']	//idx[0:静止,1:ダメージ　,2:攻撃]
				const enemy_img=ref('teki_kamae.png')
				const get_ramd_index = async (m_index)=>{
					console.log('get_ramd_index start')
					let img = player_actions[Math.floor( Math.random() * 4 )]
					/*
					if(player_img.value===img){
						player_img.value = '001.png'
						await sleep(50)
					}
					*/
					player_img.value = img
					enemy_img.value = 'teki_damaig01.png'
					await sleep(200)
					enemy_img.value = 'teki_kamae.png'
					//player_img.value = 'p_kamae.png'
				}
				function sleep(msec) {
   				return new Promise(function(resolve) {
			      setTimeout(function() {resolve()}, msec);
		   		})
				}

				//出題機能
				const mondai_disp=ref('')   //漢字読み
				const mondai=ref('')        //ひらがな
				const mondai_roma=ref('')   //ローマ字
				const mondai_list = ref([])		//問題リスト
				const level = ref('1')
				const get_mondai_List = () => {//問題リスト取得ajax
						console.log("get_mondai_List start");
						let lv = level.value
						let params = new URLSearchParams();
						params.append('lv', lv);
						axios
						.post('ajax_get_mondai.php',params)
						.then((response) => (mondai_list.value = [...response.data]))
						.catch((error) => console.log(`get_mondai_List ERROR:${error}`));
				}//問題リスト取得ajax
				const get_next_task=()=>{   //次の問題をrandomで取得
						console.log('get_next_task start')
						answer.value=''
						let index = Math.floor( Math.random() * mondai_list.value.length );
						console.log(mondai_list.value[index])
						mondai_disp.value = mondai_list.value[index].disp
						mondai.value = mondai_list.value[index].reading
						mondai_roma.value = mondai_list.value[index].roma
				}

				//startボタン関連
				const timer_viewer = ref('60')
				const btn_name = ref('スタート')

				let timelimit = 0
				let timerId
				const start_btn = () =>{
					if(btn_name.value==='リセット'){
						clearInterval(timerId)
						timer_viewer.value=timelimit/1000
						finish_flg = true
						btn_name.value='スタート'
						return
					}
					get_next_task()
					let miri_sec
					
					if(timer_viewer.value===0){
						miri_sec = timelimit
					}else{
						timelimit = timer_viewer.value * 1000
						miri_sec = timer_viewer.value * 1000
					}
					
					score.value=0
					finish_flg=false
					let now = new Date()
					let target_time = new Date(now.getTime() + miri_sec)	//60秒後
					btn_name.value = 'リセット'
					timerId = setInterval(()=>{
						if(timer(target_time.getTime())){
							clearInterval(timerId)
							finish_flg = true
							timer_viewer.value=0
							btn_name.value='スタート'
						}
					},100,)
				}
				const timer=(tt)=>{//タイマー
					let now = new Date()
					let countdowm = (tt - now.getTime())
					timer_viewer.value = (countdowm / 1000).toFixed(1)
					if(timer_viewer.value<=0){
						return true
					}
				}
				
				
				onMounted(()=>{
					document.addEventListener('keydown', onKeyPress)
					get_mondai_List()
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
					mondai_list,
					mondai_roma,
					start_btn,
					timer_viewer,
					level,
					get_mondai_List,
					score,
					get_romaji,
					btn_name,
					player_img,
					enemy_img,
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
