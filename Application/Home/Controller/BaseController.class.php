<?php
namespace Home\Controller;

use Think\Controller;

//use Common\Compose\Base;

class BaseController extends Controller {

    public function refund() {
        $this->display();
    }

    public function ordertype() {
        $this->assign('aaa',123);
        $this->display();
    }

    public function getMenu() {
        $type = session('data')['CustomersType'];             //0-服务商  1-商户
        $flag = session('flag');                            //0-服务商登陆  1-员工登陆
        $servicestoretype = session('servicestoretype');    //0-服务商员工 1-商户员工
        if ($flag == 1) {
            $data['SystemUserSysNo'] = session('SysNO');   //员工主键
            $data = json_encode($data);
            $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data),);

            $list = cookie('staff');
            if (!$list) {

                $list = http_request(C('SERVER_HOST') . "IPP3Customers/IPP3UserRoleList", $data, $head);
                $list = json_decode($list, true);
                foreach ($list as $row => $val) {
                    $info[$val['SystemRoleSysNo']]['R'] = $val['RoleName'];
                    $info[$val['SystemRoleSysNo']]['D'] = $val['Description'];
                    $info[$val['SystemRoleSysNo']]['U'] = $val['URL'];

                }

                $aaa = json_encode($info);
                cookie("staff", $aaa);
                $list = json_decode($aaa, true);
            } else {
                $aaa = cookie('staff');
                $list = json_decode($aaa, true);
            }


            foreach ($list as $row => $val) {
                $list2['D'][] = $val['D'];
                $list2['R'][] = $val['R'];
                $list2['U'][] = $val['U'];
            }

            $list3 = array_unique($list2['D']);
            $this->assign('list', $list);
            $this->assign('list3', $list3);

        } else if ($type == 0) {
            $data['PagingInfo']['PageSize'] = 200;
            $data['PagingInfo']['PageNumber'] = 0;
            $data = json_encode($data);
            $No_Menu = array("/Order/platform_order_search", "/Order/order_search_alipay", "/OrderFund/orderfund?Top=1", "/OrderFund/orderfund", "/OrderExtend/order_extend", "/Refund/refund", "/OrderStatistics/order_statistics", "/OrderExtendRecharge/order_extend_recharge", "/BusinessRefund/refund", "/Order/orderbyday", "/YiHuiPush/yihui_push", "/HandPush/hand_push", "/Conff/pf_zfbConfig", "/Conff/pf_wxConfig", "/Conff/xy_zfbConfig", "/Conff/xy_wxConfig", "/WSConfig/sh_ws_ali_config", "/WSConfig/sh_ws_wx_config", "/WSConfig/sh_ws_path_config", "/WSConfig/sh_ws_appid_config", "/PCard/p_card", "/PCard/supply_order", "/PCard/refund_error_pcard_list", "/WSConfig/sh_ws_path_config", "/HandPush/ws_hand_push_list", "/CouponCash/BatchEntry", "/CouponFund/index","/LMFConfig/WxConfig","/LMFConfig/AliConfig");
            $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data),);
            $list = cookie('service');
            if (!$list) {
                $list = http_request(C('SERVER_HOST') . "IPP3Customers/IPP3SystemRoleList", $data, $head);

                $list = json_decode($list, true);
                foreach ($list['model'] as $row => $val) {
                    if (!in_array($val['URL'],$No_Menu)) {
                        $info[$val['SysNo']]['R'] = $val['RoleName'];
                        $info[$val['SysNo']]['D'] = $val['Description'];
                        $info[$val['SysNo']]['U'] = $val['URL'];
                    }

                }

                $aaa = json_encode($info);

                cookie("service", $aaa);
                $list = json_decode($aaa, true);
            } else {
                $aaa = cookie('service');
                $list = json_decode($aaa, true);
            }
            $list3 = array(0 => ("商户配置"), 1 => ("员工管理"), 2 => ("交易订单"), 3 => ("商户列表"), 4 => ("权限分配"), 5 => ("退款"), 6 => ("卡劵"), 7 => ("免充值管理"));

            $this->assign('list', $list);
            $this->assign('list3', $list3);
        } //商户登录
        else if ($type == 1) {
            $data['CustomerServiceSysNo'] = session('SysNO');   //商户主键
            $data = json_encode($data);

            $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data),);
            $status = cookie('store');
            if (!$status) {
                $list = http_request(C('SERVER_HOST') . "IPP3Customers/IPP3CustomerRoleList", $data, $head);
                $list = json_decode($list, true);

                foreach ($list as $row => $val) {

                    $info[$val['SystemRoleSysNo']]['R'] = $val['RoleName'];
                    $info[$val['SystemRoleSysNo']]['D'] = $val['Description'];
                    $info[$val['SystemRoleSysNo']]['U'] = $val['URL'];
                }
//                dump($list);
//                exit();
                $aaa = json_encode($info);
                cookie("store", $aaa);
                $aaa = cookie('store');
                $list = json_decode($aaa, true);

            } else {
                $aaa = cookie('store');
                $list = json_decode($aaa, true);

            }

            foreach ($list as $row => $val) {
                $list2['D'][] = $val['D'];
                $list2['R'][] = $val['R'];
                $list2['U'][] = $val['U'];
            }
            $list3 = array_unique($list2['D']);
            $this->assign('list', $list);
            $this->assign('list3', $list3);
        }

    }

}

