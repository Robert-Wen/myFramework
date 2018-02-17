<?php
	//动作：判断指定函数是否已定义
	//功能：避免重复定义函数
	if (!function_exists('preVarDump')) {
		/*
		 * 功能：以漂亮的格式显示变量类型和值
		 * @param null $var  需要显示的变量
		 */
		function preVarDump($var=null) {
			//动作：输出有样式的pre标签
			//功能：使var_dump的输出在网页中保持原样
			echo '<pre style="-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;background: tan; width: 98%; border-radius: 5px; margin: 10px 1%; font-size: 18px; line-height: 22px;padding: 10px;">';
			//动作：输出变量的类型和值
			var_dump($var);
			//动作：输出</pre>
			//功能：将$var的类型和值包含在pre标签中，避免影响到后面的输出
			echo '</pre>';
		}
	}

	//动作：判断指定函数是否已定义
	//功能：避免重复定义函数
	if (!function_exists('prePrint')) {

		/**
		 * 功能：漂亮打印所有类型变量的值，仅包含少量的类型信息
		 * @param null $var  需要漂亮显示的变量
		 */
		function prePrint($var=null) {
			//动作：输出有样式的pre标签
			//功能：使var_dump的输出在网页中保持原样
			echo '<pre style="box-sizing: border-box; background: tan; width: 98%; border-radius: 5px; margin: 10px 1%; font-size: 18px; line-height: 22px; padding: 10px;">';
			//动作：判断是布尔值或者null
			//功能：根据类型安排输出方式
			if (is_bool($var) || is_null($var)) {
				//动作：var_dump布尔值和null
				//功能：使不可视的变量可视化
				var_dump($var);
			} else {
				//动作：print_r除布尔值和null外的所有变量类型
				//功能：使可视化的变量简洁输出
				echo htmlspecialchars(print_r($var, true));
			}
			//动作：输出</pre>
			//功能：将$var的类型和值包含在pre标签中，避免影响到后面的输出
			echo '</pre>';
		}
	}

	//动作：判断指定函数是否已定义
	//功能：避免重复定义函数
	if (!function_exists('b2kmg')) {
		/**
		 * 功能：将字节数转化为人类更加可读的形式
		 * @param int $bytes        字节数
		 * @param int $precision    精度
		 * @return float|string     以K, M, G为单位的转化结果
		 */
		function b2kmg($bytes = 0, $precision = 2) {
			//动作：声明一个变量，初始化为空字符串
			//功能：用来保存转化结果
			$res = '';

			//动作：根据字节数进行适合的单位转换
			//功能：智能地选择适合的单位来进行换算
			if ($bytes >= pow(1024, 3)) {
				//动作（功能）：按指定的精度将转化的结果进行四舍五入
				$res = round($bytes / pow(1024, 3), $precision);
				//动作（功能）：添加合适的单位
				$res .= 'G';
			} else if ($bytes >= pow(1024, 2)) {
				//动作（功能）：按指定的精度将转化的结果进行四舍五入
				$res = round($bytes / pow(1024, 2), $precision);
				//动作（功能）：添加合适的单位
				$res .= 'M';
			} else if ($bytes >= 1024) {
				//动作（功能）：添加合适的单位
				$res = round($bytes / 1024, $precision);
				//动作（功能）：添加合适的单位
				$res .= 'K';
			} else {
				//动作（功能）：不能进行单位换算时，直接将传入的字节数赋值给$res
				$res = $bytes . 'B';
			}

			//动作（功能）：返回进行单位换算后的结果
			return $res;
		}
	}

	/**
	 * 定义常量IS_POST用来检测当前请求的方式
	 * $_SERVER['REQUEST_METHOD']提供了
	 * 更加准确的方法去判断当前请求方式
	 * 使用常量的原因：常量的作用域是全局的
	 */
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//动作：定义常量IS_POST，值为true
		//功能：表示当前请求方式post
		define('IS_POST', true);
	} else {
		//动作：定义常量IS_POST，值为false
		//功能：表示当前请求方式get
		define('IS_POST', false);
	}


	/**
	 * 定义常量IS_AJAX用来检测当前请求是不是ajax请求
	 * $_SERVER['HTTP_X_REQUESTED_WITH']提供了
	 * 更加准确的方法去判断当前请求方式
	 * 使用常量的原因：常量的作用域是全局的
	 */
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
		//ajax请求的时候...

		//动作：定义常量IS_AJAX，值为true
		//功能：表示当前请求为ajax请求
		define('IS_AJAX', true);
	} else {
		//不是ajax请求的时候...

		//动作：定义常量IS_AJAX，值为false
		//功能：表示当前请求不是ajax请求
		define('IS_AJAX', false);
	}



	/**
	 * 功能：递归删除目录
	 * @param $dir  要进行递归删除的目录
	 */
	function rmdirRec($dir) {
		//动作：判断路径是否表示目录
		//功能：避免删除非目录文件
		if (!is_dir($dir)) {
			return ;
		}
		//动作（功能）：获得$dir目录下的所有目录项，是一个数组
		$dirItems = glob($dir . '/*');

		//动作：遍历数组中的每个元素
		//功能：遍历目录$dir下的所有目录项
		foreach ($dirItems as $k=>$v) {
			if (is_dir($v)) {
				//2.删除$dir目录下的目录
				//动作：递归调用rmdirRec()
				//功能：删除$dir目录下的目录
				rmdirRec($v);
			} else {
				//1.删除$dir目录下的普通文件
				//动作：使用unlink($filename)删除
				unlink($v);
			}
		}


		//3.删除$dir目录本身
		//动作：用rmdir($dir)删除目录本身
		//功能：删除$dir目录本身
		rmdir($dir);
	}

	/*
	 * 功能：递归复制一个目录到指定的目录中
	 */
	function cp ($src, $dest) {
		//1.从$src中截取出目录名或者文件名
		$base = basename($src);

		//2.判断$dest是否为目录
		if (!is_dir($dest)) {
			return ['valid' => false, 'msg' => realpath($dest) . '不是目录或者不存在'];
		} else {
			//动作：对$src, $dest进行修剪处理
			$src = rtrim($src, '/');
			$dest = rtrim($dest, '/');
		}

		//3.拼接出复制的目标路径
		$path = $dest . '/' . $base;

		//4.判断要创建的文件或者目录是否已经存在
		if (file_exists($path)) {
			return ['valid' => false, 'msg' => realpath($path) . '文件或者目录已经存在'];
		} else {
			//5.判断$src是否为目录
			if (is_dir($src)) {
				//动作：创建一个目录
				mkdir($path);

				//动作：将$src目录下面的内容（文件+目录）全部复制到$path下面
				$pattern = $src . '/*';
				foreach (glob($pattern) as $v) {
					cp($v, $path);
				}
			} else if(is_file($src)) {
				//普通文件...

				//动作：进行文件的复制
				copy($src, $path);
			} else {
				//$src不存在...

				return ['valid' => false, 'msg' => realpath($src) . '不存在'];
			}
		}

		//动作：返回复制的结果
		return ['valid' => true, 'msg' => realpath($src) . '->' . realpath($path) . '：OK'];
	}

	/**
	 * 功能：将一个目录或者文件移动到另外一个目录中
	 * @param $source   一个目录或者文件
	 * @param $dest     目标目录
	 */
	function mvdir($source, $dest) {
		//1.判断目标目录是否存在，不存在则创建出来
		is_dir($dest) || mkdir($dest, 0777, true);

		//2.将$source指定的目录递归地复制到目标目录中
		cp($source, $dest);

		//3.将$source指定的源目录删除
		rmdirRec($source);
	}

	/**
	 * 功能：提示并跳转
	 * //注意：这里仅仅是生成了一段<script>...</script>
	//后面的PHP代码仍然会执行到底。所以应该让error函数后面跟着一个die();
	//进而阻止后续的PHP代码的执行
	 * @param $msg      提示信息
	 * @param $url      跳转的url
	 */
	function success($msg, $url) {
		//动作：输出一段js代码
		//功能：弹窗提示操作结果，并且跳转到指定的页面
		echo "<script>alert('$msg');location.href='{$url}';</script>";
		//动作（功能）：阻止后续PHP代码的执行
		die();
	}

	/**
	 * 功能：失败提示并返回上一网页
	 * //注意：这里仅仅是生成了一段<script>...</script>
	//后面的PHP代码仍然会执行到底。所以应该让error函数后面跟着一个die();
	//进而阻止后续的PHP代码的执行
	 * @param $msg      失败提示信息
	 */
	function error ($msg) {
		//动作（功能）：弹窗提示失败信息，然后返回上一个页面
		echo "<script>alert('{$msg}');history.back();</script>";
		//动作（功能）：阻止后续PHP代码的执行
		die();
	}

	/**
	 * 功能：将一个指定的数组转换为可运行的PHP代码后写入到指定文件中
	 * @param $filename     文件名
	 * @param $data         数据（数组形式）
	 * @return bool         true表示写入成功，false表示写入失败
	 */
	function writeDB($filename, $data) {
		//第1步
		//动作：var_export代码合法化$data
		//功能：将数组变量导出为合法的PHP代码，便于数据库文件的合法化
		$varDef = var_export($data, true);

		//第2步
		//动作：以heredoc形式生成数据库内容
		//功能：使代码能够如同PHP代码一样运行，并且便于人类阅读
		$code = <<<eoc
<?php
	return $varDef;
eoc;

		//第3步
		//动作：将$code的内容写入到文件$filename中
		//功能：将可运行的PHP代码写入到指定的文件中
		if (file_put_contents($filename, $code) === false) {
			//写入文件失败时...
			return false;
		} else {
			//写入文件成功时...
			return true;
		}
	}

	/*
	 * 功能：单文件上传的文件移动
	 */
	function single_up_move ($dirname) {
		//1.获取$_FILES的第一个元素，判断是否有文件上传
		reset($_FILES);
		$fileInfo = current($_FILES);
		if (!$fileInfo) {
			//并没有通过http post上传文件...

			//动作：返回上传失败和失败的原因
			return ['valid' => false, 'msg' => '没有http post上传的文件'];
		}

		//2.判断文件是否上传成功
		if ($fileInfo['error']) {
			//上传失败...

			//动作：返回上传失败和失败的原因
			return ['valid' => false, 'msg' => up_error($fileInfo['error'])];
		}

		//3.提取上传文件的后缀名
		$suffix = ltrim(strrchr($fileInfo['type'], '/'), '/');

		//4.进行文件的类型检测和大小检测

		//5.拼接出上传文件被移动到的目录$dest="$dirname/YYYY/MM/DD"
		$time = time();
		$dest = rtrim($dirname, '/') . '/' . date('Y', $time) . '/' . date('m', $time) . '/' . date('d', $time);

		//6.创建目标目录$dest
		file_exists($dest) || mkdir($dest, 0755, true);

		//7.拼接出上传文件的随机名称，并转移上传文件
		move_uploaded_file($fileInfo['tmp_name'], $dest . '/' . $time . mt_rand() . '.' . $suffix);

		//8.返回结果
		return ['valid' => true, 'msg' => '文件转移成功'];
	}

	/*
	 * 功能：多文件上传的文件移动
	 */
	function multi_up_move ($dirname) {
		//1.重构$_FILES数组，简化数组结果，便于遍历数组
		$fileInfos = [];
		foreach ($_FILES as $k => $v) {
			foreach ($v['error'] as $k1 => $v1) {
				//动作：判断当前上传文件是否发生错误
				if ($v1) {
					//发生上传错误...

					//动作：返回上传错误的结果和错误原因
					return ['valid' => false, 'msg' => up_error($v1)];
				}

				//动作：重构数组
				$ck = count($fileInfos);
				$fileInfos[$ck]['name'] = $v['name'][$k1];
				$fileInfos[$ck]['type'] = $v['type'][$k1];
				$fileInfos[$ck]['tmp_name'] = $v['tmp_name'][$k1];
				$fileInfos[$ck]['error'] = $v1;
				$fileInfos[$ck]['size'] = $v['size'][$k1];
			}
		}

		//2.对所有上传的文件进行转移操作
		foreach ($fileInfos as $fileInfo) {
			//3.提取上传文件的后缀名
			$suffix = ltrim(strrchr($fileInfo['type'], '/'), '/');

			//4.进行文件的类型检测和大小检测

			//5.拼接出上传文件被移动到的目录$dest="$dirname/YYYY/MM/DD"
			$time = time();
			$dest = rtrim($dirname, '/') . '/' . date('Y', $time) . '/' . date('m', $time) . '/' . date('d', $time);

			//6.创建目标目录$dest
			file_exists($dest) || mkdir($dest, 0755, true);

			//7.拼接出上传文件的随机名称，并转移上传文件
			move_uploaded_file($fileInfo['tmp_name'], $dest . '/' . $time . mt_rand() . '.' . $suffix);
		}

		//8.返回结果
		return ['valid' => true, 'msg' => '文件转移成功'];
	}

	/*
	 * 功能：将上传错误码转化为文本信息
	 */
	function up_error ($code = 0) {
		$result = '';

		switch ($code) {
			case 0:
				$result = '上传成功';
				break;
			case 1:
				$result = '文件大小超过php.ini中的限制';
				break;
			case 2:
				$result = '文件大小超过html表单中的大小限制';
				break;
			case 3:
				$result = '文件部分上传';
				break;
			case 4:
				$result = '没有上传文件';
				break;
			default:
				$result = '未知错误代码';
		}

		//动作：返回代码对应的错误信息
		return $result;
	}

	/*
	 * 功能：根据“模块/控制器/方法”生成url，应该可以生成pathinfo形式的url或者路由形式的url
	 */
	function url ($mca = '', $params = []) {
		//动作：修剪$mca两端的/
		$mca = trim($mca, '/');

		//1.从$mca中提取出尽可能多的信息来生成模块、控制器和方法
		if ($mca) {
			//$mca至少携带了方法的名称...

			//动作：从$mca中提取模块、控制器和方法
			$mca = explode('/', $mca);

			switch (count($mca)) {
				case 1:
					$m = MODULE;
					$c = CONTROLLER;
					$a = $mca[0];
					break;
				case 2:
					$m = MODULE;
					$c = $mca[0];
					$a = $mca[1];
					break;
				default:
					$m = $mca[0];
					$c = $mca[1];
					$a = $mca[2];
			}
		}

		//2.组成url：“/模块/控制器/方法”
		$url = '/' . $m . '/' . $c . '/' . $a;

		//3.由$params生成url的query部分
		$query = http_build_query($params);

		//4.将url和query部分拼接在一起
		$url .= $query ? ('?' . $query) : '';

		//5.返回生成的url
		return $url;
	}

	/*
	 * 功能：读取配置项
	 */
	function config ($config = '') {
		//动作：修剪一下用户传入的配置项
		$config = trim($config, '.');

		//1.判断是否有指定要读取的配置项
		if ($config) {
			//指定了要读取的配置项...

			//2.将配置项拆分出来, $config[0]作为配置文件的名称，其余的是数组键名
			$config = explode('.', $config);

			//3.获得配置文件的名字
			$filename = array_shift($config) . '.php';

			//4.拼接出配置文件的路径
			$path = realpath(APP_PATH . '/../system/config/' . $filename);

			//5.加载配置文件，获取所有的配置项
			$data = include $path;

			//6.一层一层地读取配置项
			foreach ($config as $v) {
				if (isset($data[$v])) {
					$data = $data[$v];
				} else {
					return null;
				}
			}

			//7.返回配置项的内容
			return $data;
		} else {
			return null;
		}
	}

	/*
	 * 功能：渲染模板
	 */
	function view ($path = '', $data = []) {
		//动作：调用View类的make方法来渲染模板
		\fengphp\core\view\View::make($path, $data);
	}