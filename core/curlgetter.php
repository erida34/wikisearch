<?php

class CurlGetter
{
    function __construct()
    {
    }
    public function close()
    {
        curl_close($this->myCurl);
    }
    public function query($src)
    {
        $this->myCurl = curl_init();
        curl_setopt_array($this->myCurl, array(
            CURLOPT_URL => $src,
            CURLOPT_RETURNTRANSFER => true
        ));
        $response = curl_exec($this->myCurl);
        $this->close();
        return json_decode($response, true);
    }
}
