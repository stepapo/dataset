<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Attribute;

use Stepapo\Data\Column;
use Nette\Bridges\ApplicationLatte\Template;


class AttributeTemplate extends Template
{
    public Column $column;
}
