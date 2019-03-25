<?php

namespace Home\Controller;

use Think\Controller;

Vendor('phpqrcode.phpqrcode');

class PayQrcodeController extends Controller {

    public function pay_qrcode() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $this->assign("type", 102);
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $this->assign("type", 103);
        }
        $SystemUserSysNo=I('systemUserSysNo');
        $Customer = GetCustomerServiceSysNo($SystemUserSysNo);
        $systemUserName= QueryStaffInfo($SystemUserSysNo);
        $this->assign('SystemUserSysNo',$SystemUserSysNo);
        $this->assign('Customer',$Customer);
        $this->assign('systemUserName',$systemUserName);
        $this->display();
    }

    public function pay() {
        $data['Total_fee'] = yuan2fee(I('amount'));
        $data['systemUserSysNo'] = I('SystemUserSysNo');
        $data['product_id'] = 2;
        $data['body'] = 2;
        $data = json_encode($data);
        $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data), "X-Ywkj-Authentication:" . strlen($data));
        if (I('type') == 102) {
            $url = C('SERVER_HOST') . "Payment/Payments/GetPayUrl";
            $this->assign('type','102');
        } else if (I('type') == 103) {
            $url = C('SERVER_HOST') . "IPP3AliPay/GetAliPayUrl";
            $this->assign('type','103');
        }
        $list = http_request($url, $data, $head);
        $list = json_decode($list, true);
        $info['qcrode'] = "http://" . $_SERVER['HTTP_HOST'] . "/index.php/PayQrcode/qrcode?url=" . $list['Description'];
        $info['ordernum'] = $list['Data'];
        $info['SystemUserSysNo'] = I('SystemUserSysNo');
        $this->assign('info', $info);
        $this->display();

    }

    public function qrcode($url = 'mobile.yunlaohu.cn', $level = 3, $size = 6) {

        $errorCorrectionLevel = intval($level);//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object = new \QRcode();
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);

    }


}