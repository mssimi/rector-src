<?php

declare(strict_types=1);

namespace Rector\Php80\PhpDocInfo;

use Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;

final class DoctrineAnnotationMatcher
{
    public function matches(
        DoctrineAnnotationTagValueNode $doctrineAnnotationTagValueNode,
        string $desiredClass
    ): bool {
        if ($doctrineAnnotationTagValueNode->hasClassName($desiredClass)) {
            return true;
        }

        $identifierTypeNode = $doctrineAnnotationTagValueNode->identifierTypeNode;
        if ($this->isFnmatch($identifierTypeNode->name, $desiredClass)) {
            return true;
        }

        // FQN check
        $resolvedClass = $identifierTypeNode->getAttribute(PhpDocAttributeKey::RESOLVED_CLASS);
        return is_string($resolvedClass) && $this->isFnmatch($resolvedClass, $desiredClass);
    }

    private function isFnmatch(string $currentValue, string $desiredValue): bool
    {
        if (! \str_contains($desiredValue, '*')) {
            return false;
        }

        return fnmatch($desiredValue, $currentValue, FNM_NOESCAPE);
    }
}
