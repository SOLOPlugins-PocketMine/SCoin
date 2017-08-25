<?php

namespace solo\scoin;

use pocketmine\utils\Config;

class Account extends Config{

  public function __construct(string $name, string $filePath){
    parent::__construct($filePath, Config::YAML);
    $this->name = $name;
  }

  public function getName(){
    return $this->name;
  }

  public function getCoin(string $type){
    $type = strtoupper($type);
    if(!isset($this->{$type})){
      $this->{$type} = 0;
    }
    return $this->{$type};
  }

  public function addCoin(string $type, $amount){
    $type = strtoupper($type);
    if(!isset($this->{$type})){
      $this->{$type} = 0;
    }
    $this->{$type} += $amount;
  }

  public function setCoin(string $type, $amount){
    $type = strtoupper($type);
    $this->{$type} = $amount;
  }

  public function reduceCoin(string $type, $amount){
    $type = strtoupper($type);
    $this->{$type} -= $amount;
  }
}
