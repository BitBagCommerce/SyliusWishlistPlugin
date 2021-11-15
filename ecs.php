<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\RequireStrictTypesSniff;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use SlevomatCodingStandard\Sniffs\Classes\RequireMultiLineMethodSignatureSniff;
use SlevomatCodingStandard\Sniffs\Commenting\ForbiddenAnnotationsSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;


return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->import(SetList::PSR_12);

    $services = $containerConfigurator->services();

    $services->set(TrailingCommaInMultilineFixer::class)
        ->call('configure', [['elements' => ['arrays']]]);

    $services->set(ForbiddenAnnotationsSniff::class)
        ->property('forbiddenAnnotations', [
            '@api',
            '@author',
            '@category',
            '@copyright',
            '@created',
            '@license',
            '@package',
            '@since',
            '@subpackage',
            '@version',
        ]);

    $services->set(YodaStyleFixer::class)
        ->call('configure',[[
            'equal' => true,
            'identical' => true,
            'less_and_greater' => true,]
        ]);

    $services->set(RequireStrictTypesSniff::class);

    $services->set(ClassAttributesSeparationFixer::class);

    $services->set(NoEmptyPhpdocFixer::class);

    $services->set(NoTrailingWhitespaceFixer::class);


    $services->set(NoWhitespaceInBlankLineFixer::class);

    $services->set(BracesFixer::class)
        ->call('configure',[['allow_single_line_closure'=>true]]);

    $services->set(NoSpacesAroundOffsetFixer::class);

    $services->set(RequireMultiLineMethodSignatureSniff::class)
        ->set('minLineLength','35');

    $services->set(VisibilityRequiredFixer::class)
        ->call('configure', [['elements' => ['const', 'property', 'method']]]);

    $services->set(SingleLineCommentStyleFixer::class)
        ->call('configure',[['comment_types'=>['hash']]]);

    $services->set(ListSyntaxFixer::class)
        ->call('configure',[['syntax'=>'short']]);

    $services->set(BinaryOperatorSpacesFixer::class)
        ->call('configure',[[
            'align_double_arrow'=>false,
            'align_equals'=>false
        ]]);
    $services->set(ConcatSpaceFixer::class)
        ->call('configure',[['spacing'=>'one']]);

    $services->set(NoExtraBlankLinesFixer::class)
        ->call('configure',[['tokens'=>[
            'break',
            'case',
            'continue',
            'curly_brace_block',
            'default',
            'extra',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'switch',
            'throw',
            'use',
        ]]]);

};