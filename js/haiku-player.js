 jQuery(document).ready(function($) {
  	$("div[id^=haiku-player]").each(function() {
		var num = this.id.match(/haiku-player(\d+)/)[1];
		var ctrlId = "div#player-container" + num + " ul#player-buttons" + num;
		var audioFile = $(ctrlId + " li.play a").attr("href");
		 $(this).jPlayer({
			ready: function () {
				this.element.jPlayer("setFile", audioFile);
			},
			customCssIds: true,
			swfPath: jplayerswf,
			errorAlerts:true,
			nativeSuport:false
		})
		$(ctrlId + " li.stop").css("display","inline").fadeIn();
		$(ctrlId + " li.play").click(function() {
			pauseAllPlayers();
			$("div#haiku-player" + num).jPlayer("play");
			showPauseBtn();
			return false;
		});
		$(ctrlId + " li.pause").click(function() {
			$("div#haiku-player" + num).jPlayer("pause");
			showPlayBtn();
		});
		$(ctrlId + " li.stop").click(function() {
			$("div#haiku-player" + num).jPlayer("stop");
			showPlayBtn();
			return false;
		});
		function pauseAllPlayers() {
			$("div.haiku-player").jPlayer("pause");
			$("div.player-container ul.player-buttons li.pause").hide();					$("div.player-container ul.player-buttons li.play").show();
		}
		function showPauseBtn() {
			$(ctrlId + " li.play").fadeOut(function(){
				$(ctrlId + " li.pause").css("display","inline").fadeIn();});
		}
		function showPlayBtn() {
			$(ctrlId + " li.pause").fadeOut(function(){
				$(ctrlId + " li.play").fadeIn();});
		}

	});

});
 