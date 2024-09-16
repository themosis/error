<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exception</title>
    <style type="text/css">
        :root {
            --color-white: #ffffff;

            --color-gray-100: rgb(252, 253, 253);

            --color-primary-100: rgb(251, 253, 255);
            --color-primary-200: rgb(241, 243, 245);
            --color-primary-500: rgb(0, 123, 255);
            --color-primary-700: rgb(0, 79, 168);
            --color-primary-800: rgb(24, 78, 116);
            --color-primary-900: rgb(38, 49, 68);

            --color-secondary-100: rgb(252, 250, 248);
            --color-secondary-200: rgb(249, 244, 245);

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
            background-color: var(--color-primary-900);
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
            background: var(--color-primary-900);
            color: var(--color-white);
            text-align: center;
            text-decoration: none;
            padding-top: var(--space-sm);
            padding-bottom: var(--space-sm);
            border-radius: var(--radius-sm);
        }

        .nav li a:hover {
            background: var(--color-primary-700);
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
            background-color: var(--color-secondary-200);
            border-radius: var(--radius);
            padding: var(--space-md);
            margin-bottom: var(--space-md);
        }

        .section-title {
            display: inline-block;
            background: var(--color-primary-700);
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
            color: var(--color-primary-900);
        }

        .file {
            font-size: 1.25rem;
            margin-top: var(--space-sm);
            margin-bottom: var(--space-lg);
            color: var(--color-primary-800);
        }

        .frame {
            background: var(--color-white);
            border-radius: var(--radius);
            padding: var(--space-sm);
            margin-bottom: var(--space-sm);
        }

        .frame:hover {
            background: var(--color-primary-100);
        }

        .frame:last-of-type {
            margin-bottom: 0;
        }

        .frame span {
            display: inline-block;
            background: var(--color-primary-900);
            color: var(--color-white);
            border-radius: var(--radius-sm);
            padding: var(--space-xs) var(--space-sm);
            word-break: break-all;
        }

        .frame p {
            font-size: 1.125rem;
            color: var(--color-primary-800);
            margin-top: var(--space-sm);
            margin-bottom: 0;
        }

        .infos {
            padding-top: var(--space-md);
        }

        .info {
            background: var(--color-white);
            color: var(--color-primary-900);
            padding-top: var(--space-sm);
            padding-bottom: var(--space-sm);
            border-radius: var(--radius);
        }

        .info-key {
            background: var(--color-primary-900);
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
            .section {
                padding: var(--space-lg);
            }
        }

        @media screen and (min-width: 1024px) {
            :root {
                --sidebar-width: 80px;
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

            .wrapper {
                width: 80%;
                margin-left: auto;
                margin-right: auto;
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
                    <h2 class="section-title"><?= $exception_class ?></h2>
                    <p class="message"><?= $message ?></p>
                    <p class="file"><?= $file ?></p>
                    <?php $frames(
                        fn(string $frames) => <<<BACKTRACE
                            <div class="backtrace">{$frames}</div>
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
