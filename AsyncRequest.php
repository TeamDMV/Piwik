<?php 

namespace Piwik\Plugins\PiwikNotifiy;

class AsyncRequest{// extends Thread{
  
  private $url;
  private $queryparams;
  private $postdata;
  private $method;

  public function __construct($url, $queryparams, $postdata, $method)
  {
      $this->url = $url;
      $this->queryparams = $queryparams;

      $this->url = $this->url . "?" . http_build_query($this->queryparams);

      $this->postdata = $postdata;
      $this->method = $method;
  }

  public function run() {
    if($this->method == 'post'){
      $this->post();
    }else{
      $this->get();
    }
  }

  public function start() {
    if($this->method == 'post'){
      $this->post();
    }else{
      $this->get();
    }
  }
  
  public function post(){
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    curl_close($ch);
  }

  public function get(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    curl_close($ch);
  }

}

