<?php

declare(strict_types=1);

namespace Stepapo\Data;


class Option
{
	/** @var int|string|null */
	public $name;

	/** @var int|string|null */
	public $label;

	public ?array $condition;


	/** @var int|string|null $name */
	/** @var int|string|null $label */
	public function __construct(
		$name,
		$label = null,
		?array $condition = null
	) {
		$this->name = $name;
		$this->label = $label;
		$this->condition = $condition;
	}


	/** @param string|array $config */
	/** @param string|int $name */
	public static function createFromArray($config, $name): Option
	{
		return new self(
			$name,
			$config['label'] ?? $config[array_key_first($config)],
			$config['condition'] ?? null
		);
	}
}
