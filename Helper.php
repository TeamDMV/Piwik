<?php


namespace Piwik\Plugins\PiwikNotifiy;
use Psr\Log\LoggerInterface;
use Piwik\Container\StaticContainer;

class Helper{

    protected $logger;
    public function __construct(LoggerInterface $logger = null){
        $this->logger = $logger ?: StaticContainer::get('Psr\Log\LoggerInterface');
    }

    public function infoLog($msg){
      if($this->logger){
        $this->logger->info($msg);
      }
    }
}
