# myFramework
重新练习框架的制作过程，让我更加了解MVC框架的原理

###1.目录结构
````
myFramework
    |
    |-app(开发者书写代码的地方，存放着controller, view)
    |   |
    |   |-admin(模块)
    |   |   |
    |   |   |-controller(控制器)
    |   |   |
    |   |   |-view(视图)
    |   | 
    |   |-entry(模块)
    |   |   |
    |   |   |-controller(控制器)
    |   |   |
    |   |   |-view(视图)
    |   | 
    |   |-...
    |
    |-fengphp(框架的核心，例如框架的启动初始化、错误处理、解析请求并调用相应的控制器方法，路由等)
    |   |
    |   |-core(核心)
    |   |   |-controller(放置控制器基类)
    |   |   |   |
    |   |   |   |-controller.php
    |   |   |
    |   |   |-model(放置模型基类)
    |   |   |   |    
    |   |   |   |-model.php    
    |   |   |    
    |   |   |-view(放置视图模板处理基类)    
    |   |   |   |    
    |   |   |   |-view.php    
    |   |    
    |   |-Boot.php(引导启动文件)    
    |
    |-public(公共资源目录，存放单入口文件和一些静态资源，一般来说这个目录开放给外界访问，给网页提供资源支持)
    |   |-static(静态资源，包括图片、字体、样式表、js文件等)
    |   |
    |   |-view(公共模板文件，例如404页面)
    |   |
    |   |-index.php(单入口文件)
    |   |
    |   |-...
    |
    |-system(系统配置、用户定义的model)
    |   |
    |   |-config(放置配置文件)
    |   |
    |   |-model(放置用户自定义的model)
    |   |
    |   |-helper.php(助手文件)
    |   |
    |   |-...
    |
    |-runtime(运行时生成的文件，包括session文件、上传的临时文件、日志文件等)
    |
    |-vendor(放置第三方类库，由composer自动生成，composer会负责第三方类库的下载和自动加载)
    |   |
    |   |-composer(放置composer核心文件)
    |   |
    |   |-autoload.php(只需要加载这个文件，就能使用到composer的自动加载功能)
    |   |
    |   |-...
    |
    |-composer.json(composer的配置文件，composer init自动生成，在里面注册自动加载的文件、命名空间到目录的映射)
    |
    |-.gitignore(设置提交到GitHub时不用上传的文件或者目录)
    |
    |-README.md(框架的开发文档，能够帮助你更好的理解fengphp)
    |
    |-...
````
###2.构建框架的基本步骤
####第一步：在GitHub平台上创建框架项目，然后将空的框架项目克隆到本地
````
这样子就能实现本地编辑，上传同步到GitHub平台
````
####第二步：按照目录结构创建目录
````
按照目录结构创建出目录即可
````
####第三步：往框架中导入composer，让composer实现框架的类库（第三方类库文件和框架自身类库文件）自动加载
````
composer类库的作用：音乐指挥家的角色，负责协调各种类库文件的加载，可以方便地实现类库的自动加载。
用法：
1.composer init
作用：初始化并生成 composer.json 文件，生成“音乐家名单”
2.composer dump
作用：生成“音乐指挥家”，并将“音乐家名单”递送给“音乐指挥家”，也就是生成了composer目录和autoload.php文件
3.编辑 composer.json 文件，添加配置项 "autoload"：
{
    ...,
    /*这个只能用双引号括住配置项*/
    "autoload": {
        /*这个指定自动加载的文件*/
        "files": ["system/helper.php"],
        /*psr-4：自动加载规范。这个指定命名空间和目录的映射关系*/
        "psr-4": {
            /*命名空间: 目录*/
            "app\\": "app\\",
            "feng\\": "fengphp\\"
        }
    }
}
4.composer dump
作用：和2一样
5.在单入口文件 index.php 中加载 "../vendor/autoload.php" 文件，然后就可以
new \fengphp\Boot() 或者 use fengphp\Boot; new Boot(); 使用到框架的引导启动类Boot
````
####第四步：创建单入口文件index.php
````
//动作：别名导入（phpstorm自动生成）
use fengphp\core\Boot;

//动作：用 require_once 加载类文件 autoload.php, 
//因此在类文件 autoload.php 加载失败时中止PHP脚本的运行
//功能：实现类文件的自动加载
require_once '../vendor/autoload.php';

//动作：用 require_once 加载类文件 Boot.php 并且调用 run() 方法, 
//因此在类文件 Boot.php 加载失败时中止PHP脚本的运行
//功能：初始化框架环境，为 MVC 框架的正常运行做好准备
Boot::run();
````
####第五步：创建引导启动文件Boot.php
````
class Boot {
    public static function run() {
        //第1步：错误处理
        self::handler();
        
        //第2步：初始化框架环境
        self::init();
        
        //第3步：运行应用程序，将url请求路由到指定的模块/控制器/方法
        self::runApp();
    }
    public static function init() {
        //1.设置正确的头部
        //2.设置正确的时区
        //3.开启一个会话
    }
    public static function handler() {
        //进行错误的处理
        //1.首先到 packagist 上面找到 flip/whoops
        //2.在 phpStorm 的命令行输入 composer require flip/whoops 将下载到目录中
        //3.在这个方法中粘贴下面的代码就行了
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }
    public static function appRun() {
        //1.接收用户的url请求参数
        //2.将接收到的url参数拆分成module, controller, action(优先级：路由>pathinfo>get参数)
        //3.实例化指定的控制器类后调用指定的方法
    }
}
````
####第六步：创建控制器基类
````
功能：1.错误提示(success和error), 2.直接重定向(redirect)
````
####第七步：创建模型基类
````
功能：1.进行数据库的连接，2.原生查询方法q, 3.原生执行方法e, 4.链式操作(field, order)
````
####第八步：创建视图加载处理基类
````
功能：接收要渲染到模板中的数据，然后加载模板并把数据渲染到模板中
````
####第九步：助手函数config(), url(), view();
````
config(): 用来加载配置项
url(): 根据提供的 “模块/控制器/方法” 生成url
view(): 进行模板的渲染
````