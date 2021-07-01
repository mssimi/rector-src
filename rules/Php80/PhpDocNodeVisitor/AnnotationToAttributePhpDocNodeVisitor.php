<?php

declare(strict_types=1);

namespace Rector\Php80\PhpDocNodeVisitor;

use PHPStan\PhpDocParser\Ast\Node;
use Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Php80\PhpDocInfo\DoctrineAnnotationMatcher;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Php80\ValueObject\DoctrineTagAndAnnotationToAttribute;
use Symplify\SimplePhpDocParser\PhpDocNodeTraverser;
use Symplify\SimplePhpDocParser\PhpDocNodeVisitor\AbstractPhpDocNodeVisitor;

final class AnnotationToAttributePhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    /**
     * @var AnnotationToAttribute[]
     */
    private array $annotationsToAttributes = [];

    /**
     * @var DoctrineTagAndAnnotationToAttribute[]
     */
    private array $doctrineTagAndAnnotationToAttributes = [];

    public function __construct(
        private DoctrineAnnotationMatcher $doctrineAnnotationMatcher
    ) {
    }

    /**
     * @param AnnotationToAttribute[] $annotationsToAttributes
     */
    public function configureAnnotationsToAttributes(array $annotationsToAttributes): void
    {
        $this->annotationsToAttributes = $annotationsToAttributes;
    }

    public function beforeTraverse(Node $node): void
    {
        $this->doctrineTagAndAnnotationToAttributes = [];

        if ($this->annotationsToAttributes !== []) {
            return;
        }

        throw new ShouldNotHappenException(
            'Configure annotations to attributes via "configureAnnotationsToAttributes()" method first'
        );
    }

    public function enterNode(Node $node): int | Node | null
    {
        if (! $node instanceof DoctrineAnnotationTagValueNode) {
            return $node;
        }

        foreach ($this->annotationsToAttributes as $annotationsToAttribute) {
            if (! $this->doctrineAnnotationMatcher->matches($node, $annotationsToAttribute->getTag())) {
                continue;
            }

            $this->doctrineTagAndAnnotationToAttributes[] = new DoctrineTagAndAnnotationToAttribute(
                $node,
                $annotationsToAttribute
            );

            // to invoke re-print
            //$node->setAttribute(PhpDocAttributeKey::ORIG_NODE, null);

            // remove the original doctrine annotation, it becomes an attribute
            return PhpDocNodeTraverser::NODE_REMOVE;
        }

        return $node;
    }

    /**
     * @return DoctrineTagAndAnnotationToAttribute[]
     */
    public function provideFound(): array
    {
        return $this->doctrineTagAndAnnotationToAttributes;
    }
}
