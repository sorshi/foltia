static STATION bsSta[] = {
	{ "NHK BS1", "3001.ontvjapan.com", 16625, 4, 101},
	{ "NHK BS2", "3002.ontvjapan.com", 16625, 4, 102},//廃止 2011/3/31 
	{ "NHK BSプレミアム", "3003.ontvjapan.com", 16626, 4, 103},
	{ "BS日テレ", "3004.ontvjapan.com", 16592, 4, 141},
	{ "BS朝日", "3005.ontvjapan.com", 16400, 4, 151},
	{ "BS-TBS", "3006.ontvjapan.com", 16401, 4, 161},
	{ "BSジャパン", "3007.ontvjapan.com", 16433, 4, 171},
	{ "BSフジ", "3008.ontvjapan.com", 16593, 4, 181},
//	{ "WOWOW", "3009.ontvjapan.com", 16432, 4, 191},
//	{ "WOWOW2", "3010.ontvjapan.com", 16432, 4, 192},
//	{ "WOWOW3", "3011.ontvjapan.com", 16432, 4, 193},
	{ "WOWOWプライム", "3009.ontvjapan.com", 16432, 4, 191},
	{ "WOWOWライブ", "4192.epgdata.ontvjapan", 17488, 4, 192},
	{ "WOWOWシネマ", "4193.epgdata.ontvjapan", 17489, 4, 193},
//	{ "スター・チャンネル", "3012.ontvjapan.com", 16529, 4, 200},
	{ "スター・チャンネル1", "3012.ontvjapan.com", 16529, 4, 200},
	{ "スター・チャンネル2", "4201.epgdata.ontvjapan", 17520, 4, 201},
	{ "スター・チャンネル3", "4202.epgdata.ontvjapan", 17520, 4, 202},
	{ "BS11", "3013.ontvjapan.com", 16528, 4, 211},
	{ "TwellV", "3014.ontvjapan.com", 16530, 4, 222},
	{ "放送大学1", "4231.epgdata.ontvjapan", 18098, 4, 231},
	{ "放送大学2", "4232.epgdata.ontvjapan", 18098, 4, 232},
	{ "放送大学3", "4233.epgdata.ontvjapan", 18098, 4, 233},
	{ "グリーンチャンネル", "4234.epgdata.ontvjapan", 18224, 4, 234},
	{ "BSアニマックス", "1047.ontvjapan.com", 18033, 4, 236},
	{ "FOX bs238", "4238.epgdata.ontvjapan", 18096, 4, 238},
	{ "BSスカパー！", "4241.epgdata.ontvjapan", 18097, 4, 241},
	{ "J SPORTS 1", "4242.epgdata.ontvjapan", 18225, 4, 242},
	{ "J SPORTS 2", "4243.epgdata.ontvjapan", 18226, 4, 243},
	{ "J SPORTS 3", "4244.epgdata.ontvjapan", 18257, 4, 244},
	{ "J SPORTS 4", "4245.epgdata.ontvjapan", 18258, 4, 245},
	{ "BS釣りビジョン", "4251.epgdata.ontvjapan", 18288, 4, 251},
	{ "IMAGICA BS", "4252.epgdata.ontvjapan", 18256, 4, 252},
	{ "日本映画専門チャンネル", "4255.epgdata.ontvjapan", 18289, 4, 255},
	{ "ディズニー・チャンネル", "1090.ontvjapan.com", 18034, 4, 256},
	{ "D-Life", "4258.epgdata.ontvjapan", 18290, 4, 258},
	{ "NHK総合テレビジョン（東京）", "4291.epgdata.ontvjapan", 17168, 4, 291},
	{ "NHK教育テレビジョン（東京）", "4292.epgdata.ontvjapan", 17168, 4, 292},
	{ "日本テレビ", "4294.epgdata.ontvjapan", 17169, 4, 294},
	{ "テレビ朝日", "4295.epgdata.ontvjapan", 17169, 4, 295},
	{ "TBSテレビ", "4296.epgdata.ontvjapan", 17169, 4, 296},
	{ "テレビ東京", "4297.epgdata.ontvjapan", 17169, 4, 297},
	{ "フジテレビ", "4298.epgdata.ontvjapan", 17168, 4, 298},
	{ "放送大学ラジオ", "4531.epgdata.ontvjapan", 18098, 4, 531},
	{ "WNI", "4910.ontvjapan.com", 16626, 4, 910},
};

static int bsStaCount = sizeof(bsSta) / sizeof (STATION);



static STATION csSta[] = {
//ND2
	{ "ＴＢＳチャンネル１", "3201.ontvjapan.com", 24608, 6, 296},//2012年7月1日 CS1/Ch.296に変更
	{ "テレ朝チャンネルＨＤ", "1092.ontvjapan.com", 24608, 6, 298},//2012年7月1日 CS1/Ch.298に変更
	{ "朝日ニュースターＨＤ", "1067.ontvjapan.com", 24608, 6, 299},//2012年7月1日 CS1/Ch.299に変更

//ND4
	{ "スカパー！プロモ", "100ch.epgdata.ontvjapan", 28736, 7, 100},
	{ "チャンネルＮＥＣＯ", "1008.ontvjapan.com", 28736, 7, 223},
	{ "ザ・シネマ", "1217.ontvjapan.com", 28736, 7, 227},//228→227
	{ "ｓｋｙ・Ａスポーツ＋", "1040.ontvjapan.com", 28736, 7, 250},//2012年1月24日 Ch.255からCh.250に変更
	{ "ヒストリーチャンネル", "1050.ontvjapan.com", 28736, 7, 342},
	{ "囲碁・将棋チャンネル", "363ch.epgdata.ontvjapan", 28736, 7, 363},
		
//ND6
	{ "ホームドラマＣＨ", "294ch.epgdata.ontvjapan", 28768, 7, 294}, 
	{ "ＭＴＶ　ＨＤ", "1019.ontvjapan.com", 28768, 7, 323},
	{ "歌謡ポップス", "329ch.epgdata.ontvjapan", 28768, 7, 329},
	{ "ディスカバリー", "1062.ontvjapan.com", 28768, 7, 340},
	{ "アニマルプラネット", "1193.ontvjapan.com", 28768, 7, 341},
	{ "ＣＮＮｊ", "1069.ontvjapan.com", 28768, 7, 354},
//ND8
	{ "ショップチャンネル", "1059.ontvjapan.com", 24704, 6, 55},
	{ "東映チャンネル", "1010.ontvjapan.com", 24704, 6, 218},//2012/8/1より
	{ "衛星劇場", "1005.ontvjapan.com", 24704, 6, 219},//2012/8/1より
	{ "ミュージック・エア", "1024.ontvjapan.com", 24704, 6, 326},//2012/8/1より
	{ "ディズニージュニア", "339ch.epgdata.ontvjapan", 24704, 6, 339},
	{ "日テレＮＥＷＳ２４", "2002.ontvjapan.com", 24704, 6, 349},
//ND10
	{ "スカチャン０", "800ch.epgdata.ontvjapan", 24736, 6, 800},
	{ "スカチャン１", "801ch.epgdata.ontvjapan", 24736, 6, 801},
	{ "スカチャン２", "802ch.epgdata.ontvjapan", 24736, 6, 802},
	{ "スカチャン３", "805ch.epgdata.ontvjapan", 24736, 6, 805},
//ND12
	{ "ＧＡＯＲＡ", "1026.ontvjapan.com", 28864, 7, 254},
	{ "エムオン！ＨＤ", "1022.ontvjapan.com", 28864, 7, 325},
	{ "キッズステーション", "1045.ontvjapan.com", 28864, 7, 330},//HDに 2012/07/26ふたたび330に移動//ND14
//ND14
	{ "時代劇専門ｃｈＨＤ", "1133.ontvjapan.com", 28896, 7, 292},
	{ "ファミリー劇場ＨＤ", "1015.ontvjapan.com", 28896, 7, 293},
	{ "スーパー！ドラマＨＤ", "1006.ontvjapan.com", 28896, 7, 310},
//ND16
	{ "ＳＫＹ　ＳＴＡＧＥ", "1207.ontvjapan.com", 28928, 7, 290},
	{ "チャンネル銀河", "305ch.epgdata.ontvjapan", 28928, 7, 305},
	{ "ＡＸＮ", "1014.ontvjapan.com", 28928, 7, 311},
	{ "ＡＴ−Ｘ", "1201.ontvjapan.com", 28928, 7, 333},
	{ "ナショジオチャンネル", "1204.ontvjapan.com", 28928, 7, 343},
	{ "ＢＢＣワールド", "1070.ontvjapan.com", 28928, 7, 353},
//ND18
	{ "ムービープラスＨＤ", "1007.ontvjapan.com", 28960, 7, 240},
	{ "ゴルフネットＨＤ", "1027.ontvjapan.com", 28960, 7, 262},
	{ "女性ｃｈ／ＬａＬａ", "1074.ontvjapan.com", 28960, 7, 314},
//ND20
	{ "フジテレビＯＮＥ", "1073.ontvjapan.com", 28992, 7, 307},//フジテレビ739→
	{ "フジテレビＴＷＯ", "1072.ontvjapan.com", 28992, 7, 308},//フジテレビ721→
	{ "フジテレビＮＥＸＴ", "309ch.epgdata.ontvjapan", 28992, 6, 309},//306→309にチャンネル変更
//ND22
	{ "ＱＶＣ", "1120.ontvjapan.com", 29024, 7, 161},
	{ "ＴＢＳチャンネル２", "297ch.epgdata.ontvjapan", 29024, 7, 297},
	{ "ＦＯＸ", "1016.ontvjapan.com", 29024, 7, 312},
	{ "スペースシャワーＴＶ", "1018.ontvjapan.com", 29024, 7, 322},
	{ "カートゥーン", "1046.ontvjapan.com", 29024, 7, 331},
	{ "ＴＢＳニュースバード", "1076.ontvjapan.com", 29024, 7, 351},
//ND24
	{ "ＦＯＸムービー", "229ch.epgdata.ontvjapan", 29056, 7, 229},
	{ "日テレＧ＋　ＨＤ", "1068.ontvjapan.com", 29056, 7, 257},//HD化
	{ "日テレプラス", "300ch.epgdata.ontvjapan", 29056, 7, 300},
	{ "スペシャプラス", "321ch.epgdata.ontvjapan", 29056, 7, 321},
	{ "旅チャンネル", "1052.ontvjapan.com", 29056, 7, 362},
};

static int csStaCount = sizeof(csSta) / sizeof (STATION);
