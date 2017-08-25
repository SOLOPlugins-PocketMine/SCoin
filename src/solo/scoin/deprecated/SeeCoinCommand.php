<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\scoin\SCoin;
use onebone\economyapi\EconomyAPI;

use solo\standardapi\message\Notify;
use solo\standardapi\message\Usage;

class SeeCoinCommand extends Command{

  private $type;
  private $name;

  public function __construct(string $type){
    $this->type = strtoupper($type);
    $this->name = SCoin::$nameList[$type] ?? $type;
    parent::__construct(
      strtolower($type) . " 확인",
      $this->name . "의 수량을 확인합니다.",
      "/" . strtolower($type) . " 확인 <플레이어>",
      [$this->name . " 확인"]
    );
  }

  public function generateCustomCommandData(Player $player){
    return [
      "overloads" => [
        "default" => [
          "input" => [
            "parameters" => [
              [
                "name" => "플레이어",
                "type" => "string",
                "optional" => true
              ]
            ]
          ]
        ]
      ]
    ];
  }

  public function execute(CommandSender $sender, $label, array $args){
    $coinInfo = SCoin::getInstance()->getCoinInfo($this->type);
    if(!$coinInfo->isValid()){
      $sender->sendMessage(new Notify("현재 시세 로드중입니다. 잠시만 기다려주세요..."));
      return true;
    }

    if(!isset($args[0])){
      if(!$sender instanceof Player){
        $sender->sendMessage(new Usage("/" . $this->type . " 확인 [플레이어] - 다른 플레이어의 " . $this->name . " 수량을 확인합니다."));
        return true;
      }
      $args[0] = $sender->getName();
    }else if(($player = Server::getInstance()->getPlayer($args[0])) instanceof Player){
      $args[0] = $player->getName();
    }

    $coin = SCoin::getInstance()->getAccount($args[0])->getCoin($this->type);

    $sender->sendMessage(new Notify($args[0] . " 님의 " . $this->name . " 수량 : " . $coin . $this->type . " ( " . $coin * $coinInfo->getSellPrice() . " )"));
    return true;
  }
}
