<?php

declare(strict_types=1);

namespace Stepapo\Data;

use Nette\InvalidArgumentException;


class Button
{
	public string $name;

	public ?string $label;

	public $handleCallback;

	public $hideCallback;


	public function __construct(
		string $name,
		callable $handleCallback,
		?callable $hideCallback = null,
		?string $label = null
	) {
		$this->name = $name;
		$this->label = $label;
		$this->handleCallback = $handleCallback;
		$this->hideCallback = $hideCallback;
	}


	public static function createFromArray(array $config, string $name): Button
	{
		if (!isset($config['handleCallback'])) {
			throw new InvalidArgumentException('Action name and handle callback have to be defined.');
		}
		return new self(
			$name,
			$config['handleCallback'],
			$config['hideCallback'] ?? null,
			$config['label'] ?? null
		);
	}
}
