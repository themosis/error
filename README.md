<!--
SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>

SPDX-License-Identifier: GPL-3.0-or-later
-->

Themosis Error
==============

The Themosis error component provides a highly configurable PHP API to manage application errors and exceptions.
The package also provides a nice looking and color accessible interface to better help you debug your projects.

Installation
------------

Install the library using [Composer](https://getcomposer.org/):

```bash
composer require themosis/error
```

Features
--------

- Configurable error reporter
- Backtrace with identifiable frames
- File preview
- HTML error report template with light and dark themes
- Configurable PHP error handler
- Configurable PHP exception handler

Introduction
------------

The package provides 2 components to help you interact with PHP errors and exceptions:

1. [Report Handler](#report-handler)
    - [Reporters](#reporters)
    - [PHP Error Handler](#php-error-handler)
    - [PHP Exception Handler](#php-exception-handler)
2. [Backtrace](#backtrace)

Report Handler
--------------

The `ReportHandler` is the main component of the package, it acts as the aggregate that manages how you can report your application errors and exceptions.

The `ReportHandler` class requires two dependencies:

1. A collection of reporters
2. A collection of issues

A `Reporter` is an interface that represents any PHP class that can report an `Issue`. For example a reporter can report an issue to the standard output (stdout), or report the issue in a log file, or send the issue to a third-party service, ... The choice is yours. You can register as many reporters as you want. It is also possible to configure in which context a reporter should act against the given issue.

An `Issue` is an interface that represents an error or any custom problem in your application that you want to report.

Depending on your needs, you can configure multiple report handlers withing your application. Here is a basic configuration example where we always report to the stdout:

```php
$reporters = new InMemoryReporters();
$reporters->add(
    condition: new AlwaysReport(),
    reporter: new CallbackReporter(static function (Issue $issue) {
        echo $issue->message().PHP_EOL;
    }),
);

$handler = new ReportHandler(
    reporters: $reporters,
    issues: new InMemoryIssues(),
);

// Capture an issue.
$handler->capture(ExceptionalIssue::create(new Exception('Oops!')));

// Publish the captured issues.
$handler->publish();
```

Let's decompose the above example...

### Reporters

The `InMemoryReporters` class is a repository that contains all declared reporters. When you declare a reporter using the `add()` method, you must provide 2 parameters:

1. A condition
2. A reporter

#### Report Condition

The first required parameter is a report condition. The condition instance is reponsible to evaluate if the linked reporter must be evaluated or not. There are builtin conditions with the package: `AlwaysReport` and `CallbackCondition` but you can also build your own.

The `AlwaysReport` condition, as its name implies, is always reporting the attached reporter.

The `CallbackCondition` accepts a callback as a parameter to let you evaluate if the issue should be reported or not. The given callback has the `Issue` as an argument:

```php
// The following reporter will only report RuntimeException issues.
$reporters->add(
    condition: new CallbackCondition(static function (Issue $issue) {
        return $issue instanceof RuntimeException;
    }),
    reporter: ...
);
```

You can build your own condition class by implementing the `ReportCondition` interface. The interface exposes a `can()` method that receives an `Issue` as a parameter and must return a `boolean` value:

```php
<?php

class TerminalCondition implements ReportCondition
{
    public function can(Issue $issue): bool
    {
        return php_sapi_name() === 'cli';
    }
}
```

#### Reporter

The second required parameter is a reporter instance. A reporter is one executable element attached to the report handler and it is evaluated if its related condition is true.

There is a generic builtin `CallbackReporter` class provided by the package. The `CallbackReporter` accepts a function as a parameter, which receives the `Issue` as an argument:

```php
$reporters->add(
    condition: new AlwaysReport(),
    reporter: new CallbackReporter(static function (Issue $issue) {
        echo $issue->message().PHP_EOL;
    }),
);
```

The above example is simply reporting the issue message to the standard output (stdout).

You can build your own reporter classes by implementing the `Reporter` interface. The interface exposes a `report()` method that receives the `Issue` as an argument:

```php
<?php

use Psr\Log\LoggerInterface;
use Themosis\Components\Error\Issue;
use Themosis\Components\Error\Reporter;

class LogReporter implements Reporter
{
    public function __construct(
        private LoggerInterface $logger,
    )
    {}

    public function report(Issue $issue): void
    {
        $this->logger->error(
            message: $issue->message(),
            context: $issue->info()?->toArray() ?? [],
        );
    }
}
```

### PHP Error Handler

You can hook a `ReportHandler` instance as the default PHP error handler. See the [set_error_handler()](https://www.php.net/manual/en/function.set-error-handler.php) function in the PHP documentation for details.

The package provides a pre-configured class that can register a `ReportHandler` instance and hook it up as the default PHP error handler. Simply pass an `ErrorHandler` class instance to the `set_error_handler` PHP function like so:

```php
<?php

$reportHandler = new ReportHandler(
    reporters: new InMemoryReporters(),
    issues: new InMemoryIssues(),
);

set_error_handler(new ErrorHandler($reportHandler));
```

The `ErrorHandler` captures all triggered PHP errors. The current implementation is only reporting the **deprecated** errors (E_DEPRECATED, E_USER_DEPRECATED) with the given `ReportHandler` instance. All other errors are converted to an `ErrorException` and are thrown so the default PHP exception handler can capture them.

### PHP Exception Handler

You can register a `ReportHandler` instance as the default PHP exception handler. See the [set_exception_handler()](https://www.php.net/manual/en/function.set-exception-handler.php) function in the PHP documentation for details.

Just like for the PHP errors, the package provides a pre-configured class can be register a `ReportHandler` instance and hook it up as the default PHP exception handler.
Simply pass an `ExceptionHandler` class instance to the `set_exception_handler` PHP function like so:

```php
<?php

$reportHandler = new ReportHandler(
    reporters: new InMemoryReporters(),
    issues: new InMemoryIssues(),
);

set_exception_handler(new ExceptionHandler($reportHandler));
```

#### HTML Error Report Template

On a web context, you might want to render thrown exceptions in the browser during development to easily debug your application. The package also provides a HTML template with a light and a dark theme. Here is an example on how to use the `ExceptionHandlerHttpResponse` class that is responsible to prepare the data for usage in the `resources/views/exception.php` file from this package:

```php
<?php

$reporters = new InMemoryReporters();
$reporters->add(
    condition: new AlwaysReport(), // You might want to constraint this for "local" and "web" environments
    reporter: new CallbackReporter(static function (Issue $issue) {
        $backtrace = new Backtrace(new InMemoryFrameIdentifiers());
        $backtrace->captureException($issue->exception());

        (new ExceptionHandlerHttpResponse(
            backtrace: $backtrace,
            information: new InMemoryInformation(),
        ))->render($issue);
    }),
);

$reportHandler = new ReportHandler(
    reporters: $reporters,
    issues: new InMemoryIssues(),
);

set_exception_handler(new ExceptionHandler($reportHandler));
```

The above code snippet is attaching a `ReportHandler` to the default PHP exception handler. The report handler contains one reporter that is exposing any thrown exceptions to the standard output using HTML.

> The attached condition is to always report the issue to the stdout. On your application, make sure to constraint the condition to avoid rendering the error HTML template on a production environment.

Backtrace
---------
