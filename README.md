<!--
SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>

SPDX-License-Identifier: GPL-3.0-or-later
-->

Themosis Error
==============

The Themosis error component provides a highly configurable PHP API to manage application errors and exceptions.
The package also provides a nice looking and color accessible interface to better help you debug your web projects.

Installation
------------

Install the library using [Composer](https://getcomposer.org/):

```bash
composer require themosis/error
```

Features
--------

- Configurable error reporter
- Backtrace with filterable and identifiable frames
- File preview
- Supports nested exceptions
- HTML error report template with light and dark themes
- Configurable PHP error handler
- Configurable PHP exception handler

Example
-------

You can quickly try the example provided within the package by cloning the repository and run the PHP built-in web server:

```bash
git clone https://github.com/themosis/error.git
cd error/example
php -S localhost:8000
```

The `example` directory shows a code sample with a configured report handler hooked as the default PHP exception handler. The example code throws multiple exceptions and render them in the browser using the default HTML template. Feel free to explore!

Introduction
------------

The package provides 2 components to help you interact with PHP errors and exceptions:

1. [Report Handler](#report-handler)
    - [Reporters](#reporters)
    - [PHP Error Handler](#php-error-handler)
    - [PHP Exception Handler](#php-exception-handler)
2. [Issue](#issue)
3. [Information](#information)
    - [On Exceptions](#on-exceptions)
    - [On Issues](#on-issues)
4. [Backtrace](#backtrace)
    - [Capture Frames](#capture-frames)
    - [Get Frames](#get-frames)
    - [Tag Frames](#tag-frames)
    - [Filter Frames](#filter-frames)

Report Handler
--------------

The `ReportHandler` is the main component of the package, it acts as an aggregate that manages how you can report your application errors and exceptions.

The `ReportHandler` class requires two dependencies:

1. A repository of reporters
2. A repository of issues

A `Reporter` is an interface that represents any PHP class that can report an `Issue`. For example, a reporter can report an issue to the standard output (stdout), or report the issue in a log file, or send the issue to a third-party service, ... The choice is yours. You can register as many reporters as you want. It is also possible to configure in which context a reporter can act against a given issue.

An `Issue` is an interface that represents an error or any custom problem in your application that you want to report.

Depending on your needs, you can configure multiple report handlers within your application. Here is a basic configuration example where we always report to the stdout:

```php
<?php

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

The first required parameter is a report condition. The condition instance is reponsible to evaluate if the linked reporter must be executed or not. There are builtin conditions with the package: `AlwaysReport` and `CallbackCondition` but you can also build your own.

The `AlwaysReport` condition, as its name implies, is always reporting the attached reporter.

The `CallbackCondition` accepts a callback as a parameter to let you evaluate if the issue should be reported or not. The given callback receives the `Issue` as an argument:

```php
<?php

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

The second required parameter is a reporter instance. A reporter is one executable element attached to the report handler. The reporter is executed if its related condition is true.

There is a generic builtin `CallbackReporter` class provided by the package. The `CallbackReporter` accepts a function as a parameter, which receives the `Issue` as an argument:

```php
<?php

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

The package also provides 2 additional reporters:

1. LogReporter: automatically logs the issue to an attached PSR-3 compliant logger.
2. StdoutReporter: prints the issue to the standard output (stdout) with backtrace in text.

#### LogReporter

If you project supports a PSR-3 logger, you can pass it to the `LogReporter` instance like so:

```php
<?php

// Here is an example using the Monolog package.
// The issue is always logged to a file.
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler('/path/to/file.log'));

$reporters->add(
    condition: new AlwaysReport(),
    reporter: new LogReporter($logger),
);
```

#### StdoutReporter

The `StdoutReporter` requires a `Backtrace` instance and will print out the issue message, the additional information and a text backtrace to `stdout`:

```php
<?php

$backtrace = new Backtrace(new InMemoryFrameIdentifiers());

$reporters->add(
    condition: new AlwaysReport(),
    reporter: new StdoutReporter($backtrace),
);
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

The `ErrorHandler` class captures all triggered PHP errors. The current implementation is only reporting the **deprecated** errors (E_DEPRECATED, E_USER_DEPRECATED) with the given `ReportHandler` instance. All other errors are converted to an `ErrorException` and are thrown back again so the default PHP exception handler can capture them.

### PHP Exception Handler

You can register a `ReportHandler` instance as the default PHP exception handler. See the [set_exception_handler()](https://www.php.net/manual/en/function.set-exception-handler.php) function in the PHP documentation for details.

Just like for the PHP errors, the package provides a pre-configured class that requires a `ReportHandler` instance and hook it up as the default PHP exception handler.
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

On a web context, you might want to render thrown exceptions in the browser, during development, to easily debug your application. The package provides a HTML template with a light and a dark theme. Here is an example on how to use the `ExceptionHandlerHtmlResponse` class, responsible to render a given `Issue` as HTML to the `stdout`:

```php
<?php

$reporters = new InMemoryReporters();
$reporters->add(
    condition: new AlwaysReport(), // You might want to constraint this for "local" and "web" environments
    reporter: new CallbackReporter(static function (Issue $issue) {
        $backtrace = new Backtrace(new InMemoryFrameIdentifiers());
        $backtrace->captureException($issue->exception());

        $information = new InMemoryInformation();
        $information->add(
            (new InformationGroup('General'))
                ->add(new TextInfo('PHP', phpversion()))
                ->add(new TextInfo('OS', php_uname('s')))
        );

        (new ExceptionHandlerHtmlResponse(
            backtrace: $backtrace,
            information: $information,
        ))->render($issue);
    }),
);

$reportHandler = new ReportHandler(
    reporters: $reporters,
    issues: new InMemoryIssues(),
);

set_exception_handler(new ExceptionHandler($reportHandler));
```

The above code snippet is attaching a `ReportHandler` to replace the default PHP exception handler. The report handler contains one reporter that is exposing any thrown exceptions to the standard output using HTML.

> The attached condition is to always report the issue to the stdout. On your application, make sure to constraint the condition to avoid rendering the error HTML template on a production environment.

Issue
-----

An `Issue` is the package abstract definition for any problem that could occur inside a PHP application. The `ReportHandler` is never directly dealing with a PHP error nor a PHP exception but with an `Issue` instance.

An issue contains the following properties:

- A message: the problem description text.
- A date: when the problem occurred.
- A level: the severity of the issue, following PSR-3 levels.
- An exception: for PHP compatibility, the issue should be able to be converted/translated to an exception.
- Information: any additional information about the issue.

In most PHP applications, developers have to deal with exceptions. The package provides the `ExceptionalIssue` class that can be used to translate an exception into an issue like so:

```php
<?php

$issue = ExceptionalIssue::create(new RuntimeException('Oops!'));
```

### Custom Issue

The package only provides a wrapper to handle PHP exceptions but you are free to build your own custom issue class by implementing the `Issue` interface.

As a reminder, the `ReportHandler` is not hooked by default to the default PHP error and exception handlers, so you're free to report any "domain" or "logic" issues in your project by leveraging the package Issue abstraction. A project can also contain multiple `ReportHandler` instances configured to report on separate issues. For example, you can configure a ReportHandler inside a specific application context/domain. 

Here is an example of a custom issue class:

```php
<?php

class OrderIssue implements Issue
{
    public function __construct(
        private Order $order,
    )
    {}

    public function message(): string
    {
        return sprintf('Order %s could not be processed, card refused.', $this->order->id());
    }

    public function date(): IssueDate
    {
        return new IssueDate($this->order->checkedOutAt());
    }

    public function level(): Level
    {
        return Level::critical;
    }

    public function exception(): Throwable
    {
        return new RuntimeException('Order not processed.');
    }

    public function info(): ?InformationGroup
    {
        return (new InformationGroup('Order'))
            ->add(new TextInfo('ID', $this->order->id()))
            ->add(new TextInfo('Card Last 4', $this->order->payment->last4()))
            ->add(new TextInfo('Customer', $this->order->customer->name()));
    }
}
```

The above snippet shows a very simple issue class that can be reused anywhere under the "Order" management domain perhaps...

The idea of abstracting the Issue is that this information can easily be used on multiple outputs:

- browser
- command line
- filesystem
- remote service
- ...

Information
-----------

When reporting an issue, the default behavior is to return the error message and possibly a backtrace to help developers understand the problem.

With the `themosis/error` package, we support an "Additional Information" feature to let developers pass more information and context to the issue.
The "Information" feature can also be translated to the PSR-3 logging `context` property.

The previous code snippet is showing the usage of the `InformationGroup` class on a custom issue. Additional information can be declared on an issue but it is also possible to declare additional context/information on a PHP exception class.

### On Exceptions

If you're hooking the [ReportHandler as the default PHP exception handler](#php-exception-handler), you can declare additional information on any PHP exception class and get it rendered by the `ExceptionHandlerHtmlResponse` class (default) but also by any configured reporters.

You can attach additional information on an exception class by implementing the `AdditionalInformation` interface and declare the `information()` method that should return an `InformationGroup` instance. The information group acts as a "collection" where you can pass any number of values. On the information group, you can call and chain the `add()` method which expects an `Info` instance to handler the passed value (the package comes with a simple `TextInfo` class for strings):

```php
<?php

class OrderFailed extends RuntimeException implements AdditionalInformation
{
    public function __construct(
        private Order $order,
        private Throwable $previous = null,
    )
    {
        parent::__construct('Order failed.', 0, $previous);
    }
     
    public function information(): InformationGroup
    {
        return (new InformationGroup('Order'))
            ->add(new TextInfo('Order #', $this->order->id()))
            ->add(new TextInfo('Transaction #', $this->order->transaction->id()));
    }
}
```

### On Issues

When building a custom issue class, the `Issue` interface lets you declare additional information and context by implementing the `info()` method. The difference here is that this is optional.

```php
<?php

class OrderIssue implements Issue
{
    // ...

    public function info(): ?InformationGroup
    {
        return (new InformationGroup('Order'))
            ->add(new TextInfo('Order #', $this->order->id()));
    }
}
```

Final output for an issue and its additional information will eventually end up as a "string" value. The package provides a default `TextInfo()` class to handle string info. Each info must have a "name" (first parameter) and a corresponding "value" (second parameter). Thanks to the `Info` interface, you are free to implement custom ways to "serialize" complex data structure for usage under an issue and how it will look as a string value.

Backtrace
---------

The `themosis/error` package also comes with a `Backtrace` class to let you easily manipulate the PHP debug backtrace frames using an object-oriented interface.

The `Backtrace` class can be used to capture frames from the [debug_backtrace()](https://www.php.net/debug_backtrace) function or frames coming from an exception.

Besides capturing debug frames, the `Backtrace` class can also filter out and tag frames. For example, you might want to remove all the frames belonging to files stored in the `vendor` directory. You might also want to tag (label) frames that belong to a specific domain, ...

> Our HTML error template support all features of the backtrace class and is able to show tagged frames in the browser.

### Capture Frames

The default behavior of the `Backtrace` class is to capture debug frames. The simplest version is by using the static `debug()` method like so:

```php
<?php

$backtrace = Backtrace::debug();
```

The above code snippet is capturing the debug frames from the file where it is currently invoked. Just like the `debug_backtrace()` PHP function, the `Backtrace` class is not printing to the stdout the captured frames.

The `Backtrace` class implements the `Stringable` interface, so you can just "echo" a backtrace instance to stdout:

```php
<?php

$backtrace = Backtrace::debug();

echo $backtace;
```

It also implements the magic `__debugInfo()` method so you dump the frames when passed to the `var_dump()` function:

```php
<?php

$backtrace = Backtrace::debug();

var_dump($backtrace);
```

#### Capture Frames Manually

You can build a custom backtrace instance and manually pass an array of frames to the `capture()` method:

```php
$backtrace = new Backtrace(
    frame_identifiers: new InMemoryFrameIdentifiers(),
);

$backtrace->capture(debug_backtrace());
```

A frame returned by PHP is generally an associative array containing the following keys:
- `function`
- `line`
- `file`
- `class`
- `object`
- `type`
- `args`

Depending on your use cases, you could probably build a custom frame to report on one of your
application error but a custom exception class is probably a better approach nonetheless.

#### Capture Exception Frames

The backtrace instance can also capture frames generated by a PHP exception instance.

Use the `captureException()` method on a backtrace instance like so:

```php
$backtrace = new Backtrace(
    frame_identifiers: new InMemoryFrameIdentifiers(),
);

$exception = new RuntimeException('Something is wrong...');

$backtrace->captureException($exception);
```

The `captureException()` will fill the backtrace with all the frames of the given exception.

### Get Frames

You can access the frames by calling the `frames()` method on a backtrace instance:

```php
<?php

$backtrace = Backtrace::debug();

$frames = $backtrace->frames();
```

### Tag Frames

It is possible to tag frames or more specifically, categorize the frames. When
you tag a frame, current implementation provides two benefits:

1. You can quickly see a tagged frame in the stdout exception handler.
2. You can filter the frames (ex.: show only application frames,...)

In order to tag frames, the Backtrace instance needs a way to "identify"
them. This is the role of the `frame_identifiers` backtrace dependency.

A backtrace instance requires a `FrameIdentifiers` instance. The
`FrameIdentifiers` is a repository interface to help you define your own way of
storing the frame identifiers.

By default, the package comes with an "in-memory" frame identifier repository
implementation under the `InMemoryFrameIdentifiers` class.

A repository acts like a "collection" that holds a list of single frame
identifiers. Each "frame identifier" declares its rules on how to identify a
given backtrace frame.

#### Frame identifier

The `FrameIdentifier` is an interface that allows you to build a range
of identifier utilities in order to categorize backtrace frames.

The `FrameIdentifier` exposes two methods:
1. The `tag()` method
2. The `identify()` method

The `tag()` method must return a `FrameTag` instance. A `FrameTag` is
an interface that provides methods to give a name to a backtrace frame
as well as providing the necessary information to handle it. There are
3 provided FrameTag with the package:

1. AppFrameTag: used to name your application based frames.
2. VendorFrameTag: used to name the frames coming from a vendor.
3. CustomFrameTag: simple wrapper to let you build custom frame tags.

The `AppFrameTag` will assign the label "Application" to a frame. The
`VendorFrameTag` will apply the "Vendor" label to a frame.

You can create a custom frame tag by either building your own class,
if you need specific requirements... or leverage the "CustomFrameTag"
and pass it a slug (used for filtering for example) and label for
display like so:

```php
<?php

$tag = new CustomFrameTag('marketing', 'Marketing Feature');
```

The `identifier()` method receives a `Frame` instance as an
argument. The method is evaluating the frame and should return a
boolean value to indicate if current backtrace frame is identified. If
so, it will apply the attach frame tag for example.

There are 2 frame identifiers classes provided by the package:
1. The `VendorFrameIdentifier` class
2. The `CustomFrameIdentifier` class

The former class is a built-in class to tag any vendor frames (backtrace
frames pointing to the project "vendor" directory) while the later is
one you can leverage to define your own identifiers:

```php
<?php

$identifier = new CustomFrameIdentifier(
	tag: new CustomFrameTag('library', 'Library'),
	identifier: function (Frame $frame) {
		return str_contains($frame->getFile()->path(), 'lib');
	},
);
```

The above code snippet will apply the "Library" label on a frame where
is path may contain the string `lib`.

#### Example

The following code snippet shows how to configure frame identifiers
for usage with a backtrace instance:

```php
<?php

$identifiers = new InMemoryFrameIdentifiers();

// Tag vendor frames using built-in class.
$identifiers->add(
	identifier: new VendorFrameIdentifier(
		projectRootPath: dirname(__DIR__, 2),
	),
);

// Apply built-in AppFrameTag on frames coming from the "app" directory.
$identifiers->add(
	identifier: new CustomFrameIdentifier(
		tag: new AppFrameTag(),
		identifier: function (Frame $frame) {
			return str_contains($frame->getFile()->path, 'app');
		},
	),
);

$backtrace = new Backtrace(
	frameIdentifiers: $identifiers,
);
```

### Filter Frames

It is also possible to filter out frames from the Backtrace instance. For
example, it is possible to remove all vendor frames from the Backtrace
in order to focus on those from your core application.

The backtrace instance has a `filter()` method that accepts a callable
as an argument. The provided callable receives a `Frame` instance as
an argument.

The use of the `filter()` is non-destructive. Calling the `filter()`
is returning a new backtrace instance that captures the filtered
frames only.

Here is an exaple on how to use the filter method:

```php
<?php

$backtrace = new Backtrace(
	frameIdentifiers: new InMemoryIdentifiers(),
);

$appBacktrace = $backtrace->filter(function (Frame $frame) {
	if ($frame->getFunction() instanceof ClassFunction) {
		return $frame->getFunction()->getClass() === '\Core\Application';
	}
	
	return false;
});
```

