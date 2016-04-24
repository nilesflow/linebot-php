<?php
require_once(__DIR__ .'/Base.php');

/**
 * LINE BOT APIクラス
 * 
 * @see https://developers.line.me/bot-api/api-reference
 */
class LineBotApi extends Base {
	// APIエンドポイント
	protected $endpoint = "https://trialbot-api.line.me/v1";

	// 接続情報
	protected $channel_id = null;
	protected $channel_secret = null;
	protected $mid = null;

	/*
	 *  定数
	 */
	// ContentType values
	// @see https://developers.line.me/bot-api/api-reference#receiving_messages_contenttype
	const CONTENTTYPE_TEXT		= 1;
	const CONTENTTYPE_IMAGE		= 2;
	const CONTENTTYPE_VIDEO		= 3;
	const CONTENTTYPE_AUDIO		= 4;
	const CONTENTTYPE_LOCATION 	= 7;
	const CONTENTTYPE_STICKER	= 8;
	const CONTENTTYPE_CONTACT	= 10;
	const CONTENTTYPE_RICH		= 12;
	
	// チャンネル
	const CHANNEL_DEFAULT = '1383378250';

	// イベントタイプ 受信
	const EVENTTYPE_RECV_SINGLE		= '138311609000106303';
	const EVENTTYPE_RECV_OPERATION	= '138311609100106403';

	// イベントタイプ 送信
	const EVENTTYPE_SEND_SINGLE		= '138311608800106203';
	const EVENTTYPE_SEND_MULTIPLE	= '140177271400161403';
	
	// 宛先種別 
	const TOTYPE_USER	= 1;
	
	// opType values
	// @see https://developers.line.me/bot-api/api-reference#receiving_operations_optype
	const OPTYPE_ADDED		= 4;
	const OPTYPE_BLOCKED	= 8;
	
	/**
	 * コンストラクタ
	 * 
	 * @param array $config
	 */
	public function __construct($config) {
		$this->channel_id = $config['CHANNEL_ID'];
		$this->channel_secret = $config['CHANNEL_SECRET'];
		$this->mid = $config['MID'];
	}

	/***************************************
	 * API
	 ***************************************/
	/**
	 * Receiving messages|operations
	 * 
	 * @api
	 * @param json $strJson 受信メッセージ
	 * @return bool|array false or 取得結果
	 * @see https://developers.line.me/bot-api/api-reference#receiving_messages
	 * @see https://developers.line.me/bot-api/api-reference#receiving_operations
	 */
	public function receiving($json) {
		$arr = json_decode($json, true);
		if (is_null($arr)) {
			$this->printLog('json_decode failed.');
			return false;
		}
		if (! isset($arr['result'])) {
			$this->printLog('result none. ');
			return false;
		}
		$results = $arr['result'];

		return $results;
	}

	/**
	 * Makeing Content(Text)
	 * 
	 * @api
	 * @param array $params
	 * @return array
	 * @see https://developers.line.me/bot-api/api-reference#sending_message_text
	 */
	public function makeTextContent($params) {
		$content = array(
			'contentType'	=> self::CONTENTTYPE_TEXT,
			'toType'		=> self::TOTYPE_USER,
			'text'			=> $params['text']
		);
		return $content;
	}

	/**
	 * Makeing Content(Image)
	 * 
	 * @api
	 * @param array $params
	 * @return array
	 * @see https://developers.line.me/bot-api/api-reference#sending_message_image
	 */
	public function makeImageContent($params) {
		$content = array(
			'contentType'			=> self::CONTENTTYPE_IMAGE,
			'toType'				=> self::TOTYPE_USER,
			'originalContentUrl'	=> $params['originalContentUrl'],
			'previewImageUrl'		=> $params['previewImageUrl'],
				);
		return $content;
	}

	/**
	 * Makeing Content(Video)
	 * 
	 * @api
	 * @param array $params
	 * @return array
	 * @see https://developers.line.me/bot-api/api-reference#sending_message_video
	 */
	public function makeVideoContent($params) {
		$content = array(
			'contentType'			=> self::CONTENTTYPE_VIDEO,
			'toType'				=> self::TOTYPE_USER,
			'originalContentUrl'	=> $params['originalContentUrl'],
			'previewImageUrl'		=> $params['previewImageUrl'],
		);
		return $content;
	}

	/**
	 * Makeing Content(Audio)
	 * 
	 * @api
	 * @param string $urlContent
	 * @param array $params
	 * @return array
	 * @see https://developers.line.me/bot-api/api-reference#sending_message_video
	 */
	public function makeAudioContent($params) {
		$content = array(
			'contentType'			=> self::CONTENTTYPE_AUDIO,
			'toType'				=> self::TOTYPE_USER,
			'originalContentUrl'	=> $params['originalContentUrl'],
			'contentMetadata'		=> array(
				'AUDLEN'	=> $params['AUDLEN'],
			),
		);
		return $content;
	}

	/**
	 * Makeing Content(Location)
	 * 
	 * @api
	 * @param string $text
	 * @param array $params text, title, latitude, longitude
	 * @return array
	 * @see https://developers.line.me/bot-api/api-reference#sending_message_location
	 */
	public function makeLocationContent($params) {
		$content = array(
			'contentType'	=> self::CONTENTTYPE_LOCATION,
			'toType'		=> self::TOTYPE_USER,
			'text'			=> $params['text'],
			'location'		=> array(
				'title' 		=> $params['title'],
				'latitude' 		=> $params['latitude'],
				'longitude' 	=> $params['longitude'],
			),
		);
		return $content;
	}

	/**
	 * Makeing Content(Sticker)
	 * 
	 * @api
	 * @param array $params STKID, STKPKGID, STKVER
	 * @return array
	 * @see https://developers.line.me/bot-api/api-reference#sending_message_location
	 */
	public function makeStickerContent($params) {
		$content = array(
			'contentType'		=> self::CONTENTTYPE_STICKER,
			'toType'			=> self::TOTYPE_USER,
			'contentMetadata'	=> array(
				'STKID' 	=> $params['STKID'],
				'STKPKGID' 	=> $params['STKPKGID'],
				'STKVER' 	=> $params['STKVER'],
			),
		);
		return $content;
	}

	/**
	 * Makeing Content(Rich)
	 * 
	 * @api
	 * @param array $contentMetadata DOWNLOAD_URL, ALT_TEXT, MARKUP_JSON
	 * @return array
	 * @see https://developers.line.me/bot-api/api-reference#sending_message_location
	 */
	public function makeRichContent($contentMetadata) {
		$content = array(
			'contentType'		=> self::CONTENTTYPE_RICH,
			'toType'			=> self::TOTYPE_USER,
			"contentMetadata"	=>  array(
				"DOWNLOAD_URL"		=>  $contentMetadata['DOWNLOAD_URL'],
				"SPEC_REV" 			=>  "1",
				"ALT_TEXT"			=>  $contentMetadata['ALT_TEXT'],
				"MARKUP_JSON"		=>  json_encode($contentMetadata['MARKUP_JSON']),
			)
		);
		return $content;
	}
	
	/**
	 * Sending messages
	 * 
	 * @param string $to
	 * @param array $content
	 * @return bool|array false or 取得結果
	 * @see https://developers.line.me/bot-api/api-reference#sending_message
	 */
	public function sendMessage($to, $content) {
		// メッセージ返信
		$properties = array(
			'to' => $to,
			'eventType' => self::EVENTTYPE_SEND_SINGLE,
			'content' => $content,
		);
		return $this->_sendMessages($properties);
	}

	/**
	 * Sending multiple messages
	 * 
	 * @param string $to
	 * @param array $messages
	 * @return bool|array false or 取得結果
	 * @see https://developers.line.me/bot-api/api-reference#sending_multiple_messages
	 */
	public function sendMultipleMessages($to, $messages) {
		// メッセージ返信
		$properties = array(
			'to' => $to,
			'eventType' => self::EVENTTYPE_SEND_MULTIPLE,
			'content' => array(
				"messageNotified" => 0, // optional
				"messages" => $messages
			),
		);
		return $this->_sendMessages($properties);
	}

	/**
	 * Getting message content
	 * 
	 * @api
	 * @param string $messageId
	 * @return bool|binary false or 取得結果
	 * @see https://developers.line.me/bot-api/api-reference#getting_message_content
	 */
	public function getMessageContent($messageId) {
		$params = array();
		$params['url'] = $this->endpoint."/bot/message/{$messageId}/content";

		// 取得
		$output = $this->_requestRaw($params);
		if (!$output) {
			return false;
		}
		return $output;
	}

	/**
	 * Getting previews of message content
	 * 
	 * @api
	 * @param string $messageId
	 * @return bool|binary false or 取得結果
	 * @see https://developers.line.me/bot-api/api-reference#getting_message_content_preview
	 */
	public function getMessageContentPreviews($messageId) {
		$params = array();
		$params['url'] = $this->endpoint."/bot/message/{$messageId}/content/preview";
		
		// 取得
		$output = $this->_requestRaw($params);
		if (!$output) {
			return false;
		}
		return $output;
	}

	/**
	 * Getting user profile information
	 * 
	 * @api
	 * @param string $messageId
	 * @return bool|array false or 取得結果
	 * @see https://developers.line.me/bot-api/api-reference#getting_user_profile_information
	 */
	public function getUserProfile($messageId) {
		$params = array();
		$params['url'] = $this->endpoint."/profiles?mids={$messageId}";

		$ret = $this->_request($params);
		return $ret;
	}

	/**
	 * 保存可能なコンテンツタイプかチェック
	 * 
	 * @param integer $contentType
	 * @return bool
	 */
	public function canSavedType($contentType) {
		$ret = false;

		switch ($contentType) {
			case self::CONTENTTYPE_IMAGE:
			case self::CONTENTTYPE_VIDEO:
			case self::CONTENTTYPE_AUDIO:
				$ret = true;
				break;
			default:
				break;
		}
		return $ret;
	}

	/**
	 * Preview取得可能なコンテンツタイプかチェック
	 *
	 * @param integer $contentType
	 * @return bool
	 */
	public function canPreviewedType($contentType) {
		$ret = false;
	
		switch ($contentType) {
			case self::CONTENTTYPE_IMAGE:
			case self::CONTENTTYPE_VIDEO:
				$ret = true;
				break;
			default:
				break;
		}
		return $ret;
	}

	/***************************************
	 * 内部処理
	 ***************************************/
	/**
	 * Sending messages
	 * 
	 * @param array $properties
	 * @return bool|array false or 取得結果
	 * @see https://developers.line.me/bot-api/api-reference#sending_message
	 */
	protected function _sendMessages($properties) {
		$params = array();
		$params['url'] = $this->endpoint."/events";
		$params['headers'] = array(
				"Content-Type: application/json; charset=UTF-8",
		);
		$params['method'] = 'post';
	
		// POSTメッセージ生成
		$contents = $properties['content'];
	
		$message = array(
			'to' => array(
				$properties['to']
			),
			'toChannel' => 1383378250,
			'eventType' => $properties['eventType'],
			'content' => $contents,
		);
		$params['post'] = json_encode($message);
		
		return $this->_request($params);
	}

	/**
	 * LINE BOT APIリクエスト
	 * 
	 * @param array $params
	 * @return bool|array false or jsonデコード値
	 */
	protected function _request($params) {
		$output = $this->_requestRaw($params);
		if (! $output) {
			return false;
		}
		
		$ret = json_decode($output);
		if (is_null($ret)) {
			$this->printLog('json decode failed.');
			$this->printLog($output);
			return false;
		}
		return $ret;
	}

	/**
	 * LINE BOT APIリクエスト
	 * 
	 * @param array $params
	 * ・headers付与
	 * ・POST指定
	 * @return bool|json false or result 
	 */
	protected function _requestRaw($params) {
		$headers = array(
			"X-Line-ChannelID: {$this->channel_id}",
			"X-Line-ChannelSecret: {$this->channel_secret}",
			"X-Line-Trusted-User-With-ACL: {$this->mid}"
		);
		// 追加ヘッダ
		if (isset($params['headers'])) {
			$headers = array_merge($headers, $params['headers']);
			$headers = $headers + $params['headers'];
		}

		// request start..
		$curl = curl_init($params['url']);

		// POST処理
		if (isset($params['method']) && $params['method'] == 'post') {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params['post']);
		}
		
		// request
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($curl);
		if (! $output) {
			$this->printLog('request failed.');
			$this->printLog($headers);
			$this->printLog($params);
			return false;
		}
//		$this->printLog($output);

		return $output;
	}
}

