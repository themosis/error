<?php

namespace Themosis\Components\Error;

final class InMemoryReporters implements Reporters
{
    public function add(ReporterKey $key, Reporter $reporter): void { }

    public function find(ReporterKey $key): Reporter {
        return new class() implements Reporter {
            public function report(): void
            {
                
            }
        };
    }
}
