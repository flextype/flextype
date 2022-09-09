<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype\Parsers;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\Lexer;
use Symfony\Component\ExpressionLanguage\Parser;
use Symfony\Component\ExpressionLanguage\Compiler;
use Symfony\Component\ExpressionLanguage\ParsedExpression;
use Exception;

use function Glowy\Strings\strings;
use function Flextype\registry;
use function Flextype\cache;
use function Flextype\entries;

// Help opcache.preload discover always-needed symbols
class_exists(ParsedExpression::class);

final class Expressions
{
    /**
     * Expressions instance.
     */
    private static ?Expressions $instance = null;

    private Lexer $lexer;
    private Parser $parser;
    private Compiler $compiler;

    protected array $functions = [];

    protected function __construct()
    {
        // Register default php functions
        $this->addFunction(ExpressionFunction::fromPhp('constant'));

        // Register the expressions providers
        $this->registerExpressions(registry()->get('flextype.settings.parsers.expressions.expressions'));
    }

    /**
     * Expressions should not be cloneable.
     */
    protected function __clone()
    {
        throw new Exception('Cannot clone a Expressions.');
    }

    /**
     * Expressions should not be restorable from strings.
     */
    public function __wakeup(): void
    {
        throw new Exception('Cannot unserialize a Expressions.');
    }

    /**
     * Gets the instance via lazy initialization (created on first usage).
     */
    public static function getInstance(): Expressions
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Compiles an expression source code.
     */
    public function compile(Expressions|string $expression, array $names = []): string
    {
        return $this->getCompiler()->compile($this->parseExpression($expression, $names)->getNodes())->getSource();
    }

    /**
     * Evaluate an expression.
     */
    public function eval(Expressions|string $expression, array $values = []): mixed
    {
        return $this->parseExpression($expression, array_keys($values))->getNodes()->evaluate($this->functions, $values);
    }

    /**
     * Fallback method to evaluate an expression.
     */
    public function evaluate(Expressions|string $expression, array $values = []): mixed
    {
        return $this->eval($expression, $values);
    }

    /**
     * Parses text to evaluate or compile expressions.
     */
    public function parse(Expressions|string $string, array $values = [], bool $compile = false)
    {   
        if ($string instanceof Expressions) {
            return $string;
        }

        $selfQuote  = fn ($string) => preg_replace('/(.)/us', '\\\\$0', $string);
        $openingVariableTag = registry()->get('flextype.settings.parsers.expressions.opening_variable_tag');
        $closingVariableTag = registry()->get('flextype.settings.parsers.expressions.closing_variable_tag');
        $openingBlockTag = registry()->get('flextype.settings.parsers.expressions.opening_block_tag');
        $closingBlockTag = registry()->get('flextype.settings.parsers.expressions.closing_block_tag');
        $openingCommentTag = registry()->get('flextype.settings.parsers.expressions.opening_comment_tag');
        $closingCommentTag = registry()->get('flextype.settings.parsers.expressions.closing_comment_tag');
       
        // [# #] - comments 
        $string = preg_replace_callback('/' . $selfQuote($openingCommentTag) . ' (.*?) ' . $selfQuote($closingCommentTag) . '/sx', function ($matches) use ($values, $compile, $string) {
            return '';
        }, $string);

        // [% %] - blocks 
        $string = preg_replace_callback('/' . $selfQuote($openingBlockTag) . ' (.*?) ' . $selfQuote($closingBlockTag) . '/sx', function ($matches) use ($values, $compile, $string) {
            $this->{$compile ? 'compile' : 'eval'}($matches[1], $values);
            return '';
        }, $string);
  
        // [[ ]] - variables
        $string = preg_replace_callback('/' . $selfQuote($openingVariableTag) . ' (.*?) ' . $selfQuote($closingVariableTag) . '/sx', function ($matches) use ($values, $compile) {
            return $this->{$compile ? 'compile' : 'eval'}($matches[1], $values);
        }, $string);
 
        return $string;
    }

    /**
     * Parses an expression.
     */
    public function parseExpression(Expressions|string $expression, array $names): ParsedExpression
    {
        if ($expression instanceof ParsedExpression) {
            return $expression;
        }

        if (registry()->get('flextype.settings.parsers.expressions.cache.enabled') === true && 
            registry()->get('flextype.settings.cache.enabled') === true) {

            $cacheItem = $this->getExpressionCacheID($expression, $names);

            if (! cache()->has($cacheItem)) {
                $parsedExpression = new ParsedExpression((string) $expression, $this->getParser()->parse($this->getLexer()->tokenize((string) $expression), $names));
                cache()->set($cacheItem, $parsedExpression);
                return $parsedExpression;
            } else {
                return cache()->get($cacheItem);
            }
        }

        return new ParsedExpression((string) $expression, $this->getParser()->parse($this->getLexer()->tokenize((string) $expression), $names));
    }

    /**
     * Validates the syntax of an expression.
     *
     * @param array|null $names The list of acceptable variable names in the expression, or null to accept any names
     *
     * @throws SyntaxError When the passed expression is invalid
     */
    public function lint(Expressions|string $expression, ?array $names): void
    {
        if ($expression instanceof ParsedExpression) {
            return;
        }

        $this->getParser()->lint($this->getLexer()->tokenize((string) $expression), $names);
    }

    /**
     * Registers a function.
     *
     * @param callable $compiler  A callable able to compile the function
     * @param callable $evaluator A callable able to evaluate the function
     *
     * @throws \LogicException when registering a function after calling evaluate(), compile() or parse()
     *
     * @see ExpressionFunction
     */
    public function register(string $name, callable $compiler, callable $evaluator)
    {
        if (isset($this->parser)) {
            throw new \LogicException('Registering functions after calling evaluate(), compile() or parse() is not supported.');
        }

        $this->functions[$name] = ['compiler' => $compiler, 'evaluator' => $evaluator];
    }

    public function addFunction(ExpressionFunction $function)
    {
        $this->register($function->getName(), $function->getCompiler(), $function->getEvaluator());
    }

    public function registerProvider($provider)
    {
        foreach ($provider->getFunctions() as $function) {
            $this->addFunction($function);
        }
    }

    public function registerExpressions(array $expressions) {

        if (count($expressions) >= 0) {

            foreach ($expressions as $expression) {
                if (! isset($expression['enabled'])) {
                    continue;
                }

                if (! $expression['enabled']) {
                    continue;
                }

                if (! strings($expression['class'])->endsWith('Expression')) {
                    continue;
                }

                if (class_exists($expression['class'])) {
                    $this->registerProvider(new $expression['class']());
                }
            }
        }
    }

    private function getLexer(): Lexer
    {
        return $this->lexer ??= new Lexer();
    }

    private function getParser(): Parser
    {
        return $this->parser ??= new Parser($this->functions);
    }

    private function getCompiler(): Compiler
    {
        $this->compiler ??= new Compiler($this->functions);

        return $this->compiler->reset();
    }

    public function getExpressionCacheID(Expressions|string $expression, array $names, string $string = ''): string
    {
        if ($expression instanceof ParsedExpression) {
            return '';
        }

        // Go through...
        asort($names);

        $cacheKeyItems = [];

        foreach ($names as $nameKey => $name) {
            $cacheKeyItems[] = \is_int($nameKey) ? $name : $nameKey.':'.$name;
        }

        // Create Unique Cache ID for Expression
        return md5('expression' . $string . rawurlencode($expression.'//'.implode('|', $cacheKeyItems)));
    }
}