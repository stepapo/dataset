<?php

declare(strict_types=1);

namespace Stepapo\Data\UI;

use Nette\Localization\ITranslator;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\IRepository;
use Stepapo\Data\Column;
use Stepapo\Data\View;
use Nextras\Orm\Collection\ICollection;


interface MainComponent
{
	function getCollection(): ICollection;

	function getRepository(): IRepository;

	function getParentEntity(): ?IEntity;

	function getTranslator(): ?ITranslator;

	/** @return Column[]|null */
	function getColumns(): ?array;

	/** @return View[]|null */
	function getViews(): ?array;

	function getSelectedView(): View;
}
