<?php
require_once("config.php");	//引入配置文件
require_once("Lib\smarty\Smarty.class.php");//引入 smarty 文件
/* 
 *@name  C
 * 调用控制器函数
 *@param $name 控制器名 
 *@param $method 控制器方法
 *
 **/
function C($name,$method){
	require_once("/Lib/Controller/{$name}Controller.class.php");
	eval('$obj=new '.$name.'Controller();$obj->'.$method.'();');
	return $obj;
}

/* 
 *@name  M
 * 调用模型控制器函数
 *@param $name 模型控制器名 
 *
 **/
function M($name){
	require_once("/Lib/Model/{$name}Model.class.php");
	eval('$obj = new '.$name.'Model();');
	return $obj;
}


/*
 *@name  M
 * 调用模型控制器函数
 *@param $name 模型控制器名 
 *
 **/
function V($name){
	require_once("/Lib/View/{$name}View.class.php");
	eval('$obj = new '.$name.'View();');
	return $obj;
}

/*
 *@name 过滤非法字符
 *@param $str 需要过滤的字符串
 **/
function filter($str){
	//get_magic_quotes_gpc 判断系统是否开启魔法字符
	//开启则返回true 
	//开启将自动转义特殊符号
	return (!get_magic_quotes_gpc())?addslashes($str):$str;
}
