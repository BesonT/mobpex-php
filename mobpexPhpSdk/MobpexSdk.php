<?php  
/** 
 * Filename: MobpexSDK.php 
 * Created:  2016-04-05 
 * Author:   yang.wang-2 
 */  
define('SignRet'     , 'true'); 
define('Format',     'json');  
define('Locale', 'zh_CN');  

function sign($params, $secretKey) {  
    $items = array();  
    foreach($params as $key => $value) $items[$key] = $value;  
    ksort($items);  
    $s = $secretKey;  
    foreach($items as $key => $value) {  
        $s .= "$key$value";  
    }  
    $s .= $secretKey; 
    return strtolower(md5($s));  
}
 
function decode_parameters($mobpex_parameters) {  
    $params = array();  
    $param_array = explode('&', base64decode($mobpex_parameters));  
    foreach($param_array as $p) {  
        list($key, $value) = explode('=', $p);  
        $params[$key] = $value;  
    }  
    return $params;  
}
function getMillisecond() {
	list($t1, $t2) = explode(' ', microtime());
	return $t2  .  ceil( ($t1 * 1000) );
}
function transfer($value){
	$sResult = "";
	if(is_float($value)){
		$sResult .= sprintf("%.2f", $value) ;
	}else if(is_integer($value) ){
		$sResult .= $value ;
	}else if(is_bool($value)){
		if($value){
			$sResult .= 'true' ;
		}else{
			$sResult .= 'false' ;
		}
	}else {
		$sResult .= '"' .$value . '"';
	}
	return $sResult;
}
function array2json($params){
	$sResult = '{';
	foreach($params as $key => $value){
		$sResult .= '"'.$key.'":' .transfer($value). ',';
	}
	$sResult = substr($sResult, 0, -1) . '}';
	return $sResult;
}  
class  MobpexClient{  
	private $gateway;
    private $methodOrUri;
    private $ignoreSSLCheck;
    private $appId;
    private $secretKey;
    private $conn_time_out = 30;
    private $execute_time_out = 300;  
    private $api_params = array();  
    function MobpexClient($gateway, $methodOrUri, $ignoreSSLCheck, $appId, $secretKey, $conn_time_out, $execute_time_out) {  
        $this->methodOrUri = $methodOrUri;
        $this->gateway = $gateway;
        $this->ignoreSSLCheck = $ignoreSSLCheck; 
        $this->appId = $appId;
        $this->secretKey = $secretKey;
        if(isset($conn_time_out) && is_numeric($conn_time_out)){
        		$this->conn_time_out = $conn_time_out;
        }
        	if(isset($execute_time_out) && is_numeric($execute_time_out)){
        		$this->execute_time_out = $execute_time_out;
        	}
    }  
    function set_param($param_name, $param_vaule) {  
        $this->api_params[$param_name] = $param_vaule;  
    }  
    function get_api_params() {  
        return $this->api_params;  
    }  
    function get_method_or_uri() {  
        return $this->methodOrUri;  
    }
    function get_version() {
    	$mn_split = explode('/' , $this->methodOrUri);
    	return substr($mn_split[2],1);
    }
    function get_gateway(){
    	return $this->gateway;
    }
    function get_ignoreSSLCheck(){
    	return $this->ignoreSSLCheck;
    }
    function get_appId(){
    	return $this->appId;
    }  
    function get_secretKey(){
    	return $this->secretKey;
    }
    function get_conn_time_out(){
    	return $this->conn_time_out;
    }
    function get_execut_time_out(){
    	return $this->execute_time_out;
    }
    function execute($session = '') {  
        $req = new MobpexRequest;  
        return $req->execute($this, $session);  
    }
    
    function validSign($obj) {
		$items = array();  
    	foreach($obj as $key => $value) $items[$key] = $value;

		$s = $this->secretKey;
		$s .= $items['state'];
		if(array_key_exists('result', $items)){
			$s .= json_encode($items['result'],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
		}
		$s .= $items['ts'];
		if(array_key_exists('ext',$items)){
			$s .=json_encode($items['ext'],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
		}
		$s .= $this->secretKey;
		$s = str_replace(" " , '' , $s);
		$s = str_replace("\t" , '' , $s);
		$s = str_replace("\n" , '' , $s);
		$returnSign = strtolower(md5($s));
		return $returnSign == $items['sign'];
	}   
}  
class MobpexRequest {  
    function execute($client, $session = '') {  
        $sys_params = array(  
                'signRet' => SignRet,  
                'locale'  => Locale,  
                'format'  => Format  
        );  
        $api_params = $client->get_api_params();  
        $method_name = $client->get_method_or_uri(); 
        $sys_params['ts'] = getMillisecond();
        $sys_params['method'] = $method_name;  
        $sys_params['v'] = $client->get_version();
        $sys_params['appId'] = $client->get_appId();
        if($session != '') {  
             $sys_params['session'] = $session;  
        }  
		$merge_array = array_merge($sys_params, $api_params);
        $merge_array['sign'] = sign($merge_array, $client->get_secretKey());   
        $param_string = '';   
        foreach($merge_array as $p => $v) {  
            $param_string .= "$p=" . urlencode($v) . "&";  
        }
        
        $url = $client->get_gateway() . $method_name . '?' . substr($param_string, 0, -1);
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_POST, true);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $client->get_ignoreSSLCheck()); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $client->get_ignoreSSLCheck()); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $client->get_conn_time_out()); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $client->get_execut_time_out());  
        $postResult = curl_exec($ch);  
        if (curl_errno($ch)){  
            throw new Exception(curl_error($ch), 0);  
        } else {  
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
            if (200 !== $httpStatusCode) {  
                throw new Exception($postResult, $httpStatusCode);  
            }  
        }  
        curl_close($ch);
        $obj = json_decode($postResult); 
        return $obj;  
    }
}  
?>  