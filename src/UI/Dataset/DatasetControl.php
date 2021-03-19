<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset;

use Stepapo\Data\UI\DataControl;
use Stepapo\Data\UI\Dataset\Dataset\Dataset;
use Stepapo\Data\View;


abstract class DatasetControl extends DataControl
{   
    public function render()
    {
        parent::render();
        $this->template->views = $this->getViews();
    }


    public function getMainComponent(): ?Dataset
    {
        return $this->lookup(Dataset::class, false);
    }


    public function getComponentLevel(): int
    {
        return $this->getMainComponent()->getComponentLevel();
    }


    /** @var View[]|null */
    public function getViews(): ?array
    {
        return $this->getMainComponent()->getViews();
    }
}
