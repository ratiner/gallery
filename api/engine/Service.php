<?php
//require_once('utils.php');
	
class Service {
	public $app;
	public $data;
	protected $format;
	protected $callback;
	function __construct() {
		register_shutdown_function(array($this, 'shutdownHandler'));
        set_error_handler(array($this, 'errorHandler'));
		
		try {
			$this->set_vars();
			if(Config::$HTTPS_required && $_SERVER['HTTPS'] != 'on')
				exit_error(2);
		
			if(Config::$auth_on)
				authenticate();

			
			\Slim\Slim::registerAutoloader();
			$app = new \Slim\Slim();
			
			
			$app->error(function (\Exception $e) use ($app) {
				echo $e->getMessage();
				//$app->render('error.php');
			});
			
			$app->notFound(function () use ($app) {
				echo "404";
				//$app->render('404.html');
			});
			
			$this->app = $app;
						
			
		} catch(Exception $e) {
			$this->exit_error(0, $e->getMessage());
		}
	}
	  
	function set_vars() {	
		$this->format = ((isset($_GET["f"])) && $_GET["f"] != '') ? strtolower($_GET["f"]) : strtolower(Config::$default_format);
		$this->callback = (isset($_GET["jsonp"])) ? $_GET["jsonp"] : "";
		if(empty($this->callback) && isset($_GET["callback"]))
			$this->callback = $_GET["callback"];

		$this->data = json_decode(file_get_contents('php://input'), true);
		if($this->data)
			$this->data = RetrieveIDs($this->data);
	}
	
	function authenticate() {
		//auth stuff..
	}
	
	public function process($module, $method, $params = NULL) {
		
		$args = func_get_args();
		
		$endpoint = 'modules/' . $module . '.php';

		if(!file_exists($endpoint))
			$this->exit_error(7);
		
		require_once($endpoint);
		
		$sClass = explode('/', $module);
		$class = end($sClass);
		
		if(!method_exists($class, $method))
			$this->exit_error(8);
		
		$params = Array();
		for($i=2; $i<count($args); $i++)
			$params[] = $args[$i];
	
		try{
			$response = call_user_func_array(array(new $class, $method), $params);
			$this->deliver_response(0, $response);
		}catch(Exception $e) {
			$this->exit_error(9, $e->getMessage());
		}
	}

    public function OptionsFix() {
        $this->deliver_response(0, "OK");
    }
	
	function deliver_response($code, $response) {
		$response = UglifyIDs($response);

		if(Config::$enable_crossdomain) {
			//Additional headers
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
            Header('Access-Control-Allow-Headers: x-requested-with, Content-Type, origin, authorization, accept, client-security-token');
		}
		
		$http_response_code = array(
					200 => 'OK',
					400 => 'Bad Request',
					401 => 'Unauthorized',
					403 => 'Forbidden',
					404 => 'Not Found'
			);
		
		$response_status = $this->response_code[$code]['HTTP Response'];
		header('HTTP/1.1 '.$response_status.' '.$http_response_code[$response_status]);

		
		switch($this->format) {
			
			case 'json':   //JSON
				header('Content-Type: application/json; charset=utf-8');
				$json_response = json_encode($response);

				if(!empty($this->callback) && Config::$enable_crossdomain)
					echo $this->callback . '(' . $json_response . ')';
				else
					echo $json_response;
				break;
			
			case 'xml':  	//XML
				header('Content-Type: application/xml; charset=utf-8');
				$xml_response = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
								'<response>'."\n".
								"\t".'<code>'.$response_status.'</code>'."\n".
								"\t".'<data>'.$response.'</data>'."\n".
								'</response>';
				echo $xml_response;
				break;
			
			case 'html':	//HTML
				header('Content-Type: text/html; charset=utf-8');
				echo $response;
				break;
			
			default:
				header('Content-Type: text/plain; charset=utf-8');
				echo $response;
		}
		
		exit();
	}
	
    function exit_error($code, $err=NULL) {
		$response = array();
		$response = $this->response_code[$code]['Message'];
		if(!empty($err))
			$response .= ': '. $err;
		$this->deliver_response($code, $response);
	}
	
	public function shutdownHandler() {
	 	$error = error_get_last();
		if( $error !== NULL) {
			$this->deliver_response(9, $error);
			
	   	}
	}
	
	public function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
	{
		$error = "lvl: " . $error_level . " | msg:" . $error_message . " | file:" . $error_file . " | ln:" . $error_line;       
		echo $error;
		exit();
	}
	
	protected $response_code = array (
		0 => array('HTTP Response' => 200, 'Message' => 'Success!'),
		1 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
		2 => array('HTTP Response' => 403, 'Message' => 'HTTPS Required'),
		3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
		4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
		5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
		6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format'),
		7 => array('HTTP Response' => 404, 'Message' => 'The specified module was not found.'),
		8 => array('HTTP Response' => 404, 'Message' => 'The specified method was not found.'),
		9 => array('HTTP Response' => 400, 'Message' => 'Method execution error.')
	);
	
}