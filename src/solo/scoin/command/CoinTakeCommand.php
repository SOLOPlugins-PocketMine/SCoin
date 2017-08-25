<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use solo\scoin\SCoin;

class CoinTakeCommand extends CoinCommand{

  private $owner;

  public function __construct(SCoin $owner, string $type){
    parent::initCommand($type);
    parent::__construct($this->type . "뺏기", "플레이어의 " . $this->name . "을(를) 수량만큼 뺏습니다.", "/" . $this->type . "뺏기 <플레이어> <수량>", [str_replace(' ', '', $this->name) . "뺏기", strtolower($type) . "뺏기"]);
    $this->setPermission("scoin.command.cointake");

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
    if(($current = $account->getCoin($this->type)) < $args[1]){
      $sender->sendMessage(SCoin::$prefix . "소지한 코인보다 더 많은 양을 빼앗을 수 없습니다. " . $current . " 이하로 입력해주세요.");
      return true;
    }

    $coin = $account->reduceCoin($this->type, $args[1]);
    $sender->sendMessage(SCoin::$prefix . $target . " 플레이어의 " . $this->name . "을 " . $args[1] . $this->type . "만큼 빼앗았습니다.");
    return true;
  }
}
