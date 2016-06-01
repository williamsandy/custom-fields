<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Abstract Form Field class for the Joomla Platform.
 *
 * @since  11.1
 */
interface JFormDomFieldInterface
{
	/**
	 * Transforms the field into an XML element and appends it as child on the given parent.
	 *
	 * @param stdClass $field
	 * @param DOMElement $parent
	 * @param JForm $form
	 * @return DOMElement
	 *
	 * @since 3.7
	 */
	public function appendXMLFieldTag ($field, DOMElement $parent, JForm $form);
}
