<?php

namespace solo\scoin\task;

use solo\scoin\SCoinTask;

class CoinInfoUpdateTask extends SCoinTask{

  public function _onRun(int $currentTick){
    foreach($this->owner->getAllCoinInfo() as $coinInfo){
      $coinInfo->update();
    }
  }
}
