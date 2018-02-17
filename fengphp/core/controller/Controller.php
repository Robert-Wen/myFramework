<?php
	namespace fengphp\core\controller;

	class Controller {
		/*
		 * 功能：成功提示，并跳转到指定的url
		 */
		public function success ($msg, $url, $seconds = 3) {
			include 'view/success.php';
			die;
		}

		/*
		 * 功能：错误提示，跳转回上一个页面
		 */
		public function error ($msg, $seconds = 3) {
			include 'view/error.php';
			die;
		}

		/*
		 * 功能：直接进行重定向，并且伴随着一次性的session闪存数据s
		 */
		public function redirect ($url, $with = []) {
			//动作：将$with中的数据闪存到session中，只能使用一次
			foreach ($with as $k => $v) {
				$_SESSION[$k] = $v;
				$_SESSION['flash'][$k] = false;
			}

			//动作：直接输出一段js代码到页面中实现重定向
			echo '<script>location.href="' . $url . '";</script>';
			die;
		}
	}