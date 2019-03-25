<?php

function http_request( $url, $data = NULL, $head = NULL ){//15秒抛出
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_TIMEOUT,15);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    if( !empty( $data ) ){
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    }
    if( !empty( $head ) ){
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $head );
    }
    $output = curl_exec( $ch );
    curl_close( $ch );
    return $output;
}

function http_request_notime( $url, $data = NULL, $head = NULL ){
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
//	curl_setopt( $ch, CURLOPT_TIMEOUT,15);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    if( !empty( $data ) ){
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    }
    if( !empty( $head ) ){
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $head );
    }
    $output = curl_exec( $ch );
    curl_close( $ch );
    return $output;
}
function http( $url, $data = NULL ){
    $data = json_encode($data);
    $head = array(
        "Content-Type:application/json;charset=UTF-8",
        "Content-length:" . strlen( $data )
    );
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_TIMEOUT,15);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    if( !empty( $data ) ){
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    }
    if( !empty( $head ) ){
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $head );
    }
    $output = curl_exec( $ch );
    curl_close( $ch );
    $data = json_decode( $output, TRUE );
    return $data;
}


/**
 * 分转元
 * @param type $money
 * @return type
 */
function fee2yuan( $money ){

    switch( strlen( $money ) ){
        case 1:
            $money = '0.0' . $money;
            break;
        case 2:
            $money = '0.' . $money;
            break;
        default :
            $money = strval( $money / 100 );
    }
    return round( $money, 2 );
}

/**
 * 元转分
 * @param type $money
 * @return boolean
 */
function yuan2fee( $money ){
    if( $money <= 0 || $money > 10000000 ){
        return FALSE;
    }
    $fee = explode( ".", $money );

    if( count( $fee ) > 1 ){
        if( strlen( $fee[1] ) > 2 ){
            return FALSE;
        }

        if( $fee[1] > 9 || strlen( $fee[1] ) > 1 ){
            $total_fee = $fee[0] . $fee[1];
        }else{
            $total_fee = $fee[0] . $fee[1] . '0';
        }
    }else{
        $total_fee = $fee[0] . '00';
    }
    $total_fee = ltrim( $total_fee, '0' );
    return $total_fee;
}

function QueryCustomerSysNo($SysNO){
    $arr = array(
        "customerServiceSysNo" => $SysNO
    );

    $data['PagingInfo']['PageSize']   = 2;
    $data['PagingInfo']['PageNumber'] = 1;

    $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserByCSsysNo";
    $data = json_encode( $arr );
    //var_dump($data);
    $head = array(
        "Content-Type:application/json;charset=UTF-8",
        "Content-length:" . strlen( $data ),
        //"X-Ywkj-Authentication:" . strlen( $data ),
    );

    $list = http_request( $url, $data, $head );
    $list = json_decode( $list );
    return $list;


}

function staffquerystore($id){ //查找服务商户ID
    $data['SystemUserSysNo'] = $id;
    $data['PagingInfo']['PageSize']   = 1;
    $data['PagingInfo']['PageNumber'] = 0;
    $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetCustomerServiceSysNoList";
    $data = json_encode($data);
    $head = array(
        "Content-Type:application/json;charset=UTF-8",
        "Content-length:" . strlen( $data ),
        //"X-Ywkj-Authentication:" . strlen( $data ),
    );
    $list = http_request( $url, $data, $head );
    $list = json_decode( $list ,true);
    return $list['CustomerSysNo'];


}

function staffstoreorservice($id){//查看上级是服务商OR 商户
    $data['systemUserSysNo'] = $id;
    $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetCustomerServiceSysNoList";
    $data = json_encode($data);
    $head = array(
        "Content-Type:application/json;charset=UTF-8",
        "Content-length:" . strlen( $data ),
        //"X-Ywkj-Authentication:" . strlen( $data ),
    );
    $list = http_request( $url, $data, $head );
    $list = json_decode( $list ,true);
    return $list['CustomersType'];

}

function GetCustomerServiceSysNo($id){//查看上级名称
    $data['systemUserSysNo'] = $id;
    $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetCustomerServiceSysNoList";
    $data = json_encode($data);
    $head = array(
        "Content-Type:application/json;charset=UTF-8",
        "Content-length:" . strlen( $data ),
        //"X-Ywkj-Authentication:" . strlen( $data ),
    );
    $list = http_request( $url, $data, $head );
    $list = json_decode( $list ,true);
    return $list['CustomerName'];

}
function GetCustomerInfoBySysNo($id){
    $data['systemUserSysNo'] = $id;
    $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetCustomerServiceSysNoList";
    $data = json_encode($data);
    $head = array(
        "Content-Type:application/json;charset=UTF-8",
        "Content-length:" . strlen( $data ),
        //"X-Ywkj-Authentication:" . strlen( $data ),
    );
    $list = http_request( $url, $data, $head );
    $list = json_decode( $list ,true);
    return $list;
}



/**
 * http GET请求
 * @param type $url
 * @return type
 */
function ihttp_get( $url ){
    return ihttp_request( $url );
}

/**
 * http POST请求
 * @param type $url
 * @param type $data
 * @return type
 */
function ihttp_post( $url, $data ){
    $headers = array( 'Content-Type' => 'application/x-www-form-urlencoded' );
    return ihttp_request( $url, $data, $headers );
}


function ihttp_request( $url, $post = '', $extra = array(), $timeout = 60 ){
    $urlset = parse_url( $url );
    if( empty( $urlset['path'] ) ){
        $urlset['path'] = '/';
    }
    if( !empty( $urlset['query'] ) ){
        $urlset['query'] = "?{$urlset['query']}";
    }
    if( empty( $urlset['port'] ) ){
        $urlset['port'] = $urlset['scheme'] == 'https' ? '443' : '80';
    }
    if( strexists( $url, 'https://' ) && !extension_loaded( 'openssl' ) ){
        if( !extension_loaded( "openssl" ) ){
            // message( '请开启您PHP环境的openssl' );
        }
    }
    if( function_exists( 'curl_init' ) && function_exists( 'curl_exec' ) ){
        $ch = curl_init();
        if( ver_compare( phpversion(), '5.6' ) >= 0 ){
            curl_setopt( $ch, CURLOPT_SAFE_UPLOAD, false );
        }
        if( !empty( $extra['ip'] ) ){
            $extra['Host']  = $urlset['host'];
            $urlset['host'] = $extra['ip'];
            unset( $extra['ip'] );
        }
        curl_setopt( $ch, CURLOPT_URL,
            $urlset['scheme'] . '://' . $urlset['host'] . ($urlset['port'] == '80'
                ? '' : ':' . $urlset['port']) . $urlset['path'] . $urlset['query'] );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        @curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_HEADER, 1 );
        @curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        if( $post ){
            if( is_array( $post ) ){
                $filepost = false;
                foreach( $post as $name => $value ){
                    if( (is_string( $value ) && substr( $value, 0, 1 ) == '@') ||
                        (class_exists( 'CURLFile' ) && $value instanceof CURLFile) ){
                        $filepost = true;
                        break;
                    }
                }
                if( !$filepost ){
                    $post = http_build_query( $post );
                }
            }
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
        }
        if( !empty( $GLOBALS['_W']['config']['setting']['proxy'] ) ){
            $urls = parse_url( $GLOBALS['_W']['config']['setting']['proxy']['host'] );
            if( !empty( $urls['host'] ) ){
                curl_setopt( $ch, CURLOPT_PROXY,
                    "{$urls['host']}:{$urls['port']}" );
                $proxytype = 'CURLPROXY_' . strtoupper( $urls['scheme'] );
                if( !empty( $urls['scheme'] ) && defined( $proxytype ) ){
                    curl_setopt( $ch, CURLOPT_PROXYTYPE, constant( $proxytype ) );
                }else{
                    curl_setopt( $ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP );
                    curl_setopt( $ch, CURLOPT_HTTPPROXYTUNNEL, 1 );
                }
                if( !empty( $GLOBALS['_W']['config']['setting']['proxy']['auth'] ) ){
                    curl_setopt( $ch, CURLOPT_PROXYUSERPWD,
                        $GLOBALS['_W']['config']['setting']['proxy']['auth'] );
                }
            }
        }
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSLVERSION, 1 );
        if( defined( 'CURL_SSLVERSION_TLSv1' ) ){
            curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1 );
        }
        curl_setopt( $ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1' );
        if( !empty( $extra ) && is_array( $extra ) ){
            $headers = array();
            foreach( $extra as $opt => $value ){
                if( strexists( $opt, 'CURLOPT_' ) ){
                    curl_setopt( $ch, constant( $opt ), $value );
                }elseif( is_numeric( $opt ) ){
                    curl_setopt( $ch, $opt, $value );
                }else{
                    $headers[] = "{$opt}: {$value}";
                }
            }
            if( !empty( $headers ) ){
                curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
            }
        }
        $data   = curl_exec( $ch );
        $status = curl_getinfo( $ch );
        $errno  = curl_errno( $ch );
        $error  = curl_error( $ch );
        curl_close( $ch );
        if( $errno || empty( $data ) ){
            return error( 1, $error );
        }else{
            return ihttp_response_parse( $data );
        }
    }
    $method = empty( $post ) ? 'GET' : 'POST';
    $fdata  = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\n";
    $fdata .= "Host: {$urlset['host']}\r\n";
    if( function_exists( 'gzdecode' ) ){
        $fdata .= "Accept-Encoding: gzip, deflate\r\n";
    }
    $fdata .= "Connection: close\r\n";
    if( !empty( $extra ) && is_array( $extra ) ){
        foreach( $extra as $opt => $value ){
            if( !strexists( $opt, 'CURLOPT_' ) ){
                $fdata .= "{$opt}: {$value}\r\n";
            }
        }
    }
    $body = '';
    if( $post ){
        if( is_array( $post ) ){
            $body = http_build_query( $post );
        }else{
            $body = urlencode( $post );
        }
        $fdata .= 'Content-Length: ' . strlen( $body ) . "\r\n\r\n{$body}";
    }else{
        $fdata .= "\r\n";
    }
    if( $urlset['scheme'] == 'https' ){
        $fp = fsockopen( 'ssl://' . $urlset['host'], $urlset['port'], $errno,
            $error );
    }else{
        $fp = fsockopen( $urlset['host'], $urlset['port'], $errno, $error );
    }
    stream_set_blocking( $fp, true );
    stream_set_timeout( $fp, $timeout );
    if( !$fp ){
        return error( 1, $error );
    }else{
        fwrite( $fp, $fdata );
        $content = '';
        while( !feof( $fp ) )
            $content .= fgets( $fp, 512 );
        fclose( $fp );
        return ihttp_response_parse( $content, true );
    }
}




function strexists( $string, $find ){
    return !(strpos( $string, $find ) === FALSE);
}


function ver_compare( $version1, $version2 ){
    $version1  = str_replace( '.', '', $version1 );
    $version2  = str_replace( '.', '', $version2 );
    $oldLength = istrlen( $version1 );
    $newLength = istrlen( $version2 );
    if( $oldLength > $newLength ){
        $version2 .= str_repeat( '0', $oldLength - $newLength );
    }
    if( $newLength > $oldLength ){
        $version1 .= str_repeat( '0', $newLength - $oldLength );
    }
    $version1 = intval( $version1 );
    $version2 = intval( $version2 );
    return version_compare( $version1, $version2 );
}

function istrlen( $string, $charset = '' ){
    if( empty( $charset ) ){
//        $charset = $_W['charset'];
        $charset = 'utf8';
        ;
    }
    if( strtolower( $charset ) == 'gbk' ){
        $charset = 'gbk';
    }else{
        $charset = 'utf8';
    }
    if( function_exists( 'mb_strlen' ) ){
        return mb_strlen( $string, $charset );
    }else{
        $n      = $noc    = 0;
        $strlen = strlen( $string );

        if( $charset == 'utf8' ){

            while( $n < $strlen ){
                $t = ord( $string[$n] );
                if( $t == 9 || $t == 10 || (32 <= $t && $t <= 126) ){
                    $n++;
                    $noc++;
                }elseif( 194 <= $t && $t <= 223 ){
                    $n += 2;
                    $noc++;
                }elseif( 224 <= $t && $t <= 239 ){
                    $n += 3;
                    $noc++;
                }elseif( 240 <= $t && $t <= 247 ){
                    $n += 4;
                    $noc++;
                }elseif( 248 <= $t && $t <= 251 ){
                    $n += 5;
                    $noc++;
                }elseif( $t == 252 || $t == 253 ){
                    $n += 6;
                    $noc++;
                }else{
                    $n++;
                }
            }
        }else{

            while( $n < $strlen ){
                $t = ord( $string[$n] );
                if( $t > 127 ){
                    $n += 2;
                    $noc++;
                }else{
                    $n++;
                    $noc++;
                }
            }
        }

        return $noc;
    }
}
function ihttp_response_parse( $data, $chunked = false ){
    $rlt        = array();
    $headermeta = explode( 'HTTP/', $data );
    if( count( $headermeta ) > 2 ){
        $data = 'HTTP/' . array_pop( $headermeta );
    }
    $pos       = strpos( $data, "\r\n\r\n" );
    $split1[0] = substr( $data, 0, $pos );
    $split1[1] = substr( $data, $pos + 4, strlen( $data ) );

    $split2              = explode( "\r\n", $split1[0], 2 );
    preg_match( '/^(\S+) (\S+) (\S+)$/', $split2[0], $matches );
    $rlt['code']         = $matches[2];
    $rlt['status']       = $matches[3];
    $rlt['responseline'] = $split2[0];
    $header              = explode( "\r\n", $split2[1] );
    $isgzip              = false;
    $ischunk             = false;
    foreach( $header as $v ){
        $pos   = strpos( $v, ':' );
        $key   = substr( $v, 0, $pos );
        $value = trim( substr( $v, $pos + 1 ) );
        if( is_array( $rlt['headers'][$key] ) ){
            $rlt['headers'][$key][] = $value;
        }elseif( !empty( $rlt['headers'][$key] ) ){
            $temp                   = $rlt['headers'][$key];
            unset( $rlt['headers'][$key] );
            $rlt['headers'][$key][] = $temp;
            $rlt['headers'][$key][] = $value;
        }else{
            $rlt['headers'][$key] = $value;
        }
        if( !$isgzip && strtolower( $key ) == 'content-encoding' && strtolower( $value ) ==
            'gzip' ){
            $isgzip = true;
        }
        if( !$ischunk && strtolower( $key ) == 'transfer-encoding' && strtolower( $value ) ==
            'chunked' ){
            $ischunk = true;
        }
    }
    if( $chunked && $ischunk ){
        $rlt['content'] = ihttp_response_parse_unchunk( $split1[1] );
    }else{
        $rlt['content'] = $split1[1];
    }
    if( $isgzip && function_exists( 'gzdecode' ) ){
        $rlt['content'] = gzdecode( $rlt['content'] );
    }

    $rlt['meta'] = $data;
    if( $rlt['code'] == '100' ){
        return ihttp_response_parse( $rlt['content'] );
    }
    return $rlt;
}

function is_error( $data ){
    if( empty( $data ) || !is_array( $data ) || !array_key_exists( 'errno',
            $data ) || (array_key_exists( 'errno', $data ) && $data['errno'] == 0) ){
        return false;
    }else{
        return true;
    }
}

function QueryStaffInfo($id){//员工主键查询姓名
    $data['SysNo']= $id;
    $data = json_encode( $data );
    $head = array(
        "Content-Type:application/json;charset=UTF-8",
        "Content-length:" . strlen( $data ),
        "X-Ywkj-Authentication:" . strlen( $data)
    );
    $list = http_request(C('SERVER_HOST')."IPP3Customers/IPP3SystemUserList",$data,$head);
    $list = json_decode($list,true);
    return $list['model'][0]['DisplayName'];

}
function CheckOrderType($Pay_Type) {

    foreach(json_decode($_COOKIE['passageway_list'],true ) as $key=>$val) {
        if ($Pay_Type==$val['T']) {
            return $Type = $val['N'];
        }
    }

}

function exportpost($url,$data){

        $data['PagingInfo']['PageSize'] = 2000;//请求条数

        $data['PagingInfo']['PageNumber'] = 0;//请求页数

        $data = json_encode( $data );

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data ),

            "X-Ywkj-Authentication:" . strlen( $data )

        );

        $list = http_request($url,$data,$head);//请求

        $list = json_decode($list,true);

        $count = ceil($list['totalCount']/2000)-1;//循环请求次数

        $arr =array();

        for ($i = 0; $i < $count; $i++) {

            $data = json_decode($data,true);

            $data['PagingInfo']['PageNumber']+=1;

            $data = json_encode( $data );

			$head1 = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data ),

            "X-Ywkj-Authentication:" . strlen( $data )

			);

            $list1 = http_request($url,$data,$head1);//请求

            $list1 = json_decode($list1,true);

            usleep(100);

            if($list1){

                $list2 = array_merge_recursive($arr,$list1);

            }else{

            }

            $arr= $list2;

        }

        if($list2){

            $list = array_merge_recursive($list,$list2);

        }

        return $list;
    }

function deep_in_array( $array,$no_array=null,$value=null) {
    foreach ($array as $key => $item) {
        if (@!in_array($key, $no_array)) {
            if (is_array($item)) {
                $count = getmaxdim($item);
                if($count>1){
                    foreach ($item as $temp_k=>$temp_v) {
                        $count1=getmaxdim($temp_v);
                        if($count1>1){
                            $aaa = deep_in_array($temp_v,$no_array,$value);
                            if ($aaa) {
                                return true;
                            }else{
                                continue;
                            }
                        } elseif ($count1==0) {
                            if (@!in_array($temp_k, $no_array)) {
                                if ($temp_v == $value) {
                                    return true;
                                } else {
                                    continue;
                                }
                            }
                        }else{
                            foreach ($temp_v as $temp_k3=>$temp_v3) {
                                if (@!in_array($temp_k3, $no_array)) {
                                    if ($temp_v3 == $value) {
                                        return true;
                                    } else {
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                }else{
                    foreach ($item as  $temp_k1=>$temp_v1) {
                        if (@!in_array($temp_k1, $no_array)) {
                            if ($temp_v1 == $value) {
                                return true;
                            } else {
                                continue;
                            }
                        }
                    }
                }
            }else{
                if ($item == $value) {
                    return true;
                }else{
                    continue;
                }
            }
        }
    }
    return false;
}
function getmaxdim($vDim)
{
    if(!is_array($vDim)) return 0;
    else
    {
        $max1 = 0;
        foreach($vDim as $item1)
        {
            $t1 = getmaxdim($item1);
            if( $t1 > $max1) $max1 = $t1;
        }
        return $max1 + 1;
    }
}
function print_key($data,$flag=null) {
    foreach ($data as $k =>$v) {
        if (is_array($v)) {
            print_key($v);
        } else {
            if($flag){
                print '"'.$k.'"'.",";

            }else{
                if($v==''){
                    print '"'.$k.'"'.",";
                }
            }


        }
    }
}