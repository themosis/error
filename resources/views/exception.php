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

            --color-blue-100: rgb(251, 253, 255);
            --color-blue-200: rgb(241, 243, 245);
            --color-blue-300: rgb(230, 245, 255);
            --color-blue-400: rgb(155, 190, 195);
            --color-blue-500: rgb(0, 123, 255);
            --color-blue-700: rgb(0, 79, 168);
            --color-blue-800: rgb(24, 78, 116);
            --color-blue-900: rgb(38, 49, 68);

            --color-red-100: rgb(255, 240, 235);
            --color-red-200: rgb(250, 224, 212);
            --color-red-800: rgb(125, 30, 10);

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

        .section:target {
            background: linear-gradient(to bottom right, var(--color-gray-300), var(--color-blue-300));
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
            background: var(--color-white);
            line-height: 1.625;
        }

        .line:nth-of-type(even) {
            background: var(--color-gray-300);
        }

        .line:hover {
            background: var(--color-blue-300);
        }

        .current-line,
        .line.current-line {
            background: var(--color-yellow-500);
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
            border-right: 1px solid var(--color-blue-500);
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
            display: inline-block;
            background: var(--color-blue-300);
            color: var(--color-blue-900);
            border-radius: var(--radius-sm);
            padding: var(--space-xs) var(--space-sm);
            word-break: break-all;
        }

        .frame-function {
            background: var(--color-blue-900);
            color: var(--color-white);
        }

        .infos {
            margin-top: var(--space-md);
        }

        .info {
            margin-top: 0;
            margin-bottom: 0;
            margin-left: calc(-1 * var(--space-sm));
            margin-right: calc(-1 * var(--space-sm));
            display: block;
            padding: var(--space-sm);
            border-radius: var(--radius);
        }

        .info:hover {
            background: var(--color-blue-100);
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

            .information {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                column-gap: var(--space-md);
            }

            .info {
                margin-top: var(--space-sm);
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
                        <a href="#general" title="Go to general">General</a>
                    </li>
                    <li>
                        <a href="#request" title="Go to request">Request</a>
                    </li>
                    <li>
                        <a href="#git" title="Go to git">Git</a>
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
                        <?= $preview(
                        $render_preview = fn (string $lines) => <<<PREVIEW
                            <div class="preview">
                                <pre>
                                    <code>{$lines}</code>
                                </pre>
                            </div>
                        PREVIEW,
                        $render_preview_line = fn (string $class_name, int $length, int $number, string $line) => <<<LINE
                            <span class="line {$class_name}"><span class="line-number" style="min-width: calc(10px * {$length});">{$number}</span><span class="line-content">{$line}</span></span>
                        LINE); ?>
                    </div>
                    <?= $frames(
                        fn(string $frames) => <<<BACKTRACE
                            <div id="backtrace">{$frames}</div>
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
                        $render_preview,
                        $render_preview_line
                    ); ?>
                </section>
                <div class="information">
                    <section id="general" class="section">
                        <h2 class="section-title">General</h2>
                        <div class="infos">
                            <dl class="info">
                                <dt class="info-key">PHP</dt>
                                <dd class="info-value"><pre>8.2.13</pre></dd>
                            </dl>
                            <dl class="info">
                                <dt class="info-key">Occured At</dt>
                                <dd class="info-value"><pre>02/10/2024</pre></dd >
                            </dl>
                            <dl class="info">
                                <dt class="info-key">Timezone</dt>
                                <dd class="info-value"><pre>UTC</pre></dd >
                            </dl>
                        </div>
                    </section>
                    <section id="request" class="section">
                        <h2 class="section-title">Request</h2>
                        <div class="infos">
                            <dl class="info">
                                <dt class="info-key">Method</dt>
                                <dd class="info-value"><pre>GET</pre></dd>
                            </dl>
                            <dl class="info">
                                <dt class="info-key">Path</dt>
                                <dd class="info-value"><pre>/</pre></dd>
                            </dl>
                            <dl class="info">
                                <dt class="info-key">Query</dt>
                                <dd class="info-value"><pre>&nbsp;</pre></dd >
                            </dl>
                            <dl class="info">
                                <dt class="info-key">Headers</dt>
                                <dd class="info-value"><pre>Accept: text/html<br>User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_17)<br>X-Forwarded-For: 127.0.0.1<br>X-Forwarded-Host: localhost<br>X-Forwarded-Proto: http</pre></dd>
                            </dl>
                        </div>
                    </section>
                    <section id="git" class="section">
                        <h2 class="section-title">Git</h2>
                        <div class="infos">
                            <dl class="info">
                                <dt class="info-key">Branch</dt>
                                <dd class="info-value"><pre>fix/error-exception-renderer</pre></dd>
                            </dl>
                            <dl class="info">
                                <dt class="info-key">User</dt>
                                <dd class="info-value"><pre>jlambe</pre></dd >
                            </dl>
                        </div>
                    </section>
                </div>
            </div>
        </main>
        <!-- End Main -->
    </div>
</body>
</html>
