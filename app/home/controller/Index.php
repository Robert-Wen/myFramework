<?php
	namespace app\home\controller;

	class Index {
		public function index () {
			url('index');
		}

		public function login () {
			prePrint(__METHOD__);
		}
	}