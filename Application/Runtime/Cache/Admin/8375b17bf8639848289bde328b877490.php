<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="quxiang">

    <title><?php echo C('WEB_SITE');?></title>
    <!-- Bootstrap Core CSS -->
    <link href="/Public/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="/Public/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="/Public/dist/css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/Public/dist/css/sb-admin-2.css" rel="stylesheet">
	<link href="/Public/dist/css/admin.css" rel="stylesheet">
    <!-- Morris Charts CSS -->
   <!-- <link href="/Public/bower_components/morrisjs/morris.css" rel="stylesheet"> -->

    <!-- Custom Fonts -->
    <link href="/Public/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="/Public/plugins/spinner/dist/bootstrap-spinner.css" rel="stylesheet" type="text/css"/>
	<link href="/Public/plugins/jbox/jBox.css" rel="stylesheet" type="text/css"/>
	
	<link rel="stylesheet" href="/Public/plugins/daterangepicker/daterangepicker-bs3.css" />

    <style>

    </style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
    <!-- jQuery -->
    <script src="/Public/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/Public/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="/Public/bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <!--
	<script src="/Public/bower_components/raphael/raphael-min.js"></script>
    <script src="/Public/bower_components/morrisjs/morris.min.js"></script>
    <script src="/Public/js/morris-data.js"></script>
	-->
    <script src="/Public/plugins/jbox/jBox.js" type="text/javascript"></script>
    <script src="/Public/js/base.js" type="text/javascript"></script>
	
    <!-- Custom Theme JavaScript -->
    <script src="/Public/dist/js/sb-admin-2.js"></script>
	
	
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo U('Admin/Index/index');?>"><?php echo C('WEB_TITLE');?></a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                 <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)" id="clear_cache">
                        <i class="fa fa-refresh fa-fw"></i>  更新缓存
                    </a>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> 个人信息</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> 设置</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="javascript:void(0);" id="logout"><i class="fa fa-sign-out fa-fw"></i> 退出系统</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
				
					                    <ul class="nav" id="side-menu">
						<?php if(is_array($menu_list)): $i = 0; $__LIST__ = $menu_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
							<?php if($vo['son']): ?><a href="<?php if(($vo['func_url']) == ""): ?>#<?php else: echo U($vo['func_url']); endif; ?>" <?php if(strpos($vo['func_url'],CONTROLLER_NAME) AND strpos($vo['func_url'],ACTION_NAME)): ?>class="active"<?php endif; ?>><i class="<?php echo ($vo["icon"]); ?>"></i> <?php echo ($vo["func_name"]); ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
								  <?php if(is_array($vo['son'])): $i = 0; $__LIST__ = $vo['son'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voSecond): $mod = ($i % 2 );++$i; if($voSecond['son']): ?><a href="<?php if(($voSecond['func_url']) == ""): ?>#<?php else: echo U($voSecond['func_url']); endif; ?>"  <?php if(strpos($voSecond['func_url'],CONTROLLER_NAME) AND strpos($voSecond['func_url'],ACTION_NAME)): ?>class="active"<?php endif; ?>><?php echo ($voSecond["func_name"]); ?> <span class="fa arrow"></span></a>
										<ul class="nav nav-third-level">
											<?php if(is_array($voSecond['son'])): $i = 0; $__LIST__ = $voSecond['son'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voThird): $mod = ($i % 2 );++$i;?><li>
												<a href="<?php if(($voThird['func_url']) == ""): ?>#<?php else: echo U($voThird['func_url']); endif; ?>"  <?php if(strpos($voThird['func_url'],CONTROLLER_NAME) AND strpos($voThird['func_url'],ACTION_NAME)): ?>class="active"<?php endif; ?>> <?php echo ($voThird["func_name"]); ?></a>
											</li><?php endforeach; endif; else: echo "" ;endif; ?>
										</ul>
									<?php else: ?>
										<a href="<?php if(($voSecond['func_url']) == ""): ?>#<?php else: echo U($voSecond['func_url']); endif; ?>"  <?php if(strpos($voSecond['func_url'],CONTROLLER_NAME) AND strpos($voSecond['func_url'],ACTION_NAME)): ?>class="active"<?php endif; ?>> <?php echo ($voSecond["func_name"]); ?></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </li>
                            </ul>
							<?php else: ?>
							<a href="<?php if(($vo['func_url']) == ""): ?>#<?php else: echo U($vo['func_url']); endif; ?>"  <?php if(strpos($vo['func_url'],CONTROLLER_NAME) AND strpos($vo['func_url'],ACTION_NAME)): ?>class="active"<?php endif; ?>><i class="<?php echo ($vo["icon"]); ?>"></i> <?php echo ($vo["func_name"]); ?></a><?php endif; ?>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
					
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
		
						
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">用户管理</h3>
						<div class="row">
                            <div class="col-md-4"><button type="button" class="btn btn-outline btn-primary" data-add-trigger><b>+</b> 创建新用户</button></div>
							<div class="col-md-4"></div>
                            <div class="col-md-4"><form method="get" action="<?php echo U('Admin/Operator/manage');?>">
								<div class="form-group input-group">
									<input type="text" class="form-control" name="keyword"  placeholder="输入用户名或平台名称可模糊查询...">
									<span class="input-group-btn">
										<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
									</span>
								</div></form>
							</div>
                        </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            用户列表
                        </div>

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>类别</th>
                                            <th>用户名</th>
                                            <th>平台名称</th>
                                            <th>充值折扣</th>
											<th>状态</th>
											<th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr role="row" key="<?php echo ($vo["uid"]); ?>" primary="uid">
                                            <td><?php echo ($vo["dict_name"]); ?></td>
                                            <td><?php echo ($vo["login_name"]); ?></td>
                                            <td><?php echo ($vo["user_name"]); ?></td>
                                            <td><?php echo ($vo["discount"]); ?>% off</td>
                                            <td><?php if($vo['status'] == 1): ?><i class="fa fa-set-status fa-check text-success" status="<?php echo ($vo["status"]); ?>" field="status"></i><?php else: ?><i class="fa fa-set-status fa-lock" status="<?php echo ($vo["status"]); ?>" field="status"><?php endif; ?></td>
                                            <td>
												<a href="aa.html" class="btn btn-app btn-primary no-radius edit_user" data-toggle="modal" data-target="#myModal">
													<i class="fa fa-edit fa-fw"></i>
													编辑
												</a>
												<?php if($vo['uid'] != 10001): ?><a href="javascript:void(0)" class="btn btn-app btn-primary no-radius reset_user_pwd" data-id="<?php echo ($vo["uid"]); ?>">
													<i class="fa fa-edit fa-fw"></i>
													重置密码
												</a>
												<a href="javascript:void(0)" class="btn btn-danger no-radius delete-user">
													<i class="fa fa-trash fa-fw"></i>
													删除
												</a><?php endif; ?>
											</td>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>									
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
            </div>
            <!-- /.row -->
			<div class="row">
                <div class="col-lg-12">
					<?php echo ($page); ?>
				</div>
			</div>
			
			<script type="text/tpl" id="resetPwdTpl">
				新密码: <input type="text" id="newPwd" length="20">
				确认新密码: <input type="text" id="reNewPwd" length="20">
			</script>

			<!---->
			<div id="add-user-modal" class="modal fade" tabindex="-1" role="dialog">
			  <form class="add-form">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <h4 class="modal-title">
						<span class="glyphicon glyphicon-user"></span>
						<span>创建用户</span>
					  </h4>
					</div>
					<div class="modal-body">
					  <div class="form-horizontal">
						<div class="form-group">
                            <label class="col-sm-3 control-label">类别：</label>
							<div class="col-sm-6">
								<?php if(is_array($user_roles)): $i = 0; $__LIST__ = $user_roles;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label class="radio-inline">
									<input type="radio" name="user_role" value="<?php echo ($vo["dict_id"]); ?>"><?php echo ($vo["dict_name"]); ?>
								</label><?php endforeach; endif; else: echo "" ;endif; ?>
							</div>
                        </div>
						<div class="form-group">
						  <label class="col-sm-3 control-label">登录名：</label>
						  <div class="col-sm-6">
							<input type="text" name="login_name" class="form-control" placeholder="请输入用户名">
						  </div>
						</div>
						<div class="form-group">
						  <label class="col-sm-3 control-label">登录密码：</label>
						  <div class="col-sm-6">
							<input type="password" name="login_pwd" class="form-control" placeholder="请输入登录密码" value>
						  </div>
						</div>
						<div class="form-group">
						  <label class="col-sm-3 control-label">平台名称：</label>
						  <div class="col-sm-6">
							<input type="text" name="user_name" class="form-control" placeholder="请输入平台名称" value>
						  </div>
						</div>
						<div class="form-group">
						  <label class="col-sm-3 control-label">充值折扣(%)：</label>
						  <div class="col-sm-6">
							<div class="input-group spinner" data-trigger="spinner" id="customize-spinner">
							  <input type="text" name="discount" class="form-control text-center" value="0" data-max="100" data-min="0" data-step="1" readonly>
							  <div class="input-group-addon">
								<a href="javascript:;" class="spin-up" data-spin="up"><i class="fa fa-caret-up"></i></a>
								<a href="javascript:;" class="spin-down" data-spin="down"><i class="fa fa-caret-down"></i></a>
							  </div>
							</div>
						  </div>
						</div>
						<div class="form-group">
						  <label class="col-sm-3 control-label">账号状态：</label>
						  <div class="col-sm-6">
								<label class="radio-inline">
									<input type="radio" name="status" value="1" checked="true">启用
								</label>
								<label class="radio-inline">
									<input type="radio" name="status" value="-1">禁用
								</label>
						  </div>
						</div>
						<div class="form-group">
						  <div class="col-sm-offset-3 col-sm-6">
							<button class="btn btn-primary submit-button" data-loading-text="创建中，请稍等...">立即创建</button>
						  </div>
						</div>
						<!-- 错误提示 -->
						<div class="form-group">
						  <div class="col-sm-offset-3 col-sm-6" data-error-box></div>
						</div>
					  </div>
					</div>
				  </div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			  </form>
			</div><!-- /.modal -->

	<script src="/Public/plugins/spinner/dist/jquery.spinner.js"></script>
	<script>
		(function ($) {
		  $('[data-add-trigger]').on('click', function(e){
			e.preventDefault();
			$('#add-user-modal').modal('show');
		  });
		$('.reset_user_pwd').click(function(){
			var tr = $(this).parents('tr');
			var id = tr.attr('key');
			resetPwd(id);
		});
		$('.delete-user').click(function(){
			var tr = $(this).parents('tr');
			var id = tr.attr('key');
			$.ShowConfirm('<span class="text-danger"><i class="fa  fa-warning fa-fw"></i>本操作不可逆！您确定删除该用户？</span>', function(){
				$.post("<?php echo U('Admin/Operator/del_user');?>",{"id":id},function(res){
					if(res.status){
						window.location.reload();
						return false;
					}
					$.ShowError(res.msg);
				},'json')
			},function(){},500,'确认？');
		});
		$(document).on('click','.fa-set-status',function(){
			var obj = $(this);
			if (obj.hasClass('fa-spinner')) {
				return false;
			}
			var tr = $(this).parents('tr');
			var query = new Object();
			query.field = $(this).attr('field');
			query.val = $(this).attr('status');
			query.key = tr.attr('key');
			obj.removeClass('fa-check fa-lock text-success').addClass('fa-spinner fa-spin');
			
			$.post('<?php echo U('Admin/Operator/set_status');?>', query, function(result){
				if(result.status){
					
					if(query.val == 1){
						obj.removeClass('text-success fa-spinner fa-spin').addClass('fa-lock').attr('status',-1);
					}else{
						obj.removeClass('fa-spinner fa-spin').addClass('fa-check text-success').attr('status',1);
					}
						setTimeout(function(){
							location.reload(true);
						},1);
				}else{
					if(query.val == -1){
						obj.removeClass('fa-spinner fa-spin').addClass('fa-lock').attr('status',-1);
					}else{
						obj.removeClass('fa-spinner fa-spin').addClass('fa-check text-success').attr('status',1);
					}
					$.ShowAlert(result.msg);
				}
			},'json');
		});
		  // 表单提交
		  $('.add-form').on('submit', function(e){
			e.preventDefault();
			var $form = $(this);
			
			var $userrole = $('input[name="user_role"]:checked',$form).val();
			var $loginname = $('input[name="login_name"]', $form).val();
			var $loginpwd = $('input[name="login_pwd"]', $form).val();
			var $username = $('input[name="user_name"]', $form).val();
			
			var $message = $('[data-error-box]', $form);
			var $button = $('.submit-button', $form);
			
			// 验证
			//console.log($userrole);
			if( !$userrole ){
			  showMessage($message, '请选择用户类别');
			  return false;
			}
			if( !$loginname || $loginname.length < 4 || $loginname.length > 16 ){
			  showMessage($message, '请输入4~16位登录名');
			  return false;
			}else if( !/^\w+$/.test($loginname) ){
			  showMessage($message, '登录名只能由数字、字母、下划线组成');
			  return false;
			}
			if( !$loginpwd || $loginpwd.length < 6 || $loginpwd.length > 16 ){
			  showMessage($message, '请输入6~16位密码');
			  return false;
			}else if( !/^\w+$/.test($loginpwd) ){
			  showMessage($message, '密码只能由数字、字母、下划线组成');
			  return false;
			}
			if( !$username ){
			  showMessage($message, '请输入平台名称');
			  return false;
			}
			/***
			if( !repwd ){
			  showMessage($message, '请再次输入6~10位密码');
			  return false;
			}else if( password != repwd ){
			  showMessage($message, '两次密码输入不一致');
			  return false;
			}
			***/
			var data = $(this).serialize();
			$button.button('loading');
			$.post('<?php echo U('Admin/Operator/add_user');?>', data, function(res){			  
			  if(res.status){
				$.ShowAlert(res.msg);
				setTimeout(function(){
				  window.location.href = res.url;
				}, 1500);
			  }else{
				$.showError(res.msg);
			  }
			  $button.button('reset');
			}, 'json');
		  });
		})(jQuery);

		var showMessage = function($target, msg){
			var html = [];
			html.push('<div class="alert alert-warning alert-dismissible" role="alert">');
			html.push('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
			html.push('<strong>' + msg + '</strong>');
			html.push('</div>');
			$target.html( html.join('') );
		};
		function resetPwd(id) {
			$.ShowConfirm($("#resetPwdTpl").html(), function(){
				var newPwd = $("#newPwd").val().trim();
				var reNewPwd = $("#reNewPwd").val().trim(); 
				//if(newPwd == '' || reNewPwd == ''){
				if( !newPwd || newPwd.length < 6 || newPwd.length > 16 ){
					$.showError("请输入6~16位重置后的密码!");
					return ;
				}else if( !/^\w+$/.test(newPwd) ){
				  $.showError('密码只能由数字、字母、下划线组成');
				  return false;
				}
				if(newPwd != reNewPwd){
					$.showError("两次密码输入不一致!");
					return ;
				}
				$.post("<?php echo U('Admin/Operator/reset_user_pwd');?>",{"id":id,"pwd":newPwd},function(res){
					$.ShowAlert(res.msg);
				},'json')
			},function(){},500,'确认重置该用户密码?');
		}
	</script>
			
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->


	<script type="text/javascript">
	$(function(){
		$('#logout').click(function(){
			//console.log('logout');
			 $.get('<?php echo U('Admin/Public/logout');?>',function(){
				location.reload();
			 });
		});
		$('#clear_cache').click(function(){
			 $.get('<?php echo U('Admin/Public/clear_cache');?>',function(){
				$.ShowAlert('更新缓存完毕');
			 });
		});
	})

	</script>
</body>

</html>