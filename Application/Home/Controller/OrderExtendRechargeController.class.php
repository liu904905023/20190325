<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;

class OrderExtendRechargeController extends Base {

    public function order_extend_recharge(){
		R("Base/getMenu");
        $this->display();
    }

	public function orderextendrecharge(){
		$Time_Start = $_POST['Time_Start'];
		$Time_end = $_POST['Time_end'];
		$Out_trade_no = $_POST['out_trade_no'];
		$Ordertype = $_POST['Ordertype'];

		$data = array(
			"Out_trade_no"=>$Out_trade_no,
			"Time_Start"=>$Time_Start,
			"Time_end"=>$Time_end,
			"Pay_Type"=>$Ordertype

		);


        $flag = session('flag');                            //0-服务商登陆  1-员工登陆
        $servicestoretype = session('servicestoretype');    //0-服务商员工 1-商户员工
        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
            $data['CustomerSysNo'] = session('SysNO');
        }
        if ($servicestoretype == 1 & $flag == 1) {//商户员工
            $data['Old_SysNo'] = session('SysNO');
        }

		$url=C('SERVER_HOST')."IPP3Order/So_Master_Extend_DYC_PhoneList";

		$data['PagingInfo']['PageSize'] = $_POST['PageSize'];
		$data['PagingInfo']['PageNumber'] = $_POST['PageNumber'];
        $data = json_encode( $data );
		// var_dump($data);echo "\n".$url ;exit;
        $head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data ),
            "X-Ywkj-Authentication:" . strlen( $data ),
        );
        $list = http_request($url,$data,$head);
		$list = json_decode($list,true);
		foreach ($list['model'] as $row=>$val){
			$info['model'][$row]['loginname']=$val['LoginName'];			//登录名
			$info['model'][$row]['displayname']=$val['DisplayName'];		//真实姓名
			$info['model'][$row]['Customer']=$val['Customer'];				//商户登录名
			$info['model'][$row]['CustomerName']=$val['CustomerName'];		//商户真实姓名
			$info['model'][$row]['Out_trade_no']=$val['Out_trade_no'];		//订单号
			$info['model'][$row]['Total_fee']=fee2yuan($val['Total_fee']);	//交易金额
			$info['model'][$row]['Pay_Type'] = CheckOrderType($val['Pay_Type']);	//交易类型
			$info['model'][$row]['company']=$val['Company'];				//公司
			$info['model'][$row]['name']=$val['Name'];						//联系人
			$info['model'][$row]['phonenumber']=$val['PhoneNumber'];		//电话
			$info['model'][$row]['phonecode']=$val['PhoneCode'];			//电话编码
			$info['model'][$row]['Time_Start']=$val['Time_Start'];			//交易时间

		}
		$info['totalCount'] =$list['totalCount'];

        $this->ajaxReturn($info,json);
	
	}
}