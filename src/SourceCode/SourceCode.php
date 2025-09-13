<?php

namespace Stormmore\Framework\SourceCode;

use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\SourceCode\Scanners\ClassScanner;
use Stormmore\Framework\SourceCode\Scanners\CommandHandlerScanner;
use Stormmore\Framework\SourceCode\Scanners\EventHandlerScanner;
use Stormmore\Framework\SourceCode\Scanners\RouteScanner;
use Stormmore\Framework\SourceCode\Scanners\TaskScanner;

class SourceCode
{
    private array $classes;
    private array $routes;
    private array $commandHandlers;
    private array $eventHandlers;
    private array $tasks;

    private CommandHandlerScanner $commandHandlerScanner;
    private EventHandlerScanner $eventHandlerScanner;
    private TaskScanner $taskScanner;
    private ClassScanner $classScanner;
    private RouteScanner $routeScanner;
    private ClassCacheStorage $cache;

    public function __construct(
        private readonly AppConfiguration $configuration)
    {
        $this->cache = new ClassCacheStorage($this->configuration, 'cache.php');
        $this->classScanner = new ClassScanner($this->configuration->sourceDirectory);
        $this->routeScanner = new RouteScanner();
        $this->commandHandlerScanner = new CommandHandlerScanner();
        $this->eventHandlerScanner = new EventHandlerScanner();
        $this->taskScanner = new TaskScanner();
    }

    public function loadCache(): void
    {
        if (!$this->cache->exist()) {
            $this->scan();
            $this->writeCache();
        } else {
            $cache = $this->cache->load();
            $this->classes = $cache['classes'];
            $this->routes = $cache['routes'];
            $this->commandHandlers = $cache['commands'];
            $this->eventHandlers = $cache['handlers'];
            $this->tasks = $cache['tasks'];
        }
    }

    public function scan(): void
    {
        $scannedFileClasses = $this->classScanner->scan();
        $this->classes = $scannedFileClasses->getClassesWithRelativePaths();
        $this->routes = $this->routeScanner->scan($scannedFileClasses);
        $this->commandHandlers = $this->commandHandlerScanner->scan($scannedFileClasses);
        $this->eventHandlers = $this->eventHandlerScanner->scan($scannedFileClasses);
        $this->tasks = $this->taskScanner->scan($scannedFileClasses);
    }

    public function writeCache(): void
    {
        $this->cache->save([
            'classes' => $this->classes,
            'routes' => $this->routes,
            'commands' => $this->commandHandlers,
            'handlers' => $this->eventHandlers,
            'tasks' => $this->tasks
        ]);
    }

    public function findFileByFullyQualifiedClassName(string $className): bool|string
    {
        if (isset($this->classes) and array_key_exists($className, $this->classes)) {
            $filePath = $this->configuration->sourceDirectory . $this->classes[$className];
            if (file_exists($filePath)) {
                return $filePath;
            }
        }

        $classFileName = $this->configuration->sourceDirectory . "/" . $className . '.php';
        $classFileName = str_replace("\\", "/", $classFileName);
        if (file_exists($classFileName)) {
            return $classFileName;
        }
        return false;
    }

    public function findFullyQualifiedName(string $className): bool|string
    {
        foreach ($this->classes as $fullyQualifiedName => $fileName) {
            if (str_ends_with($fullyQualifiedName, $className)) {
                return $fullyQualifiedName;
            }
        }
        return false;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getCommandHandlers(): array
    {
        return $this->commandHandlers;
    }

    public function getEventHandlers(): array
    {
        return $this->eventHandlers;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
}