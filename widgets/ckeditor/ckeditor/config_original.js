/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	 config.language = 'ru';
	// config.uiColor = '#AADC6E';
    config.contentsCss = '/assets/css/style.css';
    config.bodyClass = 'article__text';
    config.toolbar = 'MyToolbar';
    config.toolbar_MyToolbar = [
        ['Bold', 'Italic', '-', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', 'TextColor'],
        ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],

        ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'videoDialog', 'removeformat', 'Source'],
        '/',
        ['Styles', 'Format', 'Font', 'FontSize'],
        '/'
    ]
};
