/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Язык
	config.language = 'ru';

	// Ширина редактора
	config.width = '100%';
	config.height = 400;

	// Разрешаем любой контент
	config.allowedContent = true;

	// Настройка загрузки файлов - передаём тип в URL
	config.filebrowserBrowseUrl = '/admin-panel/editor/ckeditor-browse?type=all';
	config.filebrowserImageBrowseUrl = '/admin-panel/editor/ckeditor-browse?type=image';
	config.filebrowserUploadUrl = '/admin-panel/editor/upload';
	config.filebrowserImageUploadUrl = '/admin-panel/editor/upload-image';

	// Настройка панели инструментов
	config.toolbarGroups = [
		{ name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'insert' },
		'/',
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'tools' },
		{ name: 'others' },
		{ name: 'about' }
	];

	// Убираем лишние кнопки
	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,Flash,Smiley,PageBreak,Iframe,About';

	// Настройка вставки изображений
	config.image_previewText = ' ';
	config.image_removeLinkByEmptyURL = true;
};