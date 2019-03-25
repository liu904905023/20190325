<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class OrderStatisticsController extends Base {

    public function order_statistics(){

        R("Base/getMenu");
        $this->display();

    }

    public function orderstatistics(){

        $Time_Start 	= $_POST['Time_Start'];
        $Time_end   	= $_POST['Time_end'];
        $staffloginname = I('staffloginname',"");
        $realname 		= I('realname',"");
        $ordernum 	    = I('ordernum',"");
        $PageNumber 	= I('PageNumber',"");
        $PageSize 		= I('PageSize',"");
        $Ordertype 		= I('Ordertype',"");
        if(session(flag)==1&&session(servicestoretype)==1){//员工

        }else if(session(data)['CustomersType'] == 1){
            $serviceno = session('SysNO');
        }

        $data = array(

            "CustomerSysNo"	    => $serviceno,
            "LoginName"			=> $staffloginname,
            "DisplayName"		=> $realname,
            "out_trade_no"		=> $ordernum,
            "PhoneNumber"		=> $phone,
            "Pay_Type"			=> $Ordertype,
            "Time_Start"		=> $Time_Start,
            "Time_end"			=> $Time_end

        );

        $data['PagingInfo']['PageSize'] = $PageSize;
        $data['PagingInfo']['PageNumber'] = $PageNumber;
        $data = json_encode( $data );
//         var_dump($data);exit;

        $head = array(

            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data ),
            "X-Ywkj-Authentication:" . strlen( $data )

        );

        $url = C('SERVER_HOST')."IPP3Order/IPP3OrderFundList_Shop_SP";
        $list = http_request($url,$data,$head);
        $list = json_decode($list,true);
//         var_dump($list);exit();

        foreach ($list['model'] as $row=>$val){

            $info['model'][$row]['loginname']=$val['LoginName'];                //员工登录名
            $info['model'][$row]['displayname']=$val['DisplayName'];            //员工真实姓名
            $info['model'][$row]['outtradeno']=$val['Out_trade_no'];            //订单号
            $info['model'][$row]['paytype']=CheckOrderType($val['Pay_Type']);   //交易类型
            $info['model'][$row]['totalfee']=fee2yuan($val['Total_fee']);       //总金额
            $info['model'][$row]['cashfee']=fee2yuan($val['Cash_fee']);         //折后金额
            $info['model'][$row]['refundfee']=fee2yuan($val['refund_fee']);     //已退金额
            $info['model'][$row]['fee']=fee2yuan($val['fee']);                  //可退金额
            $info['model'][$row]['refundcount']=$val['refundCount'];            //退款笔数
            $info['model'][$row]['timestart']=$val['Time_Start'];               //交易时间

        }

        $info['totalCount'] =$list['totalCount'];
        $this->ajaxReturn($info);

    }

}