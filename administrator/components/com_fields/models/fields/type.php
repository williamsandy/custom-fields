<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\String\StringHelper;

JFormHelper::loadFieldClass('list');
JLoader::import('joomla.filesystem.folder');

class JFormFieldType extends JFormFieldList
{

	public $type = 'Type';

	/**
	 * This is an array of fields which do extend the list form field,
	 * but are not supported and should not be shown as types.
	 */
	private static $unsupportedFields = array(
			'accesslevel',
			'cachehandler',
			'combo',
			'databaseconnection',
			'filelist',
			'folderlist',
			'plugins',
			'radio',
			'section',
			'sessionhandler',
			'type'
	);

	public function setup (SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		$this->onchange = "typeHasChanged(this);";
		return $return;
	}

	protected function getOptions ()
	{
		$options = parent::getOptions();

		$paths = JFormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_fields/models/fields');
		foreach ($paths as $path)
		{
			if (! JFolder::exists($path))
			{
				continue;
			}
			// Looping trough the types
			foreach (JFolder::files($path, 'php') as $filePath)
			{
				$name = str_replace('.php', '', basename($filePath));
				if (in_array($name, self::$unsupportedFields))
				{
					continue;
				}

				$className = JFormHelper::loadFieldClass($name);
				if ($className === false)
				{
					continue;
				}

				// Check if the field implements JFormDomfieldinterface
				$reflection = new ReflectionClass($className);
				if (!$reflection->isInstantiable() || !$reflection->implementsInterface('JFormDomfieldinterface'))
				{
					continue;
				}

				$label = 'COM_FIELDS_TYPE_' . strtoupper($name);
				if (! JFactory::getLanguage()->hasKey($label))
				{
					$label = StringHelper::ucfirst($name);
				}
				$options[] = JHtml::_('select.option', $name, JText::_($label));
			}
		}

		// Sorting the fields based on the text which is displayed
		usort($options, function  ($a, $b) {
			return strcmp($a->text, $b->text);
		});

		// Reload the page when the type changes
		$uri = clone JUri::getInstance('index.php');

		// Removing the catid parameter from the actual url and set it as
		// return
		$returnUri = clone JUri::getInstance();
		$returnUri->setVar('catid', null);
		$uri->setVar('return', base64_encode($returnUri->toString()));

		// Setting the options
		$uri->setVar('option', 'com_fields');
		$uri->setVar('task', 'field.storeform');
		$uri->setVar('context', 'com_fields.field');
		$uri->setVar('formcontrol', $this->form->getFormControl());
		$uri->setVar('userstatevariable', 'com_fields.edit.field.data');
		$uri->setVar('view', null);
		$uri->setVar('layout', null);
		JFactory::getDocument()->addScriptDeclaration(
				"function typeHasChanged(element){
				var cat = jQuery(element);
				jQuery('input[name=task]').val('field.storeform');
				element.form.action='" . $uri . "';
				element.form.submit();
			}");

		return $options;
	}
}
