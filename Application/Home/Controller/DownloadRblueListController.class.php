<?php

namespace Home\Controller;

use Common\Compose\Base;

class DownloadRblueListController extends Base {

	protected function _initialize() {
        ini_set("memory_limit", "1024M"); // 不够继续加大
        set_time_limit(0);
    }

//商户订单统计

    public function downloadrbluelist() {

        $data['TimeBegin'] = I('Time_Start', "");
        $data['TimeEnd'] = I('Time_End', "");
//        $data['Type'] = I('Type', "");
        $data['ApplyStatus'] = I('Type')=='全部'?"":I('Type');

        if (session('data')['CustomersType'] == 0 & session('flag') == 0) {//服务商登陆

            $data["CustomersTopSysNo"]=session('SysNO');    //服务商主键

            $data["Customer"]=I('Customer', "");            //商户用户名

            $data["CustomerName"]=I('CustomerName', "");    //商户名称

        }else{


            $this->ajaxReturn(array('Code'=>1,'Description'=>"该角色无权限,进行该操作!"));

            exit();

        }


        if (deep_in_array($data,["TimeBegin","TimeEnd","ApplyStatus","Customer","CustomerName"])) {

            $this->ajaxReturn(array('Code'=>1,'Description'=>"参数错误，请稍后再试或重新登录！"));

            exit();
        }

        $url = C('SERVER_HOST') . "IPP3WSCustomer/WS_Merchant_RblueList";
        $list = exportpost($url,$data);//分次请求

        foreach ($list['Data']['model'] as $row=>$val){

            $info[$row]['Customer']=$val['Customer']==""? "" : "'" .$val['Customer'] ;
            $info[$row]['CustomerName']=$val['CustomerName']==""? "" : $val['CustomerName'];
            $info[$row]['MerchantId']=$val['MerchantId']==""? "" : "'" .$val['MerchantId'];
            $info[$row]['Smid']=$val['Smid']==""? "" : "'" .$val['Smid'];
            $info[$row]['WechatMerchId']=$val['WechatMerchId']==""? "" : "'" .$val['WechatMerchId'];
            $info[$row]['RegisterTime']=date('Y-m-d H:i:s', strtotime($val['RegisterTime']));
            $info[$row]['ApplyStatus']=$val['ApplyStatus']==""? "" : "'" .$val['ApplyStatus'];
            $info[$row]['Type']=$val['Type']=='None'?'他行':'其他';



        }


        foreach ($info[0] as $field => $v) {

            if ($field == 'Customer') {

                $headArr[] = '商户用户名';

            }

            if ($field == 'CustomerName') {

                $headArr[] = '商户名称';

            }



            if ($field == 'MerchantId') {

                $headArr[] = '商户号';

            }

            if ($field == 'Smid') {

                $headArr[] = '支付宝SMID';

            }

            if ($field == 'WechatMerchId') {

                $headArr[] = '微信子商户号';

            }

            if ($field == 'RegisterTime') {

                $headArr[] = '入驻时间';

            }

            if ($field == 'ApplyStatus') {

                $headArr[] = '状态';

            }
            if ($field == 'Type') {

                $headArr[] = '类型';

            }


        }

        $filename = "蓝海入驻列表";

        $this->getExcel($filename, $headArr, $info);

    }

    public function getExcel($fileName, $headArr, $info) {

        import("Org.Util.PHPExcel");

        import("Org.Util.PHPExcel.Writer.Excel5");

        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y-m-d", time());

        $fileName .= "_{$date}.xls";

        $objPHPExcel = new \PHPExcel();

        $objProps = $objPHPExcel->getProperties();

        $key = ord("A");

        foreach ($headArr as $v) {

            $colum = chr($key);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

            $key += 1;

        }


        $column = 2;

        $objActSheet = $objPHPExcel->getActiveSheet();

        foreach ($info as $key => $rows) { //行写入

            $span = ord("A");

            foreach ($rows as $keyName => $value) {// 列写入

                $j = chr($span);

                $objActSheet->setCellValue($j . $column, $value);

                $span++;

            }

            $column++;

        }


        $fileName = iconv("utf-8", "gb2312", $fileName);

        $objPHPExcel->setActiveSheetIndex(0);

        ob_end_clean();//清除缓冲区,避免乱码

        header('Content-Type: application/vnd.ms-excel');

        header("Content-Disposition: attachment;filename=\"$fileName\"");

        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output'); //文件通过浏览器下载

        exit;

    }


}