<?php

declare(strict_types=1);

namespace Stepapo\Data;

use Nette\InvalidArgumentException;


class LatteFilter
{
	public string $name;

	public ?array $args;


	public function __construct(
		string $name,
		?array $args = null
	) {
		$this->name = $name;
		$this->args = $args;
	}


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
