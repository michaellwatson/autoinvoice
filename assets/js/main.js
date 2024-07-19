$( document ).ready(function() {
	var height = $('.inner_purple_box').height();
	var width = $('.inner_purple_box').width();
	$(".tennis_cover").height(height);
	$(".tennis_cover").width(width);
	
	$('.inner_purple_box').on('click',function(){
		$('.inner_purple_box').removeClass('player-selected');
		$('.yesNoBlocks').removeClass('straight_sets');

		$('#winning_player_id').val('');
		
		if(!$(this).hasClass('player-selected')){
			$('.tennis_cover').remove();
			$(this).addClass('player-selected');
			var height = $('.inner_purple_box').height();
			var width = $('.inner_purple_box').width();
			$(this).prepend('<div class="tennis_cover" style="height:'+height+'px;width:'+width+'px;"></div>');
			$('#winning_player_id').val($(this).attr('data-id'));
		}
	});
	$('.yesNoBlocks').on('click',function(){
		if(!$(this).hasClass('straight_sets')){
			$('.yesNoBlocks').removeClass('straight_sets');
			$(this).addClass('straight_sets');
			$('#straight_sets').val($(this).attr('data-id'));
			var $box = $(this).parent().parent().find('.inner_purple_box');


			if(!$box.hasClass('player-selected')){
				//$('.inner_purple_box').removeClass('player-selected');
				$('.inner_purple_box').removeClass('player-selected');
				$('.tennis_cover').remove();
				var height = $box.height();
				var width = $box.width();
				$box.prepend('<div class="tennis_cover" style="height:'+height+'px;width:'+width+'px;"></div>');
				$box.addClass('player-selected');
				$('#winning_player_id').val($box.attr('data-id'));
			}

		}else{
			$(this).removeClass('straight_sets');
			$('#straight_sets').val(0);
		}
	});

});
