<?php

namespace solo\scoin\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use solo\scoin\SCoin;
use solo\scoin\SCoinCommand;

class AllStatusCommand extends SCoinCommand{

  private $owner;

  public function __construct(SCoin $owner){
    parent::__construct("코인", "코인 관련 명령어", "/코인 [args]", ["coin"]);
    $this->setPermission("scoin.command.allstatus");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SCoin::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    switch($args[0] ?? "default"){
      case "?":
      case "도움말":
        $page = 1;
        if(is_numeric($args[1] ?? "")){
          $page = max(1, min(4, intval($args[1])));
        }

        switch($page){
          case 1:
            $sender->sendMessage("§l==========[ 코인 도움말 (전체 4페이지 중 1페이지) ]==========");
            $sender->sendMessage("§l§a* 코인이 무엇인가요?");
            $sender->sendMessage(" ");
            $sender->sendMessage("코인은 비트코인, 이더리움 등 가상화폐를 의미합니다.");
            $sender->sendMessage("서버 내에서 거래되는 코인은 실제 가상화폐가 아닌 서버 내에서 독립적으로 저장되는 데이터입니다.");
            $sender->sendMessage("하지만 시세는 실제 거래소를 기준으로 변동되오니 참고바랍니다.");
            break;

          case 2:
            $availableCoins = [];
            foreach($this->owner->getAvailableCoins() as $type){
              $availableCoins[] = (isset(SCoin::$nameList[$type])) ? SCoin::$nameList[$type] . "(" . $type . ")" : $type;
            }
            $sender->sendMessage("§l==========[ 코인 도움말 (전체 4페이지 중 2페이지) ]==========");
            $sender->sendMessage("§l§a* 서버 내에서 거래 가능한 코인에는 어떤것이 있나요?");
            $sender->sendMessage(" ");
            $sender->sendMessage(
              (count($availableCoins) === 0) ?
              "현재 서버 내에서 코인 거래는 불가능합니다. 관리자에게 문의해주세요."
              : "서버 내에서 거래 가능한 코인은 " . implode(", ", $availableCoins) . " 이 있습니다."
            );
            break;

          case 3:
            $sender->sendMessage("§l==========[ 코인 도움말 (전체 4페이지 중 3페이지) ]==========");
            $sender->sendMessage("§l§a* 코인은 어떻게 구매 또는 판매할 수 있나요?");
            $sender->sendMessage(" ");
            $sender->sendMessage("코인은 /[코인명 또는 통화명] [구매/판매] 명령어로 구매 또는 판매가 가능합니다.");
            $sender->sendMessage(" ");
            foreach($this->owner->getAvailableCoins() as $type){
              $sender->sendMessage((isset(SCoin::$nameList[$type])) ? "/" . SCoin::$nameList[$type] . " [구매/판매] 또는 /" . $type . " [구매/판매]" : "/" . $type . " [구매/판매]");
            }
            break;

          default:
            $sender->sendMessage("§l==========[ 코인 도움말 (전체 4페이지 중 4페이지) ]==========");
            $sender->sendMessage("§l§a* 코인은 어떻게 사용할 수 있나요?");
            $sender->sendMessage(" ");
            $sender->sendMessage("코인은 상시 가치가 변동되는 투자 상품이므로, 시세가 변동되었을 때 구매 또는 판매하여 차익을 남길 수 있습니다.");
            $sender->sendMessage("수익을 얻는 원리는 주식과 유사합니다.");
        }
        return true;

      case "보기":
      case "확인":
        $target;
        if(!isset($args[1])){
          if(!$sender instanceof Player){
            $sender->sendMessage(SCoin::$prefix . "사용법 : /코인 확인 [플레이어] - 플레이어가 소지한 코인을 확인합니다.");
            return true;
          }
          $target = $sender->getName();
        }else{
          $target = $args[1];
        }
        $account = $this->owner->getAccountManager()->getAccount($target);

        if($account === null){
          $sender->sendMessage(SCoin::$prefix . $target . " 님은 서버에 접속하신 적이 없습니다.");
          return true;
        }
        $send = [];
        $total = 0;
        $sender->sendMessage("§l==========[ " . $target . " 님의 코인 정보 ]==========");
        foreach($this->owner->getAvailableCoins() as $type){
          $price = $this->owner->getCoinInfo($type)->getSellPrice() * $account->getCoin($type);
          $total += $price;
          $sender->sendMessage(SCoin::$nameList[$type] . "(" . $type . ") : " . $account->getCoin($type) . "  (환전시 : " . $price . ")");
        }
        $sender->sendMessage(" ");
        $sender->sendMessage("§a§l총 자산 : " . $total);
        return true;

      default:
        $sender->sendMessage("§l==========[ 코인 명령어 도움말 ]==========");
        $sender->sendMessage("§7/코인 도움말 <페이지> - 코인에 대한 도움말을 봅니다.");
        $sender->sendMessage("§7/코인 확인 <플레이어> - 플레이어가 소지한 코인을 확인합니다.");
        return true;
    }
  }
}
