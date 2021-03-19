<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Sorting;

use Nextras\Orm\Collection\ICollection;


interface Sorting
{
    public function handleSort(?string $sort = null, ?string $direction = ICollection::ASC): void;
}
