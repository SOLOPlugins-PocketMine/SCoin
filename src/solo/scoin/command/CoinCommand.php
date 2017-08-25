<?php

namespace solo\scoin\command;

use solo\scoin\SCoin;
use solo\scoin\SCoinCommand;

abstract class CoinCommand extends SCoinCommand{

  protected $type;
  protected $name;

  public function initCommand(string $type){
    $this->type = strtoupper($type);
    $this->name = SCoin::$nameList[$type] ?? $type;
  }
}
