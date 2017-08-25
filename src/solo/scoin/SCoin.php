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

  private $coins = [];

  public function onLoad(){
    if(self::$instance !== null){
      throw new \InvalidStateException();
    }
    self::$instance = $this;
  }

  public function onEnable(){
    @mkdir($this->getDataFolder());
    $this->saveResource($this->getDataFolder() . "setting.yml");
    $this->config = new Config($this->getDataFolder() . "setting.yml", Config::YAML);

    foreach($this->getAvailableCoins() as $type){
      $type = strtoupper($type);
      switch(strtolower($this->config->get("server"))){
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

    $this->getServer()->getScheduler()->scheduleRepeatingTask(new CoinPriceBroadcastTask($this), $this->config->get("price-broadcast-interval") * 20);
    $this->getServer()->getScheduler()->scheduleRepeatingTask(new CoinInfoUpdateTask($this), 50);
  }

  public function onDisable(){
    $this->accountManager->save();

    self::$instance = null;
  }

  public function getAccountManager(){
    return $this->accountManager;
  }

  public function getAvailableCoins(){
    return array_map('strtoupper', $this->config->get("available-coins"));
  }

  public function getAllCoinInfo(){
    return $this->coins;
  }

  public function getCoinInfo(string $type){
    return $this->coins[strtoupper($type)];
  }
}
