<?php

namespace MetaTemplate\Template;

use Symfony\Component\Process\Process,
    Symfony\Component\Process\ExecutableFinder;

class TypeScriptTemplate extends Base
{
    const DEFAULT_TSC = "tsc";

    static function getDefaultContentType()
    {
        return "application/javascript";
    }

    function render($context = null, $vars = array())
    {
        $finder = new ExecutableFinder;
        $bin = @$this->options["tsc_bin"] ?: self::DEFAULT_TSC;

        $cmd = $finder->find($bin);

        if ($cmd === null) {
            throw new \UnexpectedValueException(
                "'$bin' executable was not found. Make sure it's installed."
            );
        }

        $inputFile = tempnam(sys_get_temp_dir(), 'pipe_typescript_input_');
        chmod($inputFile, 0666);
        file_put_contents($inputFile, $this->getData());

        $outputFile = tempnam(sys_get_temp_dir(), 'pipe_typescript_output_');
        chmod($outputFile, 0666);

        $cmd .= " --out " . escapeshellarg($outputFile) . ' ' . escapeshellarg($inputFile);

        $process = new Process($cmd);

        $process->setEnv(array(
            'PATH' => @$_SERVER['PATH'] ?: join(PATH_SEPARATOR, array("/bin", "/sbin", "/usr/bin", "/usr/local/bin", "/usr/local/share/npm/bin"))
        ));

        if ($this->isFile()) {
            $process->setWorkingDirectory(dirname($this->source));
        }

        $process->run();

        $data = file_get_contents($outputFile);

        unlink($outputFile);
        unlink($inputFile);

        if ($process->getErrorOutput()) {
            throw new \RuntimeException(
                "tsc({$this->source}) returned an error:\n {$process->getErrorOutput()}"
            );
        }

        return $data;
    }
}
