<?php
namespace BuyFix;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use onebone\economyapi\EconomyAPI;
use pocketmine\inventory\PlayerInventory;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Tool;
use pocketmine\item\Armor;

class Main extends PluginBase implements Listener{

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    			$this->getLogger()->info("BuyFix Enabled");
    			$this->saveResource("config.yml");  			
    }
    
    public function onDisable(){
        $this->getLogger()->info("BuyFix Disable");
    }
    
    public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
        if($command->getName() === "buyfix"){
          if(!$sender instanceof Player){
            $sender->sendMessage("Use This Command In Game");
            return true;
          }
          $economy = EconomyAPI::getInstance();
          $mymoney = $economy->myMoney($sender);
          $cash = $this->getConfig()->get("price-buyfix");
          if($mymoney >= $cash){
            $economy->reduceMoney($sender, $cash);
            $item = $sender->getInventory()->getItemInHand();
				      if($item instanceof Armor or $item instanceof Tool){
				        $id = $item->getId();
					      $meta = $item->getDamage();
					      $sender->getInventory()->removeItem(Item::get($id, $meta, 1));
					      $newitem = Item::get($id, 0, 1);
					      if($item->hasCustomName()){
						       $newitem->setCustomName($item->getCustomName());
						    }
					      if($item->hasEnchantments()){
						        foreach($item->getEnchantments() as $enchants){
						            $newitem->addEnchantment($enchants);
						        }
						     }
					      $sender->getInventory()->addItem($newitem);
					      $sender->sendMessage("" . $item->getName() . " Have Been Fixed Now");
					      return true;
					    } else {
				        	$sender->sendMessage("Please Hold The Item You Wanna Fix In Hand");
					        return false;
					    }
            return true;
          } else {
            $sender->sendMessage("You Need More Money Money To Repair This Ite,");
            return true;
          }
        }
    }
}
