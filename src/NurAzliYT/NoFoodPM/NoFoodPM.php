<?php

namespace NurAzliYT\NoFoodPM;

use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\plugin\PluginBase;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class NoFoodPM extends PluginBase implements Listener
{
    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param PlayerItemConsumeEvent $event
     * @ignoreCancelled true
     */
    public function onConsume(PlayerItemConsumeEvent $event): void
    {
        $player = $event->getPlayer();
        if (!$player instanceof Player) return;
        $lvlFolName = $player->getWorld()->getFolderName();
        $itemType = $event->getItem()->getTypeId();
        $lvlACName = (array)$this->getConfig()->get("worlds");
        $aItemAC = (array)$this->getConfig()->get("allowedFood");

        if ($player->hasPermission("nofoodpm.bypass")) return;
        if (!in_array($lvlFolName, $lvlACName, true)) return;

        foreach ($aItemAC as $aItem) {
            $uAItem = strtoupper($aItem);
            $aItemType = ItemTypeIds::AIR; // Default to AIR in case of failure
            try {
                $aItemType = constant(ItemTypeIds::class . "::$uAItem");
            } catch (Exception $e) {
                if (is_int($aItem)) $aItemType = $aItem;
            }

            if ($itemType === $aItemType) return;
        }

        $event->cancel();
    }

    /**
     * @param PlayerExhaustEvent $event
     * @ignoreCancelled true
     */
    public function onExhaust(PlayerExhaustEvent $event): void
    {
        if (!(bool)$this->getConfig()->get("noHungry")) return;

        $player = $event->getPlayer();
        if (!$player instanceof Player) return;

        $lvlFolName = $player->getWorld()->getFolderName();
        $lvlACName = (array)$this->getConfig()->get("worlds");
        
        if (!in_array($lvlFolName, $lvlACName, true)) return;
        
        $event->cancel();
    }
}
