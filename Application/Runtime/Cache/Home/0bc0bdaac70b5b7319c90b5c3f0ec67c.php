<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html><html>    <head>        <meta charset="utf-8" />        <title>多友财支付平台</title>		<meta name="keywords" content="多友财,多友财支付平台,多友财商户平台" />		<meta name="description" content="多友财,多友财支付平台,多友财商户平台" />        <meta name="viewport" content="width=device-width, initial-scale=1.0" />		<link rel="icon" href="/icon.ico">        <link href="/duoyoucai/Public/assets/css/bootstrap.min.css" rel="stylesheet" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/font-awesome.min.css" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/ace.min.css" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/shanghu.css" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/pageGroup.css" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/bootstrap-datetimepicker.min.css" />        <!--[if IE 7]>        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/font-awesome-ie7.min.css" />        <![endif]-->        <!--[if lte IE 8]>        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/ace-ie.min.css" />        <![endif]-->        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->        <!--[if lt IE 9]>        <script src="/duoyoucai/Public/assets/js/html5shiv.js"></script>        <script src="/duoyoucai/Public/assets/js/respond.min.js"></script>        <![endif]-->    </head>    <body>        <div class="navbar navbar-default" id="navbar">            <script type="text/javascript">                try {                    ace.settings.check( 'navbar','fixed' )                } catch ( e ) {                }            </script>            <!--head_start-->            <div class="navbar-container" id="navbar-container">                <div class="navbar-header pull-left">                    <a href="/duoyoucai/index.php" class="navbar-brand">                       <small><img src="/duoyoucai/Public/assets/images/i.png" alt=""> 多友财支付平台</small>                    </a>                </div>                <div class="pull-left tenant_info">                   用户名：                    <?php if(session('flag') == 1 & session('servicestoretype')==1){ ?>                    <a href="/duoyoucai/index.php/Conff/infoDetail"><?php if(!empty($_SESSION['data']['Customer'])): echo ($_SESSION['data']['Customer']); else: echo ($_SESSION['data']['LoginName']); endif; ?></a>                    <?php }else if( session('data')['CustomersType']==1){ ?>                    <a href="/duoyoucai/index.php/Conff/MerchantDetail"><?php if(!empty($_SESSION['data']['Customer'])): echo ($_SESSION['data']['Customer']); else: echo ($_SESSION['data']['LoginName']); endif; ?></a>                    <?php }else{ ?>                    <a><?php if(!empty($_SESSION['data']['Customer'])): echo ($_SESSION['data']['Customer']); else: echo ($_SESSION['data']['LoginName']); endif; ?></a>                    <?php } ?>                </div>                <div class="pull-left tenant_info2">名称：<?php if(!empty($_SESSION['data']['DisplayName'])): echo ($_SESSION['data']['DisplayName']); else: echo ($_SESSION['data']['CustomerName']); endif; ?></div>                <div class="navbar-header pull-right" role="navigation">                    <a href="/duoyoucai/index.php/Conff/password"> <i class="icon-key"></i>                        修改密码                    </a>                    <a href="/duoyoucai/index.php/Login/logout">                        <i class="icon-off"></i>                        退出                    </a>                </div>            </div>        </div>        <!--head_end-->        <!--left_start-->        <div class="main-container" id="main-container">            <script type="text/javascript">                try {                    ace.settings.check( 'main-container','fixed' )                } catch ( e ) {                }            </script>            <div class="main-container-inner">                <a class="menu-toggler" id="menu-toggler" href="index.html">                    <span class="menu-text"></span>                </a>                <div class="sidebar" id="sidebar">                    <script type="text/javascript">                        try {                            ace.settings.check(                                'sidebar'                                ,'fixed' )                        } catch ( e ) {                        }                    </script>                                        <div class="sidebar-shortcuts" id="sidebar-shortcuts">                        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">                            <span class="btn btn-success"></span>                            <span class="btn btn-info"></span>                            <span class="btn btn-warning"></span>                            <span class="btn btn-danger"></span>                        </div>                    </div>                    <asd class="nav nav-list">                    <?php if(is_array($list3)): $k1 = 0; $__LIST__ = $list3;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($k1 % 2 );++$k1; if(in_array("CONTROLLER_NAME", $list3)){ ?>										 <li class="active"><?php }else{ ?>					<li>					<?php } ?>                        <!-- <?php if(CONTROLLER_NAME == 'Conff'): ?><li class="active">                                <?php else: ?>                            <li><?php endif; ?> -->                        <a href="#" class="dropdown-toggle">                         <img src="/duoyoucai/Public/assets/images/ico_cloud.png" alt="">                                <span class="menu-text"><?php echo ($vo1); ?></span> <b class="arrow icon-angle-down"></b>                        </a>                        <ul class="submenu">                        <?php if(is_array($list)): $k = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($vo['D'] == $vo1 ): if(in_array("ACTION_NAME", $newarr)){ ?>							<li class="active"><?php }else{ ?>							<li>							<?php } ?>                                <a href="/duoyoucai/index.php<?php echo ($vo['U']); ?>">                                    <i class="icon-double-angle-right"></i><?php echo ($vo['R']); ?><br/>                                </a>                                </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>                        </ul><?php endforeach; endif; else: echo "" ;endif; ?> 										<input type="hidden" value ="<?php echo ($_SERVER['REQUEST_URI']); ?>" id = "URL">                    <div class="sidebar-collapse" id="sidebar-collapse">                        <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>                    </div>                    <script type="text/javascript">                        try {                            ace.settings.check(                                'sidebar'                                ,'collapsed' )                        } catch ( e ) {                        }												var menu = document.querySelectorAll("a");						var href = document.getElementById("URL").value;						for(var i = 0; i < menu.length; i++){							if(menu[i].getAttribute("href")==href&&menu[i].getAttribute("href")!='/index.php'&&menu[i].getAttribute("href")!='/index.php/Conff/infoDetail'&&menu[i].getAttribute("href")!='/index.php/Conff/MerchantDetail'&&menu[i].getAttribute("href")!='/index.php/Conff/password'){								menu[i].style.color = '#3b85e5';								menu[i].parentNode.parentNode.style.display='block';							}						}                    </script>                </div>

            <div class="main-content">

                <div class="breadcrumbs" id="breadcrumbs">

                    <script type="text/javascript">try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}</script>

                    <ul class="breadcrumb">

                        <li>

                            <i class="icon-home home-icon"></i>

                            <a href="/duoyoucai/index.php">首页</a>

                        </li>

                        <li>交易订单查询</li>

                    </ul>

                </div>

                <div class="page-content">

                    <div class="page-header">

                        <h1>交易订单查询</h1>

                    </div>

                    <div class="row">

                        <div class="col-xs-12 sx-search">
<form id="searchform" name="searchform" method="post">

                                <div class="col-md-6 col-xs-12 mr_mab">
                                    
                                    <div class="form-group">

                                        <label for="dtp_input1" class="control-label col-sm-4" >开始时间</label>

                                        <div class="col-sm-8 input-group">

                                            
                                            <input type="text" id="Time_Start" name="Time_Start" value="<?php echo date('Y-m-d',time()).' 00:00:00'; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" class="form-control"/>
                                            <span class="input-group-addon">

                                                <span class="glyphicon glyphicon-time"></span>

                                            </span>


                                        </div>

                                        <input type="hidden" id="dtp_input1" name="dtp_input1" value="<?php echo date('Y-m-d',time()); ?>" />
                                        <input type="hidden" id="type" value="<?php echo ($_SESSION['data']['CustomersType']); ?>" />
                                        <input type="hidden" id="serstafftype" value="<?php echo (session('servicestoretype')); ?>" />
                                        <input type= "hidden" value = "" id ="SystemUserSysNo" name = "SystemUserSysNo">
										<input type="hidden" id="flag" value="<?php echo (session('flag')); ?>" />
                                        <input type= "hidden" value = "" id ="Customer" name = "Customer">

                                    </div>

                                </div>

                                <div class="col-md-6 col-xs-12 mr_mab">

                                    <div class="form-group">

                                        <label for="sx-2" class="control-label col-sm-4">结束时间</label>

                                        <div class="col-sm-8 input-group">

                                            <input type="text" id="Time_End" name="Time_End" value="<?php echo date('Y-m-d',time()).' 23:59:59'; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" class="form-control"/>
                                            <span class="input-group-addon">

                                                <span class="glyphicon glyphicon-time"></span>

                                            </span>

                                        </div>


                                    </div>

                                </div>

                                <div class="col-md-6 col-xs-12 mr_mab">

                                    <div class="form-group">

                                        <label for="sx-8" class="control-label col-sm-4">订单号</label>

                                        <div class="col-sm-8">

                                            <input type="text" id="ordernum" name = "ordernum" class="form-control" placeholder="订单号"></div>

                                    </div>

                                </div>
                                <?php if(session('data')['CustomersType']==1&session('flag')==0){ ?>
                                <div class="col-md-6 col-xs-12 mr_mab StoreName" id="StoreName">

                                    <div class="form-group">

                                        <label for="sx-8" class="control-label col-sm-4">员工登录名</label>

                                        <div class="col-sm-8">
                                            <input type="text" id="storename" name="storename" class="form-control" placeholder="员工登录名" required="required"></div>
                                    </div>
                                    <div class="form-group " id="CheckStoreUser">

                                        <label for="sx-8" class="control-label col-sm-4"></label>

                                    </div>

                                </div>
                                <div class="col-md-6 col-xs-12 mr_mab StoreName" id="StoreName">

                                    <div class="form-group">

                                        <label for="sx-8" class="control-label col-sm-4">员工名称</label>

                                        <div class="col-sm-8">
                                            <input type="text" id="CustomerNames" name="CustomerNames" class="form-control" placeholder="员工名称" required="required"></div>
                                    </div>

                                </div>
                                <?php }else{ ?>
                                <div class="col-md-6 col-xs-12 mr_mab StoreName" id="StoreName">

                                    <div class="form-group">

                                        <label for="sx-8" class="control-label col-sm-4">商户用户名</label>

                                        <div class="col-sm-8">
                                            <input type="text" id="storename" name="storename" class="form-control" placeholder="商户用户名" required="required"></div>
                                    </div>
                                    <div class="form-group " id="CheckStoreUser">

                                        <label for="sx-8" class="control-label col-sm-4"></label>

                                    </div>

                                </div>
                                <div class="col-md-6 col-xs-12 mr_mab StoreName" id="StoreName">

                                    <div class="form-group">

                                        <label for="sx-8" class="control-label col-sm-4">商户名称</label>

                                        <div class="col-sm-8">
                                            <input type="text" id="CustomerNames" name="CustomerNames" class="form-control" placeholder="商户名称" required="required"></div>
                                    </div>

                                </div>
                                <?php } ?>


								<!-- 2016/7/11 -->

								<!-- 2016/7/11 -->

                                <div class="col-md-6 col-xs-12 mr_mab">
    <div class="form-group">
        <label for="sx-2" class="control-label col-sm-4">交易类型</label>
        <div class="col-sm-8 input-group posi">
            <input type="text" id="" name="" readonly="readonly" class="form-control list" placeholder="全部">
            <span class="input-group-addon bor">
                                                <span class="glyphicon glyphicon-chevron-down"></span>
                                            </span>
            <ul class="l-list">
                <li>
                    <label>
                        <input type="radio" name="Ordertype" checked="checked" value="" class="ace">
                        <span class="lbl">全部</span>
                        &emsp;&emsp;
                    </label>
                </li>

                <?php foreach(json_decode($_COOKIE['passageway_list'],true) as $key=>$val){ ?>
                <li>
                    <label>
                        <input type="radio" name="Ordertype" value="<?php echo $val['T']; ?>" class="ace">
                        <span class="lbl"><?php echo $val['N']; ?></span>
                    </label>
                </li>
                <?php } ?>

            </ul>
        </div>

    </div>

</div>

                                            



								<!-- 2016/7/11 -->
                                <div class="col-md-12 col-xs-12">
								<input type ="hidden" id = "buttontype" name="buttontype" value =0>
                                    <div class="form-group txrimar">

                                        <a href="#" class="btn btn-primary" id ="search">

                                            <i class="icon-search"></i>

                                            查询

                                        </a>
										&nbsp;

										<a href="#" class="btn btn-primary" id ="allcount">

                                            <i class="icon-search"></i>

                                            汇总

                                        </a>
                                        &nbsp;
                                        <a href="#" type="submit" class="btn btn-success" id="download" onclick="checkaction(0);" >

                                            <i class="icon-download-alt" ></i>

                                            下载报表

                                        </a>
                                        <input type="hidden" value="" id="input_hidden" name="input_hidden">

                                    </div>

                                </div>
</form>
                                <div class="clearfix"></div>

                                <div class="row">

                                    <div class="table-header">查询结果 
									<?php if((session('data')['CustomersType']==1&session('flag')==0)||(session('servicestoretype')==1&session('flag')==1)||(session('data')['CustomersType']==0&session('flag')==0)||(session('servicestoretype')==0&session('flag')==1)){ ?>
									<div> 
										<span>交易总金额：<strong id = "total_fee"></strong> &emsp; 实际交易总金额：<strong id="cash_fee"></strong>&emsp; 交易笔数: <strong id = "tradecount"></strong></span>
                                        <a href="#" class="btn btn-minier btn-yellow" id ="search"> <i class="icon-search"></i>
                                            查询总交易
                                        </a>
                                        </div>
									<?php } ?>
									</div>

                                    <div class="col-xs-12">

                                        <div class="table-responsive">

                                            <table id="sample-table-1" class="table table-striped table-bordered table-hover">

                                                <thead>

                                                    <tr id = "info_name">

                                                        

                                                        <th>商户名称</th>
														<?php if(session( 'data')['CustomersType']==1){ ?>
														
														<th>登陆名</th>

                                                        <th>真实姓名</th>
														
														<?php } ?>

                                                        <th>订单号</th>

                                                        <th>交易类型</th>

                                                        <th>交易金额</th>

                                                        <th>折后金额</th>

                                                        <th>交易币种</th>

                                                        <th>交易时间</th>

                                                    </tr>

													<tr id = "total_name"  style = "display:none">
													<?php if(session('data')['CustomersType']==1&session('flag')==0){ ?>
														<th>登录名称</th>
														<th>员工名称</th>
													<?php } if( session('data')['CustomersType']==0&session('flag')==0 ) { ?>
														<th>商户名称</th>
													<?php }if(session('servicestoretype')==0&session('flag')==1){ ?>
														<th>商户名称</th>
													<?php } if(session('servicestoretype')==1&session('flag')==1){ ?>
														<th>员工名称</th>

													<?php } ?>

														<th>交易金额</th>
														
														<th>实际交易金额</th>

														<th>交易币种</th>
														
														<th>交易笔数</th>

													</tr>

                                                </thead>

                                                <tbody id = "info">

                                                    <!-- <tr>

                                                        <td>01</td>

                                                        <td>021355123541</td>

                                                        <td>微信</td>

                                                        <td>58.00</td>

                                                        <td>人民币</td>

                                                        <td>2016-05-26</td>

                                                    </tr> -->

                                                </tbody>

                                            </table>
                                        <input type="hidden" id="aa" value=1 >

											<div class="page_new">
                                                <input type = "hidden" id = "totalCount" value= "">
                                                 <a id="prev">上一页</a>  <a id = "next">下一页</a> <a id = "first">最前页</a> <a id = "last">最末页</a>
                                                <select id = "SelectNo">
                                                    
                                                </select>
                                                <span>总 <label id = "TotalCount"></label> 条</span> <span>分为 <label id = "TotalPage"></label> 页</span> <span>当前第 <label id ="NowPage">1</label> 页</span><input type= "hidden" id= "PageSize" value=10>
    											
                                            </div>
										</div>
											</div>

                                        </div>

                                    </div>




                                </div>


                        </div>

                    </div>

                </div>

            </div>

        </div>

        <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">

            <i class="icon-double-angle-up icon-only bigger-110"></i>

        </a>

    </div>

  <!-- jquery库调用-start -->  <!-- <script src="/duoyoucai/Public/assets/js/jquery-2.0.3.min.js"></script> -->  <script src="/duoyoucai/Public/assets/js/jquery.js"></script>  <script src="/duoyoucai/Public/assets/js/ace-extra.min.js"></script>  <script src="/duoyoucai/Public/assets/js/jquery.mobile.custom.min.js"></script>  <script src="/duoyoucai/Public/assets/js/bootstrap.min.js"></script>  <script src="/duoyoucai/Public/assets/js/typeahead-bs2.min.js"></script>  <script src="/duoyoucai/Public/assets/js/jquery.dataTables.min.js"></script>  <script src="/duoyoucai/Public/assets/js/jquery.dataTables.bootstrap.js"></script>  <script src="/duoyoucai/Public/assets/js/ace-elements.min.js"></script>  <script src="/duoyoucai/Public/assets/js/ace.min.js"></script>  <script src="/duoyoucai/Public/assets/js/date-time/bootstrap-datetimepicker.min.js"></script>  <script src="/duoyoucai/Public/assets/js/jquery.validate.min.js"></script>  <script src="/duoyoucai/Public/assets/js/page.js"></script>  <!-- <script src="/duoyoucai/Public/assets/js/pageGroup.js"></script> -->  <script src="/duoyoucai/Public/assets/js/timestamp.js"></script> <script src="/duoyoucai/Public/assets/js/common.js"></script> <script src="/duoyoucai/Public/assets/js/My97DatePicker_v0.0.1/WdatePicker.js"></script> <script src="/duoyoucai/Public/assets/js/OrderType.js"></script>  <!-- jquery库调用-end -->

    <script type="text/javascript">

        jQuery(function($) {

            var oTable1 = $('#sample-table-2').dataTable( {

            "aoColumns": [

                { "bSortable": false },

                null, null,null, null, null,

              { "bSortable": false }

            ]

        });

        

        $('table th input:checkbox').on('click' , function(){

          var that = this;

          $(this).closest('table').find('tr > td:first-child input:checkbox')

          .each(function(){

            this.checked = that.checked;

            $(this).closest('tr').toggleClass('selected');

          });

        });

      

        $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

        function tooltip_placement(context, source) {

          var $source = $(source);

          var $parent = $source.closest('table')

          var off1 = $parent.offset();

          var w1 = $parent.width();

          var off2 = $source.offset();

          var w2 = $source.width();

          if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';

          return 'left';

        }

        })

    </script>

    <script>

    var reg_phone = /^0?1[3|4|5|8][0-9]\d{8}$/;

    var reg_email = /.+@.+\.[a-zA-Z]{2,4}$/;

    $("#sx-2").blur(function(){

      if(reg_email.test($("#sx-2").val()) || $("#sx-2").val()==""){

        $("#email").hide();

      }

      else{

        $("#email").show();

      }

    }).focus(function(){

      $(this).triggerHandler("blur");

    }).keyup(function(){

      $(this).triggerHandler("blur");

    });

    $("#sx-3").blur(function(){

      if(reg_phone.test($("#sx-3").val()) || $("#sx-3").val()==""){

        $("#phone").hide();

      }

      else{

        $("#phone").show();

      }

    }).focus(function(){

      $(this).triggerHandler("blur");

    }).keyup(function(){

      $(this).triggerHandler("blur");

    });    

    </script>

    <script type="text/javascript">

    $('.form_date').datetimepicker({

        language:  'fr',

        weekStart: 1,

        todayBtn:  1,

        autoclose: 1,

        todayHighlight: 1,

        startView: 2,

        minView: 2,

        forceParse: 0

    });



    
    
    </script>
	<script>
	
	function infoview(PageNumber,PageSize){
			var Time_Start = $("#Time_Start").val();
			var Time_end = $("#Time_End").val();
			var out_trade_no = $("#ordernum").val();
			var CustomerNames = $("#CustomerNames").val();
			var Ordertype = $("input[type='radio']:checked").val();
			var buttontype =$("#buttontype").val();
			
			if(Customername){
			Customer = Customername;
			}else{
			Customer = $("#storename").val();
			}
			var tt="";
			 if (this.ajaxRequest_ != undefined && this.ajaxRequest_.readyState < 4) {
						return false;
			}

			this.ajaxRequest_ =
			$.post("/duoyoucai/index.php/Order/ordersearch",{Time_Start:Time_Start,Time_End:Time_end,out_trade_no:out_trade_no,Customer:Customer,PageNumber:PageNumber,PageSize:PageSize,SystemUserSysNo:SystemUserSysNo,CustomerNames:CustomerNames,Ordertype:Ordertype,ButtonType:buttontype},function(data){ 
					if(data.totalCount>0){
					$(".page_new").show();
					$.each(data.model, function(k, v) {
					
					if(data.ButtonType==0){
					<?php if(session( 'data')['CustomersType']==1){ ?>

					tt += "<tr><td>"+v.CustomerName+"</td><td>"+v.loginname+"</td><td>"+v.displayname+"</td><td>"+v.Out_trade_no+"</td><td>"+v.Pay_Type+"</td><td>"+v.Total_fee+"</td><td>"+v.Cash_fee+"</td><td>人民币</td><td>"+v.Time_Start+"</td></tr>";

					<?php }else{ ?>

					tt += "<tr><td>"+v.CustomerName+"</td><td>"+v.Out_trade_no+"</td><td>"+v.Pay_Type+"</td><td>"+v.Total_fee+"</td><td>"+v.Cash_fee+"</td><td>人民币</td><td>"+v.Time_Start+"</td></tr>";

					<?php } ?>
					}else if(data.ButtonType==1){
						<?php if(session('data')['CustomersType']==1&session('flag')==0){ ?>
						tt += "<tr><td>"+v.loginname+"</td><td>"+v.displayname+"</td><td>"+v.total_fee+"</td><td>"+v.fee+"</td><td>"+v.cash_fee_type+"</td><td>"+v.tradecount+"</td></tr>";
						<?php } if( session('data')['CustomersType']==0&session('flag')==0 ) { ?>
						tt += "<tr><td>"+v.customername+"</td><td>"+v.total_fee+"</td><td>"+v.fee+"</td><td>"+v.cash_fee_type+"</td><td>"+v.tradecount+"</td></tr>";
						<?php }if(session('servicestoretype')==0&session('flag')==1){ ?>
						tt += "<tr><td>"+v.customername+"</td><td>"+v.total_fee+"</td><td>"+v.fee+"</td><td>"+v.cash_fee_type+"</td><td>"+v.tradecount+"</td></tr>";
						<?php } if(session('servicestoretype')==1&session('flag')==1){ ?>
						tt += "<tr><td>"+v.displayname+"</td><td>"+v.total_fee+"</td><td>"+v.fee+"</td><td>"+v.cash_fee_type+"</td><td>"+v.tradecount+"</td></tr>";
						<?php } ?>
					
					
					}
					});
			$('#info').html(tt);
			$('#TotalCount').html(data.totalCount);
			TotalPage = Math.ceil(data.totalCount/PageSize);
			var ff ="";
			for (i=1;i<=TotalPage ;i++ )
			{
				ff+="<option value = \""+i+"\">"+i+"</option>";
			}
			$('#SelectNo').html(ff);
			$('#TotalPage').html(TotalPage);
			$('#PageNumber').html(PageNumber);
			$('#SelectNo').val(PageNumber+1);
			$('#NowPage').text(PageNumber+1);
			$(".page_new").show();
		

			}else{
			$('#info').html("");
			$(".page_new").hide();
			}
            //alert(1111);
            
           }); 
	
	}
	var total,totalPage,pageStr; //总记录数，每页显示数，总页数
	var PageSize = $("#PageSize").val();
	var Customername = GetQueryString ("Customer");
	var SystemUserSysNo = GetQueryString ("SystemUserSysNo");
    // var Customer = GetQueryString ("Customer");

    var Cn = parseInt(Customername);
    $("#Customer").val(Cn);
    //     var bbb =$("#Customer").val();
    // alert(bbb);
    var Sn = parseInt(SystemUserSysNo);
	$("#SystemUserSysNo").val(Sn);
	// var bbb =$("#aaa").val();
	// alert(bbb);
	$(".page_new").hide();
	$("#storename").val();
	$("#buttontype").val(0);
		if(Customername){
			$("#storename").val(Customername);
			$("#storename").attr("disabled",true);
			infoview(0,PageSize);
			
		}
		if(SystemUserSysNo){
			infoview(0,PageSize);
			
		}

		$("#CheckStoreUser").hide();
		var type = $("#type").val();
		var flag = $("#flag").val();
		var serstafftype = $("#serstafftype").val();
		if(serstafftype==1&&flag==1){//商户下的员工
		infoview(0,PageSize);
		}
		if(type==1&&type){
		
		infoview(0,PageSize);
		}
	$("#search").click(function(){
		$('#info_name').show();
		$('#total_name').hide();
		$("#input_hidden").val("a");
		$("#buttontype").val(0);
		$('#info').html("");
		if(type==0&&type){
		var Customer = $("#storename").val();
			var Customer = $("#storename").val();
		}else if(serstafftype==0&&flag==1){
			var Customer = $("#storename").val();
		}
		$("#CheckStoreUser").hide();
		infoview(0,PageSize);
	});


	$('#allcount').click(function(){
		$('#info_name').hide();
		$('#total_name').show();
		$("#buttontype").val(1);
        $("#input_hidden").val("b");
		$('#info').html("");
		infoview(0,PageSize);
	});



function checkaction(v){ 
    if(v==0){ 
        document.searchform.action="/duoyoucai/index.php/Download/downloadorder"; 
    }
    searchform.submit(); 
} 

$(".table-header .btn").click(function(){
		var Time_Start = $("#Time_Start").val();
		var Time_End = $("#Time_End").val();
		var out_trade_no = $("#ordernum").val();
		var CustomerNames = $("#CustomerNames").val();
		var Ordertype = $("input[type='radio']:checked").val();
		var buttontype =$("#buttontype").val();
		if(Customername){
		Customer = Customername;
		}else{
		Customer = $("#storename").val();
		//Customer = "";
		}
		var tt="";
		 if (this.ajaxRequest_ != undefined && this.ajaxRequest_.readyState < 4) {
					return false;
		}

		this.ajaxRequest_ =
		$.post("/duoyoucai/index.php/Order/showtotalfee",{Time_Start:Time_Start,Time_End:Time_End,out_trade_no:out_trade_no,Customer:Customer,PageNumber:0,PageSize:10,CustomerNames:CustomerNames,Ordertype:Ordertype,ButtonType:buttontype,SystemUserSysNo:SystemUserSysNo},function(data){ 
			if(data){
			$('#total_fee').html(data.total.Total_fee);
			$('#cash_fee').html(data.total.Cash_fee);
			$('#tradecount').html(data.total.Tradecount);
            $(".table-header span").show(500);
			}else{
			$('#total_fee').html(0);
			$('#cash_fee').html(0);
			$('#tradecount').html(0);
            $(".table-header span").show(500);
			}

        });

})

</script>
</body></html>