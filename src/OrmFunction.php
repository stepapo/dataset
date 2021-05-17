<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nette\InvalidArgumentException;


class OrmFunction
{
	public function __construct(
		public string $class,
		public ?array $args = null
	) {}


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
