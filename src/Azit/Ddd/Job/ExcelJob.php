<?php

namespace Azit\Ddd\Job;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExcelJob implements ShouldQueue {
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $basename;
    protected string $extension;
    protected string $filename;
    protected object $exporter;

    /**
     * Parametros que requiere el excel job
     * @param object $exporter
     * @param string $basename
     * @param string $extension
     */
    public function __construct(object $exporter, string $basename, string $extension) {
        $this -> exporter = $exporter;
        $this -> basename = $basename;
        $this -> extension = $extension;
        $this -> filename = $basename . Str::start($this->extension, '.');
    }

    public function handle() {
        Excel::store($this->exporter, $this -> filename);
    }

}