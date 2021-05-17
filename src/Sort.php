<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nextras\Orm\Collection\ICollection;


class Sort
{
	public function __construct(
		public bool $isDefault = false,
		public string $direction = ICollection::ASC,
		public ?OrmFunction $function = null
	) {}


	public static function createFromArray(?array $config): Sort
	{
		return new self(
			$config['isDefault'] ?? false,
			$config['direction'] ?? ICollection::ASC,
			isset($config['function']) ? OrmFunction::createFromArray($config['function']) : null
		);
	}
}
