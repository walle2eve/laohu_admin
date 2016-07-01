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
                    <h3 class="page-header">提现记录</h3>
						<div class="row">
							<form action="<?php echo U('Admin/Withdraw/index');?>">
								<?php if($login_user['user_role'] == 100110 ): ?><div class="col-md-2">
									<select name="operator_id"  class="form-control">
									<option value="">请选择平台</option>
									<?php if(is_array($user_roles)): $i = 0; $__LIST__ = $user_roles;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($param['operator_id']) == $key): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</div><?php endif; ?>
								<div class="col-md-3">
									<div class="form-group input-group">
										<span class="add-on input-group-addon">
											<i class="glyphicon glyphicon-calendar fa fa-calendar">
											</i>
										</span>
										<input class="form-control" type="text" readonly name="date-range-picker" id="id-date-range-picker-1" value="<?php echo ($param["begin_time"]); ?> - <?php echo ($param["end_time"]); ?>"/>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group input-group">
										<input type="text" class="form-control" name="keyword" value="<?php echo ($param["keyword"]); ?>" placeholder="输入用户名或流水号进行检索..." />
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group input-group">
										<input type="submit" name="submitbtn" class="btn btn-primary" value="检索"/>
										&nbsp;&nbsp;&nbsp;
										<input type="submit" name="submitbtn" class="btn btn-primary" value="导出excel"/>
									</div>
								</div>
							</form>
                        </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                             提现记录
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>流水号</th>
                                            <th>日期（DATE）</th>
                                            <th>提现平台</th>
                                            <th>用户名</th>
											<th>提现游戏币数量</th>
											<th>提现进度</th>
											<th>提现反充游戏币</th>
											<th>游戏币总计</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                            <td><?php echo ($vo["sn"]); ?></td>
                                            <td><?php echo ($vo["create_time"]); ?></td>
											<td><?php echo ($vo["user_name"]); ?></td>
											<td><?php echo ($vo["account_id"]); ?></td>
                                            <td><?php echo ($vo["amount"]); ?></td>
                                            <td><?php if($vo['status'] == 1): ?><font color="#5cb85c">完成</span><else><?php if($vo['status'] == 0): ?><font color="#f0ad4e">进行中</span><else><font color="#d9534f">失败</span><?php endif; endif; ?></td>
											<td><?php echo ($vo["bet"]); ?></td>
											<td><?php echo ($vo["line"]); ?></td>
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


			<!-- Metis Menu Plugin JavaScript -->
			<script src="/Public/bower_components/metisMenu/dist/metisMenu.min.js"></script>

			<script src="/Public/plugins/daterangepicker/moment.min.js"></script>
			<script src="/Public/plugins/daterangepicker/daterangepicker.js"></script>

			<script>
			$(document).ready(function() {
						$('input[name=date-range-picker]').daterangepicker({
							format: 'YYYY-MM-DD',
							startDate: '<?php echo ($param["begin_time"]); ?>',
							endDate: '<?php echo ($param["end_time"]); ?>',
						}).prev().on('click', function(){
							$(this).next().focus();
						});
			});
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