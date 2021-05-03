<?php

declare(strict_types=1);

namespace Stepapo\Data;

use Nette\InvalidArgumentException;


class Part
{
	public string $name;

	/** @var callable */
	public $callback;


	public function __construct(
		string $name,
		callable $callback
	) {
		$this->name = $name;
		$this->callback = $callback;
	}


	public static function createFromArray(array $config, string $name): Part
	{
		if (!isset($config['callback']) && !$config[array_key_first($config)]) {
			throw new InvalidArgumentException('Component callback has to be defined.');
		}
		return new self(
			$name,
			$config['callback'] ?? $config[array_key_first($config)]
		);
	}
}
