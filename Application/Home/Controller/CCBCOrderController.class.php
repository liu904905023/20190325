<?php
namespace Home\Controller;

//use Think\Controller;
use Common\Compose\Base;

class CCBCOrderController extends Base {

    public function order_search() {
        R("Base/getMenu");
        $this->display();
    }

    public function ordersearch() {//交易订单查询
        $Time_Start = I('Time_Start');
        $Time_end = I('Time_End');
        $out_trade_no = I('out_trade_no', "");

        $SystemUserSysNo = I('SystemUserSysNo', "");
        $PageNumber = I('PageNumber');
        $PageSize = I('PageSize');
        $CustomerNames = I('CustomerNames');
        $Ordertype = I('Ordertype');
        $ButtonType = I('ButtonType');
        $data = array("Time_Start" => $Time_Start, "Time_end" => $Time_end, "Out_trade_no" => $out_trade_no, "Pay_Type" => $Ordertype,

        );
        $flag = session('flag');//服务商商户0 或员工1
        $type = session('servicestoretype');//员工的服务商的类型 0为服务  1为商户
//        if (session('data')['CustomersType'] == 0 & $type == 0) {
//            $stafftype = 0;
//        }

        if ((session('data')['CustomersType'] == 0 &$flag == 0)|| $type == 0&$flag == 1) {//服务商或者服务商员工登陆 必须填写商户名进行查询，不需要传递CustomerSysNo
            $data['Customer'] = I('Customer');
            $data['CustomerName'] = I('CustomerNames');
        }

        if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆 传入主键
            $data['CustomersTopSysNo'] = session('SysNO');
            $data['CustomerName'] = I('CustomerNames');
        }
        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
            $data['LoginName'] = I('Customer');
            $data['DisplayName'] = I('CustomerNames');
            $data['TerminalID'] = I('one');
            $data['UserMember'] = I('two');
            $data['CustomerSysNo'] = session('SysNO');

        }
        if($type==1&$flag==1){
            $data['Customer'] = I('Customer');
            $data['CustomerName'] = I('CustomerNames');
            $data['TerminalID'] = I('one');
            $data['UserMember'] = I('two');
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
            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商

            } else if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户

                    $url = C('SERVER_HOST') . "IPP3Order/CCBSo_MasterShop";

            } else if ($type == 0 & $flag == 1) {//服务商员工
            } else if ($type == 1 & $flag == 1) {//商户员工
                $url = C('SERVER_HOST') . "IPP3Order/CCBSo_MasterShopUser";
            }


        } else if ($ButtonType == 1) {
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户
                $url = C('SERVER_HOST') . "IPP3Order/CCBSo_Master_Fund_Shop";
            }
            if ($type == 1 & $flag == 1) {//商员工登陆 传入主键
                $url = C('SERVER_HOST') . "IPP3Order/CCBSo_Master_Fund_ShopUser";
            }

        }



        $data['PagingInfo']['PageSize'] = $PageSize;
        $data['PagingInfo']['PageNumber'] = $PageNumber;

        $list = http($url,$data);

        if ($ButtonType == 0) {
            foreach ($list['Data']['model'] as $row => $val) {
                $info['model'][$row]['SysNo'] = $val['SysNo'];
                $info['model'][$row]['loginname'] = $val['LoginName'];
                $info['model'][$row]['displayname'] = $val['DisplayName'];
                $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
                $info['model'][$row]['Pay_Type'] =CheckCCBCOrderType($val['Pay_Type']);
                $info['model'][$row]['Total_fee'] = fee2yuan($val['Total_fee']);
                $info['model'][$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);
                $info['model'][$row]['fee'] = fee2yuan($val['fee']);
                $info['model'][$row]['Time_Start'] = $val['Time_Start'];
                $info['model'][$row]['Customer'] = $val['Customer'];
                $info['model'][$row]['CustomerName'] = $val['CustomerName'];
                $info['model'][$row]['two'] = $val['Field_two'];
                $info['model'][$row]['one'] = $val['Field_one'];
            }
            $info['totalCount'] = $list['Data']['totalCount'];
            $info['ButtonType'] = $ButtonType;
            if (session(flag) == 1) {
                $list['flag'] = session('servicestoretype');
            }
        } else if ($ButtonType == 1) {
            foreach ($list['Data']['model'] as $row => $val) {
                $info['model'][$row]['loginname'] = $val['LoginName'];
                $info['model'][$row]['total_fee'] = fee2yuan($val['Total_fee']);
                $info['model'][$row]['fee'] = fee2yuan($val['fee']);
                $info['model'][$row]['Refund_fee'] = fee2yuan($val['Refund_fee']);
                $info['model'][$row]['tradecount'] = $val['Tradecount'];
                $info['model'][$row]['displayname'] = $val['DisplayName'];
                $info['model'][$row]['one'] = $val['Field_One'];
            }
            $info['totalCount'] = $list['Data']['totalCount'];
            $info['ButtonType'] = $ButtonType;
            if (session(flag) == 1) {
                $list['flag'] = session('servicestoretype');
            }

        }
        $this->ajaxReturn($info);

    }












    public function showtotalfee() {
        $Time_Start = I('Time_Start');
        $Time_end = I('Time_End');
        $out_trade_no = I('out_trade_no', "");
        $SystemUserSysNo = I('SystemUserSysNo', "");
        $PageNumber = I('PageNumber');
        $PageSize = I('PageSize');
        $CustomerNames = I('CustomerNames');
        $Customer = I('Customer');
        $Ordertype = I('Ordertype');
        $ButtonType = I('ButtonType');

        $data = array("Time_Start" => $Time_Start, "Time_end" => $Time_end, "Out_trade_no" => $out_trade_no, "CustomerName" => $CustomerNames, "Pay_Type" => $Ordertype, "Customer" => $Customer

        );
        $data['TerminalID'] = I('one');
        $data['UserMember'] = I('two');
        $flag = session('flag');
        if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆 传入主键
        }
        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登陆 传入主键

				$data['CustomerSysNo'] = session('SysNO');
        }
        if (session('servicestoretype') == 0 & $flag == 1) {

        }
        if (session('servicestoretype') == 1 & $flag == 1) {
            $data['SystemUserSysNo'] = session('SysNO');
        }
        if (session('data')['CustomersType'] == 1 & $flag == 0) {

            $url = C('SERVER_HOST') . "IPP3Order/CCBSo_Master_Group_Shop";
            $totalcount = http($url,$data);
            $info['total']['Total_fee'] = fee2yuan($totalcount['Data']['Total_fee']);
            $info['total']['Fee'] = fee2yuan($totalcount['Data']['fee']);
            $info['total']['Refund_fee'] = fee2yuan($totalcount['Data']['Refund_fee']);
            $info['total']['Tradecount'] = $totalcount['Data']['Tradecount'];
        } else if (session('data')['CustomersType'] == 0 & $flag == 0) {

        } else if (session('servicestoretype') == 0 & session('flag') == 1) {


        }else if (session('servicestoretype') == 1 & session('flag') == 1) {
            $url = C('SERVER_HOST') . "IPP3Order/CCBSo_Master_Fund_ShopUser";
            $list = http($url,$data);

            foreach ($list['Data']['model'] as $row => $val) {
                $info['total']['Total_fee'] = fee2yuan($val['Total_fee']);
                $info['total']['Fee'] = fee2yuan($val['fee']);
                $info['total']['Refund_fee'] = fee2yuan($val['Refund_fee']);
                $info['total']['Tradecount'] = $val['Tradecount'];
            }

        }
//				\Think\Log::record(json_encode($data));

        $this->ajaxreturn($info);


    }







}