<?php

namespace TheDarkGRO;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Plugin;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\plugin\PluginLoader;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\utils\Config;
use pocketmine\event\Cancellable;
use pocketmine\event\PlayerEvent;

class ServerUI extends PluginBase implements Listener {
	
	const PREFIX = "§eServer §7|§r ";
	
	public noHunger = false;
	public noDamage = false;
	public listofplayersonline = array();
	public admins = array();
	
	public function onEnable() {
		
		$this->getLogger()->info("§aSystem by TheDarkGRO started");
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		 if(!file_exists($this->getDataFolder(). "Settings.yml")){
		  $config = new Config($this->getDataFolder(). "Settings.yml", Config::YAML);
		    
            $config->set("main-server-ip", "ip.com");
            $config->set("main-server-port", "19132");
            $config->set("cb-server-ip", "ip.com");
            $config->set("cb-server-port", "19134");
            $config->save();
          }
          
          if(!is_dir($this->getDataFolder())) {
          	
            @mkdir($this->getDataFolder());
          
          }
          
              
        }
        
        
        
    
    
    public function onExhaust(PlayerExhaustEvent $event) {
    	$player = $event->getPlayer();
    
          if($this->noHunger == true) {
        	 $event->setCancelled(true);
             
          } else {
          	
              $event->setCancelled(false);
        	
        
        }
       }
       
         public function onEntityDamage(EntityDamageEvent $event) {
         	 if($this->noDamage == true) {
                  $event->setCancelled(true);
                  
                  
                  } else {
         
                  $event->setCancelled(false);
         
         }      
        }
    
    public function onChat(PlayerChatEvent $event) {
    	$player = $event->getPlayer();
         
         if($this->getConfig()->get("chat") == false) {
         	$event->setCancelled(true);
             $player->sendMessage("§cThe Chat is disabled!");
         } else {
         	$event->setCancelled(false);
        }
      }
         
        
    
    
	
	
   
   
  
  
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
	 if($command->getName() == "server") {
		
		$this->sendServerUI($sender);
		
		
		
    } else if($command->getName() == "addadmin") {
    	if(isset($args[0])) {
    	
          if(in_array($args[0], $this->$admins)) {
        
           $sender->sendMessage(self::PREFIX . "§cPlayer is already an Admin!");
        
         } else {
           if($args[0] instanceof Player) {
          	
           array_push($this->admins, $args[0]);
           
          } 
        
        
       }
	
	 } else if($command->getName() == "removeadmin") {
		if(isset($args[0])) {
		  
		  unset($this->admins[$args[0]]);
		 
		  
		 }
		}
	
	
}

    public function sendServerUI($sender) {
    
      $fdata = [];
      
      $fdata['title'] = '§cServer Settings';
      $fdata['buttons'] = [];
      $fdata['content'] = "";
      $fdata['type'] = "form";
      
      $fdata['buttons'][] = ['text' => '§aMenu'];
      $fdata['buttons'][] = ['text' => '§aDeveloper Settings'];
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 100;
      $pk->formData = json_encode($fdata);
      $sender->sendDataPacket($pk);
      
      }
    
    public function onPacketReceive(DataPacketReceiveEvent $ev) {
    $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 100) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
          
                $this->sendServerSettings($player);
              
              break;
              
               case 1:
                if(in_array($player->getName(), $this->admins)) {
               
                 $this->sendDeveloperSettings($player);
               
               } else {
               	
                $player->sendMessage(self::PREFIX . "§cYou are not an Admin!");
               
               }
               
               break;
         
     }
     
    }
   }
  }
 }
               
           public function sendServerSettings($player) {
           
           $fdata = [];
      
      $fdata['title'] = '§cServer Settings';
      $fdata['buttons'] = [];
      $fdata['content'] = "";
      $fdata['type'] = "form";
      $fdata['buttons'][] = ['text' => '§bHunger'];
      $fdata['buttons'][] = ['text' => '§6Damage'];
      $fdata['buttons'][] = ['text' => '§aCommands'];
      $fdata['buttons'][] = ['text' => '§aUser'];
      $fdata['buttons'][] = ['text' => '§aShop Menu'];
      $fdata['buttons'][] = ['text' => '§aChat System'];
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 200;
      $pk->formData = json_encode($fdata);
      $player->sendDataPacket($pk);
      
           
           
           
           
           }
           
  public function onServerSettings(DataPacketReceiveEvent $ev) {
    $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 200) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
          
              if(in_array($player->getName(), $this->admins)) {
               
                 $this->sendHunger($player);
               
               } else {
               	
                $player->sendMessage(self::PREFIX . "§cYou are not an Admin!");
               
               }
               break;
               
               
              case 1:
              
              if(in_array($player->getName(), $this->admins)) {
               
                 $this->sendDamage($player);
               
               } else {
               	
                $player->sendMessage(self::PREFIX . "§cYou are not an Admin!");
               
               }
               break;
               
               
               
                  
               
               case 2:
               
              $this->sendCommandUI($player);
               
              break;
              
              case 3:
               
              $this->sendUserInterface($player);
              
              break;
              
              case 4:
              
              $this->sendShopInterface($player);
              
              break;
              
              case 5:
              
              if(in_array($player->getName(), $this->admins)) {
               
                 $this->sendChatSystem($player);
               
               } else {
               	
                $player->sendMessage(self::PREFIX . "§cYou are not an Admin!");
               
               }
              break;
              
               
           }
          }
         }
        }
      }
    
    public function sendShopInterface($player) {
    	

      $fdata = [];
      
      $fdata['title'] = '§cShop';
      $fdata['buttons'] = [];
      $fdata['content'] = "§fThis shop is still under construction!";
      $fdata['type'] = "form";
      $fdata['buttons'][] = ['text' => '§aDiamond'];
      
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 163781;
      $pk->formData = json_encode($fdata);
      $player->sendDataPacket($pk);
         
       }
       
      public function onShop(DataPacketReceiveEvent $ev) {
       $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 163781) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
                 $name = $player->getName();
          
                 
                 $player->sendMessage(self::PREFIX . "§bComing later....");
                 
               break;
              
               
       
       
      }
     }
    }
   }
  }
  
    
    
    
    
    
     
     public function sendChatSystem($sender) {
    
               $fdata = [];

               $fdata['title'] = '§eChat System';
               $fdata['buttons'] = [];
               $fdata['content'] = "";
               $fdata['type'] = 'form';
               $fdata['buttons'][] = ['text' => '§aClear'];
               $fdata['buttons'][] = ['text' => '§aChat Settings'];
               

    $pk = new ModalFormRequestPacket();
    $pk->formId = 123789;
    $pk->formData = json_encode($fdata);
    $sender->sendDataPacket($pk);
    
    }

    public function onChatInterface(DataPacketReceiveEvent $ev) {
    $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 123789) {
        if ($data !== NULL) {
          switch($data){
             case 0:
               $name = $player->getName();
              
             $this->getServer()->broadcastMessage("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\nn\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\nn\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n"); 
             $this->getServer()->broadcastMessage("§eChat cleared by §e$name ");
   
             break;
             
             case 1:
                
                 $this->sendChatSettings($player);
             break;
             
             
}
}
 
}
}   


}

    public function sendChatSettings($player) {
         
    $fdata = [];

    $fdata['title'] = '§eChat Settings';
    $fdata['buttons'] = [];
    $fdata['content'] = "";
    $fdata['type'] = 'form';

    $fdata['buttons'][] = ['text' => 'Disable'];
    $fdata["buttons"][] = ['text' => 'Enable'];

    $pk = new ModalFormRequestPacket();
    $pk->formId = 12309;
    $pk->formData = json_encode($fdata);

    $player->sendDataPacket($pk);
  }
  
  public function onChatSettings(DataPacketReceiveEvent $ev) {
    $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 12309) {
        if ($data !== NULL) {
          switch($data){
             case 0:
               $name = $player->getName();
               
               
               $this->chatsystem = false;
               $this->getServer()->broadcastMessage("§aThe Chat disabled by §e$name");
               
               
                 
             
             break;
             
             case 1:
                $name = $player->getName();
              
                $this->chatsystem = true;
              
                $this->getServer()->broadcastMessage("§aThe Chat is now enabled by $name");
            
             
             break;
             
             
}
}
 
}
}   


}
       
       
       public function sendHunger($player) {
       	
        $fdata = [];
      
      $fdata['title'] = '§cHunger Settings';
      $fdata['buttons'] = [];
      $fdata['content'] = "";
      $fdata['type'] = "form";
      $fdata['buttons'][] = ['text' => '§aDisable'];
      $fdata['buttons'][] = ['text' => '§aEnable'];
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 300;
      $pk->formData = json_encode($fdata);
      $player->sendDataPacket($pk);
         
       }
       
      public function onHungerSettings(DataPacketReceiveEvent $ev) {
       $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 300) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
          
                  
          
                 $name = $player->getName();
          
                 $this->noHunger = false;
                 
                 
               break;
               
              case 1:
              
                   
                   $name = $player->getName();
               
                  $this->noHunger = true;
                  
                  
               break;
               
           }
          }
         }
        }
       }
      
     
     public function sendUserInterface($player) {
     	
        $fdata = [];
      
      $fdata['title'] = '§cUser Interface';
      $fdata['buttons'] = [];
      $fdata['content'] = "§fComing later";
      $fdata['type'] = "form";
      $fdata['buttons'][] = ['text' => '§a--'];
      
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 5163;
      $pk->formData = json_encode($fdata);
      $player->sendDataPacket($pk);
         
       }
       
      public function onUser(DataPacketReceiveEvent $ev) {
       $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 5163) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
          
                  
               
               break;
               
              
               
           }
          }
         }
        }
       }
      
     
       
       public function sendCommandUI($player) {
       
       $fdata = [];
      
      $fdata['title'] = '§cCommands';
      $fdata['buttons'] = [];
      $fdata['content'] = "This UI is still under construction!";
      $fdata['type'] = "form";
      
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 1459;
      $pk->formData = json_encode($fdata);
      $player->sendDataPacket($pk);
         
       }
       
      public function onCommandUI(DataPacketReceiveEvent $ev) {
       $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 1459) {
        if ($data !== NULL) {
          switch($data){
          	
               
           }
          }
         }
        }
       }
      
       
       
       public function sendAdminCommandInterface($player) {
       
       $fdata = [];
      
      $fdata['title'] = '§cAdmin Interface';
      $fdata['buttons'] = [];
      $fdata['content'] = "";
      $fdata['type'] = "form";
      $fdata['buttons'][] = ['text' => '§aStop'];
      
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 14578;
      $pk->formData = json_encode($fdata);
      $player->sendDataPacket($pk);
         
       }
       
      public function onAdminCommandInterface(DataPacketReceiveEvent $ev) {
       $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 14578) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
          
           if($player->hasPermission("thedarkgro.admininterface")) {
             
                 $name = $player->getName();
          
                 
                 $this->getServer()->shutdown();
                 }
               break;
               
            
               
           }
          }
         }
        }
       }
     
    
    public function sendCityBuildInterface($sender) {
   	
      $fdata = [];
      
      $fdata['title'] = '§eCityBuild';
      $fdata['buttons'] = [];
      $fdata['content'] = "§fThe Citybuild Managet";
      $fdata['type'] = "form";
      $fdata['buttons'][] = ['text' => '§aConnect'];
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 10569;
      $pk->formData = json_encode($fdata);
      $sender->sendDataPacket($pk);
      
      }
    
    public function onCB(DataPacketReceiveEvent $ev) {
    $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 10569) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
                $ip = $this->getConfig()->get("cb-transfer-ip");
                $port = $this->getConfig()->get("cb-transfer-port");
                $player->transfer($ip, $port);
               break;
              
          
                
              
              
         
     }
     
    }
   }
  }
 }
    
    
    
    
    
    
       public function sendDamage($player) {
       
           $fdata = [];
      
      $fdata['title'] = '§cDamage Settings';
      $fdata['buttons'] = [];
      $fdata['content'] = "";
      $fdata['type'] = "form";
      $fdata['buttons'][] = ['text' => '§aDisable'];
      $fdata['buttons'][] = ['text' => '§aEnable'];
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 400;
      $pk->formData = json_encode($fdata);
      $player->sendDataPacket($pk);
         
       }
       
      public function onDamageSettings(DataPacketReceiveEvent $ev) {
       $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 400) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
          
           
             
                 $name = $player->getName();
          
                 $this->noDamage = false;
                 $this->getServer()->broadcastMessage(self::PREFIX ."§e$name §aenabled the Damage!");
                 
               break;
               
              case 1:
              
                   if($player->hasPermission("thedarkgro.function.nodamage")) {
                  
                   $name = $player->getName();
               
                  $this->noDamage = true;
                  $this->getServer()->broadcastMessage(self::PREFIX ."§a$name disabled the Damage");
                  }
               break;
               
           }
          }
         }
        }
       }
      
     
       
       
       public function sendDeveloperSettings($player) {
       	
         $fdata = [];
      
      $fdata['title'] = '§cDeveloper Settings';
      $fdata['buttons'] = [];
      $fdata['content'] = "";
      $fdata['type'] = "form";
      $fdata['buttons'][] = ['text' => '§aPlugins Reload'];
      
      
      $pk = new ModalFormRequestPacket();
      $pk->formId = 10000;
      $pk->formData = json_encode($fdata);
      $player->sendDataPacket($pk);
         
       }
       
      public function onDeveloper(DataPacketReceiveEvent $ev) {
       $pk = $ev->getPacket();
    $player = $ev->getPlayer();
      if ($pk instanceof ModalFormResponsePacket) {
      $id = $pk->formId;
      $data = json_decode($pk->formData);
      if ($id == 10000) {
        if ($data !== NULL) {
          switch($data){
          	case 0:
                 $name = $player->getName();
          
                 
                 $player->sendMessage(self::PREFIX . "§aComing later!");
                 
               break;
              
               
       
       
      }
     }
    }
   }
  }
  
  
         
  
      
      
    
    
    
    
    
    
    
    
    
    
    
    
    
}
		
		
