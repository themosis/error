<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

enum Level: string {
	case Emergency = 'emergency';
	case Alert     = 'alert';
	case Critical  = 'critical';
	case Error     = 'error';
	case Warning   = 'warning';
	case Notice    = 'Notice';
	case Info      = 'info';
	case Debug     = 'debug';
}
