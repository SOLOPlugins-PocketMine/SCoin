<?php

namespace solo\scoin\coininfo;

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;

class CoinoneCoinInfo extends CoinInfo{

  public function update(){
    Server::getInstance()->getAsyncPool()->submitTask(new class($this->type) extends AsyncTask{
      private $type;

      public function __construct(string $type){
        $this->type = $type;
      }

      public function onRun(){
        $response = Internet::getURL("https://api.coinone.co.kr/ticker?currency=" . $this->type);

        if($response !== false){
          $response = json_decode($response, true);
          if(is_array($response)){
            $this->setResult($response);
          }
        }
      }

      public function onCompletion(Server $server){
        $response = $this->getResult();
        if($response !== null && $response["result"] === "success"){
          $coinInfo = $server->getPluginManager()->getPlugin("SCoin")->getCoinInfo($this->type);
          $coinInfo->buyPrice = intval($response["last"]);
          $coinInfo->sellPrice = intval($response["last"]);
          $coinInfo->openingPrice = intval($response["first"]);
          $coinInfo->closingPrice = intval($response["last"]);
          $coinInfo->minPrice = intval($response["low"]);
          $coinInfo->maxPrice = intval($response["high"]);
        }
      }
    });
  }
}
