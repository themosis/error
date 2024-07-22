<?php

declare(strict_types=1);

namespace Themosis\Components\Error;

use ErrorException;
use Throwable;

final class ErrorHandler {
	public function __construct(
		private Reporters $reporters,
	) {
	}

    public function capture( Throwable $exception ): void
    {
        // Capturing the exception... but I don't want to pass it to "all" reporters immediately.
        // If developer has manually captured the exception, it seems that the script is still ongoing...
        // So, perhaps we want to stack captured exceptions, and only once the script execution is finishing, we will release and report them all...
    }

	public function register(): void {
		$previous = set_error_handler( $this->handle_error( ... ), E_ALL );
        //var_dump($previous);
		set_exception_handler( $this->handle_exception( ... ) );
	}

	private function handle_error( int $errno, string $errstr, string $errfile, int $errline ): bool {
		if ( $this->is_deprecation( $errno ) ) {
			// TODO: Report deprecation type of error...
			$this->reporters->find( new ReporterKey() )->report();

			return true;
		} elseif ( error_reporting() && $errno ) {
			throw new ErrorException( $errstr, 0, $errno, $errfile, $errline );
		}

		return false;
	}

	private function handle_exception( Throwable $exception ): void {
		// TODO: Report exception...
		$this->reporters->find( new ReporterKey() )->report();
	}

	private function is_deprecation( int $level ): bool {
		return in_array( $level, [ E_DEPRECATED, E_USER_DEPRECATED ], true );
	}

	public function restore(): void {
		restore_error_handler();
		restore_exception_handler();
	}
}
