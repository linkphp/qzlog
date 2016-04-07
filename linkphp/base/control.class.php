<?php

class control{
	public function __get($var) {
		if($var == 'view') {
			return $this->view = new view();
		}elseif($var == 'db') {
			$db = 'db_'.$_ENV['_config']['db']['type'];
			return $this->db = new $db($_ENV['_config']['db']);	// 给开发者调试时使用，不建议在控制器中操作 DB
		}else{
			return $this->$var = core::model($var);
		}
	}

	public function assign($k, &$v) {
		$this->view->assign($k, $v);
	}

	public function assign_value($k, $v) {
		$this->view->assign_value($k, $v);
	}

	public function display($filename = null) {
		$this->view->display($filename);
	}

	//定向
	public function redirect($url, $time=0, $msg='') {
	    //多行URL地址支持把其中的\n,\r删掉 
	    $url = str_replace(array("\n", "\r"), '', $url);
	    if (empty($msg))
	        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	    if (!headers_sent()) {
	        // redirect 
	        if (0 === $time) {
	            header("Location: " . $url);
	        } else {
	            header("refresh:{$time};url={$url}");
	            echo($msg);
	        }
	        exit();
	    } else {
	        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
	        if ($time != 0)
	            $str .= $msg;
	        exit($str);
	    }
	}

	public function message($status, $message, $jumpurl = '', $delay = 2) {
		if(R('ajax')) {
			echo json_encode(array('link_status'=>$status, 'message'=>$message, 'jumpurl'=>$jumpurl, 'delay'=>$delay));
		}else{
			if(empty($jumpurl)) {
				$jumpurl = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
			}
			include LINK_PATH.'tpl/sys_message.php';
		}
		exit;
	}

	public function __call($method, $args) {
		// DEBUG关闭时，为防止泄漏敏感信息，用404错误代替
		if(DEBUG) {
			throw new Exception('控制器没有找到：'.get_class($this).'->'.$method.'('.(empty($args) ? '' : var_export($args, 1)).')');
		}else{
			core::error404();
		}
	}

    /**
     * Ajax输出
     * @param $data 数据
     * @param string $type 数据类型 text html xml json
     */
    protected function ajax($data, $type = "JSON")
    {
        $type = strtoupper($type);
        switch ($type) {
            case "HTML" :
            case "TEXT" :
                $_data = $data;
                break;
            case "XML" :
                //XML处理
                $_data = Xml::create($data, "root", "UTF-8");
                break;
            default :
                //JSON处理
                $_data = json_encode($data);
        }
        echo $_data;
        exit;
    }
}