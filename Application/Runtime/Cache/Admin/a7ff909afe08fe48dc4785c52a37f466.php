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
                    <h3 class="page-header">充值统计</h3>
						<div class="row"><form method="post" action="<?php echo U('Admin/Index/stat');?>" id="stat_form">
                            <div class="col-md-2"><form>
								<div class="form-group input-group">
									<select name="type" class="form-control">
										<option value="month" <?php if(($stat_type) == "month"): ?>selected="selected"<?php endif; ?>>按月统计</option>
										<option value="year" <?php if(($stat_type) == "year"): ?>selected="selected"<?php endif; ?>>按年统计</option>
									</select>
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group input-group">
									<input type="submit" name="submitbtn" value="检索" class="btn btn-app btn-primary" />&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="submit" name="submitbtn" value="导出excel" class="btn btn-app btn-primary" />
								</div>
							</div></form>
                        </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            充值统计
                        </div>

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
							<?php if($stat_type == 'year'): ?><table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="15%">年份</th>
											<?php if(is_array($title)): $i = 0; $__LIST__ = $title;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><th><?php echo ($vo["user_name"]); ?>（<?php echo ($vo["discount"]); ?>% off）</th><?php endforeach; endif; else: echo "" ;endif; ?>
											<th>总计</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php $vo_total = 0; ?>
                                        <tr>
                                            <td><?php echo ($year); ?></td>
											<?php if(is_array($year_stat)): $i = 0; $__LIST__ = $year_stat;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $vo_total = $vo_total + $vo; ?>
                                            <td><?php echo ((isset($vo) && ($vo !== ""))?($vo):'0.00'); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                                            <td><?php echo number_format($vo_total, 2, '.', '');?></td>
                                        </tr>							
                                    </tbody>
                                </table><?php endif; ?>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="15%">日期（DATE）</th>
											<?php if(is_array($title)): $i = 0; $__LIST__ = $title;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><th><?php echo ($vo["user_name"]); ?>（<?php echo ($vo["discount"]); ?>% off）</th><?php endforeach; endif; else: echo "" ;endif; ?>
											<th>总计</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$stat_list): $mod = ($i % 2 );++$i; $vo_total = 0; ?>
                                        <tr>
                                            <td><?php echo ($stat_list["date"]); ?></td>
											<?php if(is_array($stat_list['stat'])): $i = 0; $__LIST__ = $stat_list['stat'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $vo_total = $vo_total + $vo; ?>
                                            <td><?php echo ((isset($vo) && ($vo !== ""))?($vo):'0.00'); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                                            <td><?php echo number_format($vo_total, 2, '.', '');?></td>
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
			<!-- 
			<div class="row">
                <div class="col-lg-12">
					<?php echo ($page); ?>
				</div>
			</div>
			-->
			
			

			
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