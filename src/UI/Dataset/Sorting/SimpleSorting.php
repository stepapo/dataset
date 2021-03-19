<?php

namespace Stepapo\Data\UI\Dataset\Sorting;

use Stepapo\Data\UI\Dataset\DatasetControl;
use Nextras\Orm\Collection\ICollection;


/**
 * @property-read SortingTemplate $template
 */
class SimpleSorting extends DatasetControl implements Sorting
{
    /** @persistent */
    public ?string $sort = null;

    /** @persistent */
    public ?string $direction = ICollection::ASC;

    public array $onSort = [];


    public function render()
    {
        parent::render();
        $this->template->show = false;
        foreach ($this->getColumns() as $column) {
            if ($column->sort) {
                $this->template->show = true;
                break;
            }
        }
        $this->template->sort = $this->sort;
        $this->template->direction = $this->direction;
        $this->template->render($this->getSelectedView()->sortingTemplate);
    }


    public function handleSort(?string $sort = null, ?string $direction = ICollection::ASC): void
    {
        $this->sort = $sort;
        $this->direction = $direction;
        if ($this->presenter->isAjax()) {
            $this->onSort($this);
            $this->redrawControl();
        }
    }
}
