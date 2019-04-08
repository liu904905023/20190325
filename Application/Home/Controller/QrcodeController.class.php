<?php
namespace Home\Controller;
use Think\Controller;
Vendor('phpqrcode.phpqrcode');
//import( '@.Common.WxAccount' );

// 本类由系统自动生成，仅供测试用途
class QrcodeController extends Controller{

	public function code(){
		$this->display();
	}
	
     public function qrcode($url='http://www.baidu.com',$level=3,$size=6){

      $errorCorrectionLevel =intval($level) ;//容错级别 
      $matrixPointSize = intval($size);//生成图片大小 
      $object = new \QRcode();
      $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);   

     }

	 public function qrcode1($url='http://www.baidu.com',$level=3,$size=6){
        $data['systemUserSysNo'] = $url;
        $data = json_encode($data);
        $urls = C('SERVER_HOST') . "IPP3Customers/IPP3AliPayConfigBySUsysNo";
        $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data), "X-Ywkj-Authentication:" . strlen($data));
        $list = http_request($urls, $data, $head);
        $list = json_decode($list, true);
        $url1="https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=".$list['AppID']."&redirect_uri=http://".$_SERVER["SERVER_NAME"]."/index.php/Isv/index?systemUserSysNo=";
        $url1.=$url;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object = new \QRcode();
        $object->png($url1, false, $errorCorrectionLevel, $matrixPointSize, 2);

    }
	public function aliqrcode($url='http://www.baidu.com',$level=3,$size=6){
		$url1="https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=2016072101648286&scope=auth_base&redirect_uri=http://".$_SERVER["SERVER_NAME"]."/index.php/Isv/index?systemUserSysNo=";
		$url1.=$url;
		$errorCorrectionLevel =intval($level) ;//容错级别 
		$matrixPointSize = intval($size);//生成图片大小 
		$object = new \QRcode();
		$object->png($url1, false, $errorCorrectionLevel, $matrixPointSize, 2);

	}
	
	public function intelqrcode($url='http://www.baidu.com',$level=3,$size=6){
		$url1="http://".$_SERVER["SERVER_NAME"]."/index.php/Intel/index?systemUserSysNo=";
		$url1.=$url;
		$errorCorrectionLevel =intval($level) ;//容错级别 
		$matrixPointSize = intval($size);//生成图片大小 
		$object = new \QRcode();
		$object->png($url1, false, $errorCorrectionLevel, $matrixPointSize, 2);
	}
	
	
	public function index(){
		$status = session('status');
        if( !isset($status)){
            $this->redirect("Login/login");
        }else{
            R("Base/getMenu");
        }
        if(session(data)['CustomersType']==1&session(flag)==0){
            $data['CustomerServiceSysNo']=session(SysNO);
        }else{
            exit();
        }
        $data['PagingInfo']['PageSize'] = 100;
        $data['PagingInfo']['PageNumber'] = 0;
        $data["Time_Start"] = "";
        $data["Time_End"] = "";
        $data["Status"] = 1;
        $url= C('SERVER_HOST') . "IPP3WXCoupon/CustomerService_WX_CouponCashList";
        $list = http($url, $data);
        foreach ($list['Data']['model'] as $row=>$val) {

            $info['model'][$row]['Coupon_stock_id'] = $val['Coupon_stock_id'];
            $info['model'][$row]['Coupon_value'] = fee2yuan($val['Coupon_value']);

            $info['model'][$row]['CustomerServiceSysNo'] = $val['CustomerServiceSysNo'];
            $info['model'][$row]['SysNo'] = $val['SysNo'];
            $info['model'][$row]['Customer'] = $val['Customer'];
            $info['model'][$row]['CustomerName'] = $val['CustomerName'];
            $info['model'][$row]['Coupon_name'] = $val['Coupon_name'];
            $info['model'][$row]['Coupon_des'] = $val['Coupon_des'];
            $info['model'][$row]['CreateTime'] = date("Y-m-d H:i:s", substr($val['CreateTime'], 6, 10));
        }
        $this->assign("info", $info['model']);
        $this->display();
    }
	public function index2(){
	
		R("Base/getMenu");
	 $this->display();
	}
	public function scan(){

		 if( !empty( $_POST['syskeyno'] ) ){
            $data['syskeyno'] = trim( $_POST['syskeyno'] );
            $data['payfee'] = trim( yuan2fee($_POST['payfee']) );
			
			$data['url']=SITE_URL."/index.php/Qrcode/qrcode/qrcode?url=http://web.yunlaohu.cn/index.php/Wxpay/newpay/?systemUserSysNo=".$data['syskeyno']."&payfee=".$data['payfee'];
			$data['payurl']="http://web.yunlaohu.cn/index.php/Wxpay/newpay/?systemUserSysNo=".$data['syskeyno']."&payfee=".$data['payfee'];
           
 
        }
		$this->assign('data',$data);
        $this->display();
	
	}



}
