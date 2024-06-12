<?php

declare(strict_types=1);

namespace Stepapo\Dataset;


class Option
{
	public function __construct(
		public int|string|null $name,
		public int|string|null $label = null,
		public ?array $condition = null
	) {}


	public static function createFromArray(string|array $config, string|int $name): Option
	{
		return new self(
			$name,
			$config['label'] ?? $config[array_key_first($config)],
			$config['condition'] ?? null
		);
	}
}
