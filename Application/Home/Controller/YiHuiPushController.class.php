<?php


namespace Home\Controller;


//use Think\Controller;


use Common\Compose\Base;


class YiHuiPushController extends Base {


    public function yihui_push() {


        R("Base/getMenu");

        $this->display();


    }


    public function yihuipush() {

        $Time_Start =$_POST['Time_Start'];
        $Time_end =$_POST['Time_End'];
        $out_trade_no = I('out_trade_no', "");
        $PageNumber = $_POST['PageNumber'];
        $PageSize = $_POST['PageSize'];
        $Ordertype = $_POST['Ordertype'];
        $transcationnum = $_POST['transcationnum'];
        $SystemUserSysNo = I('SystemUserSysNo', "");
        $flag = session('flag');//服务商商户0 或员工1
        $type = session('servicestoretype');//员工的服务商的类型 0为服务  1为商户
//        if (session('data')['CustomersType'] == 0 & $type == 0) {
//            $stafftype = 0;
//        }
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
        if ($type == 1 & $flag == 1) {//商户员工
            if ($Ordertype==102) {
                $url  = C( 'SERVER_HOST' ) . "IPP3Order/IPP3HandPushPageList";
                $data = array(
                    "Time_Start" => $Time_Start,
                    "Time_End" => $Time_end,
                    "Out_trade_no" => $out_trade_no,
                    "Type" => '微信',
                    "SystemUserSysNo" => session('SysNO'),
                    "Transaction_id" => $transcationnum
                );
                $data['PagingInfo']['PageSize'] = $PageSize;
                $data['PagingInfo']['PageNumber'] = $PageNumber;
                $list  = http($url, $data);

                $info['Type'] = 102;
            }

            if ($Ordertype==103) {
                $url  = C( 'SERVER_HOST' ) . "IPP3Order/IPP3So_MasterNotifyLogListAli";
                $data = array(
                    "Time_Start" => $Time_Start,
                    "Time_End" => $Time_end,
                    "Out_trade_no" => $out_trade_no,
                    "Pay_Type" => $Ordertype,
                    "SystemUserSysNo" => session('SysNO'),
                    "Transaction_id" => $transcationnum
                );
                $data['PagingInfo']['PageSize'] = $PageSize;
                $data['PagingInfo']['PageNumber'] = $PageNumber;
                $list  = http($url, $data);

                $info['Type'] = 103;
            }
            foreach ($list['model'] as $row => $val) {
                $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
                $info['model'][$row]['Transaction_id'] = $val['Transaction_id'];
                $info['model'][$row]['Total_fee'] = fee2yuan($val['Total_fee']);
                $info['model'][$row]['Time_Start'] = str_replace('/','-',$val['CreateTime']);
            }
            $info['totalCount'] = $list['totalCount'];
        }


        $this->ajaxReturn( $info );
    }

    public function startmanualpush() {
        $Out_trade_no = I('Out_trade_no', "");
        $Ordertype = $_POST['Type'];
        $Transaction_id = $_POST['Transaction_id'];

        if ($Ordertype==102) {
            $url  = C( 'SERVER_HOST' ) . "IPP3Order/IPP3HandPushWX";
            $data = array(
                "Out_trade_no" => $Out_trade_no,
                "Transaction_id" => $Transaction_id,
                "Type" => "微信",
                "CustomerSysNo" => session('servicestoreno'),
                "SystemUserSysNo" => session('SysNO')
            );
        }
        if ($Ordertype==103) {
            $url  = C( 'SERVER_HOST' ) . "IPP3Order/IPP3HandPushAli";
            $data = array(
                "Out_trade_no" => $Out_trade_no,
                "Transaction_id" => $Transaction_id,
                "Type" => $Ordertype,
                "CustomerSysNo" => session('servicestoreno'),
                "SystemUserSysNo" => session('SysNO')
            );
        }
        $list  = http($url, $data);
        if($list['Code']==0){
            $return_info['Description'] ='推送成功！';
        }else{
            $return_info['Description'] ='推送失败！';
        }
        $this->ajaxReturn( $return_info );
    }






}