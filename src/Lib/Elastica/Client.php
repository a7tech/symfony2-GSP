<?php

namespace Lib\Elastica;

use Elastica\Client as BaseClient;
use Psr\Log\LoggerInterface;

class Client extends BaseClient
{
	public function setLogger(LoggerInterface $logger)
	{
		return $this;
	}
}
