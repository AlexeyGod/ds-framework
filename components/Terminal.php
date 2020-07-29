<?php

namespace framework\components;

class Terminal {
	public function run ($consoleObject)
	{
		$consoleObject->writeLn('Terminal route active');
	}
}