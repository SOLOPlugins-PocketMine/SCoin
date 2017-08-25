<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use solo\scoin\SCoin;
use onebone\economyapi\EconomyAPI;

class CoinSellCommand extends CoinCommand{

  private $owner;

  public function __construct(SCoin $owner, string $type){
    parent::initCommand($type);
    parent::__construct($this->type . "구매", $this->name . "을(를) 구매합니다.", "/" . $this->type . "구매 [args]", [str_replace(' ', '', $this->name) . "구매", strtolower($type) . "구매"]);
    $this->setPermission("scoin.command.coinsell");

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

    if(preg_match('/^([0-9]+(\.[0-9]+)?)$/', $args[0])){
      $moneyAmount = $args[0];
      $args[0] = $args[0] / $coinInfo->getSellPrice();
      if(($coin = $account->getCoin($this->type)) < $args[0]){
        $sender->sendMessage(SCoin::$prefix . "코인이 부족합니다. 현재 소지한 코인 : " . $coin . $this->type);
        return true;
      }
      $account->reduceCoin($this->type, $args[0]);
      EconomyAPI::getInstance()->addMoney($sender, $moneyAmount);

      $sender->sendMessage(SCoin::$prefix . $args[0] . $this->type . " 를 판매하셨습니다. ( " . $moneyAmount . "원 )");

    // sell as Current Coin Percentage
    }else if(preg_match('/^(((100)|[0-9]{1,2})(\.[0-9]+)?%)$/', $args[0])){
      $args[0] = str_replace('%', '', $args[0]);
      $args[0] = $account->getCoin($this->type) * $args[0] / 100;
      $account->reduceCoin($this->type, $args[0]);
      $moneyAmount = $args[0] * $coinInfo->getSellPrice();
      EconomyAPI::getInstance()->addMoney($sender, $moneyAmount);

      $sender->sendMessage(SCoin::$prefix . $args[0] . $this->type . " 를 판매하셨습니다. ( " . $moneyAmount . "원 )");

    // sell as COIN
    }else if(preg_match('/^([0-9]+(\.[0-9]+)?' . $this->type . ')$/', $args[0])){
      $args[0] = str_replace($this->type, '', $args[0]);
      if(($coin = $account->getCoin($this->type)) < $args[0]){
        $sender->sendMessage(SCoin::$prefix . "코인이 부족합니다. 현재 소지한 코인 : " . $coin . $this->type);
        return true;
      }
      $account->reduceCoin($this->type, $args[0]);
      $moneyAmount = $args[0] * $coinInfo->getSellPrice();
      EconomyAPI::getInstance()->addMoney($sender, $moneyAmount);

      $sender->sendMessage(SCoin::$prefix . $args[0] . $this->type . " 를 판매하셨습니다. ( " . $moneyAmount . "원 )");

    }else{
      $sender->sendMessage(SCoin::$prefix . "/" . $this->type . " 판매 <금액> - 금액만큼의 " . $this->name . "을 구매합니다.");
      $sender->sendMessage(SCoin::$prefix . "/" . $this->type . " 판매 <0~100>% - 가지고 있는 돈의 %만큼 " . $this->name . "을 구매합니다.");
      $sender->sendMessage(SCoin::$prefix . "/" . $this->type . " 판매 <수량>" . $this->type . " - 수량만큼 " . $this->name . "을 구매합니다.");
    }
    return true;
  }
}
