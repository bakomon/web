<?php
// Source: https://dev.to/fadymr/php-create-your-own-php-dotenv-3k2i

namespace Tools;

class DotEnv
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;
    protected $exists;
    protected $readable;


    public function __construct(string $path)
    {
        $this->path = $path;

        if (file_exists($path)) {
            $this->exists = true;
        }
    }

    public function load() :void
    {
        if (is_readable($this->path)) {
            $this->readable = true;
        }

        if ($this->exists && $this->readable) {
            $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {

                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    if (function_exists('putenv')) {
                        putenv(sprintf('%s=%s', $name, $value));
                    }
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}
