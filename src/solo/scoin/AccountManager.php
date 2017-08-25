<?php

namespace solo\scoin;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class AccountManager implements Listener{

  private $owner;

  private $accounts = [];

  public function __construct(SCoin $owner){
    $this->owner = $owner;

    @mkdir($this->owner->getDataFolder() . "players/");

    $this->owner->getServer()->getPluginManager()->registerEvents($this, $this->owner);
  }

  public function getAccounts(){
    return $this->accounts;
  }

  public function createAccount($player){
    if($player instanceof Player){
      $player = $player->getName();
    }
    $player = strtolower($player);

    return new Account($player, $this->owner->getDataFolder() . "players/" . $player . ".yml");
  }

  public function loadAccount($player){
    if($player instanceof Player){
      $player = $player->getName();
    }
    $player = strtolower($player);

    $filePath = $this->owner->getDataFolder() . "players/" . $player . ".yml";
    if(!file_exists($filePath)){
      return null;
    }
    return $this->accounts[$player] = new Account($player, $filePath);
  }

  public function getAccount($player){
    if($player instanceof Player){
      $player = $player->getName();
    }
    $player = strtolower($player);

    $account = $this->accounts[$player] ?? null;
    if($account === null){
      $account = $this->loadAccount($player);
    }
    return $account;
  }

  public function unloadAccount($player){
    if($player instanceof Player){
      $player = $player->getName();
    }
    $player = strtolower($player);

    if(!isset($this->accounts[$player])){
      return false;
    }
    $this->accounts[$player]->save();
    unset($this->accounts[$player]);
    return true;
  }

  public function handlePlayerJoin(PlayerJoinEvent $event){
    $this->createAccount($event->getPlayer());
  }

  public function handlePlayerQuit(PlayerQuitEvent $event){
    $this->unloadAccount($event->getPlayer());
  }

  public function save(){
    foreach($this->accounts as $account){
      $account->save();
    }
  }
}
