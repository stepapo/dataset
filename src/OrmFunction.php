<?php

declare(strict_types=1);

namespace Stepapo\Data;

use Nette\InvalidArgumentException;


class OrmFunction
{
	public string $class;

	public ?array $args;


	public function __construct(
		string $class,
		?array $args = null
	) {
		$this->class = $class;
		$this->args = $args;
	}


	public static function createFromArray(array $config): OrmFunction
	{
		if (!isset($config['class'])) {
			throw new InvalidArgumentException('Function class has to be defined.');
		}
		return new self(
			$config['class'],
			isset($config['args']) ? (array) $config['args'] : null
		);
	}
}
