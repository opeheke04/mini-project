<?php

namespace App\Services;

use Mike42\Escpos\PrintConnectors\PrintConnector;
use RuntimeException;

class SharedWindowsPrintConnector implements PrintConnector
{
    private array $buffer = [];

    private bool $finalized = false;

    public function __construct(private string $shareName)
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            throw new RuntimeException('The shared Windows printer connector requires Windows.');
        }

        if ($shareName === '' || strpbrk($shareName, "/\\\0") !== false) {
            throw new RuntimeException('The Windows printer share name is invalid.');
        }
    }

    public function __destruct()
    {
        if (! $this->finalized) {
            trigger_error('Print connector was not finalized. Did you forget to close the printer?', E_USER_NOTICE);
        }
    }

    public function finalize(): void
    {
        if ($this->finalized) {
            return;
        }

        $this->finalized = true;
        $tempFile = tempnam(sys_get_temp_dir(), 'escpos');
        if ($tempFile === false) {
            throw new RuntimeException('Unable to create a temporary file for the printer job.');
        }

        try {
            $data = implode('', $this->buffer);
            if (file_put_contents($tempFile, $data) === false) {
                throw new RuntimeException('Unable to write the printer job to a temporary file.');
            }

            $host = gethostname() ?: 'localhost';
            $destination = '\\\\'.$host.'\\'.$this->shareName;
            if (! copy($tempFile, $destination)) {
                throw new RuntimeException("Unable to send the printer job to the Windows share [{$this->shareName}].");
            }
        } finally {
            if (is_file($tempFile) && ! unlink($tempFile)) {
                throw new RuntimeException('Unable to remove the temporary printer job file.');
            }
        }
    }

    public function read($len): false
    {
        return false;
    }

    public function write($data): void
    {
        if ($this->finalized) {
            throw new RuntimeException('Cannot write to a finalized printer connector.');
        }

        $this->buffer[] = $data;
    }
}
