// Arrow Besides DropDown
$('.dropbtn').mouseleave(function() {
	$(this).toggleClass('bef');
});
$('.dropdown').hover(function() {
	$(this).find('.dropbtn').toggleClass('after');
});