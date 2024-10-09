<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

interface Information {
	public function add( ?InformationGroup $info ): void;

	public function get_information_by_priority(): array;
}
