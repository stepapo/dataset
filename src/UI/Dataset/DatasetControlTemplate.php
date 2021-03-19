<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset;

use Stepapo\Data\UI\DataControlTemplate;
use Stepapo\Data\View;


abstract class DatasetControlTemplate extends DataControlTemplate
{
    /** @var View[]|null */
    public ?array $views;
}
