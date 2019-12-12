<?php
if (!defined('IN_DS')) {
	die('Hacking attempt');
}

class MyException 
{
	var $errorMsg;
	var $time;
	var $scriptName;
	var $line;
	
	static function  raiseError($errorMsg = "", $scripts=__FILE__, $line = __LINE__)
	{
		$this->errorMsg = $errorMsg;
		$this->time = date("Y-m-d H:i:s");
		$this->scriptName = $scripts;
		$this->line = $line;
		MyException::setErrorLog();
		
		if(DEBUG_MODE)
		{
			echo "<style>body{color:#3e3e3e; font-size:12px; font-family: Georgia,Arial,Helvetica,sans-serif;text-decoration:none;}</style>";
			echo "Exception, Session Halted.<br>\n";
			echo "Time:{$this->time}<br>\n";
			echo "ScriptName:{$this->scriptName}<br>\n";
			echo "Line:{$this->line}<br>\n";
			echo "ErrorMsg:{$this->errorMsg}<br>\n";
			exit;
		}
		else
		{
			//return false;
			echo "<b>500 Internal Error</b>";
			exit;
		}
	}

	function setErrorLog()
	{
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$req = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

		$logString = "{$this->time}\t{$this->scriptName}\t{$this->line}\t$ip\t$req\t$ref\t{$this->errorMsg}\n";
		if(defined("LOG_LOCATION") && defined("ERROR_LOG_FILE"))
		{
			@error_log($logString,3,LOG_LOCATION.ERROR_LOG_FILE);
		}
	}
}
