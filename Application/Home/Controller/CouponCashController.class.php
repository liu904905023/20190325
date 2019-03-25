<?php

namespace Home\Controller;

//use Think\Controller;

use Common\Compose\Base;



class CouponCashController extends Base {



    public function index(){

		R("Base/getMenu");

        $this->display();

    }



	public function CouponCashList(){
		$Time_Start = I('Time_Start');
		$Time_end = I('Time_end');
		$Out_trade_no = I('out_trade_no');
		$Ordertype = I('Ordertype');
		$data = array(

			"Out_trade_no"=>$Out_trade_no,

			"Time_Start"=>$Time_Start,

			"Time_end"=>$Time_end,

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
		$data['PagingInfo']['PageSize'] = I('PageSize');
		$data['PagingInfo']['PageNumber'] = I('PageNumber');
        $url=C('SERVER_HOST')."IPP3Order/So_Master_Extend_WX_CouponCashList";
        $list = http($url,$data);
//商户用户名，商户名称，订单号，交易类型，交易金额，折后金额，应结订单金额，代金券金额，代金券数量,交易时间
		foreach ($list['Data']['model'] as $row=>$val){
            $info['model'][$row]['loginname']=$val['LoginName'];

            $info['model'][$row]['displayname']=$val['DisplayName'];

            $info['model'][$row]['CustomerName']=$val['CustomerName'];

            $info['model'][$row]['Customer']=$val['Customer'];

		    $info['model'][$row]['Out_trade_no']=$val['Out_trade_no'];

            $info['model'][$row]['Total_fee']=fee2yuan($val['Total_fee']);

            $info['model'][$row]['Cash_fee']=fee2yuan($val['Cash_fee']);

            $info['model'][$row]['Settlement_total_fee']=fee2yuan($val['Settlement_total_fee']);

            $info['model'][$row]['Coupon_fee']=fee2yuan($val['Coupon_fee']);

            $info['model'][$row]['Coupon_count']=$val['Coupon_count'];

		    $info['model'][$row]['Time_Start']=$val['Time_Start'];

		    $info['model'][$row]['Trade_type']=$this->CouponType($val['Trade_type']);

		}

		$info['totalCount'] =$list['Data']['totalCount'];
        $this->ajaxReturn($info,json);
	}
	public function CouponType($type){
        switch ($type) {
            case 'NATIVE' :
                $name = '二维码支付';
                break;
            case 'MICROPAY' :
                $name = '扫卡支付';
                break;
            case 'APP' :
                $name = 'APP支付';
                break;
            case 'JSAPI' :
                $name = 'JSAPI支付';
                break;
        }
        return $name;
    }
    /*
     * 免充值业务*/
    public function NonBatch() {
        if(IS_POST){
            if(session(data)['CustomersType']==0&session(flag)==0){
                $data['CustomersTopSysNo']=session(SysNO);
                $data['Customer'] = I('Customer');
                $data['CustomerName'] = I('CustomerNames');

            }else if(session(data)['CustomersType']==1&session(flag)==0){
                $data['CustomerServiceSysNo']=session(SysNO);
            }else{
                exit();
            }
            $data['PagingInfo']['PageSize'] = I('PageSize');
            $data['PagingInfo']['PageNumber'] = I('PageNumber');
            $data["Time_Start"] = I("Time_Start");
            $data["Time_End"] = I("Time_End");
            $data["Coupon_stock_id"] = I("BatchNumber");
            $data["Status"] = 1;
            $url= C('SERVER_HOST') . "IPP3WXCoupon/CustomerService_WX_CouponCashList";
            $list = http($url, $data);
            foreach ($list['Data']['model'] as $row=>$val) {
                $info['model'][$row]['SysNo'] = $val['SysNo'];
                $info['model'][$row]['Customer'] = $val['Customer'];
                $info['model'][$row]['CustomerName'] = $val['CustomerName'];
                $info['model'][$row]['Coupon_stock_id'] = $val['Coupon_stock_id'];
                $info['model'][$row]['CustomerServiceSysNo'] = $val['CustomerServiceSysNo'];
                $info['model'][$row]['Coupon_value'] = fee2yuan($val['Coupon_value']);
                $info['model'][$row]['Coupon_name'] = $val['Coupon_name'];
                $info['model'][$row]['Coupon_des'] = $val['Coupon_des']?$val['Coupon_des']:"";
                $info['model'][$row]['CreateTime'] = date("Y-m-d H:i:s", substr($val['CreateTime'], 6, 10));
            }
            $info['totalCount'] = $list['Data']['totalCount'];
            $this->ajaxreturn($info);
        }else{

            R("Base/getMenu");

            $this->display();
        }
    }

    public function BatchEntry() {
        if(IS_POST){
            if(session(data)['CustomersType']==1&session(flag)==0){
            }else{
                exit();
            }
            $Get_CustomerSysNo_Data['SysNo']=session(SysNO);
            $Get_CustomerSysNo_Data['PagingInfo']['PageSize'] = 1;
            $Get_CustomerSysNo_Data['PagingInfo']['PageNumber'] = 0;
            $Get_CustomerSysNo_Url = C('SERVER_HOST') . "IPP3Customers/IPP3CustomerList";
            $Return_Customer_Info = http($Get_CustomerSysNo_Url, $Get_CustomerSysNo_Data);
            $Insert_Coupon_Data['Coupon_stock_id'] = I("Batch_number");
            $Insert_Coupon_Data['Coupon_name'] = I("Voucher_name");
            $Insert_Coupon_Data['Coupon_value'] = yuan2fee(I("Voucher_denomination"));
            $Insert_Coupon_Data['Coupon_des'] = I("describe");
            $Insert_Coupon_Data['CustomersTopSysNo'] = $Return_Customer_Info['model']['0']['CustomersTopSysNo'];
            $Insert_Coupon_Data['CustomerServiceSysNo'] = session(SysNO);
            $Insert_Coupon_Data['CustomerName'] = session('data')['CustomerName'];
            $Insert_Coupon_Data['Customer'] = session('data')['Customer'];
            $Insert_Coupon_Url = C('SERVER_HOST') ."IPP3WXCoupon/CustomerService_WX_CouponCashInsert";
            $Return_Insert_Coupon = http($Insert_Coupon_Url, $Insert_Coupon_Data);
            $this->ajaxreturn($Return_Insert_Coupon);


        }else{
            R("Base/getMenu");

            $this->display();
        }
    }

    public function QueryCoupon() {
        $data['CustomerSysNo'] = I('SysNO');
        $data['ReqModel']['coupon_stock_id'] = I('stock_id');
        $url = C('SERVER_HOST') . "IPP3WXCoupon/QueryCouponStock";
        $list = http($url, $data);
        switch($list['Data']['m_values']['coupon_stock_status']){
            case 1 : $status= "未激活";
                break;
            case 2 : $status= "审批中";
                break;
            case 4 : $status= "已激活";
                break;
            case 8 : $status= "已作废";
                break;
            case 16 : $status= "中止发放";
                break;
        }
        $return_info['Code'] = $list['Code'];
        $return_info['coupon_stock_id'] = $list['Data']['m_values']['coupon_stock_id'];
        $return_info['coupon_name'] = $list['Data']['m_values']['coupon_name'];
        $return_info['coupon_value'] = fee2yuan($list['Data']['m_values']['coupon_value']);
        $return_info['coupon_mininumn'] = fee2yuan($list['Data']['m_values']['coupon_mininumn']);
        $return_info['coupon_stock_status'] =$status ;
        $return_info['coupon_total'] = $list['Data']['m_values']['coupon_total'];
        $return_info['max_quota'] = $list['Data']['m_values']['max_quota'];
        $return_info['is_send_num'] = $list['Data']['m_values']['is_send_num'];
        $return_info['begin_time'] = date("Y-m-d H:i:s",$list['Data']['m_values']['begin_time']);
        $return_info['end_time'] = date("Y-m-d H:i:s",$list['Data']['m_values']['end_time']);
        $return_info['create_time'] = date("Y-m-d H:i:s",$list['Data']['m_values']['create_time']);
        $return_info['coupon_budget'] = fee2yuan($list['Data']['m_values']['coupon_budget']);
        $this->ajaxreturn($return_info);
    }

    public function CouponCashUpdate() {
        $data['Coupon_stock_id'] = I("Batch_number");
        $data['Coupon_value'] = yuan2fee(I("Voucher_denomination"));
        $data['Coupon_name'] = I("Voucher_name");
        $data['Coupon_des'] = I("describe");
        $data['SysNo'] = I("SysNo");
        $url = C("SERVER_HOST") . "IPP3WXCoupon/CustomerService_WX_CouponCashUpdate";
        $list = http($url, $data);
        $this->ajaxreturn($list);

    }

}