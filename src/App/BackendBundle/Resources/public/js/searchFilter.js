$(document).ready(
		function(){
 		$('div.search-filter-container.inactive').hide();
 		
 		$('a.search-filter-but.active').click(function(event){
 			$('div.search-filter-container').show();
        });

 		$('input.search-filter-hide').click(function(event){
 			$('div.search-filter-container').hide();
        });		
});