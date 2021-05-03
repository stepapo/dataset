<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset;

use Stepapo\Data\UI\DataControl;
use Stepapo\Data\UI\Dataset\Dataset\Dataset;


abstract class DatasetControl extends DataControl
{
	public function getMainComponent(): ?Dataset
	{
		return $this->lookup(Dataset::class, false);
	}
}
