<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8">
        <title>致晟平台后台登录</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- CSS -->
     
        <link rel="stylesheet" href="/Public/Admin/css/reset.css">
        <link rel="stylesheet" href="/Public/Admin/css/supersized.css">
        <link rel="stylesheet" href="/Public/Admin/css/login_new.css">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

    </head>

    <body class="loginbg">

        <div class="loginbk">
            <div class="titbg">致晟游戏-玩转平台管理系统</div>
            <form action="<?php echo U('login');?>" method="post">
                <input type="text" name="username" autocomplete="off" class="username" placeholder="用户名" >
                <input type="password" name="password" autocomplete="off" class="password" placeholder="密码" >
                <button type="submit">登　　录</button>
                <div class="error"><span>+</span></div>
            </form>
        
        </div>
    

        <!-- Javascript -->

    </body>

</html>