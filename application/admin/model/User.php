<?php

namespace app\admin\model;

use think\Model;
use think\Request;
use app\Common;
use app\common\Mysql;
use app\common\MyMemcache;

class User extends Model
{
    //protected $table = 'sys_user';

    public static function clearToken($uid, $mysql = null)
    {
        $need_close = false;
        if (!$mysql) {
            $mysql = new Mysql(0);
            $need_close = true;
        }
        $memcache = new MyMemcache(0);
        $token_arr = $mysql->fetchRows("select * from sys_user_token where uid={$uid}", 1, 1000);
        foreach ($token_arr as $tk) {
            $mem_key = 'token_' . $tk['token'];
            $memcache->delete($mem_key);
        }
        $mysql->delete("uid={$uid}", 'sys_user_token');
        if ($need_close) {
            $mysql->close();
            unset($mysql);
        }
        $memcache->close();
        unset($memcache);
        return true;
    }

    //执行退出清理
    public static function doLogout()
    {
        $user = User::isLogin();
        if (!$user) {
            return true;
        }
        Common::deleteCookie();//清理cookie
        //清理token
        User::clearToken($user['id']);
        //清理节点缓存
        $memcache = new MyMemcache(0);
        $mem_key = $_ENV['CONFIG']['MEMCACHE']['PREFIX'] . 'access_ids_' . $user['id'];
        $memcache->delete($mem_key);
        unset($memcache);
        return true;
    }

    //获取用户信息
    public static function getUserinfo($uid, $mysql = null)
    {
        $uid = intval($uid);
        if (!$uid) {
            return false;
        }
        if (!$mysql) {
            $mysql = new Mysql(0);
        }
        $user = $mysql->fetchRow("select * from sys_user where id={$uid}");
        return $user;
    }

    //检查登录
    public static function checkLogin()
    {
        $user = User::isLogin();
        if ($user) {
            return $user;
        }
        if (Request::instance()->controller() == 'Login') {
            return false;
        }

        if (Request::instance()->isAjax()) {
            Common::jReturn('-98', '请先登录');
        } else {
            //$callback = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $f = isset($_GET['f']) ? intval($_GET['f']) : 0;
            $url = ADMIN_URL . "/login/index?f={$f}";;//&callback= . urlencode($callback);
            header("Location:{$url}");
            exit;
        }
    }

    //检查登录
    public static function isLogin()
    {
        $token = Common::getParam('token');
        $user = null;
        if ($token) {
            $user = User::getUserByToken($token);
        }
        if (!$user || !is_array($user)) {
            $cookie_json = Common::getCookie();
            $cookie_arr = json_decode($cookie_json,true);
            $sign = Common::sysSign($cookie_arr);
            if ($sign == $cookie_arr['sign']) {
                $token = $cookie_arr['token'];
                $user = User::getUserByToken($token);
                if (!$user || !is_array($user)) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return $user;
    }

    //根据token获取用户信息
    public static function getUserByToken($token, $mysql = null)
    {
        if (!$token) {
            return -1;
        }
        $memcache = new MyMemcache(0);
        $mem_key = 'token_' . $token;
        $user = $memcache->get($mem_key);
        if ($user) {
            $memcache->close();
            unset($memcache);
            return $user;
        }

        if (!$mysql) {
            $mysql = new Mysql(0);
        }
        $sys_user_token = $mysql->fetchRow("select * from sys_user_token where token='{$token}' and status=0");
        if (!$sys_user_token) {
            return -2;
        } else {
            //token有效期检测
            //...
        }
        $user = $mysql->fetchRow("select * from sys_user where id={$sys_user_token['uid']}");
        if (!$user) {
            return -4;
        }
        if ($user['phone']) {
            $user['phone_flag'] = substr($user['phone'], 0, 3) . '***' . substr($user['phone'], 8);
        } else {
            $user['phone_flag'] = '';
        }

        $memcache->set($mem_key, $user, 3600);
        $memcache->close();
        unset($memcache);
        return $user;
    }
}