


CKEDITOR.editorConfig = function( config ) {
	
	config.allowedContent = true;
	config.language = 'fa';
	//config.uiColor = '#009393';
	config.skin = 'moono';
	config.magicline_color = '#009393';
	config.extraPlugins = 'addmore';
	config.height = '300px';
	
	config.font_names = 'زر /BZar;' + config.font_names;
	config.font_names = 'یکان /BYekan;' + config.font_names;
	config.font_names = 'نازنین /BNazanin;' + config.font_names;
	config.font_names = 'میترا /BMitra;' + config.font_names;
	config.font_names = 'هما /BHoma;' + config.font_names;
	config.font_names = 'کوفی /Kufi;' + config.font_names;
    
	config.toolbar = [
	
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ],
		 items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
		{ name: 'tools', items: [ 'Maximize' ] },
		'/',
		
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ],
		 items: [ 'Bold', 'Italic', 'Underline', 'Strike','-', 'RemoveFormat' ] },
		
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ],
		 items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },

		
		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	
		{ name: 'others', items: [ '-' ] },
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ],
		 items: [ 'Source']}
	];

    /*
    config.toolbar = 'MyToolbar';
    config.toolbar_MyToolbar =
        [
            { name: 'document', items : [ 'NewPage','Preview' ] },
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','Scayt' ] },
            { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
            '/',
            { name: 'styles', items : [ 'Styles','Format' ] },
            { name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
            { name: 'tools', items : [ 'Maximize','-','About' ] }
        ];*/
};



