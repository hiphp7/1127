<?php

namespace app\admin\controller;

use think\Db;
use think\Request;

class Index extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    //后台框架首页
    public function index()
    {
        $pageuser = checkPower();
        $mysql_version = $this->mysql->fetchResult("select version()");

        $menu = getUserMenu($pageuser['id'], $this->mysql);
        $data = array(
            'user' => $pageuser,
            'menu_json' => json_encode(array_values($menu)),
            'sys_group' => getConfig('sys_group'),
            'mysql_version' => $mysql_version
        );
        file_put_contents(ROOT_PATH . "logs/test.txt", "index menu: " . var_export($data['menu_json'], true) . "\n\n", FILE_APPEND);
        return $this->fetch('Index/index', $data);
    }

    public function console()
    {
        return $this->fetch('Index/console');
    }
}
