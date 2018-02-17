<?php
	namespace app\admin\controller;

	use fengphp\core\controller\Controller;
	use fengphp\core\model\Model;
	use fengphp\core\view\View;
	use system\model\Classes;
	use system\model\Student;

	class Index extends Controller {
		public function index () {
//			$this -> success('操作成功', 'http://baidu.com', 2);
//			$this -> error('操作失败', 2);
//			$this -> redirect('/admin/index/login', ['error' => '这是错误信息', 'info' => '信息']);
//			prePrint(url('home/index/index', ['id' => 3, 'keyword' => '我是谁']));
		}

		public function login () {
			$student = new Student();
			prePrint($student -> where('name="wenfengze"') -> delete());
		}
	}