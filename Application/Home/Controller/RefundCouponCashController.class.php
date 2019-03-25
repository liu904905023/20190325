<?php

namespace Home\Controller;

//use Think\Controller;

use Common\Compose\Base;



class RefundCouponCashController extends Base {



    public function index(){

		R("Base/getMenu");

        $this->display();

    }



	public function RefundCouponCashList(){

        $Time_Start = I('Time_Start');
        $Time_end = I('Time_end');
        $Out_trade_no = I('out_trade_no');
        $Ordertype = I('Ordertype');
        $data = array(

            "Out_trade_no"=>$Out_trade_no,

            "Create_Time_Start"=>$Time_Start,

            "Create_Time_end"=>$Time_end,

            "Trade_type"=>$Ordertype

        );

        if(session(data)['CustomersType']==0&session(flag)==0){

            $data['CustomersTopSysNo']=session(SysNO);
            $data['Customer'] = I('Customer');
            $data['CustomerName'] = I('CustomerNames');

        }else if(session(data)['CustomersType']==1&session(flag)==0){

            $data['CustomerSysNo']=session(SysNO);
            $data['DisplayName'] = I('realName');
            $data['LoginName'] = I('LoginName');


        }else if(session('servicestoretype')==1&session('flag')==1){

            $data['SystemUserSysNo']=session(SysNO);

        }else{
            exit();
        }

		

		$url=C('SERVER_HOST')."IPP3Order/SP_RMA_Request_WX_CouponCash_List";





		$data['PagingInfo']['PageSize'] = $_POST['PageSize'];

		$data['PagingInfo']['PageNumber'] = $_POST['PageNumber'];


        $list = http($url,$data);
        foreach ($list['Data']['model'] as $row => $val) {

//		订单号，交易金额，退款金额，实际退款金额，代金券退款总金额，退款代金券使用数量,交易类型，退款时间

            $info['model'][$row]['loginname'] = $val['LoginName'];
            $info['model'][$row]['displayname'] = $val['DisplayName'];
            $info['model'][$row]['Customer'] = $val['Customer'];
            $info['model'][$row]['CustomerName'] = $val['CustomerName'];


            $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
            $info['model'][$row]['Total_fee'] = fee2yuan($val['Total_fee']);
            $info['model'][$row]['refund_fee'] = fee2yuan($val['Refund_fee']);
            $info['model'][$row]['Settlement_refund_fee'] = fee2yuan($val['Settlement_refund_fee']);
            $info['model'][$row]['Coupon_refund_fee'] = fee2yuan($val['Coupon_refund_fee']);
            $info['model'][$row]['Coupon_refund_count'] = $val['Coupon_refund_count'];
            $info['model'][$row]['RMATime'] = $val['RMATime'];

        }

		$info['totalCount'] =$list['Data']['totalCount'];



        $this->ajaxReturn($info,json);

	

	}

}