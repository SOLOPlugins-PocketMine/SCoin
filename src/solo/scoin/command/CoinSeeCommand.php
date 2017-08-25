<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use solo\scoin\SCoin;

class CoinSeeCommand extends CoinCommand{

  private $owner;

  public function __construct(SCoin $owner, string $type){
    parent::initCommand($type);
    parent::__construct($this->type . "확인", "자신 또는 다른 플레이어의 " . $this->name . " 수량을 확인합니다.", "/" . $this->type . "확인 <플레이어>", [str_replace(' ', '', $this->name) . "확인", strtolower($type) . "확인"]);
    $this->setPermission("scoin.command.coinsee");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SCoin::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    $coinInfo = $this->owner->getCoinInfo($this->type);

    if(!$coinInfo->isValid()){
      $sender->sendMessage(SCoin::$prefix . "현재 시세 로드중입니다. 잠시만 기다려주세요...");
      return true;
    }
    $target = null;
    if(empty($args) && $sender instanceof Player){
      $target = $sender->getName();
    }else if(!empty($args) && ($player = $this->owner->getServer()->getPlayer($args[0])) instanceof Player){
      $target = $player->getName();
    }else if(!empty($args)){
      $target = $args[0];
    }
    if($target === null){
      $sender->sendMessage(SCoin::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $account = $this->owner->getAccountManager()->getAccount($target);
    if($account === null){
      $sender->sendMessage(SCoin::$prefix . $target . " 님은 서버에 접속하신 적이 없습니다.");
      return true;
    }

    $coin = $account->getCoin($this->type);
    $sender->sendMessage(SCoin::$prefix . $target . " 님의 " . $this->name . " 수량 : " . $coin . $this->type . " ( " . $coin * $coinInfo->getSellPrice() . " )");
    return true;
  }
}
