
(function($) {

	function getDocumentHeight(doc) {
		return Math.max(
			doc.documentElement.clientHeight,
			doc.body.scrollHeight, doc.documentElement.scrollHeight,
			doc.body.offsetHeight, doc.documentElement.offsetHeight);
	}

	$.entwine('ss', function($){
		$('.ss-gridfield-orderable tbody .handle').entwine({
			onmousedown: function () {
				this.closest('tbody').addClass('ss-gridfield-sorting');
			},
			onmouseup: function () {
				this.closest('tbody').removeClass('ss-gridfield-sorting');
			}
		});
		$('.dataobjectpreview').entwine({
			onadd: function () {
				var $tr = this.closest('tr');
				if ($tr.length && $tr.hasClass('ui-sortable-helper')) {
					return;
				}
				var $iframe = $('<iframe style="width: 100%; overflow: hidden" scrolling="no"></iframe>');
				$iframe.bind('load', function () {
					var iframeWindow = $iframe.get(0).contentWindow,
					$iframeWindow = $(iframeWindow),
					iframeHeight = 0;
					$iframeWindow.resize(function () {
						var newHeight = getDocumentHeight( iframeWindow.document );

						if (newHeight !== iframeHeight && iframeHeight < 1000) {
							$iframe.height(newHeight + "px");
							iframeHeight = newHeight;
						}
					});
					if ($iframe.is(":visible")) {
						$iframeWindow.resize();
					}
				});
				$iframe.attr('src', this.data('src'));
				this.append($iframe);
			}
		});
	});
}(jQuery));