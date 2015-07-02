<?php

/**
 * http��httpsͨ����
 * ============================================================================
 * api˵����
 * setReqContent($reqContent),�����������ݣ�����post��get������get��ʽ�ṩ
 * getResContent(), ��ȡӦ������
 * setMethod($method),�������󷽷�,post����get
 * getErrInfo(),��ȡ������Ϣ
 * setCertInfo($certFile, $certPasswd, $certType="PEM"),����֤�飬˫��httpsʱ��Ҫʹ��
 * setCaInfo($caFile), ����CA����ʽδpem���������򲻼��
 * setTimeOut($timeOut)�� ���ó�ʱʱ�䣬��λ��
 * getResponseCode(), ȡ���ص�http״̬��
 * call(),�������ýӿ�
 * 
 * ============================================================================
 *
 */

class TenpayHttpClient {
	//��������
	var $reqContent;
	//��������
	var $reqBody;
	//Ӧ������
	var $resContent;
	//���󷽷�
	var $method;
	
	//֤���ļ�
	var $certFile;
	//֤������
	var $certPasswd;
	//֤������PEM
	var	$certType;
	
	//CA�ļ�
	var $caFile;
	
	//������Ϣ
	var $errInfo;
	
	//��ʱʱ��
	var $timeOut;
	
	//http״̬��
	var $responseCode;
	
	function __construct() {
		$this->TenpayHttpClient();
	}
	
	
	function TenpayHttpClient() {
		$this->reqContent = "";
		$this->resContent = "";
		$this->method = "post";

		$this->certFile = "";
		$this->certPasswd = "";
		$this->certType = "PEM";
		
		$this->caFile = "";
		
		$this->errInfo = "";
		
		$this->timeOut = 120;
		
		$this->responseCode = 0;
		
	}
	
	
	//������������
	function setReqContent($reqContent) {
		$this->reqContent = $reqContent;
	}
	//������������
	function setReqBody($body) {
		$this->reqBody = $body;
	}
	//��ȡ�������
	function getResContent() {
		return $this->resContent;
	}
	
	//�������󷽷�post����get	
	function setMethod($method) {
		$this->method = $method;
	}
	
	//��ȡ������Ϣ
	function getErrInfo() {
		return $this->errInfo;
	}
	
	//����֤����Ϣ
	function setCertInfo($certFile, $certPasswd, $certType="PEM") {
		$this->certFile = $certFile;
		$this->certPasswd = $certPasswd;
		$this->certType = $certType;
	}
	
	//����Ca
	function setCaInfo($caFile) {
		$this->caFile = $caFile;
	}
	
	//���ó�ʱʱ��,��λ��
	function setTimeOut($timeOut) {
		$this->timeOut = $timeOut;
	}

	
	//ִ��http����
	function call() {
		//����һ��CURL�Ự
		$ch = curl_init();


		// ����curl����ִ�е������
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
		

		// ��ȡ����Ϣ���ļ�������ʽ���أ�������ֱ�������
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

		// ��֤���м��SSL�����㷨�Ƿ����
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		
		//$arr = explode("?", $this->reqContent);
		// ���ô�������ʽΪ��ip��ַ:�˿ںš�
		//curl_setopt($ch, CURLOPT_PROXY, "10.241.32.57:3128");
		if(strtolower($this->method) == "post") {
			//����һ�������POST����
			curl_setopt($ch, CURLOPT_URL, $this->reqContent);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->reqBody);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			//Ҫ���͵���������
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->reqBody);

		}else{
		
			curl_setopt($ch, CURLOPT_URL, $this->reqContent);
		}
		
		
		//����֤����Ϣ
		if($this->certFile != "") {
			curl_setopt($ch, CURLOPT_SSLCERT, $this->certFile);
			curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certPasswd);
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, $this->certType);
		}
		
		//����CA
		if($this->caFile != "") {
			// ����֤֤����Դ�ļ�飬0��ʾ��ֹ��֤��ĺϷ��Եļ�顣1��Ҫ����CURLOPT_CAINFO
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_CAINFO, $this->caFile);
		
		} else {
			// ����֤֤����Դ�ļ�飬0��ʾ��ֹ��֤��ĺϷ��Եļ�顣1��Ҫ����CURLOPT_CAINFO
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			
		}
		
		// ִ�в���
		$res = curl_exec($ch);

		$this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($res == NULL) { 
		   $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
		   curl_close($ch);
		 
		   return false;

		} else if($this->responseCode != "200") {
			$this->errInfo = "call http err httpcode=" . $this->responseCode;
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		$this->resContent = $res;

		
		return true;
	}
	
	function getResponseCode() {
		return $this->responseCode;
	}
	
}
?>