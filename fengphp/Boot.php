<?php
	namespace fengphp;

	class Boot {
		/*
		 * 功能：运行框架
		 */
		public static function run () {
			//1.错误处理
			self::handler();

			//2.初识化框架环境，例如开启会话，设置头部，设置时区等
			self::init();

			//3.清除session数据中的闪存，更改闪存的状态
			self::clearFlash();

			//4.解析请求到模块/控制器/方法
			self::runApp();
		}

		/*
		 * 功能：错误处理
		 */
		public static function handler () {

		}

		/*
		 * 功能：初始化框架环境
		 */
		public static function init () {
			//1.设置正确的头部
			header('Content-type: text/html; charset=utf8');

			//2.设置正确的时区
			date_default_timezone_set('PRC');

			//3.开启会话session
			if (!session_id()) {
				//尚未开启会话...

				//动作：指定session文件的存储目录
				$sessionPath = '../runtime/sessions';
				file_exists($sessionPath) || mkdir($sessionPath);
				session_save_path($sessionPath);

				//动作：指定session的配对cookie的名称
				session_name('SID');

				//动作：开启一个会话
				session_start();
			}
		}

		/*
		 * 功能：解析请求到模块/控制器/方法
		 */
		public static function runApp () {
			//动作：给出默认的模块、控制器和方法
			$m = 'home';
			$c = 'Index';
			$a = 'index';

			//1.路由处理

			//2.从请求中提取出模块、控制器和方法
			if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO']) {
				//url形式(pathinfo形式)："http://domain/index.php/module/controller/action"

				//动作：接收pathinfo
				$mca = $_SERVER['PATH_INFO'];
			} else if (isset($_GET['s']) && $_GET['s']) {
				//url形式1："http://domain/index.php?s=module/controller/action"
				//url形式2："http://domain/module/controller/action"
				//注意：这里使用了tp的apache rewrite配置，隐藏掉了index.php单入口文件，
				//由apache自动将url形式2转化为"http://domain/index.php?s=module/controller/action"形式

				//动作：接收$_GET['s']参数
				$mca = $_GET['s'];
			}

			if (isset($mca)) {
				//动作：去除$mca两端的/
				$mca = trim($mca, '/');

				//动作：将$mca拆分成至少3个部分
				$mca = explode('/', $mca);

				//动作：判断$mca是否至少有3部分
				if (count($mca) >= 3) {
					//动作：拆分出模块、控制器和方法
					list($m, $c, $a) = $mca;
				} else {
					prePrint('参数过少!');
					die;
				}
			}

			//3.将提取出来的模块和控制器拼接成带有命名空间的控制器类名称
			$controller = '\app\\' . $m . '\controller\\' . ucfirst(strtolower($c));

			//4.定义常量，记录当前访问的模块、控制器和方法
			//功能：便于后续的u()助手函数生成url
			define('MODULE', $m);
			define('CONTROLLER', $c);
			define('ACTION', $a);

			//5.实例化控制器类，调用指定的方法，输出方法调用的返回值
			(new $controller()) -> $a();
		}

		/*
		 * 功能：清除已经使用过一次的闪存数据，然后将没有使用的标记为“已使用”
		 */
		public static function clearFlash () {
			if (isset($_SESSION['flash'])) {
				//动作：遍历闪存数据的名单，找出已经使用过的闪存数据
				foreach ($_SESSION['flash'] as $k => $v) {
					if ($v) {
						//$k对应的闪存数据已经使用过了...

						//动作：删除名单和闪存数据
						unset($_SESSION['flash'][$k]);
						unset($_SESSION[$k]);
					} else {
						//$k对应的闪存数据没有被使用过...

						//动作：将$k对应的闪存数据标记为"已使用"
						$_SESSION['flash'][$k] = true;
					}
				}
			}
		}
	}