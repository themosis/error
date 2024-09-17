<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <style type="text/css">
        :root {
            --color-white: #ffffff;

            --color-gray-100: rgb(252, 253, 253);
            --color-gray-300: rgb(240, 243, 245);
            --color-gray-600: rgb(121, 123, 127);

            --color-blue-100: rgb(251, 253, 255);
            --color-blue-200: rgb(241, 243, 245);
            --color-blue-300: rgb(230, 245, 255);
            --color-blue-500: rgb(0, 123, 255);
            --color-blue-700: rgb(0, 79, 168);
            --color-blue-800: rgb(24, 78, 116);
            --color-blue-900: rgb(38, 49, 68);

            --color-red-100: rgb(255, 240, 235);
            --color-red-200: rgb(250, 224, 212);

            --color-yellow-500: rgb(255, 226, 115);

            --space-none: 0;
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1.25rem;
            --space-lg: 2.5rem;
            --space-xl: 3.75rem;

            --radius-sm: 0.1875rem;
            --radius: 0.3125rem;

            --sidebar-width: 44px;
        }

        body {
            background-color: var(--color-gray-100);
            margin: var(--space-sm);
            font-family: Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif;
            font-size: 1rem;
        }

        h1,
        h2,
        h3 {
            margin-top: 0;
        }

        h2 {
            font-size: 1.5em;
            font-weight: bold;
        }

        #page {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-items: stretch;
            width: 100%;
        }

        #sidebar {
            position: fixed;
            z-index: 100;
            background-color: var(--color-blue-900);
            height: calc(100% - (var(--space-sm) * 2));
            width: var(--sidebar-width);
            flex: 0 0 auto;
            border-radius: var(--radius);
        }

        .nav {
            list-style: none;
            padding: var(--space-sm);
            margin: 0;
            display: block;
        }

        .nav li {
            display: block;
            margin-bottom: var(--space-sm);
        }

        .nav li a,
        .nav li a:link,
        .nav li a:visited {
            display: block;
            background: var(--color-blue-900);
            color: var(--color-white);
            text-align: center;
            text-decoration: none;
            padding-top: var(--space-sm);
            padding-bottom: var(--space-sm);
            border-radius: var(--radius-sm);
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav li a:hover {
            background: var(--color-blue-700);
            color: var(--color-white);
        }

        #main {
            position: relative;
            padding-left: calc(var(--sidebar-width) + var(--space-sm));
            width: 100%;
        }

        .wrapper {
            width: 100%;
        }

        #issue {
            background: var(--color-red-100);
            padding: 0;
        }

        #exception {
            padding: var(--space-md);
        }

        .section {
            background: var(--color-gray-300);
            border-radius: var(--radius);
            padding: var(--space-md);
            margin-bottom: var(--space-md);
        }

        .section-title {
            display: inline-block;
            background: var(--color-blue-700);
            color: var(--color-white);
            padding: var(--space-sm);
            font-size: 1rem;
            border-radius: var(--radius);
            margin: 0;
            font-weight: 400;
        }

        .message {
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1.1;
            margin-top: var(--space-md);
            margin-bottom: var(--space-sm);
            color: var(--color-blue-900);
        }

        .file {
            font-size: 1.25rem;
            margin-top: var(--space-sm);
            margin-bottom: var(--space-md);
            color: var(--color-blue-800);
        }

        .preview {
            color: var(--color-blue-900);
            overflow: hidden;
            background: var(--color-white);
            border-radius: var(--radius);
            padding: 0 var(--space-sm);
        }

        .preview pre {
            margin: 0;
            white-space: collapse;
        }

        .preview code {
            word-wrap: break-word;
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1;
        }

        .line-number {
            padding-right: var(--space-sm);
            margin-right: var(--space-sm);
            color: var(--color-gray-600);
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border-right: 1px solid var(--color-blue-700);
        }

        .current-line {
            background: var(--color-yellow-500);
            display: inline;
            border-radius: var(--radius-sm);
            padding: 0 var(--space-xs);
        }

        #backtrace {
            padding: var(--space-md);
            border-top: 1px solid var(--color-red-200);
        }

        .frame {
            background: var(--color-white);
            border-radius: var(--radius);
            padding: var(--space-sm);
            margin-bottom: var(--space-sm);
        }

        .frame:hover {
            background: var(--color-blue-300);
        }

        .frame:last-of-type {
            margin-bottom: 0;
        }

        .frame span {
            display: inline-block;
            background: var(--color-blue-900);
            color: var(--color-white);
            border-radius: var(--radius-sm);
            padding: var(--space-xs) var(--space-sm);
            word-break: break-all;
        }

        .frame p {
            font-size: 1.125rem;
            color: var(--color-blue-800);
            margin-top: var(--space-sm);
            margin-bottom: 0;
        }

        .infos {
            padding-top: var(--space-md);
        }

        .info {
            background: var(--color-white);
            color: var(--color-blue-900);
            padding-top: var(--space-sm);
            padding-bottom: var(--space-sm);
            border-radius: var(--radius);
        }

        .info-key {
            background: var(--color-blue-900);
            color: var(--color-white);
            padding: var(--space-sm);
            border-radius: var(--radius);
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .info-value {
            padding: var(--space-sm);
        }

        @media screen and (min-width: 640px) {
            :root {
                --sidebar-width: 80px;
            }

            .section {
                padding: var(--space-lg);
            }

            #exception,
            #backtrace {
                padding: var(--space-lg);
            }
        }

        @media screen and (min-width: 1024px) {
            body {
                margin: var(--space-md);
            }

            #main {
                padding-left: calc(var(--sidebar-width) + var(--space-md));
            }

            #sidebar {
                height: calc(100% - (var(--space-md) * 2));
            }

            .wrapper {
                width: 80%;
                margin-left: auto;
                margin-right: auto;
            }

            .section-title {
                padding: var(--space-sm) var(--space-md);
            }
        }
    </style>
</head>
<body>
    <div id="page">
        <!-- Sidebar -->
        <aside id="sidebar">
            <nav>
                <ul class="nav">
                    <li>
                        <a href="#issue" title="Go to issue">Issue</a>
                    </li>
                    <li>
                        <a href="#infos" title="Go to additional information">Infos</a>
                    </li>
                </ul>
            </nav>
        </aside>
        <!-- End Sidebar -->
        <!-- Main -->
        <main id="main">
            <div class="wrapper">
                <section id="issue" class="section">
                    <div id="exception">
                        <h2 class="section-title"><?= $exception_class ?></h2>
                        <p class="message"><?= $message ?></p>
                        <p class="file"><?= $file ?></p>
                        <div class="preview">
                            <pre>
                                <code>
<span class="line-number">1</span><span><?php echo htmlentities('<?php'); ?></span>
<span class="line-number">2</span>
<span class="line-number">3</span><span class="current-line">throw new Exception('There was an error when calling the checkout client.', previous: new RuntimeException('Oops!'));</span>
<span class="line-number">4</span>
                                </code>
                            </pre>
                        </div>
                    </div>
                    <?php $frames(
                        fn(string $frames) => <<<BACKTRACE
                            <div id="backtrace">{$frames}</div>
                        BACKTRACE)(
                        fn(string $function, string $file) => <<<FRAME
                            <div class="frame">
                                <span>{$function}</span>
                                <p>{$file}</p>
                            </div>
                        FRAME); ?>
                </section>
                <section id="infos" class="section">
                    <h2 class="section-title">Additional Information</h2>
                    <div class="infos">
                        <div class="info">
                            <span class="info-key">PHP</span>
                            <span class="info-value">8.2.13</span>
                        </div>
                    </div>
                </section>
            </div>
        </main>
        <!-- End Main -->
    </div>
</body>
</html>
