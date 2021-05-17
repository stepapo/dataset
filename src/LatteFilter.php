<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nette\InvalidArgumentException;


class LatteFilter
{
	public function __construct(
		public string $name,
		public ?array $args = null
	) {}


	public static function createFromArray(array $config): LatteFilter
	{
		if (!isset($config['name'])) {
			throw new InvalidArgumentException('Filter name has to be defined.');
		}
		return new self(
			$config['name'],
			isset($config['args']) ? (array) $config['args'] : null
		);
	}
}
