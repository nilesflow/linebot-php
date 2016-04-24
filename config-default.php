<?php

$CONFIG = array();

/**
 * サーバ情報設定
 */
$CONFIG['SERVER'] = array(
	'URL' 			=> '',					// input your info.
	'PATH_RESOUCES' => 'path/to/iot/res/',	// change your info.
);

/**
 * ログ設定
 */
$CONFIG['LOGGER'] = array(
	'VALID' 	=> true, // true:on, false:off
	'DIR' 		=> '/tmp/linebotapi/logs',			// change to your info.
	'FILENAME' 	=> 'app.log',
);

/**
 * LINE BOT API アカウント設定
 */
$CONFIG['LINEBOT_API'] = array(
	'CHANNEL_ID' 		=> '',		// input your info.
	'CHANNEL_SECRET' 	=> '',		// input your info.
	'MID' 				=> '',		// input your info.
);

/**
 * LINE BOT用コンフィグ
 */
$CONFIG['LINEBOT_CONFIG'] = array(
	// データ保存先
	'DIR_DATA' => '/tmp/linebotapi/data',	// change to your info.
);

$pathRes = $CONFIG['SERVER']['URL'].$CONFIG['SERVER']['PATH_RESOUCES'];

/**
 * LINE BOT用コンテンツ
 */
$CONFIG['LINEBOT_DATA'] = array(
	// @see https://developers.line.me/bot-api/api-reference#sending_message_image
	'image' => array(
		array(
			'originalContentUrl'	=> $pathRes."/image/sample/original.jpg",	// change to your info.
			'previewImageUrl'		=> $pathRes."/image/sample/preview.jpg",	// change to your info.
		)
	),
	// @see https://developers.line.me/bot-api/api-reference#sending_message_video
	'video' => array(
		array(
			'originalContentUrl'	=> $pathRes."/video/sample/sample.mp4",		// change to your info.
			'previewImageUrl'		=> $pathRes."/video/sample/preview.jpg",	// change to your info.
		)
	),
	// @see https://developers.line.me/bot-api/api-reference#sending_message_audio
	'audio' => array(
		array(
			'originalContentUrl' => $pathRes."/audio/sample.mp3",	// change to your info.
			'AUDLEN' => "41000",
		)
	),
	// @see https://developers.line.me/bot-api/api-reference#sending_rich_content_message
	'rich' => array(
		array(
			'DOWNLOAD_URL'	=> $pathRes."/image/rich/sample",		// change to your info.
			"ALT_TEXT"		=>  "Alt Text.",
			// 送信前にjson_encode
			"MARKUP_JSON"	=>  array(
				'canvas' => array(
					'width' => 1040,
					'height' => 1040,
					'initialScene' => 'scene1',
				),
				'images' => array(
					'image1' => array(
						'x' => 0,
						'y' => 0,
						'w' => 1040,
						'h' => 1040,
					)
				),
				'actions' => array(
					'openHomepage' => array(
						'type' => 'web',
						'text' => 'Open our homepage.',
						'params' => array(
							'linkUri' => 'http://line.me/ja/'
						)
					),
					'showItem' => array(
						'type' => 'web',
						'text' => 'Show item.',
						'params' => array(
							'linkUri' => 'http://linecorp.com/ja/'
						)
					)
				),
				'scenes' => array(
					'scene1' => array(
						'draws' => array(
							array(
								'image' => 'image1',
								'x' => 0,
								'y' => 0,
								'w' => 1040,
								'h' => 1040
							)
						),
						'listeners' => array(
							array(
								'type' => 'touch',
								'params' => [0, 0, 1040, 350],
								'action' => 'openHomepage'
							),
							array(
								'type' => 'touch',
								'params' => [0, 350, 1040, 350],
								'action' => 'showItem'
							)
						)
					)
				)
			)
		)	
	),
	// @see https://developers.line.me/bot-api/api-reference#sending_message_location
	'location' => array(
		array(
			'text' => '札幌駅', 
			"title" => "札幌駅",
			"latitude" => 43.0686606,
			"longitude" => 141.3507552,
		),
		array(
			"text" => "さっぽろテレビ塔",
			"title" => "さっぽろテレビ塔",
			"latitude" => 43.0635475,
			"longitude" => 141.343087,
		),
	),
	// @see https://developers.line.me/bot-api/api-reference#sending_message_sticker
	'sticker' => array(
		array(
			"STKID" => "100",
			"STKPKGID" => "1",
			"STKVER" => "100"
		),
		array(
			"STKID" => "101",
			"STKPKGID" => "1",
			"STKVER" => "100"
		)
	),
);

/**
 * IoT LINE BOT用コンテンツ
 */
$CONFIG['IOTLINEBOT_DATA'] = array(
		// @see https://developers.line.me/bot-api/api-reference#sending_message_image
		'image' => array(
				array(
						'originalContentUrl'	=> $pathRes."/image/iot/1/original.jpg",	// change to your info.
						'previewImageUrl'		=> $pathRes."/image/iot/1/preview.jpg",		// change to your info.
				),
				array(
						'originalContentUrl'	=> $pathRes."/image/iot/2/original.jpg",	// change to your info.
						'previewImageUrl'		=> $pathRes."/image/iot/2/preview.jpg",		// change to your info.
				),
		),
);
