<?php

declare(strict_types=1);

namespace Stepapo\Data\UI;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Security\User;
use Stepapo\Data\Column;
use Stepapo\Data\View;
use Nette\Bridges\ApplicationLatte\Template;
use Ublaboo\ImageStorage\ImageStorage;


abstract class DataControlTemplate extends Template
{
	public Presenter $presenter;

	public Control $control;

	public User $user;

	public string $basePath;

	/** @var Column[]|null */
	public ?array $columns;

	/** @var View[]|null */
	public ?array $views;

	public View $selectedView;

	public ?ImageStorage $imageStorage;
}
