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
2. [Backtrace](#backtrace)

Report Handler
--------------

The `ReportHandler` is the main component of the package, it acts as the aggregate that manages how you can report your application errors and exceptions.

The `ReportHandler` class requires two dependencies:

1. A collection of reporters
2. A collection of issues

A `Reporter` is an interface that represents any PHP class that can report an `Issue`. For example a reporter can report an issue to the standard output (stdout), or report the issue in a log file, or send the issue to a third-party service, ... The choice is yours. You can register as many reporters as you want. It is also possible to configure in which context a reporter should act against the given issue.

An `Issue` is an interface that represents an error or any custom problem in your application that you want to report.

