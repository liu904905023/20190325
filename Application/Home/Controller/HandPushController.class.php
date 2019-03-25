<?php
namespace Home\Controller;

//use Think\Controller;
use Common\Compose\Base;

class HandPushController extends Base {

    public function hand_push() {
        R("Base/getMenu");
        $this->display();
    }

    public function handpush() {//交易订单查询
        $Time_Start = $_POST['Time_Start'];
        $Time_end = $_POST['Time_End'];
        $out_trade_no = I('out_trade_no', "");
        $transcationnum = I('transcationnum', "");

        $SystemUserSysNo = I('SystemUserSysNo', "");
        $PageNumber = $_POST['PageNumber'];
        $PageSize = $_POST['PageSize'];
        $CustomerNames = $_POST['CustomerNames'];
        $Ordertype = $_POST['Ordertype'];
        $ButtonType = $_POST['ButtonType'];
        $data = array("Time_Start" => $Time_Start, "Time_end" => $Time_end, "Out_trade_no" => $out_trade_no, "Type" => '银行', "Transaction_id" => $transcationnum

        );
        $flag = session('flag');//服务商商户0 或员工1
        $type = session('servicestoretype');//员工的服务商的类型 0为服务  1为商户
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
                $url = C('SERVER_HOST') . "IPP3Order/IPP3HandPushPageList";
            }

        $data['PagingInfo']['PageSize'] = $PageSize;
        $data['PagingInfo']['PageNumber'] = $PageNumber;
//        var_dump(json_encode($data));
//        echo $url;
//        exit();

        $list = http($url,$data);

            foreach ($list['model'] as $row => $val) {
                $info['model'][$row]['SysNo'] = $val['SysNo'];
                $info['model'][$row]['loginname'] = $val['LoginName'];
                $info['model'][$row]['displayname'] = $val['DisplayName'];
                $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
                $info['model'][$row]['Transaction_id'] = $val['Transaction_id'];
                $info['model'][$row]['Total_fee'] = fee2yuan($val['Total_fee']);
                $info['model'][$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);
                $info['model'][$row]['fee'] = fee2yuan($val['fee']);
                $info['model'][$row]['Time_Start'] = str_replace('/','-',$val['CreateTime']);

        }
        $info['totalCount'] = $list['totalCount'];

        $this->ajaxReturn($info);

    }

    public function starthandpush() {
        $data['Out_trade_no']=I('Out_trade_no');
        $data['Transaction_id']=I('Transaction_id');
        $data['systemUserSysNo']=session('SysNO');
        $data['CustomerSysNo']=session('servicestoreno');
        $url = C('SERVER_HOST') . "IPP3Order/IPP3HandPush";
        $list = http($url,$data);
        if($list['Code']==0){
            $return_info['Description'] ='推送成功！';
        }else{
            $return_info['Description'] ='推送失败！';
        }
        $this->ajaxReturn( $return_info );
    }
    public function ws_hand_push_list() {
        R("Base/getMenu");
        $this->display();
    }
    public function wshandpushlist() {
        $data['Transaction_id']=I('transcationnum');
        $data['Out_trade_no']=I('out_trade_no');
        $data['SystemUserSysNo']=session('SysNO');
        $data['Time_Start']=I('Time_Start');
        $data['Time_End']=I('Time_End');
        $data['Type']='网商';

        $url = C('SERVER_HOST') . "IPP3Order/IPP3So_MasterNotifyLogListWS";

        $list = http($url, $data);
        foreach ($list['model'] as $row => $val) {
            $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
            $info['model'][$row]['Transaction_id'] = $val['Transaction_id'];
            $info['model'][$row]['Total_fee'] = fee2yuan($val['Total_fee']);
            $info['model'][$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);
            $info['model'][$row]['fee'] = fee2yuan($val['fee']);
            $info['model'][$row]['Time_Start'] = str_replace('/','-',$val['CreateTime']);

        }
        $info['totalCount'] = $list['totalCount'];
        $this->ajaxreturn($info);

    }
    public function wshandpush() {
        $data['Out_trade_no']=I('Out_trade_no');
        $data['Transaction_id']=I('Transaction_id');
        $data['systemUserSysNo']=session('SysNO');
        $data['CustomerSysNo']=session('servicestoreno');
        $url = C('SERVER_HOST') . "IPP3Order/IPP3HandPushWS";
        $list = http($url,$data);
        if($list['Code']==0){
            $return_info['Description'] ='推送成功！';
        }else{
            $return_info['Description'] ='推送失败！';
        }
        $this->ajaxReturn( $return_info );

    }



}