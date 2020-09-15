<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\ckeditor;


use framework\components\Bundle;
use framework\core\Application;


class CkeditorBundle extends Bundle
{
    public  $depends = ['framework\\assets\\jquery\\JqueryBundle'];
    public  $sourcePath = '@framework/widgets/ckeditor/ckeditor';
    public $js = [
        'ckeditor.js'
    ];

    public function init()
    {
        $activeThemeBundle = self::getActiveThemeBundle();
        $bundle = new $activeThemeBundle;
        //echo("Bundle ".var_export($bundle->css, true)); //->getBundleByClass(DsBundle::class)

        foreach ($bundle->css as $css)
        {
            $output[] = "'".$css."'";
        }
        //unset($output[0],$output[1]);

        $css_line = "[".implode(", ", $output)."]";
        //$css_line = "'/somefile.css'";


        $configJS = <<<JSCODE
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	 config.language = 'ru';
	// config.uiColor = '#AADC6E';
    config.contentsCss = $css_line;
    config.bodyClass = 'page-body';
    config.extraPlugins = 'codesnippet,tableresize';
    config.removePlugins = 'spellchecker, about, save, newpage, print, templates, scayt, flash, pagebreak, smiley,preview,find';
    config.protectedSource.push(/<(style)[^>]*>.*<\/style>/ig);
	config.protectedSource.push(/<(script)[^>]*>.*<\/script>/ig);// разрешить теги <script>
	config.protectedSource.push(/<(i)[^>]*>.*<\/i>/ig);// разрешить теги <i>
	config.protectedSource.push(/<\?[\s\S]*?\?>/g);// разрешить php-код
	config.protectedSource.push(/<!--dev-->[\s\S]*<!--\/dev-->/g);
	config.allowedContent = true;
	config.disableNativeSpellChecker = false;
    config.codeSnippet_languages = {
    javascript: 'JavaScript',
    php: 'PHP',
		html: 'HTML',
		css: 'CSS',
		mysql: 'MYSQL'
	};
 };
JSCODE;
        /*
         // Разрешает пользовательские классы
         config.indentClasses = ["ul-grey", "ul-red", "text-red", "ul-content-red", "circle", "style-none", "decimal", "paragraph-portfolio-top", "ul-portfolio-top", "url-portfolio-top", "text-grey"];

         */

    $toolBarOptional = <<<JSCODE
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
JSCODE;
        $configJS = trim($configJS);

        //exit($configJS);

        file_put_contents(Application::app()->getRealPath($this->sourcePath."/config.js"), $configJS);

    }

}