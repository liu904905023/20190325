<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class YLRateController extends Base {

    /*服务商网商配置*/
    public function index(){
        if (IS_POST) {
            $Type                    = I('Type');
            $sh_status   = I('sh_status');

            $Data['CustomerServiceSysNo']           = I('CustomerSysNo');
            if ($Type == 116) {
                $Data['ReqModel']['rate']['feeRateWechatpay'] = I('feeRateWechatpay')*1000;
                $Data['PayType']           = "WX";


            } else if ($Type == 117) {
                $Data['ReqModel']['rate']['feeRateAlipay'] = I('feeRateAlipay')*1000;
                $Data['PayType']           = "AliPay";


            } else if ($Type == 118) {
                if ($sh_status == 0) {
                    $Data['ReqModel']['rate']['bankCardRateLevel1']['feeRateUnionpayDebit']    = I('feeRateUnionpayDebit')*1000;
                    $Data['ReqModel']['rate']['bankCardRateLevel1']['feeRateUnionpayDebitCap'] = I('feeRateUnionpayDebitCap')*1000;
                    $Data['ReqModel']['rate']['bankCardRateLevel1']['feeRateUnionpayCredit']   = I('feeRateUnionpayCredit')*1000;
                } else {
                    $Data['ReqModel']['rate']['bankCardRateLevel2']['feeRateUnionpayDebit']    = I('feeRateUnionpayDebit')*1000;
                    $Data['ReqModel']['rate']['bankCardRateLevel2']['feeRateUnionpayDebitCap'] = I('feeRateUnionpayDebitCap')*1000;
                    $Data['ReqModel']['rate']['bankCardRateLevel2']['feeRateUnionpayCredit']   = I('feeRateUnionpayCredit')*1000;
                }
                $Data['PayType']           = "QUICKPASS";
            }
            $Url = C('SERVER_HOST') . 'IPP3YLPay/MerchantModify';
            $list = http($Url, $Data);
            $this->ajaxReturn($list);
        }else{
            R("Base/getMenu");

            $this->display();

        }
    }
    public function merchantQuery(){
        if (IS_POST) {
            $Type                    = I('Type');
            $Data['CustomerServiceSysNo']           = I('CustomerSysNo');
            if ($Type == 116) {
                $Data['PayType']           = "WX";
            } else if ($Type == 117) {
                $Data['PayType']           = "AliPay";
            } else if ($Type == 118) {
                $Data['PayType']           = "QUICKPASS";
            }
            $Url = C('SERVER_HOST') . 'IPP3YLPay/MerchantQuery';
            $List = http($Url, $Data);
            if($List['Code']==0){
                switch ($List['Data']['auditStatus']){
                    case '1':
                        $status = '待审核';
                        break;
                    case '2':
                        $status = '已完成';
                        break;
                    case '3':
                        $status = '审核';
                        break;
                }
                $Info = json_decode($List['Data']['rate'], true);
                $Info['status'] = $status;
                $Info['Code'] = 0;
            }else{
                $Info['Code'] = 1;
            }

            $this->ajaxReturn($Info);
        }else{
            R("Base/getMenu");

            $this->display();

        }
    }

    public function queryMchid() {
        $Type = I('Type');
        $Data['Type'] = $Type;
        $CustomerSysNo = I('CustomerSysNo');
        $Data['CustomerSysNo'] = $CustomerSysNo;
        $Url = C('SERVER_HOST') . 'IPP3Customers/CustomerServicePassageWayList';
        $List = http($Url, $Data);
        if ($List[0]['SysNo']) {
            $InfoData['CustomerPassageWaysSysNo'] = $List[0]['SysNo'];
            $InfoData['CustomerServiceSysNo'] = $CustomerSysNo;
            if ($Type == '116' || $Type == '118') {
                $InfoUrl = C('SERVER_HOST') . "IPP3Customers/YL_PassageConfigList";

            }else if ($Type == '117') {
                $InfoUrl =  C('SERVER_HOST') ."IPP3Customers/YL_PassageConfigList_Ali";
            }
            $InfoList = http($InfoUrl, $InfoData);
            $ReturnList['MCHID'] = $InfoList['Data']['MCHID'];
            $ReturnList['Code'] = 0;

        }else{
            $ReturnList['Code'] = 1;
        }

        $this->ajaxReturn($ReturnList);

    }

}

