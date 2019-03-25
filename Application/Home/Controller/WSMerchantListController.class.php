<?php

namespace Home\Controller;

//use Think\Controller;

use Common\Compose\Base;

class WSMerchantListController extends Base {



    public function ws_merchant_list(){

        R("Base/getMenu");

        $this->display();

    }
    public function ws_merchant_list_rblue(){

        R("Base/getMenu");

        $this->display();

    }

    public function wsmerchantlistrblue() {


        $data['TimeBegin'] = I('Time_Start', "");
        $data['TimeEnd'] = I('Time_end', "");
//        $data['Type'] = I('Type', "");
        $data['OutMerchantId'] = I('merchantnumber', "");
        $data['CustomerServiceSysNo'] = '';
        $data['ApplyStatus'] = I('Type');

        if (session('data')['CustomersType'] == 0 & session('flag') == 0) {//服务商登陆

            $data["CustomersTopSysNo"]=session('SysNO');    //服务商主键

            $data["Customer"]=I('Customer', "");            //商户用户名

            $data["CustomerName"]=I('CustomerName', "");    //商户名称

        }elseif (session('data')['CustomersType'] == 1 & session('flag') == 0) {//商户登录

            $data["CustomerServiceSysNo"]=session('SysNO'); //商户主键

        }else{

            $Return_Data['Code'] = 1;

            $Return_Data['Description'] ="该角色无权限,进行该操作!";

            $this->ajaxReturn($Return_Data);

            exit();

        }

        $data['PagingInfo']['PageSize'] = I('PageSize',"");

        $data['PagingInfo']['PageNumber'] = I('PageNumber',"");

        $url  = C('SERVER_HOST')."IPP3WSCustomer/WS_Merchant_RblueList";

        $list = http( $url, $data );


        foreach ($list['Data']['model'] as $row=>$val){

            $info['model'][$row]['SysNo']=str_replace("null","",$val['SysNo']);

            $info['model'][$row]['CustomerServiceSysNo']=str_replace("null","",$val['CustomerServiceSysNo']);

            $info['model'][$row]['Customer']=str_replace("null","",$val['Customer']);

            $info['model'][$row]['CustomerName']=str_replace("null","",$val['CustomerName']);

            $info['model'][$row]['OutMerchantId']=str_replace("null","",$val['OutMerchantId']);

            $info['model'][$row]['OrderNo']=str_replace("null","",$val['OrderNo']);

            $info['model'][$row]['MerchantId']=str_replace("null","",$val['MerchantId']);

            $info['model'][$row]['AccountNo']=str_replace("null","",$val['AccountNo']);

            $info['model'][$row]['Smid']=str_replace("null","",$val['Smid']);

            $info['model'][$row]['ChannelId']=str_replace("null","",$val['ChannelId']);

            $info['model'][$row]['WechatMerchId']=str_replace("null","",$val['WechatMerchId']);

            $info['model'][$row]['Type']=str_replace("null","",$val['Type']);

            $info['model'][$row]['Status']=str_replace("null","",$val['Status']);

            $info['model'][$row]['ActivityType']=str_replace("null","",$val['ActivityType']);

            $info['model'][$row]['OutTradeNo']=str_replace("null","",$val['OutTradeNo']);

            $info['model'][$row]['OrderId']=str_replace("null","",$val['OrderId']);

            $info['model'][$row]['ApplyStatus']=str_replace("null","",$val['ApplyStatus']);

            $info['model'][$row]['QueryJson']=$val['QueryJson'];

            $info['model'][$row]['RegisterTime']=date('Y-m-d H:i:s', strtotime($val['RegisterTime']));


        }

        $info['totalCount'] =$list['Data']['totalCount'];

        $this->ajaxReturn($info);



    }

    //商户入驻列表查询

    public function wsmerchantlist(){

        $data = array(

            "TimeBegin"     => I('Time_Start',""),      //开始时间

            "TimeEnd"       => I('Time_end',""),        //结束时间

            "Type"          => I('Type', ""),           //类型

            "OutMerchantId" => I('merchantnumber',"")   //外部商户号

        );

        $flag = session('flag');//服务商商户0 或员工1

        if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆

            $data["CustomersTopSysNo"]=session('SysNO');    //服务商主键

            $data["Customer"]=I('Customer', "");            //商户用户名

            $data["CustomerName"]=I('CustomerName', "");    //商户名称

        }elseif (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录

            $data["CustomerServiceSysNo"]=session('SysNO'); //商户主键

        }else{

            $Return_Data['Code'] = 1;

            $Return_Data['Description'] ="该角色无权限,进行该操作!";

            $this->ajaxReturn($Return_Data);

            exit();

        }

        $data['PagingInfo']['PageSize'] = I('PageSize',"");

        $data['PagingInfo']['PageNumber'] = I('PageNumber',"");

        $url  = C('SERVER_HOST')."IPP3WSCustomer/WSMerchantList";

        $list = http( $url, $data );

        foreach ($list['Data']['model'] as $row=>$val){

            $info['model'][$row]['SysNo']=str_replace("null","",$val['SysNo']);

            $info['model'][$row]['CustomerServiceSysNo']=str_replace("null","",$val['CustomerServiceSysNo']);

            $info['model'][$row]['Customer']=str_replace("null","",$val['Customer']);

            $info['model'][$row]['CustomerName']=str_replace("null","",$val['CustomerName']);

            $info['model'][$row]['OutMerchantId']=str_replace("null","",$val['OutMerchantId']);

            $info['model'][$row]['OrderNo']=str_replace("null","",$val['OrderNo']);

            $info['model'][$row]['MerchantId']=str_replace("null","",$val['MerchantId']);

            $info['model'][$row]['AccountNo']=str_replace("null","",$val['AccountNo']);

            $info['model'][$row]['Smid']=str_replace("null","",$val['Smid']);

            $info['model'][$row]['ChannelId']=str_replace("null","",$val['ChannelId']);

            $info['model'][$row]['WechatMerchId']=str_replace("null","",$val['WechatMerchId']);

            $info['model'][$row]['Type']=str_replace("null","",$val['Type']);

            $info['model'][$row]['Status']=str_replace("null","",$val['Status']);

        }

        $info['totalCount'] =$list['Data']['totalCount'];

        $this->ajaxReturn($info);



    }



    //商户入驻结果查询

    public function wsmerchantentry(){



        if (I('orderno',"")=="") {

            $Return_Data['Code'] = 1;

            $Return_Data['Description'] ="申请单号为空，商户入驻结果查询失败！";

            $this->ajaxReturn($Return_Data);

            exit();

        }

        $data = array(

            "CustomerServiceSysNo"  => I('CustomerServiceSysNo',""),//商户主键

            "SysNo"	                => I('sysno',""),               //商户信息主键

        );

        $data['ReqModel']['OrderNo'] = I('orderno',"");             //申请单号

        $url  = C('SERVER_HOST')."IPP3WSCustomer/WSMerchantEntryQueryUnion";

        $list = http( $url, $data );
        if($list){
            if ($list['Code']==0) {

                $info["Code"]=0;
                $info["Description"]=$list['Description'];

            }else{
                $info["Code"]=1;
                $info["Description"]="商户入驻审核失败！".$list['Data']['FailReason']."!";

            }
        }else{
            $info['Code'] = 1;
            $info['Description'] = '系统异常，请稍后再试！';
        }


        $this->ajaxReturn($info);

    }



    //余利宝签约

    public function sign(){

        $CustomerServiceSysNo = I('customerservicesysno',"");   //商户主键

        $MerchantId           = I('merchantid',"");             //商户号

        $SysNo                = I('sysno',"");                  //商户信息主键

        if ($CustomerServiceSysNo=="") {

            $Return_Data['Code'] = 1;

            $Return_Data['Description'] ="商户主键为空值，不能签约！";

            $this->ajaxReturn($Return_Data);

            exit();

        }

        if ($MerchantId=="") {

            $Return_Data['Code'] = 1;

            $Return_Data['Description'] ="商户号为空值，不能签约！";

            $this->ajaxReturn($Return_Data);

            exit();

        }

        if ($SysNo=="") {

            $Return_Data['Code'] = 1;

            $Return_Data['Description'] ="商户信息主键为空，不能签约！";

            $this->ajaxReturn($Return_Data);

            exit();

        }

        $data = array(

            "CustomerServiceSysNo" => $CustomerServiceSysNo,

            "MerchantId"	       => $MerchantId,

            "SysNo"	               => $SysNo

        );


        //签约结果查询

        $url  = C('SERVER_HOST')."IPP3WSCustomer/WSAccountOpenQuery";

        $list = http( $url, $data );

        if ($list['Code']==0) {

            $Return_Data['Code'] = 0;

            $Return_Data['Description'] = "商户签约成功！";

            $this->ajaxReturn($list);

            exit();

        }else{

            $data_sign = array(

                "CustomerServiceSysNo" => $CustomerServiceSysNo,    //商户主键

                "MerchantId"           => $MerchantId               //商户号

            );

            //签约

            $url_sgin = C('SERVER_HOST')."IPP3WSCustomer/WSAccountOpen";

            $list_sign = http( $url_sgin, $data_sign );


            if ($list_sign['Code']==0) {

                $data_sgin_second = array(

                    "CustomerServiceSysNo" => $CustomerServiceSysNo,

                    "MerchantId"	       => $MerchantId,

                    "SysNo"	               => $SysNo

                );

                //签约结果查询

                $url_sgin_second  = C('SERVER_HOST')."IPP3WSCustomer/WSAccountOpenQuery";

                $list_sgin_second = http( $url_sgin_second, $data_sgin_second );


                if ($list_sgin_second['Code']==0) {

                    $Return_Data['Code'] = 0;

                    $Return_Data['Description'] = "商户签约成功！";

                    $this->ajaxReturn($Return_Data);

                    exit();

                }else{

                    $Return_Data['Code'] = 1;

                    $Return_Data['Description'] = $list_sgin_second['Description'];

                    $this->ajaxReturn($Return_Data);

                    exit();

                }

            }else{

                $Return_Data['Code'] = 1;

//              $str="您使用的私钥格式错误,请检查RSA私钥配置,charset =";

                $str = $list_sign['Description'];

                $new = substr($str,0,strrpos($str,'格式错误'));

                if ($new=="您使用的私钥") {

                    $new1 = substr($str,0,strrpos($str,','))."!";

                    $Return_Data['Description'] = $new1;

                }else{

                    $Return_Data['Description'] = $list_sign['Description'];

                }

                $this->ajaxReturn($Return_Data);

                exit();

            }

        }



    }











}