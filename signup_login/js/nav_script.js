( function( $ ) {
$( document ).ready(function() {
$('#pageTop').prepend('<div id="menu-button">Menu</div>');
	$('#pageTop #menu-button').on('click', function(){
		var menu = $(this).next('ul');
		if (menu.hasClass('open')) {
			menu.removeClass('open');
		}
		else {
			menu.addClass('open');
		}
	});
});
} )( jQuery );
