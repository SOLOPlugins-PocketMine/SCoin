<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use solo\scoin\SCoin;

class CoinPriceCommand extends CoinCommand{

  private $owner;

  public function __construct(SCoin $owner, string $type){
    parent::initCommand($type);
    parent::__construct($this->type . "시세", $this->name . "의 시세를 확인합니다.", "/" . $this->type . "시세", [str_replace(' ', '', $this->name) . "시세", strtolower($type) . "시세"]);
    $this->setPermission("scoin.command.coinprice");

    $this->owner = $owner;
  }

  public function execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SCoin::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    $coinInfo = $this->owner->getCoinInfo($this->type);

    if(!$coinInfo->isValid()){
      $sender->sendMessage(SCoin::$prefix . "현재 시세 로드중입니다. 잠시만 기다려주세요...");
      return true;
    }
    $sender->sendMessage("§l==========[ " . $this->name . " 현재 시세 ]==========");
    $sender->sendMessage("§l§a구매가§r§f : " . $this->owner->getEconomy()->koreanWonFormat($coinInfo->getBuyPrice()));
    $sender->sendMessage("§l§a판매가§r§f : " . $this->owner->getEconomy()->koreanWonFormat($coinInfo->getSellPrice()));
    $sender->sendMessage("§l§a24시간 최고가§r§f : " . $this->owner->getEconomy()->koreanWonFormat($coinInfo->getMaxPrice()));
    $sender->sendMessage("§l§a24시간 최저가§r§f : " . $this->owner->getEconomy()->koreanWonFormat($coinInfo->getMinPrice()));
    return true;
  }
}
