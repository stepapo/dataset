<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Display;

use Stepapo\Data\Control\DataTemplate;
use Stepapo\Data\Control\MainComponent;
use Stepapo\Data\Text;


class DisplayTemplate extends DataTemplate
{
	public MainComponent $main;
	public DisplayControl $control;
	public ?string $viewName;
	public array $views;
	public Text $text;
}
