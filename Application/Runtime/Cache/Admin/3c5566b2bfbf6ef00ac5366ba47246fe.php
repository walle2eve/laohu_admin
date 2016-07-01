<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo C('WEB_SITE');?>后台管理系统</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	
    <link href="/Public/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<link href="/Public/dist/css/AdminLTE.min.css" rel="stylesheet">
	
	<link href="/Public/dist/css/admin.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="/Public/bower_components/morrisjs/morris.css" rel="stylesheet">

    <link href="/Public/plugins/jbox/jBox.css" rel="stylesheet" type="text/css"/>
    <!--[if lt IE 9]>
    <script src="{{ asset('static/html5shiv.min.js') }}"></script>
    <script src="{{ asset('static/respond.min.js') }}"></script>
    <![endif]-->
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-box-body">
            <p class="login-box-msg"><?php echo C('WEB_SITE');?></p>
            <form id="loginForm" name="loginForm" action="<?php echo U('Admin/Public/dologin');?>" method="post">
                <div class="form-group has-feedback">
                    <input type="text" name="account" id="account" class="form-control" placeholder="帐号"/>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="pwd" id="pwd" class="form-control" placeholder="密码"/>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">登录</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="/Public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script src="/Public/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="/Public/plugins/jbox/jBox.js" type="text/javascript"></script>
    <script src="/Public/js/base.js" type="text/javascript"></script>
</body>
<script>
jQuery(function($){
    $('#loginForm').submit(function(){
        $.post(this.action, $(this).serialize(), function(result){
            if (result.status) {
                location.href = result.url;
            } else {
                $.showError(result.msg);
            }
			return false;
        }, 'json');
		// console.log('登录');
        return false;
    });
})
</script>
</html>