<?php

namespace Home\Controller;

//use Think\Controller;

use Common\Compose\Base;



class CouponFundController extends Base {



    public function index(){

		R("Base/getMenu");

        $this->display();

    }



	public function CouponFundList(){
		$Time_Start = I('Time_Start');
		$Time_end = I('Time_end');
		$Out_trade_no = I('out_trade_no');
		$Ordertype = I('Ordertype');
		$data = array(

			"Out_trade_no"=>$Out_trade_no,

			"Time_Start"=>$Time_Start,

			"Time_end"=>$Time_end,

			"Pay_Type"=>$Ordertype

		);

		if(session(data)['CustomersType']==0&session(flag)==0){
			if(I('CustomerSysNo')=='null'){
                return false;
            }
		    $data['CustomerSysNo']=I('CustomerSysNo');
            $data['DisplayName'] = I('realName');
            $data['LoginName'] = I('LoginName');

		}else if(session(data)['CustomersType']==1&session(flag)==0){

		    $data['CustomerSysNo']=session(SysNO);
            $data['DisplayName'] = I('realName');
            $data['LoginName'] = I('LoginName');


		}else if(session('servicestoretype')==0&session('flag')==1){

            exit();

		}else{
            exit();
        }
		$data['PagingInfo']['PageSize'] = I('PageSize');
		$data['PagingInfo']['PageNumber'] = I('PageNumber');
        if (deep_in_array($data, ["DisplayName","LoginName","Out_trade_no","Pay_Type","Time_end","Time_Start"])) {

            $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));

            exit();
        }
		if(I('ButtonType')==0){
            $url=C('SERVER_HOST')."IPP3WXCoupon/IPP3Order_RealtimeShopRate_WX_CouponCashSP";
            $list = http($url,$data);
            foreach ($list['model'] as $row=>$val){
                $info['model'][$row]['loginname']=$val['LoginName'];
                $info['model'][$row]['displayname']=$val['DisplayName'];
                $info['model'][$row]['CustomerName']=$val['CustomerName'];
//            $info['model'][$row]['Customer']=$val['Customer'];
                $info['model'][$row]['Out_trade_no']=$val['Out_trade_no'];
                $info['model'][$row]['Total_fee']=fee2yuan($val['Total_fee']);
                $info['model'][$row]['Settlement_total_fee']=fee2yuan($val['Settlement_total_fee']);
                $info['model'][$row]['Settlement_refund_fee']=fee2yuan($val['Settlement_refund_fee']);
                $info['model'][$row]['fee']=fee2yuan($val['fee']);
                $info['model'][$row]['Tradecount']=($val['Tradecount']);
                $info['model'][$row]['Rate']=($val['Rate']);
                $info['model'][$row]['Rate_fee']=fee2yuan($val['Rate_fee']);
                $info['model'][$row]['Total_Rate_fee']=fee2yuan($val['Total_Rate_fee']);
                $info['model'][$row]['Pay_Type']=CheckOrderType($val['Pay_Type']);
                $info['model'][$row]['Time_Start']=($val['Time_Start']);


            }
        }else{
            $url=C('SERVER_HOST')."IPP3WXCoupon/IPP3Order_Fund_ShopSPRate_WX_CouponCash";
            $list = http($url,$data);
            foreach ($list['model'] as $row=>$val){
                $info['model'][$row]['loginname']=$val['LoginName'];
                $info['model'][$row]['displayname']=$val['DisplayName'];
                $info['model'][$row]['CustomerName']=$val['CustomerName'];
//            $info['model'][$row]['Customer']=$val['Customer'];
                $info['model'][$row]['Out_trade_no']=$val['Out_trade_no'];
                $info['model'][$row]['Total_fee']=fee2yuan($val['Total_fee']);
                $info['model'][$row]['Settlement_total_fee']=fee2yuan($val['Settlement_total_fee']);
                $info['model'][$row]['Settlement_refund_fee']=fee2yuan($val['Settlement_refund_fee']);
                $info['model'][$row]['fee']=fee2yuan($val['fee']);
                $info['model'][$row]['Tradecount']=($val['Tradecount']);
                $info['model'][$row]['Rate']=($val['Rate']);
                $info['model'][$row]['Rate_fee']=fee2yuan($val['Rate_fee']);
                $info['model'][$row]['Total_Rate_fee']=fee2yuan($val['Total_Rate_fee']);
                $info['model'][$row]['Pay_Type']=CheckOrderType($val['Pay_Type']);
                $info['model'][$row]['Time_Start']=($val['Time_Start']);


            }

        }


		$info['totalCount'] =$list['totalCount'];
		$info['ButtonType'] =I('ButtonType');
        $this->ajaxReturn($info,json);
	}

    public function showratefee() {
        $Time_Start = I('Time_Start');
        $Time_end = I('Time_End');
        $Out_trade_no = I('out_trade_no');
        $Ordertype = I('Ordertype');
        $data = array(

            "Out_trade_no"=>$Out_trade_no,

            "Time_Start"=>$Time_Start,

            "Time_end"=>$Time_end,

            "Pay_Type"=>$Ordertype

        );

        if(session(data)['CustomersType']==0&session(flag)==0){
            $data['CustomerSysNo']=I('CustomerSysNo', "");
            $data['DisplayName'] = I('DisplayName');
            $data['LoginName'] = I('LoginName');
        }else if(session(data)['CustomersType']==1&session(flag)==0){
            $data['CustomerSysNo']=session(SysNO);
            $data['DisplayName'] = I('DisplayName');
            $data['LoginName'] = I('LoginName');
        }else if(session('servicestoretype')==1&session('flag')==1){
            exit();
        }else{
            exit();
        }
        if (deep_in_array($data, ["DisplayName","LoginName","Out_trade_no","Pay_Type","Time_end","Time_Start"])) {

            $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));

            exit();
        }
        $url = C("SERVER_HOST") . "IPP3WXCoupon/IPP3Order_Group_ShopSPRate_WX_CouponCash";
        $list = http($url, $data);
        $info['total']['Total_fee'] = fee2yuan($list['Total_fee']);
        $info['total']['fee'] = fee2yuan($list['fee']);
        $info['total']['Tradecount'] = $list['Tradecount'];
        $info['total']['Rate_fee'] = fee2yuan($list['Rate_fee']);
        $info['total']['Total_Rate_fee'] = fee2yuan($list['Total_Rate_fee']);
        $this->ajaxreturn($info);
    }

}