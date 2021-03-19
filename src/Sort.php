<?php

declare(strict_types=1);

namespace Stepapo\Data;

use Nextras\Orm\Collection\ICollection;


class Sort
{
	public bool $isDefault;

	public string $direction;

	public ?OrmFunction $function;


	public function __construct(
		bool $isDefault = false,
		string $direction = ICollection::ASC,
		?OrmFunction $function = null
	) {
		$this->isDefault = $isDefault;
		$this->direction = $direction;
		$this->function = $function;
	}


	public static function createFromArray(?array $config): Sort
	{
		return new self(
			$config['isDefault'] ?? false,
			$config['direction'] ?? ICollection::ASC,
			isset($config['function']) ? OrmFunction::createFromArray($config['function']) : null
		);
	}
}
