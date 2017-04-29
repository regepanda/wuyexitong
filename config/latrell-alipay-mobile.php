<?php
return [

	// 安全检验码，以数字和字母组成的32位字符。
	'key' => 'gyvw9x57m911gnw76oz1f7d85txnkrqh',

	// 签名方式
	'sign_type' => 'RSA',

	// 商户私钥。
	'private_key_path' => __DIR__ . '/../storage/app/ali_rsa/alipay_my_private_key.pem',

	// 阿里公钥。
	'public_key_path' => __DIR__ . '/../storage/app/ali_rsa/alipay_my_public_key.pem',

	// 异步通知连接。
	'notify_url' => 'http://wuyexitong.jtcool.com/openApi_AliPay_alreadyPay'
];
