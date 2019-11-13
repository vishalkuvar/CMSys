<?php
// Is 32bit or 64bit System?
if (PHP_INT_SIZE <= 4)
	$is32Bit = true;
else
	$is32Bit = false;

/**
 * Maximum Number of Items to display in ProfileCards
 * @var int
 */
$limitDisplay = 5;

/**
 * Should the menu bar be shown at fixed position irrespective of scrolls?
 * @var bool
 */
$menuAbove = false;
?>