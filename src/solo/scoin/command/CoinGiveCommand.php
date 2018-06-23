<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use solo\scoin\SCoin;

class CoinGiveCommand extends CoinCommand{

  private $owner;

  public function __construct(SCoin $owner, string $type){
    parent::initCommand($type);
    parent::__construct($this->type . "주기", "플레이어의 " . $this->name . "을(를) 수량만큼 줍니다.", "/" . $this->type . "주기 <플레이어> <수량>", [str_replace(' ', '', $this->name) . "주기", strtolower($type) . "주기"]);
    $this->setPermission("scoin.command.coingive");

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
    if(count($args) < 2 || !is_numeric($args[1]) || $args[1] < 0){
      $sender->sendMessage(SCoin::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $target = $args[0];
    if(($player = $this->owner->getServer()->getPlayer($args[0])) instanceof Player){
      $target = $player->getName();
    }
    $account = $this->owner->getAccountManager()->getAccount($target);
    if($account === null){
      $sender->sendMessage(SCoin::$prefix . $target . " 님은 서버에 접속하신 적이 없습니다.");
      return true;
    }

    $coin = $account->addCoin($this->type, $args[1]);
    $sender->sendMessage(SCoin::$prefix . $target . " 플레이어의 " . $this->name . "을 " . $args[1] . $this->type . "만큼 주었습니다.");
    return true;
  }
}
