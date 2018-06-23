<?php

namespace solo\scoin\coininfo;

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Utils;

class BithumbCoinInfo extends CoinInfo{

  public function update(){
    Server::getInstance()->getAsyncPool()->submitTask(new class($this->type) extends AsyncTask{
      private $type;

      public function __construct(string $type){
        $this->type = $type;
      }

      public function onRun(){
        $response = Utils::getURL("https://api.bithumb.com/public/ticker/" . $this->type);

        if($response !== false){
          $response = json_decode($response, true);
          if(is_array($response)){
            $this->setResult($response);
          }
        }
      }

      public function onCompletion(Server $server){
        $response = $this->getResult();
        if($response !== null && $response["status"] === "0000"){
          $coinInfo = $server->getPluginManager()->getPlugin("SCoin")->getCoinInfo($this->type);
          $coinInfo->buyPrice = intval($response["data"]["buy_price"]);
          $coinInfo->sellPrice = intval($response["data"]["sell_price"]);
          $coinInfo->openingPrice = intval($response["data"]["opening_price"]);
          $coinInfo->closingPrice = intval($response["data"]["closing_price"]);
          $coinInfo->minPrice = intval($response["data"]["min_price"]);
          $coinInfo->maxPrice = intval($response["data"]["max_price"]);
        }
      }
    });
  }
}
