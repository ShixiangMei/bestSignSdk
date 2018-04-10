<?php
namespace Msx\BestSignSdk;

use Illuminate\Session\SessionManager;
use Illuminate\Config\Repository;
use Msx\BestSignSdk\Helpers\HttpUtils;



class BestSignSdk
{
    private $_developerId = '';
    private $_pem = '';
    private $_host = '';
    private $_http_utils = null;

    public function __construct($_developerId, $pem, $host, $pem_type)
    {
        $this->_pem = $this->_formatPem($pem, $pem_type);
        $this->_developerId = $_developerId;
        $this->_host = $host;
        $this->_http_utils = new HttpUtils();
    }

    /**
     * 获得企业社会码json
     */
    public function getUifieldCode(){
        $data =  array(
            'regcode'=>$this->_credential,
            'orgcode'=>$this->_credential,
            'taxcode'=>$this->_credential,
        );
        return json_encode($data);

    }

    //********************************************************************************
    // 接口
    //********************************************************************************

    /**
     * User: mei
     * Date: 2018/4/8 15:21
     * @param $account 用户帐号
     * @param $mail 用户邮箱
     * @param $mobile 用户手机号
     * @param $name 用户名称
     * @param $userType 用户类型(1 代表个人)
     * @param null $credential 用户证件信息对象
     * @param string $applyCert 是否申请证书(1 代表申请)
     * @return mixed
     * @throws \Exception
     */
    public function regUser($account, $mail, $mobile, $name, $userType, $credential=null, $applyCert='0')
    {

        $path = "/user/reg/";

        //post data
        $post_data['email'] = $mail;
        $post_data['mobile'] = $mobile;
        $post_data['name'] = $name;
        $post_data['userType'] = $userType;
        $post_data['account'] = $account;
        $post_data['credential'] = json_encode($credential);
        $post_data['applyCert'] = $applyCert;

        $post_data = json_encode($post_data);

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        return json_decode($response);
    }

    public function regCorUser($account,  $name, $userType, $credential=null, $applyCert='0')
    {

        $path = "/user/reg/";

        //post data
//        $post_data['email'] = $mail;
//        $post_data['mobile'] = $mobile;
        $post_data['name'] = $name;
        $post_data['userType'] = $userType;
        $post_data['account'] = $account;
        $post_data['credential'] = $credential;
        $post_data['applyCert'] = $applyCert;

        $post_data = json_encode($post_data);

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return $response;
    }

    /**
     * 4要素验证
     * User: mei
     * Date: 2018/4/9 20:40
     * @param $name
     * @param $identity
     * @param $legalPerson
     * @param $legalPersonIdentity
     * @param int $legalPersonIdentityType
     * @return mixed
     * @throws \Exception
     */
    public function identity4($name, $identity, $legalPerson, $legalPersonIdentity, $legalPersonIdentityType = 0)
    {
        $path = "/credentialVerify/enterprise/identity4/";
        $post_data['name'] = $name;
        $post_data['identity'] = $identity;
        $post_data['legalPerson'] = $legalPerson;
        $post_data['legalPersonIdentity'] = $legalPersonIdentity;

        $post_data = json_encode($post_data);
        \Log::info('identity4_send_data: ' . print_r($post_data,true));

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        \Log::info('identity4_send_url: ' . print_r($url,true));

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return $response;
    }

    /**
     * 发送合同
     * User: mei
     * Date: 2018/4/7 20:54
     * @param $contractId
     * @param $signer
     * @return mixed
     * @throws \Exception
     */
    public function contractSend($contractId, $signer)
    {
        $path = "/contract/send/";
        $post_data['contractId'] = $contractId;
        $post_data['signer'] = $signer;

        $post_data = json_encode($post_data);
        \Log::info('contract_send_data: ' . print_r($post_data,true));

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        \Log::info('contract_send_url: ' . print_r($url,true));

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return $response;
    }

    /**
     * 预览页url
     * User: mei
     * Date: 2018/4/7 20:53
     * @param $contractId 合同id
     * @param $account 签约者账号(通常为email)
     * @return mixed
     * @throws \Exception
     */
    public function contractPreviewURL($contractId, $account)
    {
        $path = "/contract/getPreviewURL";
        $post_data['contractId'] = $contractId;
        $post_data['account'] = $account;

        $post_data = json_encode($post_data);
        \Log::info('contract_preview_data: ' . print_r($post_data,true));

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        \Log::info('contract_preview_url: ' . print_r($url,true));

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return json_decode($response);
    }

    /**
     * 签署合同
     * User: mei
     * Date: 2018/4/7 20:06
     * @param $contractId 合同id
     * @param $signer 签署账号(即签署者的account)
     * @param $signaturePositions 指定的签署位置，json arra
     * @return mixed
     * @throws \Exception
     */
    public function contractSign($contractId, $signer, $signaturePositions)
    {

        $path = "/storage/contract/sign/cert/";
        $post_data['contractId'] = $contractId;
        $post_data['signer'] = $signer;
        $post_data['signaturePositions'] = $signaturePositions;

        $post_data = json_encode($post_data);
        \Log::info('contract_sign_data: ' . print_r($post_data,true));

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        \Log::info('contract_sign_url: ' . print_r($url,true));

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return json_decode($response);
    }

    /**
     * 创建合同
     * User: mei
     * Date: 2018/4/7 20:03
     * @param $account
     * @param $fid
     * @param $expireTime
     * @param $title
     * @param null $description
     * @return mixed
     * @throws \Exception
     */
    public function contractCreate($account, $fid, $expireTime, $title, $description = null)
    {
        $path = "/contract/create/";
        $post_data['account'] = $account;
        $post_data['fid'] = $fid;
        $post_data['expireTime'] = $expireTime;
        $post_data['title'] = $title;
        $post_data['description'] = $description;

        $post_data = json_encode($post_data);
        \Log::info('contract_create_data: ' . print_r($post_data,true));

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        \Log::info('contract_create_url: ' . print_r($url,true));

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return json_decode($response);
    }


    /**
     * 上传合同文件
     * User: mei
     * Date: 2018/4/7 19:57
     * @param $user
     * @param $url
     * @param $page
     * @return mixed
     * @throws \Exception
     */
    public function signUpdate($account, $url, $page)
    {
        $path = "/storage/upload/";
        $file = file_get_contents('http://'. config('oss.bucket') . '.' . config('oss.end_point') . '/' . $url);
        $post_data['account'] = $account;
        $post_data['fdata'] = base64_encode($file);
        $post_data['ftype'] = 'pdf';
        $post_data['fname'] = $url;
        $post_data['fpages'] = $page;
        $post_data['fmd5']  = md5($file);

        $post_data = json_encode($post_data);

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return $response;

    }

    /**
     * 文件添加描述
     * User: mei
     * Date: 2018/4/7 19:56
     * @param $fid
     * @param $account
     * @param $elements
     * @return mixed
     * @throws \Exception
     */
    public function addPDFElements($fid, $account, $elements)
    {
        $path = "/storage/addPDFElements/";

        $post_data['account']  = $account;
        $post_data['fid']      = $fid;
        $post_data['elements'] = $elements;

        $post_data = json_encode($post_data);

        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick']       = $rtick;
        $params['signType']    = 'rsa';
        $params['sign']        = $sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return $response;

    }

    /**
     * 上传并创建合同
     * User: mei
     * Date: 2018/4/7 19:56
     * @param $account
     * @param $url
     * @param $page
     * @param $title
     * @param $expireTime
     * @param null $description
     * @return mixed
     * @throws \Exception
     */
    public function contractUpdate($account, $url, $page, $title, $expireTime, $description = null)
    {
        $path = "/storage/contract/upload/";
        $file = file_get_contents('http://' . config('oss.bucket') . '.' . config('oss.end_point') . '/' . $url);
        $post_data['account'] = $account;
        $post_data['fdata'] = base64_encode($file);
        $post_data['ftype'] = 'pdf';
        $post_data['fname'] = $url;
        $post_data['fpages'] = $page;
        $post_data['fmd5']  = md5_file($file);
        $post_data['title'] = $title;
        $post_data['expireTime'] = $expireTime;
        $post_data['description'] = $description;

        $post_data = json_encode($post_data);

        //rtick
        $rtick = time().rand(1000, 9999);

        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));

        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;

        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);

        //header data
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return $response;
    }

    public function downloadSignatureImage($account, $image_name)
    {
        $path = "/signatureImage/user/download/";

        $url_params['account'] = $account;
        $url_params['imageName'] = $image_name;

        //rtick
        $rtick = time() . rand(1000, 9999);

        //sign
        $sign_data = $this->_genSignData($path, $url_params, $rtick, null);
        $sign = $this->getRsaSign($sign_data);

        $url = $this->_getRequestUrl($path, $url_params, $sign, $rtick);

        //header data
        $header_data = array();

        //content
        $response = $this->execute('GET', $url, null, $header_data, true);

        return $response;
    }

    /**
     * 下载模板文件
     * User: mei
     * Date: 2018/4/4 9:28
     * @param $fid
     */
    public function downloadTemplate($fid)
    {
        $path = "/storage/download/";

        $url_params['fid'] = $fid;

        //rtick
        $rtick = time() . rand(1000, 9999);

        //sign
        $sign_data = $this->_genSignData($path, $url_params, $rtick, null);
        $sign = $this->getRsaSign($sign_data);

        $url = $this->_getRequestUrl($path, $url_params, $sign, $rtick);
        \Log::info('url: ' . print_r($url,true));

        //header data
        $header_data = array();

        //content
        $response = $this->execute('GET', $url, null, $header_data, true);

        return $response;
    }

    /**
     * 下载合同文件
     * User: mei
     * Date: 2018/4/4 9:33
     * @param $contractId
     * @return mixed
     * @throws \Exception
     */
    public function downloadContract($contractId)
    {
        $path = "/storage/contract/download/";

        $url_params['contractId'] = $contractId;

        //rtick
        $rtick = time() . rand(1000, 9999);

        //sign
        $sign_data = $this->_genSignData($path, $url_params, $rtick, null);
        $sign = $this->getRsaSign($sign_data);

        $url = $this->_getRequestUrl($path, $url_params, $sign, $rtick);
        \Log::info('url: ' . print_r($url,true));

        //header data
        $header_data = array();

        //content
        $response = $this->execute('GET', $url, null, $header_data, true);

        return $response;
    }

    /**
     * @param $path：接口名
     * @param $url_params: get请求需要放进参数中的参数
     * @param $rtick：随机生成，标识当前请求
     * @param $post_md5：post请求时，body的md5值
     * @return string
     */
    public function _genSignData($path, $url_params, $rtick, $post_md5)
    {
        $request_path = parse_url($this->_host . $path)['path'];

        $url_params['developerId'] = $this -> _developerId;
        $url_params['rtick'] = $rtick;
        $url_params['signType'] = 'rsa';

        ksort($url_params);

        $sign_data = '';
        foreach ($url_params as $key => $value)
        {
            $sign_data = $sign_data . $key . '=' . $value;
        }
        $sign_data = $sign_data . $request_path;

        if (null != $post_md5)
        {
            $sign_data = $sign_data . $post_md5;
        }
        return $sign_data;
    }

    private function _getRequestUrl($path, $url_params, $sign, $rtick)
    {
        $url = $this->_host .$path . '?';

        //url
        $url_params['sign'] = $sign;
        $url_params['developerId'] = $this -> _developerId;
        $url_params['rtick'] = $rtick;
        $url_params['signType'] = 'rsa';

        foreach ($url_params as $key => $value)
        {
            $value = urlencode($value);
            $url = $url . $key . '=' . $value . '&';
        }

        $url = substr($url, 0, -1);
        return $url;
    }

    private function _formatPem($rsa_pem, $pem_type = '')
    {
        //如果是文件, 返回内容
        if (is_file($rsa_pem))
        {
            return file_get_contents($rsa_pem);
        }

        //如果是完整的证书文件内容, 直接返回
        $rsa_pem = trim($rsa_pem);
        $lines = explode("\n", $rsa_pem);
        if (count($lines) > 1)
        {
            return $rsa_pem;
        }

        //只有证书内容, 需要格式化成证书格式
        $pem = '';
        for ($i = 0; $i < strlen($rsa_pem); $i++)
        {
            $ch = substr($rsa_pem, $i, 1);
            $pem .= $ch;
            if (($i + 1) % 64 == 0)
            {
                $pem .= "\n";
            }
        }
        $pem = trim($pem);
        if (0 == strcasecmp('RSA', $pem_type))
        {
            $pem = "-----BEGIN RSA PRIVATE KEY-----\n{$pem}\n-----END RSA PRIVATE KEY-----\n";
        }
        else
        {
            $pem = "-----BEGIN PRIVATE KEY-----\n{$pem}\n-----END PRIVATE KEY-----\n";
        }
        return $pem;
    }

    /**
     * 获取签名串
     * @param $args
     * @return
     */
    public function getRsaSign()
    {
        $pkeyid = openssl_pkey_get_private($this->_pem);
        if (!$pkeyid)
        {
            throw new \Exception("openssl_pkey_get_private wrong!", -1);
        }

        if (func_num_args() == 0) {
            throw new \Exception('no args');
        }
        $sign_data = func_get_args();
        $sign_data = trim(implode("\n", $sign_data));

        openssl_sign($sign_data, $sign, $this->_pem);
        openssl_free_key($pkeyid);
        return base64_encode($sign);
    }

    //执行请求
    public function execute($method, $url, $request_body = null, array $header_data = array(), $auto_redirect = true, $cookie_file = null)
    {
        $response = $this->request($method, $url, $request_body, $header_data, $auto_redirect, $cookie_file);

        $http_code = $response['http_code'];
        if ($http_code != 200)
        {
            throw new \Exception("Request err, code: " . $http_code . "\nmsg: " . $response['response'] );
        }

        return $response['response'];
    }

    public function request($method, $url, $post_data = null, array $header_data = array(), $auto_redirect = true, $cookie_file = null)
    {
        $headers = array();
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Connection: keep-alive';

        foreach ($header_data as $name => $value)
        {
            $line = $name . ': ' . rawurlencode($value);
            $headers[] = $line;
        }

        if (strcasecmp('POST', $method) == 0)
        {
            $ret = $this->_http_utils->post($url, $post_data, null, $headers, $auto_redirect, $cookie_file);
        }
        else
        {
            $ret = $this->_http_utils->get($url, $headers, $auto_redirect, $cookie_file);
        }
        return $ret;
    }
}