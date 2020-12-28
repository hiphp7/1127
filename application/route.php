<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;


Route::rule('admin/index',                 'admin/Index/index');
Route::rule('admin/index/console',         'admin/Index/console');

Route::rule('admin/login',                 'admin/Login/index');
Route::rule('admin/login/captcha',         'admin/Login/captcha');
Route::rule('admin/login/loginact',        'admin/Login/loginAct');
Route::rule('admin/login/logoutact',       'admin/Login/logoutAct');

Route::rule('admin/user/agent',            'admin/User/agent');
Route::rule('admin/user/agentlist',        'admin/User/agentList');

Route::rule('admin/user/updateuser',       'admin/User/updateUser');
Route::rule('admin/user/deleteuser',       'admin/User/deleteUser');
Route::rule('admin/user/onlinestatus',     'admin/User/onlineStatus');
Route::rule('admin/user/forbiddenstatus',  'admin/User/forbiddenStatus');
Route::rule('admin/user/channelrate',      'admin/User/channelRate');

Route::rule('admin/user/channelquery',      'admin/User/channelQuery');
Route::rule('admin/user/agentquery',        'admin/User/agentQuery');

Route::rule('admin/user/merchant',          'admin/User/merchant');
Route::rule('admin/user/merchantlist',      'admin/User/merchantList');


