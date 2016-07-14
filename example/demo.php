<?php
function findChannelInfoByAppId(){
	include_once('MobpexSdk.php');   
	$client = new MobpexClient('https://www.mobpex.com/yop-center',//Mobpex对外提供的地址 , 'http://172.17.102.188:8093/yop-center'
						'/rest/v1.0/query/findChannelInfoByAppId',  //接口对应的uri
						false,                                      //是否需要进行证书验证
						'15122404366710489367',                     //appid15122404163671048936
						'LS_1bfs9nsAFBWqFncutrdt3du3qm2bi0s'      //密钥值  LS_1ba0jd2AFBWkQVJ2bc2ck9baj4q8dav
						,30									      //连接超时时间 单位秒
						,60);   								  //请求执行超时时间 单位秒
	$client->set_param('userId', 'long.chen-1@yeepay.com');            //设置用户id 'test@test.com' 
	$result = $client->execute(); // 对于不需要session的api，则可以不用session参数  
	if($client->validSign($result)){ //返回结果的验签
		var_dump($result);
	}
}  

function unifiedOrder(){
	include_once('MobpexSdk.php'); 
	$client = new MobpexClient('https://www.mobpex.com/yop-center',// Mobpex对外提供的地址  'https://www.mobpex.com/yop-center',       
						'/rest/v1.0/pay/unifiedOrder',  //接口对应的uri
						false,                                      //是否需要进行证书验证
						'15122404366710489367',                     //appid
						'LS_1bfs9nsAFBWqFncutrdt3du3qm2bi0s'      //密钥值 LS_1ba0jd2AFBWkQVJ2bc2ck9baj4q8dav
						,30									      //连接超时时间 单位秒
						,60);   								  //请求执行超时时间 单位秒
	$client->set_param('userId', 'long.chen-1@yeepay.com');//'long.chen-1@yeepay.com'); 
	$prePayRequest = array('productName' => "123456",
					   'productDescription' => "Gcup",
					   'payType' => "APP",
					   'payChannel' => "WECHAT",
					   'payCurrency' => "CNY",
					   'amount' => "200.1",
					   'tradeNo' => "1134376521");
	$client->set_param('prePayRequest',json_encode($prePayRequest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	$result = $client->execute(); // 对于不需要session的api，则可以不用session参数  
	if($client->validSign($result)){ //返回结果的验签
		var_dump($result);
	}
}

function refund(){
	include_once('MobpexSdk.php'); 
	$client = new MobpexClient('https://www.mobpex.com/yop-center',//Mobpex对外提供的地址  'https://www.mobpex.com/yop-center', 
						'/rest/v1.0/pay/refund',  //接口对应的uri
						false,                                      //是否需要进行证书验证
						'15122404366710489367',                     //appid
						'LS_1bfs9nsAFBWqFncutrdt3du3qm2bi0s'      //密钥值 LS_1ba0jd2AFBWkQVJ2bc2ck9baj4q8dav
						,30									      //连接超时时间 单位秒
						,60);   								  //请求执行超时时间 单位秒
	$client->set_param('userId', 'long.chen-1@yeepay.com');//'long.chen-1@yeepay.com'); 
	$prePayRequest = array("tradeNo" => "656113769613",//商户系统的支付请求流水号
    					   "amount" => "3.00",//合计金额，精确到小数点后2位
    					   "description"=> "我要退款~~~",//描述
    					   "refundNo"=> "20485228279"//商户系统的退款请求流水号
    					 );
	$client->set_param('refundRequest',json_encode($prePayRequest, JSON_UNESCAPED_UNICODE| JSON_UNESCAPED_SLASHES));  
	$result = $client->execute(); // 对于不需要session的api，则可以不用session参数  
	
	if($client->validSign($result)){ //返回结果的验签
		var_dump($result);
	}
}

function queryPaymentOrder(){
	include_once('MobpexSdk.php'); 
	$client = new MobpexClient('https://www.mobpex.com/yop-center',//Mobpex对外提供的地址  'https://www.mobpex.com/yop-center',    
						'/rest/v1.0/pay/queryPaymentOrder',  //接口对应的uri
						false,                                      //是否需要进行证书验证
						'15122404366710489367',                     //appid
						'LS_1bfs9nsAFBWqFncutrdt3du3qm2bi0s'      //密钥值
						,30									      //连接超时时间 单位秒
						,60);   								  //请求执行超时时间 单位秒
	$client->set_param('userId', 'long.chen-1@yeepay.com');//'long.chen-1@yeepay.com'); 
    $client->set_param('tradeNo','656113769613'); 
	$result = $client->execute(); // 对于不需要session的api，则可以不用session参数  
	if($client->validSign($result)){ //返回结果的验签
		var_dump($result);
	}
}

function queryRefundOrder(){
	include_once('MobpexSdk.php'); 
	$client = new MobpexClient('https://www.mobpex.com/yop-center',  //Mobpex对外提供的地址  'https://www.mobpex.com/yop-center',   
						'/rest/v1.0/pay/queryRefundOrder',  //接口对应的uri
						false,                                      //是否需要进行证书验证
						'15122404366710489367',                     //appid
						'LS_1bfs9nsAFBWqFncutrdt3du3qm2bi0s'      //密钥值
						,30									      //连接超时时间 单位秒
						,60);   								  //请求执行超时时间 单位秒
	$client->set_param('userId', 'long.chen-1@yeepay.com');//'long.chen-1@yeepay.com'); 
    $client->set_param('tradeNo','656113769613'); 
    $client->set_param('refundNo','20485228279'); 
	$result = $client->execute(); // 对于不需要session的api，则可以不用session参数  
	if($client->validSign($result)){ //返回结果的验签
		var_dump($result);
	}
}
//findChannelInfoByAppId();
//unifiedOrder();
//refund();
//queryRefundOrder();
queryPaymentOrder();

?>
