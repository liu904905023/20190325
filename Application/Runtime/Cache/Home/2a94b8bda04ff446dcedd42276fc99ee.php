<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html><html>    <head>        <meta charset="utf-8" />        <title>多友财支付平台</title>		<meta name="keywords" content="多友财,多友财支付平台,多友财商户平台" />		<meta name="description" content="多友财,多友财支付平台,多友财商户平台" />        <meta name="viewport" content="width=device-width, initial-scale=1.0" />		<link rel="icon" href="/icon.ico">        <link href="/duoyoucai/Public/assets/css/bootstrap.min.css" rel="stylesheet" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/font-awesome.min.css" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/ace.min.css" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/shanghu.css" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/pageGroup.css" />        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/bootstrap-datetimepicker.min.css" />        <!--[if IE 7]>        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/font-awesome-ie7.min.css" />        <![endif]-->        <!--[if lte IE 8]>        <link rel="stylesheet" href="/duoyoucai/Public/assets/css/ace-ie.min.css" />        <![endif]-->        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->        <!--[if lt IE 9]>        <script src="/duoyoucai/Public/assets/js/html5shiv.js"></script>        <script src="/duoyoucai/Public/assets/js/respond.min.js"></script>        <![endif]-->    </head>    <body>        <div class="navbar navbar-default" id="navbar">            <script type="text/javascript">                try {                    ace.settings.check( 'navbar','fixed' )                } catch ( e ) {                }            </script>            <!--head_start-->            <div class="navbar-container" id="navbar-container">                <div class="navbar-header pull-left">                    <a href="/duoyoucai/index.php" class="navbar-brand">                       <small><img src="/duoyoucai/Public/assets/images/i.png" alt=""> 多友财支付平台</small>                    </a>                </div>                <div class="pull-left tenant_info">                   用户名：                    <?php if(session('flag') == 1 & session('servicestoretype')==1){ ?>                    <a href="/duoyoucai/index.php/Conff/infoDetail"><?php if(!empty($_SESSION['data']['Customer'])): echo ($_SESSION['data']['Customer']); else: echo ($_SESSION['data']['LoginName']); endif; ?></a>                    <?php }else if( session('data')['CustomersType']==1){ ?>                    <a href="/duoyoucai/index.php/Conff/MerchantDetail"><?php if(!empty($_SESSION['data']['Customer'])): echo ($_SESSION['data']['Customer']); else: echo ($_SESSION['data']['LoginName']); endif; ?></a>                    <?php }else{ ?>                    <a><?php if(!empty($_SESSION['data']['Customer'])): echo ($_SESSION['data']['Customer']); else: echo ($_SESSION['data']['LoginName']); endif; ?></a>                    <?php } ?>                </div>                <div class="pull-left tenant_info2">名称：<?php if(!empty($_SESSION['data']['DisplayName'])): echo ($_SESSION['data']['DisplayName']); else: echo ($_SESSION['data']['CustomerName']); endif; ?></div>                <div class="navbar-header pull-right" role="navigation">                    <a href="/duoyoucai/index.php/Conff/password"> <i class="icon-key"></i>                        修改密码                    </a>                    <a href="/duoyoucai/index.php/Login/logout">                        <i class="icon-off"></i>                        退出                    </a>                </div>            </div>        </div>        <!--head_end-->        <!--left_start-->        <div class="main-container" id="main-container">            <script type="text/javascript">                try {                    ace.settings.check( 'main-container','fixed' )                } catch ( e ) {                }            </script>            <div class="main-container-inner">                <a class="menu-toggler" id="menu-toggler" href="index.html">                    <span class="menu-text"></span>                </a>                <div class="sidebar" id="sidebar">                    <script type="text/javascript">                        try {                            ace.settings.check(                                'sidebar'                                ,'fixed' )                        } catch ( e ) {                        }                    </script>                                        <div class="sidebar-shortcuts" id="sidebar-shortcuts">                        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">                            <span class="btn btn-success"></span>                            <span class="btn btn-info"></span>                            <span class="btn btn-warning"></span>                            <span class="btn btn-danger"></span>                        </div>                    </div>                    <asd class="nav nav-list">                    <?php if(is_array($list3)): $k1 = 0; $__LIST__ = $list3;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($k1 % 2 );++$k1; if(in_array("CONTROLLER_NAME", $list3)){ ?>										 <li class="active"><?php }else{ ?>					<li>					<?php } ?>                        <!-- <?php if(CONTROLLER_NAME == 'Conff'): ?><li class="active">                                <?php else: ?>                            <li><?php endif; ?> -->                        <a href="#" class="dropdown-toggle">                         <img src="/duoyoucai/Public/assets/images/ico_cloud.png" alt="">                                <span class="menu-text"><?php echo ($vo1); ?></span> <b class="arrow icon-angle-down"></b>                        </a>                        <ul class="submenu">                        <?php if(is_array($list)): $k = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($vo['D'] == $vo1 ): if(in_array("ACTION_NAME", $newarr)){ ?>							<li class="active"><?php }else{ ?>							<li>							<?php } ?>                                <a href="/duoyoucai/index.php<?php echo ($vo['U']); ?>">                                    <i class="icon-double-angle-right"></i><?php echo ($vo['R']); ?><br/>                                </a>                                </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>                        </ul><?php endforeach; endif; else: echo "" ;endif; ?> 										<input type="hidden" value ="<?php echo ($_SERVER['REQUEST_URI']); ?>" id = "URL">                    <div class="sidebar-collapse" id="sidebar-collapse">                        <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>                    </div>                    <script type="text/javascript">                        try {                            ace.settings.check(                                'sidebar'                                ,'collapsed' )                        } catch ( e ) {                        }												var menu = document.querySelectorAll("a");						var href = document.getElementById("URL").value;						for(var i = 0; i < menu.length; i++){							if(menu[i].getAttribute("href")==href&&menu[i].getAttribute("href")!='/index.php'&&menu[i].getAttribute("href")!='/index.php/Conff/infoDetail'&&menu[i].getAttribute("href")!='/index.php/Conff/MerchantDetail'&&menu[i].getAttribute("href")!='/index.php/Conff/password'){								menu[i].style.color = '#3b85e5';								menu[i].parentNode.parentNode.style.display='block';							}						}                    </script>                </div>
      <div class="main-content">
        <div class="breadcrumbs" id="breadcrumbs">
          <script type="text/javascript">try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}</script>
          <ul class="breadcrumb">
            <li>
              <i class="icon-home home-icon"></i>
              <a href="/duoyoucai/index.php">首页</a>
            </li>
          </ul>
        </div>
        <div class="page-content">
          <div class="row">
              <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->

                <!-- <div class="alert alert-block alert-success">
                  <button type="button" class="close" data-dismiss="alert">
                    <i class="icon-remove"></i>
                  </button>

                  <i class="icon-ok green"></i>

                  欢迎使用
                  <strong class="green">
                    云老虎商户管理系统
                    <small>(v1.0)</small>
                  </strong>
                
                </div> -->
                <div class="space-6"></div>
              <div><img width="100%" src="/duoyoucai/Public/assets/images/main.jpg" alt=""></div>
                <!-- <div class="row">
                  <div class="space-6"></div>

                  <div class="col-sm-12 infobox-container">
                    <div class="infobox infobox-green  ">
                      <div class="infobox-icon">
                        <i class="icon-comments"></i>
                      </div>

                      <div class="infobox-data">
                        <span class="infobox-data-number">32</span>
                        <div class="infobox-content">2个评论</div>
                      </div>
                      <div class="stat stat-success">8%</div>
                    </div>

                    <div class="infobox infobox-blue  ">
                      <div class="infobox-icon">
                        <i class="icon-twitter"></i>
                      </div>

                      <div class="infobox-data">
                        <span class="infobox-data-number">11</span>
                        <div class="infobox-content">新粉丝</div>
                      </div>

                      <div class="badge badge-success">
                        +32%
                        <i class="icon-arrow-up"></i>
                      </div>
                    </div>

                    <div class="infobox infobox-pink  ">
                      <div class="infobox-icon">
                        <i class="icon-shopping-cart"></i>
                      </div>

                      <div class="infobox-data">
                        <span class="infobox-data-number">8</span>
                        <div class="infobox-content">新订单</div>
                      </div>
                      <div class="stat stat-important">4%</div>
                    </div>

                    <div class="infobox infobox-red  ">
                      <div class="infobox-icon">
                        <i class="icon-beaker"></i>
                      </div>

                      <div class="infobox-data">
                        <span class="infobox-data-number">7</span>
                        <div class="infobox-content">调查</div>
                      </div>
                    </div>

                    <div class="infobox infobox-orange2  ">
                      <div class="infobox-chart">
                        <span class="sparkline" data-values="196,128,202,177,154,94,100,170,224"></span>
                      </div>

                      <div class="infobox-data">
                        <span class="infobox-data-number">6,251</span>
                        <div class="infobox-content">页面查看次数</div>
                      </div>

                      <div class="badge badge-success">
                        7.2%
                        <i class="icon-arrow-up"></i>
                      </div>
                    </div>

                    <div class="infobox infobox-blue2  ">
                      <div class="infobox-progress">
                        <div class="easy-pie-chart percentage" data-percent="42" data-size="46">
                          <span class="percent">42</span>%
                        </div>
                      </div>

                      <div class="infobox-data">
                        <span class="infobox-text">交易使用</span>

                        <div class="infobox-content">
                          <span class="bigger-110">~</span>
                          剩余58GB
                        </div>
                      </div>
                    </div>

                    <div class="space-6"></div>

                    <div class="infobox infobox-green infobox-small infobox-dark">
                      <div class="infobox-progress">
                        <div class="easy-pie-chart percentage" data-percent="61" data-size="39">
                          <span class="percent">61</span>%
                        </div>
                      </div>

                      <div class="infobox-data">
                        <div class="infobox-content">任务</div>
                        <div class="infobox-content">完成</div>
                      </div>
                    </div>

                    <div class="infobox infobox-blue infobox-small infobox-dark">
                      <div class="infobox-chart">
                        <span class="sparkline" data-values="3,4,2,3,4,4,2,2"></span>
                      </div>

                      <div class="infobox-data">
                        <div class="infobox-content">获得</div>
                        <div class="infobox-content">$32,000</div>
                      </div>
                    </div>

                    <div class="infobox infobox-grey infobox-small infobox-dark">
                      <div class="infobox-icon">
                        <i class="icon-download-alt"></i>
                      </div>

                      <div class="infobox-data">
                        <div class="infobox-content">下载次数</div>
                        <div class="infobox-content">1,205</div>
                      </div>
                    </div>
                  </div>

                </div> -->

                <!-- <div class="hr hr32 hr-dotted"></div> -->

                <!-- <div class="row">
                  <div class="col-sm-7">
                    <div class="widget-box transparent">
                      <div class="widget-header widget-header-flat">
                        <h4 class="lighter">
                          <i class="icon-signal"></i>
                          销售统计
                        </h4>

                        <div class="widget-toolbar">
                          <a href="#" data-action="collapse">
                            <i class="icon-chevron-up"></i>
                          </a>
                        </div>
                      </div>

                      <div class="widget-body">
                        <div class="widget-main padding-4">
                          <div id="sales-charts"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-5">
                    <div class="widget-box transparent">
                      <div class="widget-header widget-header-flat">
                        <h4 class="lighter">
                          <i class="icon-star orange"></i>
                          热门域名
                        </h4>

                        <div class="widget-toolbar">
                          <a href="#" data-action="collapse">
                            <i class="icon-chevron-up"></i>
                          </a>
                        </div>
                      </div>

                      <div class="widget-body">
                        <div class="widget-main no-padding">
                          <table class="table table-bordered table-striped">
                            <thead class="thin-border-bottom">
                              <tr>
                                <th>
                                  <i class="icon-caret-right blue"></i>
                                  名称
                                </th>

                                <th>
                                  <i class="icon-caret-right blue"></i>
                                  价格
                                </th>

                                <th class="hidden-480">
                                  <i class="icon-caret-right blue"></i>
                                  状态
                                </th>
                              </tr>
                            </thead>

                            <tbody>
                              <tr>
                                <td>internet.com</td>

                                <td>
                                  <small>
                                    <s class="red">$29.99</s>
                                  </small>
                                  <b class="green">$19.99</b>
                                </td>

                                <td class="hidden-480">
                                  <span class="label label-info arrowed-right arrowed-in">销售中</span>
                                </td>
                              </tr>

                              <tr>
                                <td>online.com</td>

                                <td>
                                  <small>
                                    <s class="red"></s>
                                  </small>
                                  <b class="green">$16.45</b>
                                </td>

                                <td class="hidden-480">
                                  <span class="label label-success arrowed-in arrowed-in-right">可用</span>
                                </td>
                              </tr>

                              <tr>
                                <td>newnet.com</td>

                                <td>
                                  <small>
                                    <s class="red"></s>
                                  </small>
                                  <b class="green">$15.00</b>
                                </td>

                                <td class="hidden-480">
                                  <span class="label label-danger arrowed">待定</span>
                                </td>
                              </tr>

                              <tr>
                                <td>web.com</td>

                                <td>
                                  <small>
                                    <s class="red">$24.99</s>
                                  </small>
                                  <b class="green">$19.95</b>
                                </td>

                                <td class="hidden-480">
                                  <span class="label arrowed">
                                    <s>无货</s>
                                  </span>
                                </td>
                              </tr>

                              <tr>
                                <td>domain.com</td>

                                <td>
                                  <small>
                                    <s class="red"></s>
                                  </small>
                                  <b class="green">$12.00</b>
                                </td>

                                <td class="hidden-480">
                                  <span class="label label-warning arrowed arrowed-right">售完</span>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> -->

              </div><!-- /.col -->
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
    $('.form_datetime').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    });
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
</body></html>