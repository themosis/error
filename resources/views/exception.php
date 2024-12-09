<!--
SPDX-FileCopyrightText: 2024 Julien Lambé <julien@themosis.com>

SPDX-License-Identifier: GPL-3.0-or-later
-->
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
            --color-gray-200: rgb(246, 249, 251);
            --color-gray-300: rgb(240, 243, 245);
            --color-gray-500: rgb(141, 153, 155);
            --color-gray-600: rgb(121, 123, 127);
            --color-gray-800: rgb(60, 65, 65);
            --color-gray-900: rgb(30, 35, 35);

            --color-blue-100: rgb(251, 253, 255);
            --color-blue-200: rgb(241, 243, 245);
            --color-blue-300: rgb(230, 245, 255);
            --color-blue-400: rgb(155, 190, 195);
            --color-blue-500: rgb(0, 123, 255);
            --color-blue-700: rgb(0, 79, 168);
            --color-blue-800: rgb(24, 78, 116);
            --color-blue-900: rgb(38, 49, 68);

            --color-red-100: hsl(15, 100%, 96%);
            --color-red-200: hsl(19, 79%, 91%);
            --color-red-300: hsl(345, 100%, 96%);
            --color-red-700: hsl(10, 85%, 26%);
            --color-red-800: hsl(342, 38%, 25%);
            --color-red-900: hsl(323, 36%, 20%);

            --color-yellow-300: rgb(255, 244, 200);
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

            color-scheme: light dark;
        }

        body {
            --_body-bg: var(--body-bg, var(--color-gray-100));
            background-color: var(--_body-bg);
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
            --_sidebar-bg: var(--sidebar-bg, var(--color-blue-900));
            --_sidebar-width: var(--sidebar-width);
            position: fixed;
            z-index: 100;
            background-color: var(--_sidebar-bg);
            height: calc(100% - (var(--space-sm) * 2));
            width: var(--_sidebar-width);
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


        .section {
            --_section-bg: var(--section-bg, var(--color-gray-300));
            background: var(--_section-bg);
            border-radius: var(--radius);
            padding: var(--space-md);
            margin-bottom: var(--space-md);
        }

        .section:target {
            --section-bg: linear-gradient(to bottom right, var(--color-gray-300), var(--color-blue-300));
        }

        #issue .section {
            --_gradient-1: hsl(15, var(--gradient-1-saturation, 100%), 96%);
            --_gradient-2: hsl(345, var(--gradient-2-saturation, 100%), 96%);

            --section-bg: linear-gradient(to bottom right, var(--_gradient-1) 30%, var(--_gradient-2));
        }

        #issue .section:nth-of-type(2) {
            --gradient-1-saturation: 80%;
            --gradient-2-saturation: 80%;
        }
        
        #issue .section:nth-of-type(3) {
            --gradient-1-saturation: 60%;
            --gradient-2-saturation: 60%;
        }
        
        #issue .section:nth-of-type(4) {
            --gradient-1-saturation: 40%;
            --gradient-2-saturation: 40%;
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
            word-break: break-word;
        }

        .file {
            font-size: 1.25rem;
            margin-top: var(--space-sm);
            margin-bottom: var(--space-lg);
            color: var(--color-blue-800);
        }

        .preview {
            color: var(--color-blue-900);
            overflow: hidden;
            background: var(--color-white);
            border-radius: var(--radius);
            padding: 0;
        }

        .preview pre {
            margin: 0;
            white-space: collapse;
        }

        .preview code {
            word-wrap: anywhere;
        }

        .line {
            display: inline-block;
            width: 100%;
            background: var(--color-gray-100);
            line-height: 1.625;
        }

        .line:nth-of-type(even) {
            background: var(--color-gray-100);
        }

        .line:hover {
            background: var(--color-blue-300);
        }

        .current-line,
        .line.current-line {
            background: var(--color-yellow-300);
        }

        .line-number {
            display: inline-block;
            padding-left: var(--space-sm);
            padding-right: var(--space-sm);
            color: var(--color-gray-500);
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border-right: 1px solid var(--color-blue-400);
            text-align: right;
        }

        .current-line .line-number {
            color: var(--color-red-800);
        }

        .line:nth-of-type(even) .line-number {
            color: var(--color-gray-600);
        }

        .line-content {
            position: relative;
            padding-left: var(--space-sm);
            padding-right: var(--space-sm);
            z-index: 1;
        }

        .current-line .line-content {
            color: var(--color-red-800);
        }

        .backtrace {
            padding-top: var(--space-lg);
        }

        .frame {
            background: var(--color-white);
            border-radius: var(--radius);
            padding: var(--space-sm);
            margin-bottom: var(--space-sm);
        }

        .frame:hover {
            background: var(--color-blue-100);
        }

        .frame:last-of-type {
            margin-bottom: 0;
        }

        .frame p {
            font-size: 1.125rem;
            color: var(--color-blue-800);
            margin-top: var(--space-sm);
            margin-bottom: 0;
        }

        .frame summary {
            list-style: none;
            border: none;
            cursor: pointer;
        }

        .frame summary::-webkit-details-marker {
            display: none;
        }

        .frame .preview {
            margin-top: var(--space-md);
        }

        .frame-identifiers {
            display: flex;
            justify-content: flex-start;
            align-items: stretch;
            width: 100%;
            gap: var(--space-sm);
        }

        .frame-identifier {
            --_identifier-bg: var(--identifier-bg, var(--color-blue-300));
            --_identifier-color: var(--identifier-color, var(--color-blue-900));
            display: inline-block;
            background: var(--_identifier-bg);
            color: var(--_identifier-color);
            border-radius: var(--radius-sm);
            padding: var(--space-xs) var(--space-sm);
            word-break: break-all;
        }

        .frame-function {
            --identifier-bg: var(--color-blue-900);
            --identifier-color: var(--color-white);
        }

        .infos {
            margin-top: var(--space-md);
        }

        .info {
            margin-top: 0;
            margin-bottom: 0;
            display: block;
            padding: var(--space-sm) 0;
        }

        .info-key {
            color: var(--color-blue-900);
            border-bottom: 1px solid var(--color-blue-400);
            padding-bottom: var(--space-sm);
            font-weight: 700;
        }

        .info-value {
            padding: var(--space-sm) 0;
            margin-left: 0;
        }

        .info-value pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
        }

        @media screen and (min-width: 640px) {
            :root {
                --sidebar-width: 80px;
            }

            .section {
                padding: var(--space-lg);
            }
        }

        @media screen and (min-width: 1024px) {
	    :root {
		--sidebar-width: 128px;
            }

            body {
                margin: var(--space-md);
            }

            #main {
                padding-left: calc(var(--sidebar-width) + var(--space-md));
            }

            #sidebar {
                height: calc(100% - (var(--space-md) * 2));
            }

            .nav li a,
            .nav li a:link,
            .nav li a:visited {
                padding: var(--space-sm);
            }

            .wrapper {
                width: 80%;
                margin-left: auto;
                margin-right: auto;
            }

            .section-title {
                padding: var(--space-sm) var(--space-md);
            }

            .information {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(40%, 1fr));
                column-gap: var(--space-md);
            }

            .info {
                margin-top: var(--space-sm);
                margin-left: calc(-1 * var(--space-sm));
                margin-right: calc(-1 * var(--space-sm));
                padding: var(--space-sm);
                border-radius: var(--radius);
            }

            .info:hover {
                background: var(--color-blue-100);
            }
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --body-bg: var(--color-gray-900);
                --section-bg: var(--color-gray-800);
            }
            
            #issue .section {
                --_gradient-1: hsl(342, var(--gradient-1-saturation, 38%), 25%);
                --_gradient-2: hsl(323, var(--gradient-2-saturation, 36%), 20%);

                --section-bg: linear-gradient(to bottom right, var(--_gradient-1) 30%, var(--_gradient-2));
            }

            #issue .section:nth-of-type(2) {
                --gradient-1-saturation: 28%;
                --gradient-2-saturation: 26%;
            }
            
            #issue .section:nth-of-type(3) {
                --gradient-1-saturation: 18%;
                --gradient-2-saturation: 16%;
            }
            
            #issue .section:nth-of-type(4) {
                --gradient-1-saturation: 8%;
                --gradient-2-saturation: 6%;
            }

            .section:target {
                --section-bg: linear-gradient(to bottom right, var(--color-gray-800), var(--color-blue-800));
            }
            
            .message {
                color: var(--color-blue-100);
            }

            .file {
                color: var(--color-blue-300);
            }

            .preview {
                color: var(--color-blue-100);
                background: var(--color-blue-900);
            }

            .line {
                background: var(--color-blue-800);
            }

            .line:nth-of-type(even) {
                background: var(--color-blue-800);
            }

            .line:hover {
                background: var(--color-blue-700);
            }

            .current-line,
            .line.current-line {
                background: var(--color-yellow-300);
            }

            .line-number {
                color: var(--color-gray-100);
            }

            .line:nth-of-type(even) .line-number {
                color: var(--color-gray-300);
            }

            .current-line .line-number, 
            .current-line:nth-of-type(even) .line-number {
                color: var(--color-red-800);
            }

            .frame {
                background: var(--color-blue-800);
            }

            .frame:hover {
                background: var(--color-blue-700);
            }

            .frame p {
                color: var(--color-white);
            }

            .frame-function {
                --identifier-bg: var(--color-gray-900);
                --identifier-color: var(--color-blue-300);
            }

            .frame .line {
                background: var(--color-blue-900);
            }

            .frame .line:nth-of-type(even) {
                background: var(--color-blue-800);
            }

            .frame .current-line,
            .frame .line.current-line {
                background: var(--color-yellow-300);
            }

            .frame .current-line .line-number, 
            .frame .current-line:nth-of-type(even) .line-number {
                color: var(--color-red-800);
            }

            .info-key {
                color: var(--color-blue-100);
            }

            .info:hover {
                background: var(--color-gray-900);
            }
        }
    </style>
</head>
<body>
    <div id="page">
        <aside id="sidebar">
            <nav>
                <ul class="nav">
                    <?= $navigation(
                        fn (string $id, string $title) => <<<NAV
                            <li>
                                <a href="#{$id}" title="Go To: {$title}">{$title}</a>
                            </li>
                        NAV,
                    ) ?>
                </ul>
            </nav>
        </aside>
        <main id="main">
            <div class="wrapper">
                <section id="issue"><?= $issues(
                    fn (string $exceptionClass, string $message, string $file, string $preview, string $backtrace) => <<<ISSUE
                        <div class="section">
                            <div class="issue">
                                <h2 class="section-title">{$exceptionClass}</h2>
                                <p class="message">{$message}</p>
                                <p class="file">{$file}</p>
                                {$preview}
                                {$backtrace}
                            </div>
                        </div>
                    ISSUE,
                    fn (string $lines) => <<<PREVIEW
                        <div class="preview">
                            <pre>
                                <code>{$lines}</code>
                            </pre>
                        </div>
                    PREVIEW,
                    fn (string $className, int $length, int $number, string $line) => <<<LINE
                        <span class="line {$className}"><span class="line-number" style="min-width: calc(10px * {$length});">{$number}</span><span class="line-content">{$line}</span></span>
                    LINE,
                    fn(string $frames) => <<<BACKTRACE
                        <div class="backtrace">{$frames}</div>
                    BACKTRACE,
                    fn(string $function, string $file, string $tags, string $preview) => <<<FRAME
                        <details name="backtrace" class="frame">
                            <summary>
                                <div class="frame-identifiers">
                                    <span class="frame-identifier frame-function">{$function}</span>
                                    {$tags}
                                </div> 
                                <p>{$file}</p>
                            </summary>
                            {$preview}
                        </details>
                    FRAME,
                    fn(string $tagname) => <<<TAG
                        <span class="frame-identifier">{$tagname}</span>
                    TAG,
                ) ?></section>
                <?= $information(
                    fn (string $infogroups) => <<<INFORMATION
                        <section class="information">{$infogroups}</section>
                    INFORMATION,
                    fn (string $slug, string $title, string $infos) => <<<INFOGROUP
                        <div id="{$slug}" class="section">
                            <h2 class="section-title">{$title}</h2>
                            <div class="infos">{$infos}</div>
                        </div>
                    INFOGROUP,
                    fn (string $label, string $value) => <<<INFO
                        <dl class="info">
                            <dt class="info-key">{$label}</dt>
                            <dd class="info-value"><pre>{$value}</pre></dd >
                        </dl>
                    INFO,
                ); ?>
            </div>
        </main>
    </div>
</body>
</html>
