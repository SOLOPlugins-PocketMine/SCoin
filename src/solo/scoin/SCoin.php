<?php

namespace solo\scoin;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Config;

use solo\scoin\coininfo\BithumbCoinInfo;
use solo\scoin\coininfo\CoinoneCoinInfo;
use solo\scoin\task\CoinInfoUpdateTask;
use solo\scoin\task\CoinPriceBroadcastTask;

class SCoin extends PluginBase{

  public static $prefix = "§b§l[SCoin] §r§7";

  public static $nameList = [
    "BTC" => "비트코인",
    "ETH" => "이더리움",
    "LTC" => "라이트코인",
    "ETC" => "이더리움 클래식",
    "XRP" => "리플",
    "BCH" => "비트코인 캐시"
  ];

  private static $instance = null;

  public static function getInstance(){
    return self::$instance;
  }



  private $config;

  private $economy;

  private $accountManager = null;

  private $coins = [];

  private $availableCoins = [];

  public function onLoad(){
    if(self::$instance !== null){
      throw new \InvalidStateException();
    }
    self::$instance = $this;
  }

  public function onEnable(){
    $this->economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    if($this->economy === null){
      $this->getServer()->getLogger()->critical("[SCoin] EconomyAPI 플러그인을 찾을 수 없습니다. 플러그인을 비활성화합니다.");
      return;
    }

    @mkdir($this->getDataFolder());
    $this->saveResource("setting.yml");
    $this->config = new Config($this->getDataFolder() . "setting.yml", Config::YAML);
    $this->availableCoins = array_map('strtoupper', $this->config->get("available-coins", []));

    foreach($this->getAvailableCoins() as $type){
      $type = strtoupper($type);
      switch(strtolower($this->config->get("server", "coinone"))){
        case "bithumb":
          $this->coins[$type] = new BithumbCoinInfo($type);
          break;

        case "coinone":
        default:
          $this->coins[$type] = new CoinoneCoinInfo($type);
      }
      foreach([
        "CoinGiveCommand", "CoinPriceCommand", "CoinPurchaseCommand",
        "CoinSeeCommand", "CoinSellCommand", "CoinSetCommand",
        "CoinTakeCommand"
      ] as $class){
        $class = "\\solo\\scoin\\command\\" . $class;
        $this->getServer()->getCommandMap()->register("scoin", new $class($this, $type));
      }
    }
    $this->getServer()->getCommandMap()->register("scoin", new \solo\scoin\command\AllStatusCommand($this));

    $this->accountManager = new AccountManager($this);

    $this->getScheduler()->scheduleRepeatingTask(new CoinPriceBroadcastTask($this), $this->config->get("price-broadcast-interval", 60) * 20);
    $this->getScheduler()->scheduleRepeatingTask(new CoinInfoUpdateTask($this), 50);
  }

  public function onDisable(){
    if($this->accountManager !== null){
      $this->accountManager->save();
    }

    self::$instance = null;
  }

  public function getEconomy(){
    return $this->economy;
  }

  public function getAccountManager(){
    return $this->accountManager;
  }

  public function getAvailableCoins(){
    return $this->availableCoins;
  }

  public function getAllCoinInfo(){
    return $this->coins;
  }

  public function getCoinInfo(string $type){
    return $this->coins[strtoupper($type)];
  }
}
