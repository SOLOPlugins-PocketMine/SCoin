<?php

namespace solo\scoin\task;

use solo\scoin\SCoin;
use solo\scoin\SCoinTask;

class CoinPriceBroadcastTask extends SCoinTask{

  private $lastCheck = [];

  public function _onRun(int $currentTick){
    $message = "§a[코인시세] ";
    $isEmpty = true;
    foreach($this->owner->getAllCoinInfo() as $coinInfo){
      if($coinInfo->isValid()){
        $isEmpty = false;

        $price = round(($coinInfo->getSellPrice() + $coinInfo->getBuyPrice()) / 2);
        $message .= "§f" . (SCoin::$nameList[$coinInfo->getType()] ?? $coinInfo->getType()) . ": " . $price;
        if(!isset($this->lastCheck[$coinInfo->getType()])){
          $this->lastCheck[$coinInfo->getType()] = $price;
        }
        $changed = $price - $this->lastCheck[$coinInfo->getType()];

        if($changed > 0){
          $message .= " (§c▲" . $changed . "§f)";
        }else if($changed < 0){
          $message .= " (§1▼" . abs($changed) . "§f)";
        }

        $this->lastCheck[$coinInfo->getType()] = $price;

        $message .= "   ";
      }
    }
    if(!$isEmpty){
      $this->owner->getServer()->broadcastMessage($message);
    }
  }
}
