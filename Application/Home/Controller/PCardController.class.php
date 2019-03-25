<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class PCardController extends Base {

    public function p_card(){

        R("Base/getMenu");
        $this->display();

    }
    public function refund_error_pcard_list(){

        R("Base/getMenu");
        $this->display();

    }
    public function supply_order(){

        R("Base/getMenu");
        $this->display();

    }

    public function PCardList(){

        $Time_Start     = $_POST['Time_Start'];
        $Time_end       = $_POST['Time_end'];
        $Out_trade_no   = $_POST['out_trade_no'];
        $Ordertype      = $_POST['Ordertype'];
        $PageNumber     = $_POST['PageNumber'];
        $PageSize       = $_POST['PageSize'];
        $data = array(
            "Time_Start"    =>$Time_Start,
            "Time_end"      =>$Time_end,
            "Out_trade_no"  =>$Out_trade_no,
            "Pay_Type"      =>$Ordertype
        );
        $data['PagingInfo']['PageSize']     = $PageSize;
        $data['PagingInfo']['PageNumber']   = $PageNumber;
        $flag = session('flag');            //服务商商户0 或 员工1
        $type = session('servicestoretype');//员工的服务商的类型 0为服务  1为商户
        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
            $data['CustomerSysNo']=session(SysNO);
        }
        if ($type == 1 & $flag == 1) {//商户员工
            $data['Old_SysNo']=session(SysNO);
        }
        $url = C('SERVER_HOST')."IPP3PoliceCard/PoliceCardList";
        $list = http($url,$data);
        foreach ($list['model'] as $row=>$val){
            $info['model'][$row]['Customer']=$val['Customer'];              //商户号
            $info['model'][$row]['CustomerName']=$val['CustomerName'];      //商户名称
            $info['model'][$row]['loginname']=$val['LoginName'];            //登录名
            $info['model'][$row]['displayname']=$val['DisplayName'];        //真实姓名
            $info['model'][$row]['Total_fee']=fee2yuan($val['Total_fee']);  //金额
            $info['model'][$row]['Fee']=fee2yuan($val['Fee']);              //可退金额
            $info['model'][$row]['Cash_fee']=fee2yuan($val['Cash_fee']);    //折后金额
            $info['model'][$row]['Out_trade_no']=$val['Out_trade_no'];      //订单号
            $info['model'][$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']);//交易类型
            $info['model'][$row]['CompanyId']=$val['CompanyId'];            //商户编号
            $info['model'][$row]['CardNumber']=$val['CardNumber'];          //卡号
            $info['model'][$row]['Time_Start']=$val['Time_Start'];          //交易时间
        }
        $info['totalCount'] =$list['totalCount'];
        $this->ajaxReturn($info,json);

    }

    public function checkuserpass(){

        $password = I("password");
        $data = 0;
        if(session(password)==$password){
            $data = 1;
        }
        $this->ajaxReturn( $data ,json);

    }
    //退款
    public function refundinsert(){

        $out_trade_no = $_POST['out_trade_no'];
        $total_fee = yuan2fee($_POST['total_fee']);
        $refund_fee = yuan2fee($_POST['refund_fee']);
        $SOSysNo = $_POST['SOSysNo'];
        $paytype = 'WX';
        $timestart = $_POST['timestart'];
        $time=explode(" ",$timestart);
        $Ymd = $time[0];
        $NowDay = date("Y-m-d",time());
        //权限功能列表-url
        $CheckRefund_Url = C('SERVER_HOST') . "IPP3Customers/IPP3RoleApplicationList";
        $CheckRefund_Data['SystemUserSysNo'] =session(SysNO);
        $CheckRefund_List = http($CheckRefund_Url, $CheckRefund_Data);
        if(strtotime($Ymd)==strtotime($NowDay)){

        }else{
            if($CheckRefund_List['Data'][0]['ApplicationSysNo']==1){

            }else{
                $list['Description'] = "非当天交易不允许退款";
                $this->ajaxReturn($list, json);
                exit();
            }
        }
        $data = array(
            "YwMch_id2"=>session(SysNO),
            "Total_Fee"=>$total_fee,
            "Refund_Fee"=>$refund_fee,
            "cardNumber"=>$SOSysNo,
            "Transaction_ID"=>$out_trade_no,
            "Pay_Type"=>$paytype
        );
        $url = C('SERVER_HOST')."IPP3PoliceCard/PoliceCardRefund";
        $list = http($url,$data);
        if ($list['Code']==0) {
            $list['Description']="退款成功！";
        } else {
            $list['Description']="退款失败！";
        }
        $this->ajaxReturn( $list ,json);

    }
    //退款异常列表
    public function refunderrorpcardlist(){



        $data = array(

            "Time_Start"     => I('Time_Start',""),      //开始时间

            "Time_End"       => I('Time_end',""),        //结束时间

            "CardNumber" => I('CardNumber',"")             //卡号

        );

        $flag = session('flag');//服务商商户0 或员工1

        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录

            $data["CustomerSysNo"] = session('SysNO'); //商户主键

        }elseif (session('servicestoretype') == 1 & $flag == 1){//商户员工登录

            $data["SystemUserSysNo"] = session('SysNO'); //商户员工主键

        }else{

            $Return_Data['Code'] = 1;

            $Return_Data['Description'] ="该角色无权限,进行该操作!";

            $this->ajaxReturn($Return_Data);

            exit();

        }

        $data['PagingInfo']['PageSize'] = I('PageSize',"");

        $data['PagingInfo']['PageNumber'] = I('PageNumber',"");

        $url  = C('SERVER_HOST')."IPP3PoliceCard/RefundErrorIPoliceCardList";

        $list = http( $url, $data );

        foreach ($list['model'] as $row=>$val){

            $info['model'][$row]['LoginName']=str_replace("null","",$val['LoginName']);

            $info['model'][$row]['DisplayName']=str_replace("null","",$val['DisplayName']);

            $info['model'][$row]['Out_trade_no']=str_replace("null","",$val['Out_trade_no']);

            $info['model'][$row]['CardNumber']=str_replace("null","",$val['CardNumber']);

            $info['model'][$row]['Out_refund_no']=str_replace("null","",$val['Out_refund_no']);

            $info['model'][$row]['Amount']=str_replace("null","",fee2yuan($val['Amount']));

            $info['model'][$row]['Balance']=str_replace("null","",fee2yuan($val['Balance']));

            $info['model'][$row]['CreateTime']=str_replace("null","",$val['CreateTime']);

            $info['model'][$row]['Description']=str_replace("null","",$val['Description']);


        }

        $info['totalCount'] =$list['totalCount'];
        $this->ajaxReturn($info);

    }
    //警察学院补单
    public function supplyorder(){
        $data = array(
            "Out_trade_no" => trim(I('out_trade_no',""))
        );

        if (session('servicestoretype') == 1 &  session('flag') == 1){//商户员工登录
            $data["SystemUserSysNo"] = session('SysNO'); //商户员工主键
        }else{
            $info = "该角色无权限,进行该操作!";
            $this->ajaxReturn($info);
            exit();
        }
        $url  = C('SERVER_HOST')."IPP3PoliceCard/PoliceCardSwiftSupplyOrder";
        $list = http( $url, $data );
        $info = $list['Description'];
        $this->ajaxReturn($info);

    }


}