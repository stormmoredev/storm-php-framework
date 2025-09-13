<?php

namespace Stormmore\Framework\SourceCode\Parser;

use ArrayIterator;
use Stormmore\Framework\SourceCode\Parser\Models\PhpAttributes;
use Stormmore\Framework\SourceCode\Parser\Models\PhpClass;
use Stormmore\Framework\SourceCode\Parser\Models\PhpClassMethod;

class PhpClassParser
{
    public static function parse(ArrayIterator $it, string $namespace, array $uses, PhpAttributes $attributes): PhpClass
    {
        $it->next();
        $name = $it->current()->text;
        $class = new PhpClass($namespace, $uses,  $name, $attributes);

        $buffer = new PhpTokenBuffer(3);
        $attributes = [];
        $open = $closed = 0;
        while($it->valid() and ($open != $closed or ($open == 0 and $closed == 0))) {
            $it->next();
            $token = $it->current();
            if ($token->text == '{') {
                $open++;
            }
            if ($token->text == '}') {
                $closed++;
            }
            if ($token->text == '#[') {
                $attributes[] = AttributeParser::parse($it);
            }
            if ($token->getTokenName() == 'T_STRING') {
                if ($buffer->getFromEnd() == 'function') {
                    $access = 'public';
                    $tokenBefore = strtolower($buffer->getFromEnd(1));
                    if (in_array($tokenBefore, array('private', 'protected'))) {
                        $access = $tokenBefore;
                    }
                    $class->functions[] = new PhpClassMethod($access, $token->text, $attributes);
                    $attributes = [];
                }
            }

            $buffer->add($token->text);
        }


        return $class;
    }
}