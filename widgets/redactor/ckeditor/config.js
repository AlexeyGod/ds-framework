CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	 config.language = 'ru';
	// config.uiColor = '#AADC6E';
    config.contentsCss = ['/assets/f17aaabc20/css/style.css'];
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