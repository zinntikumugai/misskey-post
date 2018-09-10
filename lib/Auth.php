<?php
/**
 * 認証周りのAPI
 */
Class MisskeyAuth{
    private $host = null;
    private $appToken = null;
    private $appSecret = null;
    private $accessToken = null;

    public function __construct($host, $appSecret) {
        $this->host = $host;
        $this->appSecret = $appSecret;
    }

    public function setAppToken($token) {
        $this->appToken = $token;
    }

    private function post($url, $data) {
        $header = [
            'Content-Type: application/json'
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL =>  $url,
            CURLOPT_CUSTOMREQUEST   =>  "POST",
            CURLOPT_HTTPHEADER      =>  $header,
            CURLOPT_POSTFIELDS      =>  json_encode($data),
            CURLOPT_SSL_VERIFYPEER  =>  false,
            CURLOPT_FOLLOWLOCATION  =>  true,
            CURLOPT_AUTOREFERER     =>  true,
            CURLOPT_RETURNTRANSFER  =>  true
        ]);
        $res = curl_exec($curl);
        $res = json_decode($res);
        curl_close($curl);
        return $res;
    }

    //めんどくせぇ全部保存しちまえ
    public function saveSession() {
        $_SESSION['host'] = $this->host;
        $_SESSION['appToken'] = $this->appToken;
        $_SESSION['appSecret'] = $this->appSecret;
        $_SESSION['accessToken'] = $this->accessToken;
    }

    //全部読み込み
    public function loadSession() {
        $this->host = $_SESSION['host'];
        $this->appToken = $_SESSION['appToken'];
        $this->appSecret = $_SESSION['appSecret'];
        $this->accessToken = $_SESSION['accessToken'];
    }

    //やっぱ掃除しないとね☆
    public function dropSession() {
        unset($_SESSION['host']);
        unset($_SESSION['appToken']);
        unset($_SESSION['appSecret']);
        unset($_SESSION['accessToken']);
    }

    //認証
    public function auth() {
        $data = [
            'appSecret' =>  $this->appSecret
        ];
        $res = $this->post($this->host.'/api/auth/session/generate', $data);
        if(isset($res->token))
            $this->appToken = $res->token;
        return $res->url;
    }

    //手動でアクセストークン取得
    public function getAccessToken() {
        $data = [
            'appSecret' =>  $this->appSecret,
            'token'     =>  $this->appToken
        ];
        var_dump($data);
        $res = $this->post($this->host.'/api/auth/session/userkey', $data);
        if(isset($res->accessToken))
            $this->accessToken = $res->accessToken;
        return $res;
    }

    public function getI() {
        if(!isset($this->accessToken) || !isset($this->appSecret))
            return null;
        return hash('sha256', $this->accessToken.$this->appSecret);
    }
}