<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25
 * Time: 13:14
 */

namespace Home\Controller;

use Common\Compose\Base;

class LMFConfigController extends Base
{
    public function index()
    {
        if (IS_POST) {
            if ((session('data')['CustomersType'] == 0 & session('flag') == 0)) {

                /*插入之前先查询，看看数据库中是否有该数据*/
                $Query_Url =  C('SERVER_HOST') . "IPP3Customers/LMFConfigList";
                $Query_Data['CustomerServiceSysNo'] = session('data')['SysNo'];
                $Query_Return = http($Query_Url, $Query_Data);

                $Update_Or_Insert_Data = array(
                    'OrgNo'                => I('OrgNo', '', 'htmlspecialchars'),
                    'KEY'                  => I('KEY', '', 'htmlspecialchars'),
                    'CustomerServiceSysNo' => session('data')['SysNo'],
                    'Field_one'            => "",
                    'Field_two'            => "",
                    'Field_three'          => "",
                );
                if($Query_Return['Data']){
                   /*执行插入接口*/
                    $Update_Url = C('SERVER_HOST') . "IPP3Customers/LMFConfigUpdate";
                    $Insert_Return = http($Update_Url, $Update_Or_Insert_Data);
                    $this->ajaxReturn( $Insert_Return );
                }else{
                    /*执行插入接口*/
                    $Insert_Url  = C('SERVER_HOST') . "IPP3Customers/LMFConfigInsert";
                    $Insert_Return = http($Insert_Url, $Update_Or_Insert_Data);
                    $this->ajaxReturn( $Insert_Return );
                }
                exit();


            } else {
                $arrData['Code']        = 1;
                $arrData['Description'] = "该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
        } else {
            R("Base/getMenu");
            if (session('data')['CustomersType'] == 0 & session('flag') == 0) {
                $Query_Url  = C( 'SERVER_HOST' ) . "IPP3Customers/LMFConfigList";
                $Query_Data['CustomerServiceSysNo'] = session('data')['SysNo'];
                $arrData  = http( $Query_Url, $Query_Data );
                $this->assign( 'data', $arrData['Data'] );
            }else{
                $this->assign( 'passtype', -1 );
            }
        }


        $this->display();

    }

    public function AliConfig()
    {
        if ((session('data')['CustomersType'] == 1 & session('flag') == 0)) {
            $Post_PassageWay_Data['CustomerSysNo']            = session('data')['SysNo'];
            $Post_PassageWay_Data['Type']                     = 115;
            $Post_PassageWay_Url                              = C('SERVER_HOST') . 'IPP3Customers/CustomerServicePassageWayList';
            $Post_PassageWay_list                             = http($Post_PassageWay_Url, $Post_PassageWay_Data);
            $Query_Or_Insert_Data['CustomerPassageWaysSysNo'] = $Post_PassageWay_list[0]['SysNo'];

            $Query_Or_Insert_Data['CustomerServiceSysNo'] = session('data')['SysNo'];

            $Query_Url    = C('SERVER_HOST') . "IPP3Customers/LMF_PassageConfigList_Ali";
            $Query_Return = http($Query_Url, $Query_Or_Insert_Data);
            if (IS_POST) {


                $Query_Or_Insert_Data['Field_one']   = "";
                $Query_Or_Insert_Data['Field_two']   = "";
                $Query_Or_Insert_Data['Field_three'] = "";
                $Query_Or_Insert_Data['MCHID']       = I('MCHID', "", "htmlspecialchars");
                $Query_Or_Insert_Data['ALi_APPID']       = I('APPID', "", "htmlspecialchars");
                $Query_Or_Insert_Data['ALi_alipay_public_key']       = I('public_key', "", "htmlspecialchars");
                $Query_Or_Insert_Data['ALi_merchant_private_key']       = I('private_key', "", "htmlspecialchars");

                if ($Query_Return['Code'] == 0) {

                    $Update_Url = C('SERVER_HOST') . "IPP3Customers/LMF_PassageConfigUpdate_Ali";
                    $Arr_Return = http($Update_Url, $Query_Or_Insert_Data);
                } else {
                    $Insert_Url = C('SERVER_HOST') . "IPP3Customers/LMF_PassageConfigInsert_Ali";
                    $Arr_Return = http($Insert_Url, $Query_Or_Insert_Data);
                }

                $this->ajaxreturn($Arr_Return);

                exit();

            } else {
                R("Base/getMenu");
                if ($Post_PassageWay_list) {
                    $Query_Url    = C('SERVER_HOST') . "IPP3Customers/LMF_PassageConfigList_Ali";
                    $Query_Return = http($Query_Url, $Query_Or_Insert_Data);
                    $this->assign('data', $Query_Return);
                } else {
                    $this->assign('passtype', -1);
                }
            }

        }else{
            $this->assign( 'passtype', -1 );
        }


        $this->display();

    }

    public function WxConfig()
    {
        if ((session('data')['CustomersType'] == 1 & session('flag') == 0)) {
            $Post_PassageWay_Data['CustomerSysNo']            = session('data')['SysNo'];
            $Post_PassageWay_Data['Type']                     = 114;
            $Post_PassageWay_Url                              = C('SERVER_HOST') . 'IPP3Customers/CustomerServicePassageWayList';
            $Post_PassageWay_list                             = http($Post_PassageWay_Url, $Post_PassageWay_Data);
            $Query_Or_Insert_Data['CustomerPassageWaysSysNo'] = $Post_PassageWay_list[0]['SysNo'];

            $Query_Or_Insert_Data['CustomerServiceSysNo'] = session('data')['SysNo'];

            $Query_Url    = C('SERVER_HOST') . "IPP3Customers/LMF_PassageConfigList";
            $Query_Return = http($Query_Url, $Query_Or_Insert_Data);
            if (IS_POST) {
                if ((session('data')['CustomersType'] == 1 & session('flag') == 0)) {
                    $Query_Or_Insert_Data['Field_one']   = "";
                    $Query_Or_Insert_Data['Field_two']   = "";
                    $Query_Or_Insert_Data['Field_three'] = "";
                    $Query_Or_Insert_Data['MCHID']       = I('MCHID', "", "htmlspecialchars");
                    $Query_Or_Insert_Data['APPID']       = I('APPID', "", "htmlspecialchars");
                    $Query_Or_Insert_Data['APPSECRET']       = I('APPSECRET', "", "htmlspecialchars");

                    if ($Query_Return['Code'] == 0) {
                        $Update_Url = C('SERVER_HOST') . "IPP3Customers/LMF_PassageConfigUpdate";
                        $Arr_Return = http($Update_Url, $Query_Or_Insert_Data);
                    } else {
                        $Insert_Url = C('SERVER_HOST') . "IPP3Customers/LMF_PassageConfigInsert";
                        $Arr_Return = http($Insert_Url, $Query_Or_Insert_Data);
                    }
                } else {
                    $Arr_Return['Code']        = 1;
                    $Arr_Return['Description'] = "该角色无权限,进行该操作!";
                }
                $this->ajaxreturn($Arr_Return);

                exit();

            } else {
                R("Base/getMenu");
                if ($Post_PassageWay_list) {
                    $Query_Url    = C('SERVER_HOST') . "IPP3Customers/LMF_PassageConfigList";
                    $Query_Return = http($Query_Url, $Query_Or_Insert_Data);
                    $this->assign('data', $Query_Return);
                } else {
                    $this->assign('passtype', -1);
                }
            }
        }else{
            R("Base/getMenu");
            $this->assign( 'passtype', -1 );
        }

        $this->display();

    }

}