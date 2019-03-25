<?php
namespace Home\Controller;
use Think\Controller;
class AdController extends Controller{

    
    
	
	public function ads()
	{
//
////        $url = C('SERVER_HOST').'IPP3Customers/IPP3GetAdBody';
//		$url ='https://pos.yunlaohu.cn/IPP3Customers/IPP3GetAdBody';
//        $data ["sex"]           ="0";
//        $data ["province"]      ="吉林省";
//        $data ["city"]          ="长春市" ;
//        $data ["request_type"]  ="0";
//        $data ["appid"]         = I('appid');
//        $data ["openid"]        = I('userid');
//        $data ["advert_type"]   ="0";
//        $data ["out_trade_no"]  ="";
//        $data ["mch_name"]      = urldecode(I('product'));
//        $data ["body"]          = urldecode(I('product'));
//        $data ["pay_type"]      ="0";
//        $data ["amount"]        =I('amount');
//        $data ["trade_time"]    = "";
//        $data ["first_trade"]   ="";
//        $data ["second_trade"]  ="";
//        $data ["photo_size"]    ="0";
//		$data ['systemUserSysNo']=I('sysno');
////		$aaa = json_encode($data);
////		\Think\Log::record($aaa);
//        $list = http($url,$data);
//        $Ad_Info['imgPath'] = $list['Data']['imgPath'];
//        $Ad_Info['url'] = $list['Data']['url'];
//		$Ad_Info['amount'] =I('amount') ;
//        $Ad_Info['paytype'] = $list['Data']['Customer_field_one'];
//
//        $this->assign(Ad_Info,$Ad_Info);
//        $this->display();



    }
	
	public function ydt(){

        $AccessTokenStatus = $this->get_AccessToken(false);
//		dump($AccessTokenStatus);exit;
        if ($AccessTokenStatus['Code']==0) {
            $AccessToken = $AccessTokenStatus['Data'];
        }
        $data['ShopName']=I('product');
//        $data['useragent']=2406;
        $data['Paid_Money']=I('amount');
//        $url = 'http://yiditui.com:18888/pay/done.do?accessToken=eyJjbGllbnRTZWNyZXQiOiJkaGFzaXU4NmUyIiwiaWQiOjEsImNsaWVudElkIjoiMzI3Zm1hc2o4IiwidGltZSI6MTUyMTA3OTE1NDE5NSwidXVpZCI6Ijc4YjQ5NzkzLTM2YmItNGVkMi1hNTI2LTkyZWNkZmE4M2FhNiJ9&data={%22ShopName%22:%22\u5546\u6237\u4e00%22,%22useragent%22:2406,%22Paid_Money%22:0.01}';
        $url = 'http://yiditui.com:18888/pay/done.do?accessToken='.$AccessToken.'&data='.json_encode($data);
        $head = array(
            "Content-Type:application/json;charset=UTF-8"
        );
        $list = http_request($url,'',$head);
        $list = json_decode($list, true);
		if($list['code']==1003){
		$this->get_AccessToken(true);
		}else{
        $get_redirect_data['data'] = ($list['data'][rand(0, count($list['data'])-1)]);
        $get_redirect_data['accessToken'] = $AccessToken;
        $get_redirect_list = $this->get_redirect($get_redirect_data);

		}
        $Ad_Info['imgPath'] = $get_redirect_data['data']['image'];
        $Ad_Info['url'] =$get_redirect_list['data']['linkUrl'];
		$Ad_Info['amount'] =I('amount') ;
				header( 'Location:'.$Ad_Info['url'] );
exit;
		$this->assign('Ad_Info',$Ad_Info);
        $this->display();
    }
    private function get_AccessToken ($bool_) {

        $url = C('SERVER_HOST').'IPP3Customers/IPP3GetAdToken';
        $data['IfGetTokenNow'] = $bool_;
        $data['ClientID'] = '734fa5sn8';
        $list = http($url, $data);
        return $list;
    }

    private function get_redirect($get_redirect_data) {
        $url = 'http://yiditui.com:18888//pay/redirect.do?accessToken=' . $get_redirect_data['accessToken'] . '&adId=' . $get_redirect_data['data']['id'];
        $head = array(
            "Content-Type:application/json;charset=UTF-8"
        );
        $list = http_request($url,'',$head);
        $list = json_decode($list, true);

		if($list['code']==1003){
		$this->get_AccessToken(true);
		}else{
        return $list;
		}
    }
}
