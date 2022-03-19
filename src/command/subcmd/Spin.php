<?php
declare(strict_types=1);

namespace lmao\command\subcmd;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use lmao\command\args\PlayerArgument;
use lmao\command\args\SpinWayArgument;
use lmao\Lmao;
use lmao\task\SpinTask;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class Spin extends BaseSubCommand{

	/**
	 * @throws ArgumentOrderException
	 */
	protected function prepare() : void{
		$this->setPermission("lmao.spin");
		$this->registerArgument(0, new PlayerArgument());
		$this->registerArgument(1, new FloatArgument("speed", true));
		$this->registerArgument(2, new IntegerArgument("times", true));
		$this->registerArgument(3, new SpinWayArgument(true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if (!isset($args["player"])){
			$sender->sendMessage("Please add player name !");
			return;
		}
		$player = Server::getInstance()->getPlayerByPrefix($args["player"]);
		if (is_null($player)){
			$sender->sendMessage("Invalid player name !");
			return;
		}
		$speed = $args["speed"] ?? 1;
		$times = $args["times"] ?? 1;
		if (isset($args["spinway"])){
			$spinWay = match ($args["spinway"]) {
				"right" => SpinTask::SPINWAY_RIGHT,
				default => SpinTask::SPINWAY_LEFT //Default value
			};
		} else {
			$spinWay = SpinTask::SPINWAY_LEFT;
		}
		Lmao::getInstance()->getScheduler()->scheduleRepeatingTask(new SpinTask($player, $speed, $times, $spinWay), 1);
		$sender->sendMessage($player->getName() . " now spinning at a speed of speed for times");
	}
}