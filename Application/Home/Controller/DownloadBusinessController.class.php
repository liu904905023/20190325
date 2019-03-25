<?php

namespace Home\Controller;

use Common\Compose\Base;

class DownloadBusinessController extends Base{
//20170327 start
//服务商-商户列表报表下载
    public function downloadbusiness(){

        if(isset($_POST['systemusersysno'])){

            $data['SystemUserSysNo']=I("systemusersysno");

        }

        if(session(flag)==0){

            $data['CustomersTopSysNo'] = session(data)['SysNo'];

        }

        if(session(flag)==1){

            $data['SystemUserSysNo'] = session(data)['SysNO'];

        }

        $Time_Start =$_POST['Time_Start'];//开始时间

        $Time_end   =$_POST['Time_End'];//截止时间

        $data['RegisterTimeStart'] = $Time_Start;

        $data['RegisterTimeEnd'] = $Time_end;

        $data['Customer']   = I("customersname");

        $data['CustomerName']   = I("customerstruename");

        $url  = C('SERVER_HOST')."IPP3Customers/IPP3CustomerShopList";

        // $data['PagingInfo']['PageSize']   = 100000;

        // $data['PagingInfo']['PageNumber'] = 0;

        // $data = json_encode($data);

        // $head = array(

        //     "Content-Type:application/json;charset=UTF-8",

        //     "Content-length:" . strlen( $data ),

        //     //"X-Ywkj-Authentication:" . strlen( $data ),

        // );

        // $list = http_request( $url, $data, $head );

        // $list = json_decode( $list ,true);

        $list = exportpost($url,$data);//分次请求

        if(session(data)['CustomersType']){

            $list['type']=session(data)['CustomersType'];

        }

        if(session(flag)==1){

            $list['flag']= staffstoreorservice(session(SysNO));

        }

        foreach ($list['model'] as $row=>$val){

        $info[$row]['SysNo']=$val['SysNo'];

        $info[$row]['Customer']="'".$val['Customer'];

        $info[$row]['CustomerName']=$val['CustomerName'];

        $info[$row]['DwellAddress']=$val['DwellAddress'];



        $info[$row]['RegisterTime']=date("Y-m-d h:m:s",substr($val['RegisterTime'],6,10));

        }

        foreach ($info[0] as $field=>$v){

            if($field == 'SysNo'){

                $headArr[]='商户号';

            }

            if($field == 'Customer'){

                $headArr[]='商户用户名';

            }

            if($field == 'CustomerName'){

                $headArr[]='商户名称';

            }



            if($field == 'DwellAddress'){

                $headArr[]='地址';

            }



            if($field == 'RegisterTime'){

                $headArr[]='注册时间';

            }

        }

        $filename="商户查询报表";

        $this->getExcel($filename,$headArr,$info);
    }
//20170327 end
    public  function getExcel($fileName,$headArr,$info){

        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入

        import("Org.Util.PHPExcel");

        import("Org.Util.PHPExcel.Writer.Excel5");

        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y-m-d",time());

        $fileName .= "_{$date}.xls";

        //创建PHPExcel对象，注意，不能少了\

        $objPHPExcel = new \PHPExcel();

        $objProps = $objPHPExcel->getProperties();

        //设置表头

        $key = ord("A");

        //print_r($headArr);exit;

        foreach($headArr as $v){

            $colum = chr($key);

            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);

            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);

            $key += 1;

        }

        $column = 2;

        $objActSheet = $objPHPExcel->getActiveSheet();

        foreach($info as $key => $rows){ //行写入

            $span = ord("A");

            foreach($rows as $keyName=>$value){// 列写入

                $j = chr($span);

                $objActSheet->setCellValue($j.$column, $value);

                $span++;

            }

            $column++;

        }

        // $objActSheet->setCellValue("A".$column,"总计:111!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");

        $fileName = iconv("utf-8", "gb2312", $fileName);

        //重命名表

        //$objPHPExcel->getActiveSheet()->setTitle('test');

        //设置活动单指数到第一个表,所以Excel打开这是第一个表

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