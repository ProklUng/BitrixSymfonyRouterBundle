<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Router\Annotations;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Class SearchAnnotatedClasses
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Router\Annotations
 *
 * @since 09.10.2020
 */
class SearchAnnotatedClasses
{
    /**
     * @var array|null $paths Пути, где искать классы. Указываются с DOCUMENT_ROOT.
     */
    private $paths;

    /**
     * @var array $classes Результат. Классы.
     */
    private $classes = [];

    /**
     * SearchAnnotatedClasses constructor.
     *
     * @param array|null $paths        Пути, где искать классы.
     */
    public function __construct(
        ?array $paths = []
    ) {
        $this->paths = $paths;
    }

    /**
     * Собрать классы по всем путям.
     *
     * @return array
     */
    public function collect() : array
    {
        if ($this->paths === null || count($this->paths) === 0) {
            return [];
        }

        $result = [];
        foreach ($this->paths as $path) {
            $result[] = $this->listClassesByPath($path);
        }

        $result = (array)$result[0];

        $this->classes = array_merge($this->classes, $result);

        return $this->classes;
    }

    /**
     * Классы по пути.
     *
     * @param string $path Путь.
     *
     * @return array
     *
     * @internal Код с stackoverflow.
     */
    private function listClassesByPath(string $path) : array
    {
        $fqcns = [];

        $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $phpFiles = new RegexIterator($allFiles, '/\.php$/');
        foreach ($phpFiles as $phpFile) {
            $content = (string)file_get_contents($phpFile->getRealPath());
            $tokens = token_get_all($content);
            $namespace = '';
            for ($index = 0; array_key_exists($index, $tokens); $index++) {
                // @phpstan-ignore-next-line
                if (!isset($tokens[$index][0])) {
                    continue;
                }

                if (T_NAMESPACE === $tokens[$index][0]) {
                    $index += 2; // Skip namespace keyword and whitespace
                    // @phpstan-ignore-next-line
                    while (isset($tokens[$index]) && is_array($tokens[$index])) {
                        $namespace .= $tokens[$index++][1];
                    }
                }
                if ($tokens[$index][0] === T_CLASS
                    && T_WHITESPACE === $tokens[$index + 1][0]
                    && T_STRING === $tokens[$index + 2][0]
                ) {
                    $index += 2; // Skip class keyword and whitespace
                    $fqcns[] = $namespace.'\\'.$tokens[$index][1];

                    # break if you have one class per file (psr-4 compliant)
                    # otherwise you'll need to handle class constants (Foo::class)
                    break;
                }
            }
        }

        return $fqcns;
    }
}
