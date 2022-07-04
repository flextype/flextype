<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS 
 * and with the full functionality of a traditional CMS!
 * 
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype\Entries\Expressions;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Finder\Finder;
use Glowy\Arrays\Arrays as Collection;
use function Glowy\Filesystem\filesystem;
use Glowy\Macroable\Macroable;

class FilesystemExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('filesystem', fn() => '(new FilesystemExpressionMethods())', fn($arguments) => (new FilesystemExpressionMethods()))
        ];
    }
}

class FilesystemExpressionMethods
{
    use Macroable;

    /**
     * Create a File instance.
     */
    public function file($path): FilesystemFileExpressionMethods
    {
        return new FilesystemFileExpressionMethods($path);
    }

    /**
     * Create a Directory instance.
     */
    public function directory($path): FilesystemDirectoryExpressionMethods
    {
        return new FilesystemDirectoryExpressionMethods($path);
    }

    /**
     * Create a Finder instance.
     */
    public function find(): Finder
    {
        return filesystem()->find();
    }

    /**
     * Determine if the given path is a stream path.
     *
     * @param  string $path Path to check.
     *
     * @return bool Returns TRUE if the given path is stream path, FALSE otherwise.
     */
    public function isStream(string $path): bool
    {
        return filesystem()->isStream($path);
    }

   /**
    * Determine if the given path is absolute path.
    *
    * @param  string $path Path to check.
    *
    * @return bool Returns TRUE if the given path is absolute path, FALSE otherwise.
    */
    public function isAbsolute(string $path): bool
    {
        return filesystem()->isAbsolute($path);
    }

    /**
     * Determine if the given path is a Windows path.
     *
     * @param  string $path Path to check.
     *
     * @return bool true if windows path, false otherwise
     */
    public function isWindowsPath(string $path): bool
    {
        return filesystem()->isWindowsPath($path);
    }

    /**
     * Find path names matching a given pattern.
     *
     * @param  string $pattern The pattern.
     * @param  int    $flags   Valid flags.
     *
     * @return array Returns an array containing the matched files/directories, an empty array if no file matched.
     */
    public function glob(string $pattern, int $flags = 0): array
    {
        return filesystem()->glob($pattern, $flags);
    }
}

class FilesystemFileExpressionMethods
{
    use Macroable;

    /**
     * Path property
     *
     * Current file absolute path
     *
     * @var string|null
     */
    public $path;

    /**
     * Constructor
     *
     * @param string $path Path to file.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Get the contents of a file.
     *
     * @return string|false The file contents or false on failure.
     */
    public function get()
    {
        return filesystem()->file($this->path)->get();
    }

    /**
     * Checks the existence of file and returns false if any of them is missing.
     *
     * @return bool Returns true or false if any of them is missing.
     */
    public function exists(): bool
    {
        return filesystem()->file($this->path)->exists();
    }

    /**
     * Get the file's last modification time.
     *
     * @return int Returns the time the file was last modified.
     */
    public function lastModified(): int
    {
        return filesystem()->file($this->path)->lastModified();
    }

    /**
     * Get the file's last access time.
     *
     * @return int Returns the time the file was last assecc.
     */
    public function lastAccess(): int
    {
        return filesystem()->file($this->path)->lastAccess();
    }

    /**
     * Get the mime-type of a given file.
     *
     * @return string The mime-type of a given file.
     */
    public function mimeType(): string
    {
        return filesystem()->file($this->path)->mimeType();
    }

    /**
     * Get the file type of a given file.
     *
     * @return string The file type of a given file.
     */
    public function type(): string
    {
        return filesystem()->file($this->path)->type();
    }

    /**
     * Get the file extension from a file path.
     *
     * @return string The extension of a given file.
     */
    public function extension(): string
    {
        return filesystem()->file($this->path)->extension();
    }

    /**
     * Get the trailing name component from a file path.
     *
     * @return string The trailing name of a given file.
     */
    public function basename(): string
    {
        return filesystem()->file($this->path)->basename();
    }

    /**
     * Get the file name from a file path.
     *
     * @return string The file name of a given file.
     */
    public function name(): string
    {
        return filesystem()->file($this->path)->name();
    }

    /**
     * Return current path.
     *
     * @return string|null Current path
     */
    public function path(): ?string
    {
        return filesystem()->file($this->path)->path();
    }

    /**
     * Gets file size in bytes.
     *
     * @return int Returns the size of the file in bytes.
     */
    public function size(): int
    {
        return filesystem()->file($this->path)->size();
    }

    /**
     * Get the MD5 hash of the file at the given path.
     *
     * @return string Returns a string on success, FALSE otherwise.
     */
    public function hash(): string
    {
        return filesystem()->file($this->path)->hash();
    }

    /**
     * Determine if the given path is readable.
     *
     * @return bool Returns TRUE if the given path exists and is readable, FALSE otherwise.
     */
    public function isReadable(): bool
    {
        return filesystem()->file($this->path)->isReadable();
    }

    /**
     * Determine if the given path is writable.
     *
     * @return bool Returns TRUE if the given path exists and is writable, FALSE otherwise.
     */
    public function isWritable(): bool
    {
        return filesystem()->file($this->path)->isWritable();
    }

    /**
     * Determine if the given path is a regular file.
     *
     * @return bool Returns TRUE if the filename exists and is a regular file, FALSE otherwise.
     */
    public function isFile(): bool
    {
        return filesystem()->file($this->path)->isFile();
    }
}

class FilesystemDirectoryExpressionMethods
{
    use Macroable;

    /**
     * Path property
     *
     * Current directory path.
     *
     * @var string|null
     */
    public $path;

    /**
     * Constructor
     *
     * @param string $path Path to directory.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Checks the existence of directory and returns false if any of them is missing.
     *
     * @return bool Returns true or false if any of them is missing.
     */
    public function exists(): bool
    {
        return filesystem()->directory($this->path)->exists();
    }

    /**
     * Gets size of a given directory in bytes.
     *
     * @return int Returns the size of the directory in bytes.
     */
    public function size(): int
    {
        return filesystem()->directory($this->path)->size();
    }

    /**
     * Determine if the given path is a directory.
     *
     * @return bool Returns TRUE if the given path exists and is a directory, FALSE otherwise.
     */
    public function isDirectory(): bool
    {
        return filesystem()->directory($this->path)->isDirectory();
    }

    /**
     * Return current path.
     *
     * @return string|null Current path
     */
    public function path(): ?string
    {
        return filesystem()->directory($this->path)->path();
    }
}
