<?php

namespace solo\scoin\coininfo;

use solo\scoin\SCoin;

abstract class CoinInfo{

  const STATUS_AVAILABLE = 0;
  const STATUS_UNAVAILABLE = 1;

  protected $type;
  public $buyPrice;
  public $sellPrice;
  public $openingPrice;
  public $closingPrice;
  public $minPrice;
  public $maxPrice;

  public function __construct(string $type){
    $this->type = strtoupper($type);
  }

  public function getType(){
    return $this->type;
  }

  abstract public function update();

  public function isValid(){
    return $this->buyPrice !== null && $this->sellPrice !== null;
  }

  public function getBuyPrice(){
    return $this->buyPrice;
  }

  public function getSellPrice(){
    return $this->sellPrice;
  }

  public function getOpeningPrice(){
    return $this->openingPrice;
  }

  public function getClosingPrice(){
    return $this->closingPrice;
  }

  public function getMinPrice(){
    return $this->minPrice;
  }

  public function getMaxPrice(){
    return $this->maxPrice;
  }
}
