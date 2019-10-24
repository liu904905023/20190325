<?php

namespace Home\Controller;

//use Think\Controller;
use Common\Compose\Base;
use http\Url;

class CCBCRefundSearchController extends Base{
   
	public function refund_search(){
  		R("Base/getMenu");
		//var_dump(session(data));
        $this->display();
    }


  
	public function refundsearch(){
		$Time_Start = empty(I('Time_Start'))? I('Time_Start'):I('Time_Start')." 00:00:00";
		$Time_end = empty(I('Time_end'))? I('Time_end'):I('Time_end')." 23:59:59";
		$CustomerNames =  I('CustomerNames');
		$Storename =  I('Storename');
		$Out_trade_no = I('Out_trade_no');
		$Ordertype = I('Ordertype');
		//$refund_fee = yuan2fee(I('refund_fee'));
		$Create_Time_Start =I('Create_Time_Start');
		$Create_Time_end = I('Create_Time_end');
		
		$data = array(
			"Out_trade_no"=>$Out_trade_no,
			"Time_Start"=>$Time_Start,
			"Time_end"=>$Time_end,
			"DisplayName"=>$CustomerNames,
			"LoginName"=>$Storename,
			"Pay_Type"=>$Ordertype

		);
        $data['TerminalID'] = I('one');
        $data['UserMember'] = I('two');

		$flag =  session(flag);//�������̻�0 ��Ա��1
		$type=3;
		if($flag ==1){
		$type =  staffstoreorservice(session(SysNO));
		}
		

		if(session(data)['CustomersType']==0&$flag==0){
		}
		if(session(data)['CustomersType']==1&$flag==0){
		$data['CustomerSysNo']=session(SysNO);

		}
		if($type==1){
		$data['SystemUserSysNo']=session(SysNO);
		}
		if($type==0){

            exit;
		}
		$url=C('SERVER_HOST')."IPP3Order/CCBRMA_Request";


		$data['PagingInfo']['PageSize'] = I('PageSize');
		$data['PagingInfo']['PageNumber'] = I('PageNumber');
//		var_dump($data);echo "\n".$url ;exit;

        $list = http($url,$data);
//		var_dump($list);
		foreach ($list['Data']['model'] as $row=>$val){
		$info['model'][$row]['Customer']=$val['Customer'];
		$info['model'][$row]['CustomerName']=$val['CustomerName'];
		$info['model'][$row]['LoginName']=$val['LoginName'];
		$info['model'][$row]['DisplayName']=$val['DisplayName'];
		$info['model'][$row]['Out_trade_no']=$val['Out_trade_no'];
		$info['model'][$row]['Out_refund_no']=$val['Out_refund_no'];
		$info['model'][$row]['refund_fee']=fee2yuan($val['Refund_fee']);
		$info['model'][$row]['Pay_Type']=CheckCCBCOrderType($val['Pay_Type']);
		$info['model'][$row]['CreateTime']=$val['CreateTime'];
		$info['model'][$row]['Total_fee']=fee2yuan($val['Total_fee']);
		$info['model'][$row]['one']=$val['Field_one'];
		$info['model'][$row]['two']=$val['Field_two'];

		
		}
		$info['totalCount'] =$list['Data']['totalCount'];


        $this->ajaxReturn($info,json);
	
	
	
	}

}
