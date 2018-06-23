<?php

namespace solo\scoin\command;

use pocketmine\command\Command;
use solo\scoin\SCoin;

abstract class CoinCommand extends Command{

  protected $type;
  protected $name;

  public function initCommand(string $type){
    $this->type = strtoupper($type);
    $this->name = SCoin::$nameList[$type] ?? $type;
  }
}
