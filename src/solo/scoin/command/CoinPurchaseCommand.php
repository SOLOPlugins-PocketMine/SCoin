<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use solo\scoin\SCoin;
use onebone\economyapi\EconomyAPI;

class CoinPurchaseCommand extends CoinCommand{

  private $owner;

  public function __construct(SCoin $owner, string $type){
    parent::initCommand($type);
    parent::__construct($this->type . "구매", $this->name . "을(를) 구매합니다.", "/" . $this->type . "구매 [args]", [str_replace(' ', '', $this->name) . "구매", strtolower($type) . "구매"]);
    $this->setPermission("scoin.command.coinpurchase");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender instanceof Player){
      $sender->sendMessage(SCoin::$prefix . "인게임에서만 사용하실 수 있습니다.");
      return true;
    }
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SCoin::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }

    $coinInfo = $this->owner->getCoinInfo($this->type);

    if(!$coinInfo->isValid()){
      $sender->sendMessage(SCoin::$prefix . "현재 시세 로드중입니다. 잠시만 기다려주세요...");
      return true;
    }
    $account = $this->owner->getAccountManager()->getAccount($sender);
    $money = EconomyAPI::getInstance()->myMoney($sender);
    $args[0] = strtoupper($args[0] ?? "invalid");

    // buy as money
    if(preg_match('/^([0-9]+(\.[0-9]+)?)$/', $args[0])){
      if($money < $args[0]){
        $sender->sendMessage(SCoin::$prefix . "돈이 부족합니다. 현재 소지한 돈 : " . $money);
        return true;
      }
      EconomyAPI::getInstance()->reduceMoney($sender, $args[0]);
      $coinAmount = $args[0] / $coinInfo->getBuyPrice();
      $account->addCoin($this->type, $coinAmount);

      $sender->sendMessage(SCoin::$prefix . $coinAmount . $this->type . " 를 구매하셨습니다. ( " . $args[0] . "원 )");

    // buy as Current Money Percentage
    }else if(preg_match('/^(((100)|[0-9]{1,2})(\.[0-9]+)?%)$/', $args[0])){
      $args[0] = str_replace('%', '', $args[0]);
      $args[0] = $money * $args[0] / 100;
      EconomyAPI::getInstance()->reduceMoney($sender, $args[0]);
      $coinAmount = $args[0] / $coinInfo->getBuyPrice();
      $account->addCoin($this->type, $coinAmount);

      $sender->sendMessage(SCoin::$prefix . $coinAmount . $this->type . " 를 구매하셨습니다. ( " . $args[0] . "원 )");

    // buy as COIN
    }else if(preg_match('/^([0-9]+(\.[0-9]+)?' . $this->type . ')$/', $args[0])){
      $coinAmount = str_replace($this->type, '', $args[0]);
      $args[0] = $coinAmount * $coinInfo->getBuyPrice();
      if($money < $args[0]){
        $sender->sendMessage(SCoin::$prefix . "돈이 부족합니다. 현재 소지한 돈 : " . $money);
        return true;
      }
      EconomyAPI::getInstance()->reduceMoney($sender, $args[0]);
      $account->addCoin($this->type, $coinAmount);

      $sender->sendMessage(SCoin::$prefix . $coinAmount . $this->type . " 를 구매하셨습니다. ( " . $args[0] . "원 )");
    }else{
      $sender->sendMessage(SCoin::$prefix . "/" . $this->type . " 구매 <금액> - 금액만큼의 " . $this->name . "을 구매합니다.");
      $sender->sendMessage(SCoin::$prefix . "/" . $this->type . " 구매 <0~100>% - 가지고 있는 돈의 %만큼 " . $this->name . "을 구매합니다.");
      $sender->sendMessage(SCoin::$prefix . "/" . $this->type . " 구매 <수량>" . $this->type . " - 수량만큼 " . $this->name . "을 구매합니다.");
    }
    return true;
  }
}
