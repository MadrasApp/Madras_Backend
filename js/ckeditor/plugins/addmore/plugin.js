
CKEDITOR.plugins.add( 'addmore',
{
	init: function( editor )
	{
		editor.addCommand( 'addMore',
			{
				exec : function( editor )
				{   					
					if(editor.getData()){
						if(String(editor.getData()).match('class="AddMore"')){
							
							alert('این ویژگی قبلا در متن تنظیم شده است .');
													
						}else{
							var div = editor.document.createElement( 'div' );
							div.setAttribute( 'title','ادامه مطالب');
							div.setText('ادامه مطالب');				
							div.setAttribute( 'contentEditable', 'false' );
							div.setAttribute( 'class', 'AddMore' );									
							editor.insertElement(div);
						}
					}					
				}
			});
			
		editor.ui.addButton( 'addmore',
		{
			label: 'افزودن ادامه مطالب',
			command: 'addMore',
			icon: this.path + 'images/addmore.png'
		} );
	}
} );
