<?php
	namespace fengphp\core\view;

	class View {
		/*
		 * 功能：拼接出视图模板的路径，将渲染要用到的数据导入到当前作用域中
		 */
		public static function make ($path = '', $data = []) {
			//动作：获得模板默认的后缀名
			$suffix = config('view.suffix');

			//1.拼接出视图模板的路径
			if ($path) {
				//动作：判断$path是否是真正的路径
				if (preg_match('#/#', $path)) {
					//$path是一个真正的路径...

					$template = $path;
				} else {
					//$path只是一个模板的名称...

					$template = rtrim(APP_PATH, '/') . '/' . MODULE . '/view/' . CONTROLLER . '/' . $path . (preg_match('/\./', $path) ? "" : ".{$suffix}");
				}
			} else {
				//没有指定$path，使用"app/模块/view/控制器/方法.后缀"这个模板文件

				//动作：路径拼接
				$template = rtrim(APP_PATH, '/') . '/' . MODULE . '/view/' . CONTROLLER . '/' . ACTION . '.' . $suffix;
			}

			//2.将渲染模板要用到的数据导入到当前作用域中
			extract($data);

			//3.加载模板文件
			include $template;
		}
	}