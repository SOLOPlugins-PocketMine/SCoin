<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\scoin\SCoin;
use onebone\economyapi\EconomyAPI;

use solo\standardapi\message\Notify;
use solo\standardapi\message\Alert;
use solo\standardapi\message\Usage;

class SellCoinCommand extends Command{

  private $type;
  private $name;

  public function __construct(string $type){
    $this->type = strtoupper($type);
    $this->name = SCoin::$nameList[$type] ?? $type;
    parent::__construct(
      strtolower($type) . " 판매",
      $this->name . "을 판매합니다.",
      "/" . strtolower($type) . " 판매 [args]",
      [$this->name . " 판매"]
    );
  }

  public function generateCustomCommandData(Player $player){
    return [
      "overloads" => [
        "byMoney" => [
          "input" => [
            "parameters" => [
              [
                "name" => "금액",
                "type" => "string"
              ]
            ]
          ]
        ],
        "byPercentage" => [
          "input" => [
            "parameters" => [
              [
                "name" => "0~100",
                "type" => "string"
              ]
            ]
          ],
          "parser" => [
            "tokens" => "{0}%"
          ]
        ],
        "byCoin" => [
          "input" => [
            "parameters" => [
              [
                "name" => "수량",
                "type" => "string"
              ]
            ]
          ],
          "parser" => [
            "tokens" => "{0}" . strtolower($this->type)
          ]
        ]
      ]
    ];
  }

  public function execute(CommandSender $sender, $label, array $args){
    if(!$sender instanceof Player){
      $sender->sendMessage(new Alert("인게임에서만 사용가능합니다."));
      return true;
    }

    $coinInfo = SCoin::getInstance()->getCoinInfo($this->type);
    if(!$coinInfo->isValid()){
      $sender->sendMessage(new Notify("현재 시세 로드중입니다. 잠시만 기다려주세요..."));
      return true;
    }

    $money = EconomyAPI::getInstance()->myMoney($sender);
    $account = SCoin::getInstance()->getAccount($sender);
    $coin = $account->getCoin($this->type);

    $args[0] = strtoupper($args[0] ?? "invalid");
    if(is_numeric($args[0])){
      if($args[0] <= 0){
        $sender->sendMessage(new Alert("양수를 입력하세요."));
        return true;
      }
      $moneyAmount = $args[0];
      $args[0] = $args[0] / $coinInfo->getSellPrice();
      if($coin < $args[0]){
        $sender->sendMessage(new Alert("코인이 부족합니다. 현재 소지한 코인 : " . $coin . $this->type));
        return true;
      }
      $account->reduceCoin($this->type, $args[0]);
      EconomyAPI::getInstance()->addMoney($sender, $moneyAmount);

      $sender->sendMessage(new Notify($args[0] . $this->type . " 를 판매하셨습니다. ( " . $moneyAmount . "원 )"));
      return true;

    // sell as Current Coin Percentage
    }else if(strpos($args[0], "%") && is_numeric(str_replace("%", "", $args[0]))){
      $args[0] = str_replace("%", "", $args[0]);
      if($args[0] <= 0 || $args[0] > 100){
        $sender->sendMessage(new Alert("0 ~ 100 사이의 수를 입력하세요."));
        return true;
      }
      $args[0] = $coin * $args[0] / 100;
      $account->reduceCoin($this->type, $args[0]);
      $moneyAmount = $args[0] * $coinInfo->getSellPrice();
      EconomyAPI::getInstance()->addMoney($sender, $moneyAmount);

      $sender->sendMessage(new Notify($args[0] . $this->type . " 를 판매하셨습니다. ( " . $moneyAmount . "원 )"));
      return true;

    // sell as COIN
    }else if(strpos($args[0], $this->type) && is_numeric(str_replace($this->type, "", $args[0]))){
      $args[0] = str_replace($this->type, "", $args[0]);
      if($args[0] <= 0){
        $sender->sendMessage(new Alert("양수를 입력하세요."));
        return true;
      }
      if($coin < $args[0]){
        $sender->sendMessage(new Alert("코인이 부족합니다. 현재 소지한 코인 : " . $coin . $this->type));
        return true;
      }
      $account->reduceCoin($this->type, $args[0]);
      $moneyAmount = $args[0] * $coinInfo->getSellPrice();
      EconomyAPI::getInstance()->addMoney($sender, $moneyAmount);

      $sender->sendMessage(new Notify($args[0] . $this->type . " 를 판매하셨습니다. ( " . $moneyAmount . "원 )"));
      return true;
    }

    $sender->sendMessage(new Usage("/" . $this->type . " 판매 [금액] - 금액만큼의 " . $this->name . "을 구매합니다."));
    $sender->sendMessage(new Usage("/" . $this->type . " 판매 [0~100]% - 가지고 있는 돈의 %만큼 " . $this->name . "을 구매합니다."));
    $sender->sendMessage(new Usage("/" . $this->type . " 판매 [수량]" . $this->type . " - 수량만큼 " . $this->name . "을 구매합니다."));
    return true;
  }
}
