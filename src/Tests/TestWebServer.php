<?php

namespace Stormmore\Framework\Tests;

class TestWebServer
{
    private static $instances = array();

    private $process;

    public function __construct(private readonly string $directory, private readonly int $port)
    {
    }

    public function run(): void
    {
        $directory = realpath($this->directory);
        $key = $directory . $this->port;
        if (!$directory or array_key_exists($key, self::$instances)) {
            return;
        }
        self::$instances[$key] = $this;

        $cwd = getcwd();
        chdir($this->directory);

        $pipes = [];
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        $cmd = "php -S localhost:{$this->port}";
        $this->process = proc_open($cmd, $descriptors, $pipes);
        if (is_resource($this->process)) {
           fclose($pipes[1]);
           fclose($pipes[2]);
           fclose($pipes[0]);
        }

        chdir($cwd);
    }

    public function shutdown(): void
    {
        if ($this->process) {
            $os = php_uname('s');
            $isWindows = str_contains(strtolower($os), 'win');
            $status = proc_get_status($this->process);
            $pid = $status['pid'];
            $isWindows  ? exec("taskkill /F /T /PID $pid") : exec("kill -9 $pid");
            proc_close($this->process);
            $this->process = null;
        }
    }

    function __destruct()
    {
        $this->shutdown();
    }
}