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
                    <h3 class="page-header">充值记录</h3>
						<div class="row">
							<form action="<?php echo U('Admin/Deposit/index');?>">
							<?php if($login_user['user_role'] == 100110 ): ?><div class="col-md-2">
									<div class="form-group input-group">
										<a href="javascript:void(0);" class="btn btn-outline btn-primary" data-add-trigger><b>+</b> 添加</a>
									</div><?php endif; ?>
								</div>
								<div class="col-md-2">
									<select name="operator_id"  class="form-control">
									<option value="">请选择平台</option>
									<?php if(is_array($user_roles)): $i = 0; $__LIST__ = $user_roles;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($param['operator_id']) == $key): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</div>
							</if>
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
										<input type="text" class="form-control" name="deposit_sn" value="<?php echo ($param["deposit_sn"]); ?>" placeholder="输入流水号进行检索..." />
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
                            充值记录
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>流水号</th>
                                            <th>日期（DATE）</th>
                                            <th>平台</th>
                                            <th>充值金额</th>
											<th>折扣</th>
											<th>获得游戏币</th>
											<th>充值状态</th>
											<th>游戏币总计</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                            <td><?php echo ($vo["sn"]); ?></td>
                                            <td><?php echo ($vo["create_time"]); ?></td>
											<td><?php echo ($vo["user_name"]); ?></td>
                                            <td><?php echo ($vo["amount"]); ?></td>
                                            <td><?php echo ($vo["discount"]); ?>%</td>
											<td><?php echo ($vo["gold"]); ?></td>
											<td><?php if($vo['status'] == 1): ?><font color="#5cb85c">完成</span><else><?php if($vo['status'] == 0): ?><font color="#f0ad4e">进行中</span><else><font color="#d9534f">失败</span><?php endif; endif; ?></td>
											<td><?php echo ($vo["total_gold"]); ?></td>
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
			<?php if($login_user['user_role'] == 100110 ): ?><!---->
			<div id="add-order-modal" class="modal fade" tabindex="-1" role="dialog">
			  <form class="add-form">
				<div class="modal-dialog">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <h4 class="modal-title">
						<span class="glyphicon glyphicon-yen"></span>
						<span>添加充值记录</span>
					  </h4>
					</div>
					<div class="modal-body">
					  <div class="form-horizontal">
						<div class="form-group">
                            <label class="col-sm-3 control-label">平台：</label>
							<div class="col-sm-6">
								<select name="operator_id" id="operator_id" class="form-control">
									<option value="">请选择平台</option>
									<?php if(is_array($user_roles)): $i = 0; $__LIST__ = $user_roles;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($param['operator_id']) == $key): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
							</div>
                        </div>
						<div class="form-group">
						  <label class="col-sm-3 control-label">折扣：</label>
						  <div class="col-sm-3">
							<input type="text" name="discount" id="discount" class="form-control" value="0%" readonly>
						  </div>
						</div>
						<div class="form-group">
						  <label class="col-sm-3 control-label">充值金额：</label>
						  <div class="col-sm-6">
							<input type="text" name="amount" id="amount" class="form-control" onkeyup="value=value.replace(/[^\d.]/g,'')" placeholder="请输入充值金额">
						  </div>
						</div>
						<div class="form-group">
						  <label class="col-sm-3 control-label">游戏币：</label>
						  <div class="col-sm-6">
							<input type="text" name="gold" id="gold" class="form-control" value="0" readonly>
						  </div>
						</div>
						
						<div class="form-group">
						  <label class="col-sm-3 control-label">备注(选填)：</label>
						  <div class="col-sm-6">
							<input type="text" name="remark" id="remark" class="form-control" maxlength="250"/>
						  </div>
						</div>
						<div class="form-group">
						  <div class="col-sm-offset-3 col-sm-6">
							<button class="btn btn-primary submit-button" data-loading-text="添加中，请稍等...">立即添加</button>
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
			</div><!-- /.modal --><?php endif; ?>
			<!-- Metis Menu Plugin JavaScript -->
			<script src="/Public/bower_components/metisMenu/dist/metisMenu.min.js"></script>

			<!-- Page-Level Demo Scripts - Tables - Use for reference -->
			<script src="/Public/plugins/daterangepicker/moment.min.js"></script>
			<script src="/Public/plugins/daterangepicker/daterangepicker.js"></script>
			<!-- Page-Level Demo Scripts - Tables - Use for reference -->
			<script>
				$(document).ready(function() {
						<?php if($login_user['user_role'] == 100110 ): ?>$('[data-add-trigger]').on('click', function(e){
							e.preventDefault();
							$('#add-order-modal').modal('show');
						});
						
						$('select[name=operator_id]').change(function(){
						
							var op_id = $(this).val();
							
							$('#discount').val('0' + '%');
							
							$('#gold').val(0);
							
							if(op_id == ''){
								return false;
							}
							
							$.post('<?php echo U('Admin/Deposit/get_operator_discount');?>', {operator_id:op_id}, function(res){
							
								if(res.status){
								
									var discount = parseFloat(res.discount);
									var amount = parseInt($('#amount').val());
																		
									if(amount > 0){
										var gold = amount + (amount * (discount / 100));
										$('#gold').val(gold);
									}
									
									discount = res.discount + '%';
									$('#discount').val(discount);
									
								}else{
								
									$.showError(res.msg);
									
								}
								
							});
						});
						$('input[name=amount]').blur(function(){
							var amount = parseFloat($(this).val());
							var discount = parseFloat($('#discount').val());
							console.log(discount);
							if(amount >= 0 && discount != NaN){
								var gold = amount + (amount * (discount / 100));
								$('#gold').val(gold);
							}
						});<?php endif; ?>
						$('input[name=date-range-picker]').daterangepicker({
							format: 'YYYY-MM-DD',
							startDate: '<?php echo ($param["begin_time"]); ?>',
							endDate: '<?php echo ($param["end_time"]); ?>',
						}).prev().on('click', function(){
							$(this).next().focus();
						});

					<?php if($login_user['user_role'] == 100110 ): ?>// 表单提交
					  $('.add-form').on('submit', function(e){
						e.preventDefault();
						var $form = $(this);
						
						var $operator_id = $('#operator_id').val();
						var $amount = $('input[name="amount"]', $form).val();
						var $discount = $('input[name="discount"]', $form).val();
						var $gold = $('input[name="gold"]', $form).val();
						var $remark = $('input[name="remark"]', $form).val();

						var $message = $('[data-error-box]', $form);
						var $button = $('.submit-button', $form);
						
						// 验证
						if( !$operator_id || $operator_id == '' || $operator_id == null){
						  showMessage($message, '请选择平台');
						  return false;
						}
						
						if( !$amount || $amount <= 0 ){
						  showMessage($message, '请输入要充值的金额，不能小于等于0');
						  return false;
						}

						if( !$gold || $gold <= 0 ){
						  showMessage($message, '请输入要充值的金额，不能小于等于0');
						  return false;
						}

						if( $remark != '' && $remark.length >= 251 ){
						  showMessage($message, '备注信息请不要超过251个字符');
						  return false;
						}
						var data = $(this).serialize();
						$button.button('loading');
						$.post('<?php echo U('Admin/Deposit/add');?>', data, function(res){			  
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
					  });<?php endif; ?>

				});
				var showMessage = function($target, msg){
					var html = [];
					html.push('<div class="alert alert-warning alert-dismissible" role="alert">');
					html.push('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
					html.push('<strong>' + msg + '</strong>');
					html.push('</div>');
					$target.html( html.join('') );
				};
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