<?php namespace system\libraries;

use GuzzleHttp\Client;

class httpLib
{
	public $httpStatusMessage = '';
	public $httpStatusCode 	= '';
	public $httpHeader 		= '';
	public $httpBody 		= '';

	function __construct()
	{
		$this->httpStatusMessage = '';
		$this->httpStatusCode = '';
		$this->httpHeader = '';
		$this->httpBody = '';
	}

	# Hit API with guzzle
	public function hitAPI($url='',$param='',$auth=array(),$method='GET')
	{
	    ## Class Client from Guzzle
	    $client = new Client();

	    ## Authentication API
	    $authenticationAPI = '';
	    if (count($auth) > 0)
	    {
		    foreach ($auth as $key => $value)
		    {
		    	if ($authenticationAPI == '')
		    	{
		    		$authenticationAPI .= '?' . $key . '=' . $value;
		    	} else {
		    		$authenticationAPI .= '&' . $key . '=' . $value;
		    	}
		    }
	    }
	    $url = $url.$authenticationAPI;

	    ## Check parameter
	    if ($param != "") {
	        $url = $url . "&" . $param;
	    }

	    $authGuz = array('auth' => array('user', 'pass'));

	    try {
		    ## Get Response
		    $res = $client->request($method, $url, $authGuz);

		    /**
		     * echo $res->getStatusCode(); 200
			 * $res->getHeaderLine('content-type'); 'application/json; charset=utf8'
			 * $res->getBody(); {"type":"User"...'
			*/
		    
		    if ($this->isJson($res->getBody()))
		    {
		        $response = json_decode($res->getBody(), true);
		    } else {
		        $response = $res->getBody();
		    }

			$this->httpStatusMessage = $res->getReasonPhrase();
		    $this->httpHeader 		= $res->getHeaderLine('content-type');
		    $this->httpStatusCode 	= $res->getStatusCode();
		    $this->httpBody 		= $response;
	    }
	    catch (\GuzzleHttp\Exception\ClientException $e) {
	    	$this->httpStatusMessage = $e->getResponse()->getReasonPhrase();
	    	$this->httpHeader 		= $e->getResponse()->getHeaderLine('content-type');
	    	$this->httpStatusCode 	= $e->getResponse()->getStatusCode();
	    	$this->httpBody 		= $e->getResponse()->getBody();
	    }
	    // return array("httpStatusCode" => $res->getStatusCode(), "httpBody" => $res->getBody());
	}

	# Function for hit API
	public function callAPI($url='',$data='',$auth=array(),$method='GET')
	{
	    ## Authentication API
	    $authenticationAPI = '';
	    if (count($auth) > 0)
	    {
		    foreach ($auth as $key => $value)
		    {
		    	if ($authenticationAPI == '')
		    	{
		    		$authenticationAPI .= '?' . $key . '=' . $value;
		    	} else {
		    		$authenticationAPI .= '&' . $key . '=' . $value;
		    	}
		    }
	    }
	    $url = $url.$authenticationAPI;
	    
	    $curl = curl_init();
	    switch ($method)
	    {
	        case "POST":
	            curl_setopt($curl, CURLOPT_POST, 1);
	            if ($data)
	                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	            break;
	        case "PUT":
	            curl_setopt($curl, CURLOPT_PUT, 1);
	            break;
	        default:
	            if ($data)
	                // $url = sprintf("%s?%s", $url, http_build_query($data));
	                $url .= $data;
	    }

	    # Optional Authentication:
	    // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    // curl_setopt($curl, CURLOPT_USERPWD, "username:password");

	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($curl, CURLOPT_VERBOSE, true);
	    curl_setopt($curl, CURLOPT_HEADER, 1);

	    $result = curl_exec($curl);

	    # Then, after your curl_exec call:
	    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
	    $header = substr($result, 0, $header_size);
	    $body = substr($result, $header_size);
	    $resp = explode("\n", $header);
	    $contentType = substr($resp[10], 14, 9);

	    $httpresponse = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    curl_close($curl);

	    if ($this->isJson($body))
	    {
	        $response = json_decode($body, true);
	    } else {
	        $response = $body;
	    }

	    $this->_http_response_code($httpresponse);
	    $this->httpHeader 		= $contentType;
	    $this->httpStatusCode 	= $httpresponse;
	    $this->httpBody 		= $response;
	    // return array("httpStatusCode" => $httpresponse, "httpBody" => $response);
	}

	public function isJson($string)
	{
	    json_decode($string);
	    return (json_last_error() == JSON_ERROR_NONE);
	}

    public function _http_response_code($code = NULL)
    {
        if ($code !== NULL)
        {
            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }
            $this->httpStatusMessage = $text;
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        }
        else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }
        return $code;
    }
}