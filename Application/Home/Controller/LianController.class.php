<?php
namespace Home\Controller;
use Think\Controller;
class LianController extends Controller{

	public function index(){
		$this->display();
	}
	public function GetAddress(){
		$type=I('type');
		if($type==1){

		}else{
			$data['Parent_id']=I('parent_id');
		}
		$data = json_encode( $data );
		$url  = C('SERVER_HOST')."IPP3Customers/IPP3GetAddress";
        $head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data ),
            "X-Ywkj-Authentication:" . strlen( $data )
        );
		$list = http_request($url,$data,$head);
		$list = json_decode($list,true);
		foreach ($list as $row=>$val){
			$info[$row]['region_id'] = $val['SysNo'];
			$info[$row]['region_name']   = $val['AddressName'];
		}
		$this->ajaxreturn($info);
	}
	public function GetClass(){
		if(I('class_id')==0){
			$data['ClassID']=0;
		}else{
			$data['TopSysNO']=I('sysno');
			$data['ClassID']=I('class_id');
		}
		$data = json_encode( $data );
		$url  = C('SERVER_HOST')."IPP3Customers/SystemClassList";
        $head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data ),
            "X-Ywkj-Authentication:" . strlen( $data )
        );
		$list = http_request($url,$data,$head);
		$list = json_decode($list,true);
		foreach ($list as $row=>$val){
			$info[$row]['sysno'] = $val['SysNo'];
			$info[$row]['class_id'] = $val['ClassID'];
			$info[$row]['class_name']   = $val['ClassName'];
		}
		$this->ajaxreturn($info);
	}

//1.省市区码表
    public function GetWS_Address(){
        $type=I('type');
        if($type==1){

        }else{
            $data['Parent_id']=I('parent_id');
        }
        $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetWS_Address";
        $list = http($url,$data);
        foreach ($list as $row=>$val){
            $info[$row]['region_id'] = $val['Priority'];
            $info[$row]['region_name']   = $val['AddressName'];
        }
        $this->ajaxreturn($info);
    }
//2.类目码表
    public function GetWS_Category(){
        $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetWS_Category";
        $list = http($url,$data);
        foreach ($list as $row=>$val){
            $info[$row]['sysno'] = $val['CategoryID'];
            $info[$row]['class_name']   = $val['CategoryName'];
        }
        $this->ajaxreturn($info);
    }
//3.银行省市区码表
    public function GetWS_BankArea(){
        $type=I('type');
        if($type==1){

        }else{
            $data['Parent_id']=I('parent_id');
        }
        $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetWS_BankArea";
        $list = http($url,$data);
        foreach ($list as $row=>$val){
            $info[$row]['region_id'] = $val['Priority'];
            $info[$row]['region_name']   = $val['AddressName'];
            if ($val['Priority']==null) {
                $info[$row]['city_code'] = $val['CityCode'];
            }
        }


        $this->ajaxreturn($info);
    }
    public function GetWS_ContactLine(){
        if (I('bankname')=="") {

        }else {
            $data['CityCode'] = I('parent_id');
            $data['BankName'] = I('bankname');
            $url = C('SERVER_HOST') . "IPP3Customers/IPP3GetWS_ContactLine";
            $list = http($url, $data);
            if($list){
                foreach ($list as $row => $val) {
                    $info['data'][$row]['region_id'] = $val['SysNo'];
                    $info['data'][$row]['region_name'] = $val['BankName'];
                }
                $info['city_code'] = $list[0]['CityCode'];
                $info['Count']=count($list);
            }else{
                $info['Count']=0;
            }


            $this->ajaxreturn($info);
        }
    }

    public function GetWS_ContactLine_Number(){
        $data['CityCode']=I('parent_id');
        $data['BankName']=I('bankname');

        $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetWS_ContactLine";
        $list = http($url,$data);

        $this->ajaxreturn($list[0]['ContactLine']);
    }


}

