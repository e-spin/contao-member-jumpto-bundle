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

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// The member chooses the page in the frontend, in the backend an administrator can look it up and reset it.
$GLOBALS['TL_DCA']['tl_member']['fields']['memberJumpToPage'] = [
    'exclude'   => true,
    'inputType' => 'pageTree',
    'eval'      => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql'       => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
];

PaletteManipulator::create()
    ->addField('memberJumpToPage', 'login', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_member');
