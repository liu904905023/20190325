<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class OrderChannelController extends Base {

    public function order_channel(){

        R("Base/getMenu");
        $this->display();

    }

    public function orderchannellist(){
        $Time_Start = I('Time_Start');
        $Time_end = I('Time_End');
        $out_trade_no = I('out_trade_no', "");
        $channel = I('channel', "");
        $SystemUserSysNo = I('SystemUserSysNo', "");
        $PageNumber = I('PageNumber');
        $PageSize = I('PageSize');
        $CustomerNames = I('CustomerNames');
        $Ordertype = 103;
        $ButtonType = I('ButtonType');
        $data = array("Time_Start" => $Time_Start, "Time_end" => $Time_end, "Out_trade_no" => $out_trade_no, "Pay_Type" => $Ordertype,
            "FundChannel"=>$channel
        );
        $flag = session('flag');//服务商商户0 或员工1
        $type = session('servicestoretype');//员工的服务商的类型 0为服务  1为商户
//        if (session('data')['CustomersType'] == 0 & $type == 0) {
//            $stafftype = 0;
//        }


        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
            $data['LoginName'] = I('Customer');
            $data['DisplayName'] = I('CustomerNames');
            $data['CustomerSysNo'] = session('SysNO');

        } if (session('data')['CustomersType'] == 0 & $flag == 0) {//商户登录
            $data['Customer'] = I('Customer');
            $data['CustomerName'] = I('CustomerNames');
            $data['CustomersTopSysNo'] = session('SysNO');

        }


        if ($SystemUserSysNo != 'null') {

            $data['SystemUserSysNo'] = $SystemUserSysNo;

        } else {

            if (session('data')['CustomersType'] == 1) {  //商户登陆 查询全部员工的 传$CustomerSysNo
                $data['CustomerSysNo'] = session('SysNO');
            }
            if ($flag == 1 & $type == 1) {
                $data['SystemUserSysNo'] = session('SysNO');//员工登陆 查全部 传$CustomerSysNo

            }
        }
        if ($ButtonType == 0) {
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户

                    $url = C('SERVER_HOST') . "IPP3Order/So_Master_Extend_FundChannelList";

            }else if ($type == 1 & $flag == 1) {//商户员工
                $url = C('SERVER_HOST') . "IPP3Order/So_Master_Extend_FundChannelList";
            }else if (session('data')['CustomersType'] == 0 && $flag ==0){
                $url = C('SERVER_HOST') . "IPP3Order/So_Master_Extend_FundChannelListCustomer";
            }


            $data['PagingInfo']['PageSize'] = $PageSize;
            $data['PagingInfo']['PageNumber'] = $PageNumber;
        } else if ($ButtonType == 1) {

            if ($type == 1 & $flag == 1) {//员工登陆 传入主键
                $url = C('SERVER_HOST') . "IPP3Order/So_Master_Extend_FundChannelListGroupShopUser";

            }
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商员工登陆 传入主键

                $url = C('SERVER_HOST') . "IPP3Order/So_Master_Extend_FundChannelListGroupShop";

            }
            if (session('data')['CustomersType'] == 0 && $flag ==0){
                $url = C('SERVER_HOST') . "IPP3Order/So_Master_Extend_FundChannelListGroupCustomer";
            }
        }




//        var_dump($data);
//        echo $url;
//        exit;
        $list = http($url,$data);

        if ($ButtonType == 0) {
            if (session('data')['CustomersType'] == 0 && $flag ==0){
                foreach ($list['Data']['model'] as $row => $val) {
                    $info['model'][$row]['customer'] = $val['Customer'];
                    $info['model'][$row]['customername'] = $val['CustomerName'];
                    $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
                    $info['model'][$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']);
                    $info['model'][$row]['Total_fee'] = fee2yuan($val['FundChannelFee']);
                    $info['model'][$row]['Time_Start'] = $val['Time_Start'];
                    $info['model'][$row]['channel'] =CheckOrderChannel($val['FundChannel']) ;
                }
                $info['totalCount'] = $list['Data']['totalCount'];
                $info['ButtonType'] = $ButtonType;
                if (session(flag) == 1) {
                    $list['flag'] = session('servicestoretype');
                }
            }else{
                foreach ($list['Data']['model'] as $row => $val) {
                    $info['model'][$row]['loginname'] = $val['LoginName'];
                    $info['model'][$row]['displayname'] = $val['DisplayName'];
                    $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
                    $info['model'][$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']);
                    $info['model'][$row]['Total_fee'] = fee2yuan($val['FundChannelFee']);
                    $info['model'][$row]['Time_Start'] = $val['Time_Start'];
                    $info['model'][$row]['channel'] =CheckOrderChannel($val['FundChannel']) ;
                }
                $info['totalCount'] = $list['Data']['totalCount'];
                $info['ButtonType'] = $ButtonType;
                if (session(flag) == 1) {
                    $list['flag'] = session('servicestoretype');
                }
            }



        } else if ($ButtonType == 1) {
            if (session('data')['CustomersType'] == 0 && $flag ==0){
                foreach ($list['Data']['model'] as $row => $val) {
                    $info['model'][$row]['total_fee'] = fee2yuan($val['FundChannelFee']);
                    $info['model'][$row]['channel'] =CheckOrderChannel($val['FundChannel']) ;
                    $info['model'][$row]['tradecount'] = $val['Tradecount'];
                    $info['model'][$row]['customer'] = $val['Customer'];
                    $info['model'][$row]['customername'] = $val['CustomerName'];
                }
                $info['totalCount'] =1;
                $info['ButtonType'] = $ButtonType;
                if (session(flag) == 1) {
                    $list['flag'] = session('servicestoretype');
                }
            }else{
                foreach ($list['Data'] as $row => $val) {
                    $info['model'][$row]['total_fee'] = fee2yuan($val['FundChannelFee']);
                    $info['model'][$row]['channel'] =CheckOrderChannel($val['FundChannel']) ;
                    $info['model'][$row]['tradecount'] = $val['Tradecount'];
                }
                $info['totalCount'] =1;
                $info['ButtonType'] = $ButtonType;
                if (session(flag) == 1) {
                    $list['flag'] = session('servicestoretype');
                }
            }


        }
        $this->ajaxReturn($info);

    }

}