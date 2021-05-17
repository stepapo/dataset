<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nette\InvalidArgumentException;


class Link
{
	public function __construct(
		public string $destination,
		public ?array $args = null
	) {}


	public static function createFromArray(array $config): Link
	{
		if (!isset($config['destination'])) {
			throw new InvalidArgumentException('Link destination has to be defined.');
		}
		return new self(
			$config['destination'],
			isset($config['args']) ? (array) $config['args'] : null
		);
	}
}
