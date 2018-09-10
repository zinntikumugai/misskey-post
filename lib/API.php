<?php
/**
 * 投稿周り(しか無い)のAPI
 */
class MisskeyAPI {
    private $host = null;
    private $i = null;

    public function __construct($host, $i) {
        $this->host = $host;
        $this->i = $i;
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

    public function CreateNote($parm = []) {
        $endpoint = $this->host.'/api/notes/create';
        $parm['i'] = $this->i;
        $res = $this->post($endpoint, $parm);
        return $res;
    }
}