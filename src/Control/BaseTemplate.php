<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control;

use Contributte\ImageStorage\ImageStorage;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\User;
use Stepapo\Dataset\Column;
use Stepapo\Dataset\Text;
use Stepapo\Dataset\View;


abstract class BaseTemplate extends Template
{
	public Presenter $presenter;

	public User $user;

	public string $basePath;

	public Text $text;

	/** @var Column[] */
	public array $columns;

	/** @var View[] */
	public array $views;

	public View $selectedView;

	public ?ImageStorage $imageStorage;
}
