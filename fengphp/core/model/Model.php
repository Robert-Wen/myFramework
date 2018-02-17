<?php
	namespace fengphp\core\model;

	class Model {
		//动作（功能）：将 $pdo 属性声明为静态是为了在整个PHP脚本中只进行一次数据库的连接，避免重复的连接
		private static $pdo = null;
		private static $driver = '';        //数据库类型，驱动
		private static $host = '';          //主机地址
		private static $dbname = '';        //数据库名称
		private static $user = '';          //数据库用户
		private static $password = '';      //数据库密码
		protected $table = '';              //模型对应的数据表
		protected $pk = 'id';               //数据表的主键名称

		private $field = '';       //查询字段
		private $join = '';         //select语句join部分
		private $where = '';        //where条件
		private $group = '';        //group by 字段
		private $having = '';       //having条件
		private $order = '';        //order by字段和升降序
		private $limitCount = '';    //limit部分，仅仅用于delete
		private $limit = '';        //limit部分，仅仅用于select
		private $values = '';       //values部分，仅仅用于insert
		private $set = '';          //set部分，仅仅用于update

		/*
		 * 功能：构造方法，进行数据库连接
		 */
		public function __construct() {
			//动作：从数据库配置文件中读取配置项
			$driver = config('database.driver');
			$host = config('database.host');
			$dbname = config('database.dbname');
			$user = config('database.user');
			$password = config('database.password');

			//动作：获得实际被实例化的类名，带有命名空间，将类名转化为表名
			$tmp = explode('\\', get_called_class());
			$this -> table = isset($tmp[count($tmp) - 1]) ? strtolower($tmp[count($tmp) - 1]) : '';

			//动作：判断当前数据库连接请求和先前的数据库连接请求是不是同一个数据库连接
			//功能：如果是同一个数据库连接则尝试使用先前的数据库连接；
			//否则另外进行新的数据库连接
			if ($driver == self::$driver && $host == self::$host && $dbname == self::$dbname && $user == self::$user && $password == self::$password && !is_null(self::$pdo)) {
				//当前数据库连接和先前数据库连接是同一个连接的情况...

			} else {
				//当前数据库连接是一个全新的连接...

				//进行数据库的连接
				try {
					//第1步
					//动作（功能）：指定数据库连接的数据源，包含信息：数据库类型、主机地址和数据库名称
					$dsn = $driver . ':host=' . $host . ';dbname=' . $dbname;

					//第2步
					//动作：实例化PDO类
					//功能：进行数据库的连接
					self::$pdo = new \PDO($dsn, $user, $password);

					//第3步
					//动作：用 self::$pdo -> setAttribute() 设置PDO错误处理为抛出异常
					//功能：PDO方法出错时将会抛出一个异常
					self::$pdo -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

					//第4步
					//动作：用 self::$pdo -> query() 设置客户端于SQL服务器通信过程中的字符集
					self::$pdo -> query('set names ' . config('database.charset'));

					//到这里，一个正常的数据库连接就已经成功了...

					//动作（功能）：将当前的数据库连接参数保存起来，方便下次的连接请求
					self::$host = $host;
					self::$dbname = $dbname;
					self::$user = $user;
					self::$password = $password;
					self::$driver = $driver;
				} catch (\Exception $e) {
					//动作（功能）：输出接收到的异常中的错误信息并且中止PHP脚本的执行
					die($e -> getMessage());
				}
			}

			//动作：获得当前表名的主键名称
			$tableInfo = $this -> q('desc ' . $this -> table);
			foreach ($tableInfo as $v) {
				if ($v['Key'] == 'PRI') {
					$this -> pk = $v['Field'];
					break;
				}
			}
		}

		/*
		 * 功能：数据库查询
		 */
		public function q($sql) {
			//动作：将查询数据库的代码放在try{}catch(){}块中，PDO方法操作失败时就会抛出一个异常
			try {
				//动作（功能）：将SQL语句发送到SQL服务器中执行，以 PDOStatement对象 的形式返回数据查询结果
				$res = self::$pdo -> query($sql);

				//动作（功能）：使用 $res -> fetchAll() 以关联数组的形式返回数据查询结果
				return $res -> fetchAll(\PDO::FETCH_ASSOC);
			} catch(\Exception $e) {
				//动作（功能）：输出捕获到的异常中的错误信息并中止PHP脚本的执行
				return ['valid' => false, 'msg' => $e -> getMessage()];
			}
		}

		/*
		 * 功能：数据库操作
		 */
		public function e($sql) {
			//动作：将查询数据库的代码放在try{}catch(){}块中，PDO方法操作失败时就会抛出一个异常
			try {
				//动作（功能）：将SQL语句发送到SQL服务器中执行，返回受影响的数据的行数
				return self::$pdo -> exec($sql);
			} catch (\Exception $e) {
				//动作（功能）：输出捕获到的异常中的错误信息并中止PHP脚本的执行
				return ['valid' => false, 'msg' => $e -> getMessage()];
			}
		}

		/**
		 * 功能：指定要操作的数据表
		 * @param $table        表名
		 * @return $this        对象
		 */
		public function table ($table) {
			//动作（功能）：指定数据库中的表名
			$this -> table = ' ' . $table;

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}

		/**
		 * 功能：拼接select语句的join部分
		 * @param $table        第二表名
		 * @param $on           on连接条件
		 * @return $this        对象，用于支持链式操作
		 */
		public function join ($table, $on) {
			//动作（功能）：追加select语句的join部分
			$this -> join .= " join {$table} on {$on}";

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}


		/**
		 * 功能：指定 insert, select 语句要使用的字段
		 * @param $field        字段
		 * @return $this        对象
		 */
		public function field ($field) {
			//动作（功能）：指定select, insert中要用到的字段
			$this -> field = $field;

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}

		/**
		 * 功能：构造SQL语句的 where 条件部分
		 * @param $where    where 条件
		 * @return $this    对象
		 */
		public function where ($where) {
			//动作（功能）：指定where条件部分
			//动作：在 $where 不为空时才需要构造SQL where条件部分
			if ($where) {
				$this -> where = ' where ' . $where;
			}

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}

		/**
		 * 功能：构造SQL语句的 group by 部分
		 * @param $group    group by 字段
		 * @return $this    对象
		 */
		public function group ($group) {
			//动作（功能）：指定group by部分
			//动作：在 $group 不为空时才需要构造SQL group by部分
			if ($group) {
				$this -> group = ' group by ' . $group;
			}

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}

		/**
		 * 功能：构造SQL语句的 having 部分
		 * @param $having       having条件
		 * @return $this        对象
		 */
		public function having ($having) {
			//动作（功能）：指定having条件部分
			//动作：在 $having 不为空时才需要构造SQL having条件部分
			if ($having) {
				$this -> having = ' having ' . $having;
			}

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}

		/**
		 * 功能：指定SQL语句的 order by 部分
		 * @param $order        order by的内容
		 * @return $this        对象
		 */
		public function order ($order) {
			//动作（功能）：指定order by部分
			//动作：在 $order 不为空时才需要构造SQL order by部分
			if ($order) {
				$this -> order = ' order by ' . $order;
			}

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}

		/**
		 * 功能：构造SQL语句的limit部分
		 * @param $offset       数据偏移量
		 * @param $count        数据条数
		 * @return $this        对象，用于支持链式操作
		 */
		public function limit ($count, $offset = 0) {
			//动作（功能）：构造limit部分，用于delete语句
			$this -> limitCount = ' limit ' . $count;
			//动作（功能）：构造limit部分，用于select语句
			$this -> limit = ' limit ' . $offset . ',' . $count;

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}

		/**
		 * 功能：指定values部分的值。
		 * @param array ...$valuesArr   可变数量参数，每个参数必须是一维数组，数组元素的值就是要插入数据库的值
		 * @return $this                对象，支持链式操作
		 */
		public function values (...$valuesArr) {
			//功能：$valuesStr表示$valuesArr的字符串形式
			$valuesStr = ' values';

			//动作：遍历二维数组，将$valuesArr
			foreach ($valuesArr as $valueArr) {
				//功能：$valueStr表示$valueArr的字符串形式
				$valueStr = '';
				foreach ($valueArr as $v) {
					//动作：判断$v是否是字符串，如果是则要加引号；否则不加引号
					$valueStr .= (is_string($v) ? "'{$v}'" : $v) . ',';
				}
				//功能：将valueStr后面多余的 ',' 去除掉
				$valueStr = rtrim($valueStr, ',');
				$valuesStr .= '(' . $valueStr . '),';
			}

			//功能：将valuesStr后面多余的 ',' 去除掉
			$valuesStr = rtrim($valuesStr, ',');

			//动作（功能）：用属性$this -> values保存指定的values部分
			$this -> values = $valuesStr;

			//动作（功能）：返回一个对象，支持链式操作
			return $this;
		}

		/**
		 * 功能：构造update语句set部分
		 * @param $data     set部分数据，关联数组形式，键名表示要更新的字段，键值表示要更新的值
		 * @return $this    对象，用于支持链式操作
		 */
		public function set ($data) {
			$set = ' set ';

			//动作：遍历数组$data
			foreach ($data as $k => $v) {
				//动作：将键名和键值拼接成$k=$v的形式，如果$v是字符串则需要加引号；否则不加引号
				$set .= $k . '=' . (is_string($v) ? "'{$v}'" : $v) . ',';
			}

			//动作（功能）：去除后面多余的逗号后保存到 $this -> set 属性中
			$this -> set = rtrim($set, ',');

			//动作：返回一个对象 $this 以支持链式操作
			return $this;
		}

		/**
		 * 功能：将先前指定的SQL片段全部清空，避免先前的设置干扰到后面的操作，仅仅保留 table 这个设置
		 */
		private function resetSQL () {
			//动作（功能）：将之前指定的SQL片段全部清空
			$this -> field = '';
			$this -> where = '';
			$this -> group = '';
			$this -> having = '';
			$this -> order = '';
			$this -> limit = '';
			$this -> limitCount = '';
			$this -> join = '';
		}

		/**
		 * 功能：将select语句的多个片段拼接在一起，然后执行查询
		 */
		public function select () {
			//1.将SQL语句的多个片段拼接在一起
			$sql = 'select ' . ($this -> field ? $this -> field : '*') .
				' from ' . $this -> table . $this -> join .
				$this -> where .
				$this -> group .
				$this -> having .
				$this -> order .
				$this -> limit . ';';

			//2.清空先前设置好的SQL片段，仅仅保留设置好的 table
			$this -> resetSQL();

//			prePrint($sql); return ;

			//3.执行SQL语句，返回查询的结果
			return $this -> q ($sql);
		}

		/**
		 * 功能：将insert语句的多个片段拼接在一起，然后执行查询
		 */
		public function insert () {
			//动作（功能）：拼接字段
			$field = $this -> field ? ('(' . $this -> field . ')') : '';
			//动作（功能）：拼接insert语句
			$sql = 'insert into ' . $this -> table . $field . $this -> values . ';';

			//2.清空先前设置好的SQL片段，仅仅保留设置好的 table
			$this -> resetSQL();

			//动作（功能）：执行SQL语句，返回受影响的数据条数
			return $this -> e ($sql);
		}

		/**
		 * 功能：将update语句的多个片段拼接在一起，然后执行查询
		 */
		public function update () {
			//动作：判断是否有where字段，如果没有则不作更新；有则作出更新
			if ($this -> where == '') {
				return false;
			}

			//动作（功能）：拼接update语句
			$sql = 'update ' . $this -> table .
				$this -> set .
				$this -> where . ';';

			//2.清空先前设置好的SQL片段，仅仅保留设置好的 table
			$this -> resetSQL();

			//动作（功能）：执行SQL语句，返回受影响的数据条数
			return $this -> e ($sql);
		}

		/**
		 * 功能：将delete语句的多个片段拼接在一起，然后执行查询
		 * @return bool|int     成功返回操作影响的数据条数；失败返回false
		 */
		public function delete () {
			//动作：判断是否有where字段，如果没有则不作删除；有则作出删除
			if ($this -> where == '') {
				return false;
			}

			//动作（功能）：构造delete语句
			$sql = 'delete from ' . $this -> table .
				$this -> where .
				$this -> order .
				$this -> limitCount . ';';

			//2.清空先前设置好的SQL片段，仅仅保留设置好的 table
			$this -> resetSQL();

			//动作（功能）：执行SQL语句，返回受影响的数据条数
			return $this -> e ($sql);
		}
	}