<?php

/**
 * This file is part of e-spin/contao-member-jumpto-bundle.
 *
 * (c) 2026 e-spin
 *
 * @package   e-spin/contao-member-jumpto-bundle
 * @author    Ingolf Steinhardt <info@e-spin.de>
 * @copyright 2026 e-spin
 * @license   LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['member_jumpto'] =
    '{title_legend},name,headline,type;{config_legend},memberJumpToPages;'
    . '{protected_legend:hide},protected;{expert_legend:hide},cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['memberJumpToPages'] = [
    'exclude'   => true,
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'mandatory'    => true,
        'columnFields' => [
            'page'    => [
                'label'     => &$GLOBALS['TL_LANG']['tl_module']['memberJumpToPage'],
                'inputType' => 'pageTree',
                'eval'      => [
                    'fieldType'     => 'radio',
                    'mandatory'     => true,
                    'wrapper_style' => 'width:40%',
                    'style'         => 'width:100%'
                ],
            ],
            'label'   => [
                'label'     => &$GLOBALS['TL_LANG']['tl_module']['memberJumpToLabel'],
                'inputType' => 'text',
                'eval'      => [
                    'maxlength'     => 255,
                    'wrapper_style' => 'width:40%',
                    'style'         => 'width:100%'
                ],
            ],
            'default' => [
                'label'     => &$GLOBALS['TL_LANG']['tl_module']['memberJumpToDefault'],
                'inputType' => 'checkbox',
                'eval'      => [
                    'wrapper_style' => 'width:10%',
                    'style'         => 'width:100%'
                ],
            ],
        ],
    ],
    'sql'       => 'blob NULL',
];
