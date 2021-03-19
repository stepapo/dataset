<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Filter;

use Stepapo\Data\Column;
use Stepapo\Data\UI\Dataset\DatasetControlTemplate;


class FilterTemplate extends DatasetControlTemplate
{
    public Column $column;

    public ?string $value;
}
