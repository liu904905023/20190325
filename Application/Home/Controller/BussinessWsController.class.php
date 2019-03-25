<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class BussinessWsController extends Base {

    public function register_Ws() {
        R("Base/getMenu");
        $this->display();
    }
    public function register_Ws_zhiwen() {
        R("Base/getMenu");
        $this->display();
    }

    public function activitycreate() {
        R("Base/getMenu");

        $this->display();
    }


    public function rblue() {
        R("Base/getMenu");
        $this->display();
    }

    public function registerWs_rblue() {

//        if ((session('data')['CustomersType'] == 0 & session('flag') == 0)) {
//
//        } else {
//            $Return_Data['Code'] = 1;
//            $Return_Data['Description'] = "该角色无权限,进行该操作!";
//            $this->ajaxReturn($Return_Data);
//            exit();
//
//        }
//
//

        $FeeWxList[0]['ChannelType']=I('PassageWx');
        $FeeWxList[0]['FeeType']=I('wx_Type');
        $FeeWxList[0]['FeeValue']=I('wx_rate');

        $FeeAliList[0]['ChannelType']=I('PassageAli');
        $FeeAliList[0]['FeeType']=I('zfb_Type');
        $FeeAliList[0]['FeeValue']=I('zfb_rate');
        $PayTypeArray = explode(",",I("Payment_Channel"));
        if(!strstr(I('Payment_Channel'),'01')){
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="低费率必须支持支付宝!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if(count($PayTypeArray)>1) {
            if(I('wx_rate')==0||I('zfb_rate')!=0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="微信费率不允许为0、支付宝费率必须为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = array_merge_recursive($FeeWxList, $FeeAliList);
        }else if(I("Payment_Channel")=="01"){
            if(I('zfb_rate')!=0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="支付宝费率必须为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = $FeeAliList;
        }else if(I("Payment_Channel")=="02"){
            if(I('wx_rate')==0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="微信费率不允许为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = $FeeWxList;
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请选择一种或多种支付渠道!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if(I('SysNo')){
            $data['CustomerServiceSysNo']= I('SysNo');//商户主键
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请从服务商角色登录,并进行该操作!";
            $this->ajaxReturn($Return_Data);
            exit();
        }

        if (I('Merchant_Type')=="02") {
            if (I('Registration_Number')&&$_FILES['Business_License_Photo']['name']!="") {
            }else{
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业、个体工商户请填写营业执照工商注册号并上传营业执照图片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Cardholder_Number')!=I('Leader_Certificates_Number')){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="个人商户及个体工商户开户人身份证需要与负责人身份证一致!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if (I('Account_Holder_Name') != I('Legal_Person_Name')) {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="个人商户及个体工商户开户人名称需要与负责人名称一致";
                $this->ajaxReturn($Return_Data);
                exit();
            }

        }else if (I('Merchant_Type')=="01") {
            if (I('Registration_Number')||$_FILES['Business_License_Photo']['name']!="") {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="自然人无须填写营业执照工商注册号并上传营业执照图片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
        }else if (I('Merchant_Type')=="03") {
            if(I('Settlement_Method')!='01'){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户只支持结算到他行卡!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Account_Type')!='02'){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户账户类型必须为对公账户!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if ($_FILES['Licence_Photo']['name']=="") {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户需上传开户许可证照片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if (I('Enterprise_Name')=="") {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户需填写企业名称!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Merchant_Name')!=I("Enterprise_Name")){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户商户名称与法人信息必须一致!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Enterprise_Name')!=I('Account_Holder_Name')){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户开户人名称需要与企业法人必须一致！";
                $this->ajaxReturn($Return_Data);
                exit();

            }
        }



        if(I('T_Zero_Account')=="Y"){
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="商户清算资金暂不支持T0到账! ";
            $this->ajaxReturn($Return_Data);
            exit();
        }


        $data['ReqModel']['MerchantDetail']['Alias']=I('Merchant_Abbreviation');//商户简称。
        $data['ReqModel']['MerchantDetail']['ContactMobile']=I('Linkman_Mobile');//联系人手机号。为商户常用联系人联系手机号。。
        $data['ReqModel']['MerchantDetail']['ContactName']=I('Linkman_Name');//联系人姓名。
        $data['ReqModel']['MerchantDetail']['Province']=I('Province_Details');//省份。
        $data['ReqModel']['MerchantDetail']['City']=I('City_Details');//城市。
        $data['ReqModel']['MerchantDetail']['District']=I('Area_Details');//区。
        $data['ReqModel']['MerchantDetail']['Address']=I('Address_Details');//地址。
        $data['ReqModel']['MerchantDetail']['ServicePhoneNo']=I('Merchant_Service_Number');//商户客服电话。
        $data['ReqModel']['MerchantDetail']['Email']=I('Email');//邮箱。
        $data['ReqModel']['MerchantDetail']['LegalPerson']=I('Enterprise_Name');//企业名称。
        $data['ReqModel']['MerchantDetail']['PrincipalMobile']=I('Leader_Number');//负责人手机号。
        $data['ReqModel']['MerchantDetail']['PrincipalCertType']=I('Leader_Certificates_Type');//负责人证件类型。
        $data['ReqModel']['MerchantDetail']['PrincipalCertNo']=I('Leader_Certificates_Number');//负责人证件号码。。
        $data['ReqModel']['MerchantDetail']['PrincipalPerson']=I('Legal_Person_Name');//负责人名称或企业法人代表姓名。。。
        $data['ReqModel']['MerchantDetail']['BussAuthNum']=I('Registration_Number');//营业执照工商注册号。。
        $data['ReqModel']['MerchantDetail']['CertOrgCode']=I('Organization_Number');//组织机构代码证号。。




        if(I('Settlement_Method')=='01') {

            /*清算卡*/
            $data['ReqModel']['BankCardParam']['BankCardNo']=I('Bank_Card_Number');//银行卡号。。
            $data['ReqModel']['BankCardParam']['BankCertName']=I('Account_Holder_Name');//开户人名称。。
            $data['ReqModel']['BankCardParam']['AccountType']=I('Account_Type');//账户类型。可选值：。。
            $data['ReqModel']['BankCardParam']['ContactLine']=I('Couplet_Number');//联行号。。。
            $data['ReqModel']['BankCardParam']['BranchName']=I('Bank_Branch');//开户支行。。
            $data['ReqModel']['BankCardParam']['BranchProvince']=I('Province');//开户支行所在省。。
            $data['ReqModel']['BankCardParam']['BranchCity']=I('City');//开户支行所在市。。
            $data['ReqModel']['BankCardParam']['CertType']=I('Leader_Certificates_Type');//持卡人地址。。
            $data['ReqModel']['BankCardParam']['CertNo']=I('Cardholder_Number');//持卡人证件号码。。
            $data['ReqModel']['BankCardParam']['CardHolderAddress']=I('Cardholder_Address');//持卡人地址。。
            $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/MCHRegisterUnion';/*结算到他行*/


        }else{
            if (I('Other_Card_Number')) {
                $data['ReqModel']['MerchantDetail']['OtherBankCardNo']=I('Other_Card_Number');//他行卡储蓄卡卡号。。
            }else{
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="请填写他行卡储蓄卡卡号!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/MCHRegisterWithAccountUnion';/*结算到余利宝*/
        }
        $data['ReqModel']['OutMerchantId']= I('External_Merchant_Number'); //外部商户号。合作商对商户的自定义编码，要求同一个合作商下保持唯一。
        $data['ReqModel']['MerchantName']= I('Merchant_Name');//商户名称。有营业执照的，要求与营业执照上的名称一致。
        $data['ReqModel']['MerchantType']= I('Merchant_Type');//
        $data['ReqModel']['DealType']= I('Merchant_Business_Type');//商户经营类型
        $data['ReqModel']['SupportPrepayment']= I('T_Zero_Account');//从T1改成T0，须将结算方式调整为结算到余利宝，同时必送设置T0费率
        $data['ReqModel']['SettleMode']= I('Settlement_Method');//结算方式
        $data['ReqModel']['Mcc']= I('Business_Category');//经营类目。参见附录的经营类目上送。
        $data['ReqModel']['TradeTypeList']= I('Transaction_Type')=='null'?'':I('Transaction_Type');//支持交易类型列表
        $data['ReqModel']['PayChannelList']= I('Payment_Channel')=='null'?'':I('Payment_Channel');//支持支付渠道列表
        $data['ReqModel']['DeniedPayToolList']= I('Disable_Payment_Method')=='null'?'':I('Disable_Payment_Method');//禁用支付方式
        $data['ReqModel']['AuthCode']= I('Verification_Code');//手机验证码
        $data['ReqModel']['SupportStage']= I('Flower_Staging');//是否使用花呗
        $data['ReqModel']['PartnerType']=I('Merchant_Public_Number');//商户在进行微信支付H5支付时所使用的公众号相关信息的类型，枚举值有：
        $data['ReqModel']['RateVersion']='RBLUE';//蓝海
        if (I('Merchant_Public_Number')=="03") {
            $data['ReqModel']['MerchantDetail']['SubscribeAppId'] = "";
        }else{
            $data['ReqModel']['MerchantDetail']['SubscribeAppId']=I('Public_Number');//需关注的公众号对应的APPID。。
        }

        if ($_FILES['Legal_Person_ID_Front']['name']!=""){
            $CertPhotoA= $this->uploadpic($_FILES['Legal_Person_ID_Front']  ,I('SysNo'),'01' );
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请上传负责人或企业法人代身份证图片正面不能为空!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if ($_FILES['Legal_Person_ID_Back']['name']!=""){
            $CertPhotoB= $this->uploadpic($_FILES['Legal_Person_ID_Back'],I('SysNo'),'02' );
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请上传负责人或企业法人代表的身份证图片反面不能为空!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if ($_FILES['Business_License_Photo']['name']!=""){
            $LicensePhoto= $this->uploadpic($_FILES['Business_License_Photo'],I('SysNo'),'03' );
        }
        if ($_FILES['Organization_Photo']['name']!=""){
            $PrgPhoto= $this->uploadpic($_FILES['Organization_Photo'],I('SysNo'),'04' );
        }
        if ($_FILES['Licence_Photo']['name']!=""){
            $IndustryLicensePhoto= $this->uploadpic($_FILES['Licence_Photo'],I('SysNo'), '05');
        }
        if ($_FILES['Storefront_Photo']['name']!=""){
            $ShopPhoto= $this->uploadpic($_FILES['Storefront_Photo'],I('SysNo'), '06');
        }
        if ($_FILES['Other_Photo']['name']!=""){
            $OtherPhoto= $this->uploadpic($_FILES['Other_Photo'],I('SysNo'), '07');
        }
//
//        $data['ReqModel']['MerchantDetail']['CertPhotoA'] = "b03438ce-265a-44d3-bd17-0b328b7f3ea5";
//        $data['ReqModel']['MerchantDetail']['CertPhotoB'] = "aec723db-d074-4c61-b6a2-43d5fd6a8c0c";
//        $data['ReqModel']['MerchantDetail']['LicensePhoto'] = "5522b586-30fd-4e7c-913d-dc0e41560c2a";
//        $data['ReqModel']['MerchantDetail']['PrgPhoto'] = "5907c5fd-0654-47db-96f9-f291ecf59cff";
//        $data['ReqModel']['MerchantDetail']['IndustryLicensePhoto'] = "57cd6924-ac7d-4f28-ad1a-54b5f126b28d";
//        $data['ReqModel']['MerchantDetail']['ShopPhoto'] = "0fc0b1c3-6195-4989-8ece-2130183e6682";
//        $data['ReqModel']['MerchantDetail']['OtherPhoto'] = "693559b3-9919-4233-94a5-6b10e7bfb875";
        $data['ReqModel']['MerchantDetail']['CertPhotoA'] = $CertPhotoA;
        $data['ReqModel']['MerchantDetail']['CertPhotoB'] = $CertPhotoB;
        $data['ReqModel']['MerchantDetail']['LicensePhoto'] = $LicensePhoto;
        $data['ReqModel']['MerchantDetail']['PrgPhoto'] = $PrgPhoto;
        $data['ReqModel']['MerchantDetail']['IndustryLicensePhoto'] = $IndustryLicensePhoto;
        $data['ReqModel']['MerchantDetail']['ShopPhoto'] = $ShopPhoto;
        $data['ReqModel']['MerchantDetail']['OtherPhoto'] = $OtherPhoto;


//        var_dump(json_encode($data));
//        echo $url;
//        exit();

        $list = http($url, $data);
        $Return_Data['Code'] = $list['Code'];
        if($list['Code']==0){
            $Return_Data['Description'] ='商户入驻成功';

        }else{
            $Return_Data['Description'] =$list['Description'];

        }
        $this->ajaxReturn($Return_Data);

    }
    public function update_Ws() {
        R("Base/getMenu");
//        I('OutMerchantId');
        $Transaction_Name = "";
        $Payment_Name = "";
        $DeniedPay_Name = "";
        $data['CustomerServiceSysNo'] = I('CustomerSysNo');
        $data['ReqModel']['MerchantId'] = I('MerchaId');
        $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/WSMerchantQuery';


        $list = http($url, $data);
//        dump($list);
//        exit();
        $Transaction_Type = array("01" => "正扫交易","02"=>"反扫交易","06"=>"退款交易","08"=>"动态扫码交易");
        $Payment_Type = array("01" => "支付宝","02"=>"微信支付");
        $DeniedPay_Type = array("02" => "信用卡","03"=>"花呗(仅支付宝)");
        $Transaction_Temp = explode(",",$list['Data']['ResultData']['m_values']['TradeTypeList']) ;
        $Payment_Temp = explode(",",$list['Data']['ResultData']['m_values']['PayChannelList']) ;
        if ($list['Data']['ResultData']['m_values']['DeniedPayToolList']) {
            $DeniedPay_Temp = explode(",",$list['Data']['ResultData']['m_values']['DeniedPayToolList']) ;
        }

        if($Transaction_Temp){
            foreach ($Transaction_Temp as $k => $v) {
                $Transaction_Name .= $Transaction_Type[$v] . ",";
            }
        }
        if ($Payment_Temp) {
            foreach ($Payment_Temp as $k => $v) {
                $Payment_Name .= $Payment_Type[$v] . ",";
            }
        }
        if ($DeniedPay_Temp) {
            foreach ($DeniedPay_Temp as $k => $v) {
                $DeniedPay_Name .= $DeniedPay_Type[$v] . ",";
            }
        }
        foreach ($list['Data']['ResultData']['m_values']['FeeParamList'] as $k => $v) {
            if($v['ChannelType']=="01"){
                $Ali['FeeType'] = $v['FeeType'];
                $Ali['FeeValue'] = $v['FeeValue'];
            }else if($v['ChannelType']=="02"){
                $Wx['FeeType'] = $v['FeeType'];
                $Wx['FeeValue'] = $v['FeeValue'];
            }
        }


        $MerchantProvinceList = $this->GetWS_Address(0,1);
        $MerchantCityList = $this->GetWS_Address($list['Data']['ResultData']['m_values']['MerchantDetail']['Province']);
        $MerchantDistrictList = $this->GetWS_Address($list['Data']['ResultData']['m_values']['MerchantDetail']['City']);

        $BankProvinceList = $this->GetWS_BankArea(0,1);
        if($list['Data']['ResultData']['m_values']['BankCardParam']['BranchProvince']==''){
            $BankCityList=array(0=>array('region_id'=>"",'region_name'=>'请选择市'));
        }else{
            $BankCityList = $this->GetWS_BankArea($list['Data']['ResultData']['m_values']['BankCardParam']['BranchProvince']);
        }
        if($list['Data']['ResultData']['m_values']['BankCardParam']['BranchCity']==''){
            $BankAreaList=array(0=>array('region_id'=>"",'region_name'=>'请选择区'));
        }else{
            $BankAreaList = $this->GetWS_BankArea($list['Data']['ResultData']['m_values']['BankCardParam']['BranchCity']);
        }
//        $BankAreaList = $this->GetWS_BankArea($list['Data']['ResultData']['m_values']['BankCardParam']['BranchCity']);
//        var_dump($BankAreaList);
//        exit();



        $this->assign('Transaction_Name',substr($Transaction_Name,0,strlen($Transaction_Name)-1));
        $this->assign('Payment_Name',substr($Payment_Name,0,strlen($Payment_Name)-1));
        $this->assign('DeniedPay_Name',substr($DeniedPay_Name,0,strlen($DeniedPay_Name)-1));

        $this->assign('MerchantProvinceList',$MerchantProvinceList);
        $this->assign('MerchantCityList',$MerchantCityList);
        $this->assign('MerchantDistrictList',$MerchantDistrictList);

        $this->assign('BankProvinceList',$BankProvinceList);
        $this->assign('BankCityList',$BankCityList);
        $this->assign('BankAreaList',$BankAreaList);

        $this->assign('Wx',$Wx);
        $this->assign('Ali',$Ali);

        $this->assign('data',$list['Data']['ResultData']['m_values']);
        $this->display();
    }
    public function rblue_update() {
        R("Base/getMenu");
//        I('OutMerchantId');
        $Transaction_Name = "";
        $Payment_Name = "";
        $DeniedPay_Name = "";
        $data['CustomerServiceSysNo'] = I('CustomerSysNo');
        $data['ReqModel']['MerchantId'] = I('MerchaId');
        $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/WSMerchantQuery';


        $list = http($url, $data);
//        dump($list);
//        exit();
        $Transaction_Type = array("01" => "正扫交易","02"=>"反扫交易","06"=>"退款交易","08"=>"动态扫码交易");
        $Payment_Type = array("01" => "支付宝","02"=>"微信支付");
        $DeniedPay_Type = array("02" => "信用卡","03"=>"花呗(仅支付宝)");
        $Transaction_Temp = explode(",",$list['Data']['ResultData']['m_values']['TradeTypeList']) ;
        $Payment_Temp = explode(",",$list['Data']['ResultData']['m_values']['PayChannelList']) ;
        if ($list['Data']['ResultData']['m_values']['DeniedPayToolList']) {
            $DeniedPay_Temp = explode(",",$list['Data']['ResultData']['m_values']['DeniedPayToolList']) ;
        }

        if($Transaction_Temp){
            foreach ($Transaction_Temp as $k => $v) {
                $Transaction_Name .= $Transaction_Type[$v] . ",";
            }
        }
        if ($Payment_Temp) {
            foreach ($Payment_Temp as $k => $v) {
                $Payment_Name .= $Payment_Type[$v] . ",";
            }
        }
        if ($DeniedPay_Temp) {
            foreach ($DeniedPay_Temp as $k => $v) {
                $DeniedPay_Name .= $DeniedPay_Type[$v] . ",";
            }
        }
        foreach ($list['Data']['ResultData']['m_values']['FeeParamList'] as $k => $v) {
            if($v['ChannelType']=="01"){
                $Ali['FeeType'] = $v['FeeType'];
                $Ali['FeeValue'] = $v['FeeValue'];
            }else if($v['ChannelType']=="02"){
                $Wx['FeeType'] = $v['FeeType'];
                $Wx['FeeValue'] = $v['FeeValue'];
            }
        }


        $MerchantProvinceList = $this->GetWS_Address(0,1);
        $MerchantCityList = $this->GetWS_Address($list['Data']['ResultData']['m_values']['MerchantDetail']['Province']);
        $MerchantDistrictList = $this->GetWS_Address($list['Data']['ResultData']['m_values']['MerchantDetail']['City']);

        $BankProvinceList = $this->GetWS_BankArea(0,1);
        if($list['Data']['ResultData']['m_values']['BankCardParam']['BranchProvince']==''){
            $BankCityList=array(0=>array('region_id'=>"",'region_name'=>'请选择市'));
        }else{
            $BankCityList = $this->GetWS_BankArea($list['Data']['ResultData']['m_values']['BankCardParam']['BranchProvince']);
        }
        if($list['Data']['ResultData']['m_values']['BankCardParam']['BranchCity']==''){
            $BankAreaList=array(0=>array('region_id'=>"",'region_name'=>'请选择区'));
        }else{
            $BankAreaList = $this->GetWS_BankArea($list['Data']['ResultData']['m_values']['BankCardParam']['BranchCity']);
        }
//        $BankAreaList = $this->GetWS_BankArea($list['Data']['ResultData']['m_values']['BankCardParam']['BranchCity']);
//        var_dump($BankAreaList);
//        exit();



        $this->assign('Transaction_Name',substr($Transaction_Name,0,strlen($Transaction_Name)-1));
        $this->assign('Payment_Name',substr($Payment_Name,0,strlen($Payment_Name)-1));
        $this->assign('DeniedPay_Name',substr($DeniedPay_Name,0,strlen($DeniedPay_Name)-1));

        $this->assign('MerchantProvinceList',$MerchantProvinceList);
        $this->assign('MerchantCityList',$MerchantCityList);
        $this->assign('MerchantDistrictList',$MerchantDistrictList);

        $this->assign('BankProvinceList',$BankProvinceList);
        $this->assign('BankCityList',$BankCityList);
        $this->assign('BankAreaList',$BankAreaList);


        $this->assign('Wx',$Wx);
        $this->assign('Ali',$Ali);

        $this->assign('data',$list['Data']['ResultData']['m_values']);
        $this->display();
    }

    public function registerWs (){

//        if ((session('data')['CustomersType'] == 0 & session('flag') == 0)) {
//
//        } else {
//            $Return_Data['Code'] = 1;
//            $Return_Data['Description'] = "该角色无权限,进行该操作!";
//            $this->ajaxReturn($Return_Data);
//            exit();
//
//        }
//
//

        $FeeWxList[0]['ChannelType']=I('PassageWx');
        $FeeWxList[0]['FeeType']=I('wx_Type');
        $FeeWxList[0]['FeeValue']=I('wx_rate');

        $FeeAliList[0]['ChannelType']=I('PassageAli');
        $FeeAliList[0]['FeeType']=I('zfb_Type');
        $FeeAliList[0]['FeeValue']=I('zfb_rate');
        $PayTypeArray = explode(",",I("Payment_Channel"));
        if(count($PayTypeArray)>1) {
            if(I('wx_rate')==0||I('zfb_rate')==0){
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="支付宝、微信费率不允许为0!";
            $this->ajaxReturn($Return_Data);
            exit();
            }
            $data['ReqModel']['FeeParamList'] = array_merge_recursive($FeeWxList, $FeeAliList);
        }else if(I("Payment_Channel")=="01"){
            if(I('zfb_rate')==0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="支付宝费率不允许为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = $FeeAliList;
        }else if(I("Payment_Channel")=="02"){
            if(I('wx_rate')==0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="微信费率不允许为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = $FeeWxList;
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请选择一种或多种支付渠道!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if(I('SysNo')){
            $data['CustomerServiceSysNo']= I('SysNo');//商户主键
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请从服务商角色登录,并进行该操作!";
            $this->ajaxReturn($Return_Data);
            exit();
        }

        if (I('Merchant_Type')=="02") {
            if (I('Registration_Number')&&$_FILES['Business_License_Photo']['name']!="") {
            }else{
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业、个体工商户请填写营业执照工商注册号并上传营业执照图片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Cardholder_Number')!=I('Leader_Certificates_Number')){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="个人商户及个体工商户开户人身份证需要与负责人身份证一致!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if (I('Account_Holder_Name') != I('Legal_Person_Name')) {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="个人商户及个体工商户开户人名称需要与负责人名称一致";
                $this->ajaxReturn($Return_Data);
                exit();
            }

        }else if (I('Merchant_Type')=="01") {
            if (I('Registration_Number')||$_FILES['Business_License_Photo']['name']!="") {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="自然人无须填写营业执照工商注册号并上传营业执照图片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
        }else if (I('Merchant_Type')=="03") {
            if(I('Settlement_Method')!='01'){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户只支持结算到他行卡!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Account_Type')!='02'){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户账户类型必须为对公账户!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if ($_FILES['Licence_Photo']['name']=="") {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户需上传开户许可证照片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if (I('Enterprise_Name')=="") {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户需填写企业名称!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Merchant_Name')!=I("Enterprise_Name")){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户商户名称与法人信息必须一致!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Enterprise_Name')!=I('Account_Holder_Name')){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户开户人名称需要与企业法人必须一致！";
                $this->ajaxReturn($Return_Data);
                exit();

            }
        }



        if(I('T_Zero_Account')=="Y"){
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="商户清算资金暂不支持T0到账! ";
            $this->ajaxReturn($Return_Data);
            exit();
        }


        $data['ReqModel']['MerchantDetail']['Alias']=I('Merchant_Abbreviation');//商户简称。
        $data['ReqModel']['MerchantDetail']['ContactMobile']=I('Linkman_Mobile');//联系人手机号。为商户常用联系人联系手机号。。
        $data['ReqModel']['MerchantDetail']['ContactName']=I('Linkman_Name');//联系人姓名。
        $data['ReqModel']['MerchantDetail']['Province']=I('Province_Details');//省份。
        $data['ReqModel']['MerchantDetail']['City']=I('City_Details');//城市。
        $data['ReqModel']['MerchantDetail']['District']=I('Area_Details');//区。
        $data['ReqModel']['MerchantDetail']['Address']=I('Address_Details');//地址。
        $data['ReqModel']['MerchantDetail']['ServicePhoneNo']=I('Merchant_Service_Number');//商户客服电话。
        $data['ReqModel']['MerchantDetail']['Email']=I('Email');//邮箱。
        $data['ReqModel']['MerchantDetail']['LegalPerson']=I('Enterprise_Name');//企业名称。
        $data['ReqModel']['MerchantDetail']['PrincipalMobile']=I('Leader_Number');//负责人手机号。
        $data['ReqModel']['MerchantDetail']['PrincipalCertType']=I('Leader_Certificates_Type');//负责人证件类型。
        $data['ReqModel']['MerchantDetail']['PrincipalCertNo']=I('Leader_Certificates_Number');//负责人证件号码。。
        $data['ReqModel']['MerchantDetail']['PrincipalPerson']=I('Legal_Person_Name');//负责人名称或企业法人代表姓名。。。
        $data['ReqModel']['MerchantDetail']['BussAuthNum']=I('Registration_Number');//营业执照工商注册号。。
        $data['ReqModel']['MerchantDetail']['CertOrgCode']=I('Organization_Number');//组织机构代码证号。。




        if(I('Settlement_Method')=='01') {

            /*清算卡*/
            $data['ReqModel']['BankCardParam']['BankCardNo']=I('Bank_Card_Number');//银行卡号。。
            $data['ReqModel']['BankCardParam']['BankCertName']=I('Account_Holder_Name');//开户人名称。。
            $data['ReqModel']['BankCardParam']['AccountType']=I('Account_Type');//账户类型。可选值：。。
            $data['ReqModel']['BankCardParam']['ContactLine']=I('Couplet_Number');//联行号。。。
            $data['ReqModel']['BankCardParam']['BranchName']=I('Bank_Branch');//开户支行。。
            $data['ReqModel']['BankCardParam']['BranchProvince']=I('Province');//开户支行所在省。。
            $data['ReqModel']['BankCardParam']['BranchCity']=I('City');//开户支行所在市。。
            $data['ReqModel']['BankCardParam']['CertType']=I('Leader_Certificates_Type');//持卡人地址。。
            $data['ReqModel']['BankCardParam']['CertNo']=I('Cardholder_Number');//持卡人证件号码。。
            $data['ReqModel']['BankCardParam']['CardHolderAddress']=I('Cardholder_Address');//持卡人地址。。
            $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/MCHRegisterUnion';/*结算到他行*/


        }else{
            if (I('Other_Card_Number')) {
                $data['ReqModel']['MerchantDetail']['OtherBankCardNo']=I('Other_Card_Number');//他行卡储蓄卡卡号。。
            }else{
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="请填写他行卡储蓄卡卡号!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/MCHRegisterWithAccountUnion';/*结算到余利宝*/
        }
        $data['ReqModel']['OutMerchantId']= I('External_Merchant_Number'); //外部商户号。合作商对商户的自定义编码，要求同一个合作商下保持唯一。
        $data['ReqModel']['MerchantName']= I('Merchant_Name');//商户名称。有营业执照的，要求与营业执照上的名称一致。
        $data['ReqModel']['MerchantType']= I('Merchant_Type');//
        $data['ReqModel']['DealType']= I('Merchant_Business_Type');//商户经营类型
        $data['ReqModel']['SupportPrepayment']= I('T_Zero_Account');//从T1改成T0，须将结算方式调整为结算到余利宝，同时必送设置T0费率
        $data['ReqModel']['SettleMode']= I('Settlement_Method');//结算方式
        $data['ReqModel']['Mcc']= I('Business_Category');//经营类目。参见附录的经营类目上送。
        $data['ReqModel']['TradeTypeList']= I('Transaction_Type')=='null'?'':I('Transaction_Type');//支持交易类型列表
        $data['ReqModel']['PayChannelList']= I('Payment_Channel')=='null'?'':I('Payment_Channel');//支持支付渠道列表
        $data['ReqModel']['DeniedPayToolList']= I('Disable_Payment_Method')=='null'?'':I('Disable_Payment_Method');//禁用支付方式
        $data['ReqModel']['AuthCode']= I('Verification_Code');//手机验证码
        $data['ReqModel']['SupportStage']= I('Flower_Staging');//是否使用花呗
        $data['ReqModel']['PartnerType']=I('Merchant_Public_Number');//商户在进行微信支付H5支付时所使用的公众号相关信息的类型，枚举值有：
        if (I('Merchant_Public_Number')=="03") {
            $data['ReqModel']['MerchantDetail']['SubscribeAppId'] = "";
        }else{
            $data['ReqModel']['MerchantDetail']['SubscribeAppId']=I('Public_Number');//需关注的公众号对应的APPID。。
        }

        if ($_FILES['Legal_Person_ID_Front']['name']!=""){
            $CertPhotoA= $this->uploadpic($_FILES['Legal_Person_ID_Front']  ,I('SysNo'),'01' );
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请上传负责人或企业法人代身份证图片正面不能为空!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if ($_FILES['Legal_Person_ID_Back']['name']!=""){
            $CertPhotoB= $this->uploadpic($_FILES['Legal_Person_ID_Back'],I('SysNo'),'02' );
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请上传负责人或企业法人代表的身份证图片反面不能为空!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if ($_FILES['Business_License_Photo']['name']!=""){
            $LicensePhoto= $this->uploadpic($_FILES['Business_License_Photo'],I('SysNo'),'03' );
        }
        if ($_FILES['Organization_Photo']['name']!=""){
            $PrgPhoto= $this->uploadpic($_FILES['Organization_Photo'],I('SysNo'),'04' );
        }
        if ($_FILES['Licence_Photo']['name']!=""){
            $IndustryLicensePhoto= $this->uploadpic($_FILES['Licence_Photo'],I('SysNo'), '05');
        }
        if ($_FILES['Storefront_Photo']['name']!=""){
            $ShopPhoto= $this->uploadpic($_FILES['Storefront_Photo'],I('SysNo'), '06');
        }
        if ($_FILES['Other_Photo']['name']!=""){
            $OtherPhoto= $this->uploadpic($_FILES['Other_Photo'],I('SysNo'), '07');
        }
//
//        $data['ReqModel']['MerchantDetail']['CertPhotoA'] = "b03438ce-265a-44d3-bd17-0b328b7f3ea5";
//        $data['ReqModel']['MerchantDetail']['CertPhotoB'] = "aec723db-d074-4c61-b6a2-43d5fd6a8c0c";
//        $data['ReqModel']['MerchantDetail']['LicensePhoto'] = "5522b586-30fd-4e7c-913d-dc0e41560c2a";
//        $data['ReqModel']['MerchantDetail']['PrgPhoto'] = "5907c5fd-0654-47db-96f9-f291ecf59cff";
//        $data['ReqModel']['MerchantDetail']['IndustryLicensePhoto'] = "57cd6924-ac7d-4f28-ad1a-54b5f126b28d";
//        $data['ReqModel']['MerchantDetail']['ShopPhoto'] = "0fc0b1c3-6195-4989-8ece-2130183e6682";
//        $data['ReqModel']['MerchantDetail']['OtherPhoto'] = "693559b3-9919-4233-94a5-6b10e7bfb875";
        $data['ReqModel']['MerchantDetail']['CertPhotoA'] = $CertPhotoA;
        $data['ReqModel']['MerchantDetail']['CertPhotoB'] = $CertPhotoB;
        $data['ReqModel']['MerchantDetail']['LicensePhoto'] = $LicensePhoto;
        $data['ReqModel']['MerchantDetail']['PrgPhoto'] = $PrgPhoto;
        $data['ReqModel']['MerchantDetail']['IndustryLicensePhoto'] = $IndustryLicensePhoto;
        $data['ReqModel']['MerchantDetail']['ShopPhoto'] = $ShopPhoto;
        $data['ReqModel']['MerchantDetail']['OtherPhoto'] = $OtherPhoto;




        $list = http($url, $data);
        $Return_Data['Code'] = $list['Code'];
        if($list['Code']==0){
            $Return_Data['Description'] ='商户入驻成功';

        }else{
            $Return_Data['Description'] =$list['Description'];

        }
        $this->ajaxReturn($Return_Data);

    }
    public function updateWs (){
////        if ((session('data')['CustomersType'] == 0 & session('flag') == 0)) {
////
////        } else {
////            $Return_Data['Code'] = 1;
////            $Return_Data['Description'] = "该角色无权限,进行该操作!";
////            $this->ajaxReturn($Return_Data);
////            exit();
////
////        }
////
////

        $FeeWxList[0]['ChannelType']=I('PassageWx');
        $FeeWxList[0]['FeeType']=I('wx_Type');
        $FeeWxList[0]['FeeValue']=I('wx_rate');

        $FeeAliList[0]['ChannelType']=I('PassageAli');
        $FeeAliList[0]['FeeType']=I('zfb_Type');
        $FeeAliList[0]['FeeValue']=I('zfb_rate');
        $PayTypeArray = explode(",",I("Payment_Channel"));
        if(count($PayTypeArray)>1) {
            if(I('wx_rate')==0||I('zfb_rate')==0){
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="支付宝、微信费率不允许为0!";
            $this->ajaxReturn($Return_Data);
            exit();
            }
            $data['ReqModel']['FeeParamList'] = array_merge_recursive($FeeWxList, $FeeAliList);
        }else if(I("Payment_Channel")=="01"){
            if(I('zfb_rate')==0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="支付宝费率不允许为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = $FeeAliList;
        }else if(I("Payment_Channel")=="02"){
            if(I('wx_rate')==0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="微信费率不允许为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = $FeeWxList;
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请选择一种或多种支付渠道!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if(I('SysNo')){
            $data['CustomerServiceSysNo']= I('SysNo');//商户主键
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请从服务商角色登录,并进行该操作!";
            $this->ajaxReturn($Return_Data);
            exit();
        }

        if (I('Merchant_Type')=="02") {
            if (I('Registration_Number')&&I('Business_License_Photos')!=''){

            }else if (I('Registration_Number')&&$_FILES['Business_License_Photo']['name']!=""){

            }else{
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业、个体工商户请填写营业执照工商注册号并上传营业执照图片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if (I('Account_Holder_Name') != I('Legal_Person_Name')) {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="个人商户及个体工商户开户人名称需要与负责人名称一致";
                $this->ajaxReturn($Return_Data);
                exit();
            }

        }else if (I('Merchant_Type')=="01") {
            if (I('Registration_Number')||$_FILES['Business_License_Photo']['name']!="") {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="自然人无须填写营业执照工商注册号并上传营业执照图片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
        }else if (I('Merchant_Type')=="03") {
            if(I('Settlement_Method')!='01'){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户只支持结算到他行卡!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Account_Type')!='02'){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户账户类型必须为对公账户!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if ($_FILES['Licence_Photo']['name'] =""||I('Licence_Photos')=='') {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户需上传开户许可证照片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Merchant_Name')!=I("Enterprise_Name")){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户商户名称与法人信息必须一致!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Enterprise_Name')!=I('Account_Holder_Name')){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户开户人名称需要与企业法人必须一致！";
                $this->ajaxReturn($Return_Data);
                exit();

            }
        }

//        if (I('Account_Holder_Name')) {
//            if (I('Account_Holder_Name') != I('Legal_Person_Name')) {
//                $Return_Data['Code'] = 1;
//                $Return_Data['Description'] ="个人商户及个体工商户开户人名称需要与负责人名称一致";
//                $this->ajaxReturn($Return_Data);
//                exit();
//            }
//        }
        if(I('T_Zero_Account')=="Y"){
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="商户清算资金暂不支持T0到账! ";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        $data['OutMerchantId']= I('OutMerchantId');

        $data['ReqModel']['MerchantDetail']['Alias']=I('Merchant_Abbreviation');//商户简称。
        $data['ReqModel']['MerchantDetail']['ContactMobile']=I('Linkman_Mobile');//联系人手机号。为商户常用联系人联系手机号。。
        $data['ReqModel']['MerchantDetail']['ContactName']=I('Linkman_Name');//联系人姓名。
        $data['ReqModel']['MerchantDetail']['Province']=I('Province_Details');//省份。
        $data['ReqModel']['MerchantDetail']['City']=I('City_Details');//城市。
        $data['ReqModel']['MerchantDetail']['District']=I('Area_Details');//区。
        $data['ReqModel']['MerchantDetail']['Address']=I('Address_Details');//地址。
        $data['ReqModel']['MerchantDetail']['ServicePhoneNo']=I('Merchant_Service_Number');//商户客服电话。
        $data['ReqModel']['MerchantDetail']['Email']=I('Email');//邮箱。
        $data['ReqModel']['MerchantDetail']['LegalPerson']=I('Enterprise_Name');//企业名称。
        $data['ReqModel']['MerchantDetail']['PrincipalCertType']=I('Leader_Certificates_Type');//负责人证件类型。
        $data['ReqModel']['MerchantDetail']['PrincipalCertNo']=I('Leader_Certificates_Number');//负责人证件号码。。
        $data['ReqModel']['MerchantDetail']['PrincipalPerson']=I('Legal_Person_Name');//负责人名称或企业法人代表姓名。。。
        $data['ReqModel']['MerchantDetail']['BussAuthNum']=I('Registration_Number');//营业执照工商注册号。。
        $data['ReqModel']['MerchantDetail']['CertOrgCode']=I('Organization_Number');//组织机构代码证号。。




        if(I('Settlement_Method')=='01') {

            /*清算卡*/
            $data['ReqModel']['BankCardParam']['BankCardNo']=I('Bank_Card_Number');//银行卡号。。
            $data['ReqModel']['BankCardParam']['BankCertName']=I('Account_Holder_Name');//开户人名称。。
            $data['ReqModel']['BankCardParam']['AccountType']=I('Account_Type');//账户类型。可选值：。。
            $data['ReqModel']['BankCardParam']['ContactLine']=I('Couplet_Number');//联行号。。。
            $data['ReqModel']['BankCardParam']['BranchName']=I('Bank_Branch');//开户支行。。
            $data['ReqModel']['BankCardParam']['BankCode']=I('Bank_Branch');//开户支行Code。。
            $data['ReqModel']['BankCardParam']['BranchProvince']=I('Province');//开户支行所在省。。
            $data['ReqModel']['BankCardParam']['BranchCity']=I('City');//开户支行所在市。。
            $data['ReqModel']['BankCardParam']['CertType']=I('Leader_Certificates_Type');//持卡人地址。。
            $data['ReqModel']['BankCardParam']['CertNo']=I('Cardholder_Number');//持卡人证件号码。。
            $data['ReqModel']['BankCardParam']['CardHolderAddress']=I('Cardholder_Address');//持卡人地址。。


        }else{

        }
        $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/MCHMerchantUpdateUnion';//请求链接

        $data['ReqModel']['MerchantName']= I('Merchant_Name');//商户名称。有营业执照的，要求与营业执照上的名称一致。
        $data['ReqModel']['MerchantId']= I('MerchantId');//商户号
        $data['ReqModel']['MerchantType']= I('Merchant_Type');//
        $data['ReqModel']['DealType']= I('Merchant_Business_Type');//商户经营类型
        $data['ReqModel']['SupportPrepayment']= I('T_Zero_Account');//从T1改成T0，须将结算方式调整为结算到余利宝，同时必送设置T0费率
        $data['ReqModel']['SettleMode']= I('Settlement_Method');//结算方式
        $data['ReqModel']['Mcc']= I('Business_Category');//经营类目。参见附录的经营类目上送。
        $data['ReqModel']['TradeTypeList']= I('Transaction_Type')=='null'?'':I('Transaction_Type');//支持交易类型列表
        $data['ReqModel']['PayChannelList']= I('Payment_Channel')=='null'?'':I('Payment_Channel');//支持支付渠道列表
        $data['ReqModel']['DeniedPayToolList']= I('Disable_Payment_Method')=='null'?'':I('Disable_Payment_Method');//禁用支付方式
        $data['ReqModel']['AuthCode']= I('Verification_Code');//手机验证码
        $data['ReqModel']['SupportStage']= I('Flower_Staging');//是否使用花呗
        $data['ReqModel']['PartnerType']=I('Merchant_Public_Number');//商户在进行微信支付H5支付时所使用的公众号相关信息的类型，枚举值有：


        if ($_FILES['Legal_Person_ID_Front']['name']!=""){
            $CertPhotoA= $this->uploadpic($_FILES['Legal_Person_ID_Front']  ,I('SysNo'),'01' );
//            \Think\Log::record(('抓到了，'));
        }else{
            $CertPhotoA = I('Legal_Person_ID_Fronts');
        }
        if ($_FILES['Legal_Person_ID_Back']['name']!=""){
            $CertPhotoB= $this->uploadpic($_FILES['Legal_Person_ID_Back'],I('SysNo'),'02' );
        }else{
            $CertPhotoB= I("Legal_Person_ID_Backs");
        }
        if ($_FILES['Business_License_Photo']['name']!=""){
            $LicensePhoto= $this->uploadpic($_FILES['Business_License_Photo'],I('SysNo'),'03' );
        }else{
            $LicensePhoto = I("Business_License_Photos");
        }


        if ($_FILES['Organization_Photo']['name']!=""){
            $PrgPhoto= $this->uploadpic($_FILES['Organization_Photo'],I('SysNo'),'04' );
        }else{
            $PrgPhoto= I('Organization_Photos');

        }
        if ($_FILES['Licence_Photo']['name']!=""){
            $IndustryLicensePhoto= $this->uploadpic($_FILES['Licence_Photo'],I('SysNo'), '05');
        }else{
            $IndustryLicensePhoto= I('Licence_Photos');
        }
        if ($_FILES['Storefront_Photo']['name']!=""){
            $ShopPhoto= $this->uploadpic($_FILES['Storefront_Photo'],I('SysNo'), '06');
        }else{
            $ShopPhoto= I('Storefront_Photo');
        }
        if ($_FILES['Other_Photo']['name']!=""){
            $OtherPhoto= $this->uploadpic($_FILES['Other_Photo'],I('SysNo'), '07');
        }else{
            $OtherPhoto= I('Other_Photo');
        }
//
//        $data['ReqModel']['MerchantDetail']['CertPhotoA'] = "b03438ce-265a-44d3-bd17-0b328b7f3ea5";
//        $data['ReqModel']['MerchantDetail']['CertPhotoB'] = "aec723db-d074-4c61-b6a2-43d5fd6a8c0c";
//        $data['ReqModel']['MerchantDetail']['LicensePhoto'] = "5522b586-30fd-4e7c-913d-dc0e41560c2a";
//        $data['ReqModel']['MerchantDetail']['PrgPhoto'] = "5907c5fd-0654-47db-96f9-f291ecf59cff";
//        $data['ReqModel']['MerchantDetail']['IndustryLicensePhoto'] = "57cd6924-ac7d-4f28-ad1a-54b5f126b28d";
//        $data['ReqModel']['MerchantDetail']['ShopPhoto'] = "0fc0b1c3-6195-4989-8ece-2130183e6682";
//        $data['ReqModel']['MerchantDetail']['OtherPhoto'] = "693559b3-9919-4233-94a5-6b10e7bfb875";
        $data['ReqModel']['MerchantDetail']['CertPhotoA'] = $CertPhotoA;
        $data['ReqModel']['MerchantDetail']['CertPhotoB'] = $CertPhotoB;
        $data['ReqModel']['MerchantDetail']['LicensePhoto'] = $LicensePhoto;
        $data['ReqModel']['MerchantDetail']['PrgPhoto'] = $PrgPhoto;
        $data['ReqModel']['MerchantDetail']['IndustryLicensePhoto'] = $IndustryLicensePhoto;
        $data['ReqModel']['MerchantDetail']['ShopPhoto'] = $ShopPhoto;
        $data['ReqModel']['MerchantDetail']['OtherPhoto'] = $OtherPhoto;


        $list = http($url, $data);
        $info['Code'] = $list['Code'];
        if($list['Code']==0){
            $info['Description'] ='商户入驻信息修改成功！';

        }else{
            $info['Description'] =$list['Description'];

        }


        $this->ajaxReturn($info);

    }

    public function updateWsRblue (){
////        if ((session('data')['CustomersType'] == 0 & session('flag') == 0)) {
////
////        } else {
////            $Return_Data['Code'] = 1;
////            $Return_Data['Description'] = "该角色无权限,进行该操作!";
////            $this->ajaxReturn($Return_Data);
////            exit();
////
////        }
////
////

        $FeeWxList[0]['ChannelType']=I('PassageWx');
        $FeeWxList[0]['FeeType']=I('wx_Type');
        $FeeWxList[0]['FeeValue']=I('wx_rate');

        $FeeAliList[0]['ChannelType']=I('PassageAli');
        $FeeAliList[0]['FeeType']=I('zfb_Type');
        $FeeAliList[0]['FeeValue']=I('zfb_rate');
        $PayTypeArray = explode(",",I("Payment_Channel"));
        if(!strstr(I('Payment_Channel'),'01')){
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="低费率必须支持支付宝!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if(count($PayTypeArray)>1) {
            if(I('wx_rate')==0||I('zfb_rate')!=0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="微信费率不允许为0、支付宝费率必须为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = array_merge_recursive($FeeWxList, $FeeAliList);
        }else if(I("Payment_Channel")=="01"){
            if(I('zfb_rate')!=0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="支付宝费率必须为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = $FeeAliList;
        }else if(I("Payment_Channel")=="02"){
            if(I('wx_rate')==0){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="微信费率不允许为0!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            $data['ReqModel']['FeeParamList'] = $FeeWxList;
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请选择一种或多种支付渠道!";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        if(I('SysNo')){
            $data['CustomerServiceSysNo']= I('SysNo');//商户主键
        }else{
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="请从服务商角色登录,并进行该操作!";
            $this->ajaxReturn($Return_Data);
            exit();
        }

        if (I('Merchant_Type')=="02") {
            if (I('Registration_Number')&&I('Business_License_Photos')!=''){

            }else if (I('Registration_Number')&&$_FILES['Business_License_Photo']['name']!=""){

            }else{
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业、个体工商户请填写营业执照工商注册号并上传营业执照图片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if (I('Account_Holder_Name') != I('Legal_Person_Name')) {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="个人商户及个体工商户开户人名称需要与负责人名称一致";
                $this->ajaxReturn($Return_Data);
                exit();
            }

        }else if (I('Merchant_Type')=="01") {
            if (I('Registration_Number')||$_FILES['Business_License_Photo']['name']!="") {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="自然人无须填写营业执照工商注册号并上传营业执照图片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
        }else if (I('Merchant_Type')=="03") {
            if(I('Settlement_Method')!='01'){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户只支持结算到他行卡!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Account_Type')!='02'){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户账户类型必须为对公账户!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if ($_FILES['Licence_Photo']['name'] =""||I('Licence_Photos')=='') {
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户需上传开户许可证照片!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Merchant_Name')!=I("Enterprise_Name")){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户商户名称与法人信息必须一致!";
                $this->ajaxReturn($Return_Data);
                exit();
            }
            if(I('Enterprise_Name')!=I('Account_Holder_Name')){
                $Return_Data['Code'] = 1;
                $Return_Data['Description'] ="企业商户开户人名称需要与企业法人必须一致！";
                $this->ajaxReturn($Return_Data);
                exit();

            }
        }

//        if (I('Account_Holder_Name')) {
//            if (I('Account_Holder_Name') != I('Legal_Person_Name')) {
//                $Return_Data['Code'] = 1;
//                $Return_Data['Description'] ="个人商户及个体工商户开户人名称需要与负责人名称一致";
//                $this->ajaxReturn($Return_Data);
//                exit();
//            }
//        }
        if(I('T_Zero_Account')=="Y"){
            $Return_Data['Code'] = 1;
            $Return_Data['Description'] ="商户清算资金暂不支持T0到账! ";
            $this->ajaxReturn($Return_Data);
            exit();
        }
        $data['OutMerchantId']= I('OutMerchantId');

        $data['ReqModel']['MerchantDetail']['Alias']=I('Merchant_Abbreviation');//商户简称。
        $data['ReqModel']['MerchantDetail']['ContactMobile']=I('Linkman_Mobile');//联系人手机号。为商户常用联系人联系手机号。。
        $data['ReqModel']['MerchantDetail']['ContactName']=I('Linkman_Name');//联系人姓名。
        $data['ReqModel']['MerchantDetail']['Province']=I('Province_Details');//省份。
        $data['ReqModel']['MerchantDetail']['City']=I('City_Details');//城市。
        $data['ReqModel']['MerchantDetail']['District']=I('Area_Details');//区。
        $data['ReqModel']['MerchantDetail']['Address']=I('Address_Details');//地址。
        $data['ReqModel']['MerchantDetail']['ServicePhoneNo']=I('Merchant_Service_Number');//商户客服电话。
        $data['ReqModel']['MerchantDetail']['Email']=I('Email');//邮箱。
        $data['ReqModel']['MerchantDetail']['LegalPerson']=I('Enterprise_Name');//企业名称。
        $data['ReqModel']['MerchantDetail']['PrincipalCertType']=I('Leader_Certificates_Type');//负责人证件类型。
        $data['ReqModel']['MerchantDetail']['PrincipalCertNo']=I('Leader_Certificates_Number');//负责人证件号码。。
        $data['ReqModel']['MerchantDetail']['PrincipalPerson']=I('Legal_Person_Name');//负责人名称或企业法人代表姓名。。。
        $data['ReqModel']['MerchantDetail']['BussAuthNum']=I('Registration_Number');//营业执照工商注册号。。
        $data['ReqModel']['MerchantDetail']['CertOrgCode']=I('Organization_Number');//组织机构代码证号。。




        if(I('Settlement_Method')=='01') {

            /*清算卡*/
            $data['ReqModel']['BankCardParam']['BankCardNo']=I('Bank_Card_Number');//银行卡号。。
            $data['ReqModel']['BankCardParam']['BankCertName']=I('Account_Holder_Name');//开户人名称。。
            $data['ReqModel']['BankCardParam']['AccountType']=I('Account_Type');//账户类型。可选值：。。
            $data['ReqModel']['BankCardParam']['ContactLine']=I('Couplet_Number');//联行号。。。
            $data['ReqModel']['BankCardParam']['BranchName']=I('Bank_Branch');//开户支行。。
            $data['ReqModel']['BankCardParam']['BankCode']=I('Bank_Branch');//开户支行Code。。
            $data['ReqModel']['BankCardParam']['BranchProvince']=I('Province');//开户支行所在省。。
            $data['ReqModel']['BankCardParam']['BranchCity']=I('City');//开户支行所在市。。
            $data['ReqModel']['BankCardParam']['CertType']=I('Leader_Certificates_Type');//持卡人地址。。
            $data['ReqModel']['BankCardParam']['CertNo']=I('Cardholder_Number');//持卡人证件号码。。
            $data['ReqModel']['BankCardParam']['CardHolderAddress']=I('Cardholder_Address');//持卡人地址。。


        }else{

        }
        $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/MCHMerchantUpdateUnion';//请求链接

        $data['ReqModel']['MerchantName']= I('Merchant_Name');//商户名称。有营业执照的，要求与营业执照上的名称一致。
        $data['ReqModel']['MerchantId']= I('MerchantId');//商户号
        $data['ReqModel']['MerchantType']= I('Merchant_Type');//
        $data['ReqModel']['DealType']= I('Merchant_Business_Type');//商户经营类型
        $data['ReqModel']['SupportPrepayment']= I('T_Zero_Account');//从T1改成T0，须将结算方式调整为结算到余利宝，同时必送设置T0费率
        $data['ReqModel']['SettleMode']= I('Settlement_Method');//结算方式
        $data['ReqModel']['Mcc']= I('Business_Category');//经营类目。参见附录的经营类目上送。
        $data['ReqModel']['TradeTypeList']= I('Transaction_Type')=='null'?'':I('Transaction_Type');//支持交易类型列表
        $data['ReqModel']['PayChannelList']= I('Payment_Channel')=='null'?'':I('Payment_Channel');//支持支付渠道列表
        $data['ReqModel']['DeniedPayToolList']= I('Disable_Payment_Method')=='null'?'':I('Disable_Payment_Method');//禁用支付方式
        $data['ReqModel']['AuthCode']= I('Verification_Code');//手机验证码
        $data['ReqModel']['SupportStage']= I('Flower_Staging');//是否使用花呗
        $data['ReqModel']['PartnerType']=I('Merchant_Public_Number');//商户在进行微信支付H5支付时所使用的公众号相关信息的类型，枚举值有：
//        $data['ReqModel']['RateVersion']='RBLUE';//蓝海

        if ($_FILES['Legal_Person_ID_Front']['name']!=""){
            $CertPhotoA= $this->uploadpic($_FILES['Legal_Person_ID_Front']  ,I('SysNo'),'01' );
//            \Think\Log::record(('抓到了，'));
        }else{
            $CertPhotoA = I('Legal_Person_ID_Fronts');
        }
        if ($_FILES['Legal_Person_ID_Back']['name']!=""){
            $CertPhotoB= $this->uploadpic($_FILES['Legal_Person_ID_Back'],I('SysNo'),'02' );
        }else{
            $CertPhotoB= I("Legal_Person_ID_Backs");
        }
        if ($_FILES['Business_License_Photo']['name']!=""){
            $LicensePhoto= $this->uploadpic($_FILES['Business_License_Photo'],I('SysNo'),'03' );
        }else{
            $LicensePhoto = I("Business_License_Photos");
        }


        if ($_FILES['Organization_Photo']['name']!=""){
            $PrgPhoto= $this->uploadpic($_FILES['Organization_Photo'],I('SysNo'),'04' );
        }else{
            $PrgPhoto= I('Organization_Photos');

        }
        if ($_FILES['Licence_Photo']['name']!=""){
            $IndustryLicensePhoto= $this->uploadpic($_FILES['Licence_Photo'],I('SysNo'), '05');
        }else{
            $IndustryLicensePhoto= I('Licence_Photos');
        }
        if ($_FILES['Storefront_Photo']['name']!=""){
            $ShopPhoto= $this->uploadpic($_FILES['Storefront_Photo'],I('SysNo'), '06');
        }else{
            $ShopPhoto= I('Storefront_Photo');
        }
        if ($_FILES['Other_Photo']['name']!=""){
            $OtherPhoto= $this->uploadpic($_FILES['Other_Photo'],I('SysNo'), '07');
        }else{
            $OtherPhoto= I('Other_Photo');
        }
//
//        $data['ReqModel']['MerchantDetail']['CertPhotoA'] = "b03438ce-265a-44d3-bd17-0b328b7f3ea5";
//        $data['ReqModel']['MerchantDetail']['CertPhotoB'] = "aec723db-d074-4c61-b6a2-43d5fd6a8c0c";
//        $data['ReqModel']['MerchantDetail']['LicensePhoto'] = "5522b586-30fd-4e7c-913d-dc0e41560c2a";
//        $data['ReqModel']['MerchantDetail']['PrgPhoto'] = "5907c5fd-0654-47db-96f9-f291ecf59cff";
//        $data['ReqModel']['MerchantDetail']['IndustryLicensePhoto'] = "57cd6924-ac7d-4f28-ad1a-54b5f126b28d";
//        $data['ReqModel']['MerchantDetail']['ShopPhoto'] = "0fc0b1c3-6195-4989-8ece-2130183e6682";
//        $data['ReqModel']['MerchantDetail']['OtherPhoto'] = "693559b3-9919-4233-94a5-6b10e7bfb875";
        $data['ReqModel']['MerchantDetail']['CertPhotoA'] = $CertPhotoA;
        $data['ReqModel']['MerchantDetail']['CertPhotoB'] = $CertPhotoB;
        $data['ReqModel']['MerchantDetail']['LicensePhoto'] = $LicensePhoto;
        $data['ReqModel']['MerchantDetail']['PrgPhoto'] = $PrgPhoto;
        $data['ReqModel']['MerchantDetail']['IndustryLicensePhoto'] = $IndustryLicensePhoto;
        $data['ReqModel']['MerchantDetail']['ShopPhoto'] = $ShopPhoto;
        $data['ReqModel']['MerchantDetail']['OtherPhoto'] = $OtherPhoto;

        if (deep_in_array($data,["LegalPerson","CertOrgCode","PrgPhoto","IndustryLicensePhoto","ShopPhoto","OtherPhoto","DeniedPayToolList","AuthCode","PartnerType"])) {

            $this->ajaxReturn(array('Code'=>1,'Description'=>"参数错误，请稍后再试或重新登录！"));

            exit();
        }
        $list = http($url, $data);


//        var_dump($list);
//        exit();
        $info['Code'] = $list['Code'];
        if($list['Code']==0){
            $info['Description'] ='蓝海行动信息修改成功！';

        }else{
            $info['Description'] =$list['Description'];

        }


        $this->ajaxReturn($info);

    }


    public function rbluecreate() {

        $data['SysNo'] = I('SysNo');
        $data['CustomerServiceSysNo'] = I('CustomerSysNo');
        $data['ReqModel']['MerchantId'] = I('MerchantId');
        $data['ReqModel']['Smid'] = I('Smid');
        $data['ReqModel']['ActivityType'] = I('ActivityType');
        $data['ReqModel']['Name'] = I('CustomerName');
        $data['ReqModel']['AliasName'] = I('AliasName');

        if ($_FILES['ShopEntrancePic']['name'] != "") {
            $ShopEntrancePic = $this->uploadpic($_FILES['ShopEntrancePic'],I('CustomerSysNo'), '06');//06 门头照
        }
        if ($_FILES['IndoorPic']['name'] != "") {
            $IndoorPic = $this->uploadpic($_FILES['IndoorPic'], I('CustomerSysNo'), '09');//09店内环境照片
        }
        if ($_FILES['SettledPic']['name'] != "") {
            $SettledPic = $this->uploadpic($_FILES['SettledPic'], I('CustomerSysNo'), '10');//10入驻证明材料
        }
        if ($_FILES['BusinessLicensePic']['name'] != "") {
            $BusinessLicensePic = $this->uploadpic($_FILES['BusinessLicensePic'], I('CustomerSysNo'), '03');//03店内门店营业执照
        }
        if ($_FILES['CheckstandPic']['name'] != "") {
            $CheckstandPic = $this->uploadpic($_FILES['CheckstandPic'], I('CustomerSysNo'), '08');//08收银台照片
        }
        $data['ReqModel']['ShopEntrancePic'] = $ShopEntrancePic;
        $data['ReqModel']['CheckstandPic'] = $CheckstandPic;
        $data['ReqModel']['BusinessLicensePic'] = $BusinessLicensePic;
        $data['ReqModel']['IndoorPic'] = $IndoorPic;
        $data['ReqModel']['SettledPic'] = $SettledPic;

        if (deep_in_array($data)) {

            $this->ajaxReturn(array('Code'=>1,'Description'=>"参数错误，请稍后再试或重新登录！"));

            exit();
        }
        $url = C('SERVER_HOST') . 'IPP3WSCustomer/WSMerchantActivityCreate';
        $list = http($url, $data);
        if ($list['Code'] == 0) {
            $info['Code'] = $list['Code'];
            $info['Description'] = '报名成功！';

        }else{
            $info['Code'] = $list['Code'];
            $info['Description'] = $list['Data']['ResultData']['m_values']['RespInfo']['ResultMsg'];
        }

        $this->ajaxreturn($info);

    }


    public function rbluequery() {
        $data_ = $_POST['data_'];

        if (!json_decode($data_,true)) {

            $this->ajaxReturn(array('Code'=>1,'Description'=>"参数错误，请稍后再试或重新登录！"));

            exit();
        }
        $url = C('SERVER_HOST') . 'IPP3WSCustomer/WSMerchantActivityQuery';;
        $list = http($url, json_decode($data_,TRUE));
        if ($list) {
            $info['Code'] = $list['Code'];
            switch ($list['Description']) {
                case 'ACCEPTED':
                    $Description = '已报名';
                    break;
                case 'AUDITING':
                    $Description = '审核中';
                    break;
                case 'AUDIT_PASSED':
                    $Description = '报名通过';
                    break;
                case 'AUDIT_REJECTED':
                    $Description = '审核失败';
                    break;
                case 'ACTIVITY_CONFIRMED':
                    $Description = '活动确认';
                    break;
                default:
                    $Description=$list['Description'];

            }
            $info['Description'] = $Description;
        }else{
            $info['Code'] = 1;
            $info['Description'] = '系统异常，请稍后再试！';
        }


        $this->ajaxreturn($info);

    }

    public function rblueconfirm() {
        $data['ReqModel']['OrderId'] = I('OrderId');
        $data['SysNo'] = I('SysNo');
        $data['CustomerServiceSysNo'] = I('CustomerServiceSysNo');
        if (deep_in_array($data)) {

            $this->ajaxReturn(array('Code'=>1,'Description'=>"参数错误，请稍后再试或重新登录！"));

            exit();
        }
        $url = C('SERVER_HOST') . 'IPP3WSCustomer/WSRblueConfirmUnion';
//        var_dump(json_encode($data));
//        exit();
        $list = http($url, $data);
        if ($list) {
            if($list['Code']==0){
                $info['Code']=$list['Code'];
                $info['Description']='蓝海行动报名确认成功！';
            }else{
                $info['Code']=$list['Code'];
                $info['Description']='蓝海行动报名确认失败！';
            }
        }else{
            $info['Code'] = 1;
            $info['Description'] = '系统异常，请稍后再试！';
        }

        $this->ajaxreturn($info);
    }


    private  function  uploadpic($pic,$sysno,$type){

        $uniqid = uniqid();
        $img_data = [
            $pic['name'] => $pic['tmp_name']
        ];
        $text_data['PhotoType']=$type;
        $text_data['CustomerServiceSysNo']=$sysno;
        $text_data['ImageName']= $pic['name'];
        $post_data='';
        foreach ($text_data as $key=>$value){
            $post_data .= "--8d" . $uniqid . "\r\n"
                . "Content-Disposition: form-data; name=\"$key\"". "\r\n"
                . "Content-Type: text/plain;charset=UTF-8" . "\r\n"
                ."Content-Transfer-Encoding:8bit" . "\r\n" . "\r\n"
                . $value . "\r\n";


        }

        foreach ($img_data as $key=>$value){
            $post_data .=  "--8d" . $uniqid . "\r\n"
                . "Content-Disposition: form-data; name=\"Picture\"; filename=\"$key\"" . "\r\n"
                . "Content-Type: multipart/form-data" . "\r\n"
                . "Content-Transfer-Encoding: binary" . "\r\n" . "\r\n"
                . file_get_contents($value) . "\r\n";

        }



        $url=C( 'SERVER_HOST' ) . 'IPP3WSCustomer/SMSUploadphotoUnion';
        $head = array(
            'Content-Type: multipart/form-data; boundary=8d' . $uniqid,
            'Connection: Keep-Alive' );
        $post_data .= "\r\n".'--8d' . $uniqid . '--'."\r\n";
        $list=http_request_notime($url, $post_data,$head);

        $list = json_decode($list, true);
        if ($list['Data']['PhotoUrl']) {

        }else{
            $info['Code'] = 1;
            $info['Description'] = '图片上传失败，请稍后再试！';
            $this->ajaxreturn($info);
            exit();
        }

        return ($list['Data']['PhotoUrl']);


    }

    public function getWSSMSCode() {

        if (I("Settlement_Method")=="01") {
            //他行
            $BizType = '04';
        }else if (I("Settlement_Method") == "02") {
            //余利宝
            $BizType = '01';
        }
        $data['CustomerServiceSysNo']= I('SysNo');
        $data['ReqModel']['BizType'] = $BizType;
        $data['ReqModel']['Mobile'] = I("Leader_Number");
        $url = C('SERVER_HOST')."IPP3WSCustomer/WSSMSCode";
        $list = http($url, $data);
        $this->ajaxreturn($list);

    }
    private function GetWS_Address($parent_id,$type=0){
        if($type==1){

        }else{
            $data['Parent_id']=$parent_id;
        }
        $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetWS_Address";

        $list = http($url,$data);

        foreach ($list as $row=>$val){
            $info[$row]['region_id'] = $val['Priority'];
            $info[$row]['region_name']   = $val['AddressName'];
        }
        return $info;
    }
    private function GetWS_BankArea($parent_id,$type=0){
        if($type==1){

        }else{
            $data['Parent_id']=$parent_id;
        }
        $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetWS_BankArea";
        $list = http($url,$data);
        foreach ($list as $row=>$val){
            $info[$row]['region_id'] = $val['Priority'];
            $info[$row]['region_name']   = $val['AddressName'];
            if ($val['Priority']==null) {
                $info[$row]['city_code'] = $val['CityCode'];
            }
        }
        return $info;
    }
    private function GetWS_ContactLine($parent_id,$bankname){
        if (I('bankname')=="") {

        }else {
            $data['CityCode'] = $parent_id;
            $data['BankName'] = $bankname;
            $url = C('SERVER_HOST') . "IPP3Customers/IPP3GetWS_ContactLine";
            $list = http($url, $data);
            foreach ($list as $row => $val) {
                $info[$row]['region_id'] = $val['SysNo'];
                $info[$row]['region_name'] = $val['BankName'];
                $info[$row]['city_code'] = $val['CityCode'];
            }
            $this->ajaxreturn($info);
        }
    }
}