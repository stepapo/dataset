<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Latte;


class Filters
{
	public static function intlDate(\DateTimeInterface $time, string $pattern, ?string $locale = null): ?string
	{
		$formatter = new \IntlDateFormatter(
			$locale ?: setlocale(LC_TIME, 0),
			\IntlDateFormatter::LONG,
			\IntlDateFormatter::LONG
		);
		$formatter->setPattern($pattern);
		return $formatter->format($time);
	}
}
