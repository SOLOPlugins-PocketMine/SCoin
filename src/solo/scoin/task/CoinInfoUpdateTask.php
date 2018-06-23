<?php

namespace solo\scoin\task;

use pocketmine\scheduler\Task;
use solo\scoin\SCoin;

class CoinInfoUpdateTask extends Task{

  private $owner;

  public function __construct(SCoin $owner){
    $this->owner = $owner;
  }

  public function onRun(int $currentTick){
    foreach($this->owner->getAllCoinInfo() as $coinInfo){
      $coinInfo->update();
    }
  }
}
