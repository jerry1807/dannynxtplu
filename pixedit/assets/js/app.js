

    var pixeditApp = angular.module('pixeditApp', ['toaster']);
	
	pixeditApp.directive('iframeSetDimensionsOnload', function ($window) {
		return {
			restrict: 'A',
			link: function (scope, element, attrs) {

				var contentMinWidth = 1200;

				element.on('load', setSizeWithZoom());

				angular.element($window).bind('resize', function () {
					setSizeWithZoom();
					scope.$digest();
					
				});

			function setSize() {
				var iFrameWidth = element[0].contentWindow.parent.document.body.offsetWidth;
				var iFrameHeight = window.innerHeight - 220;
				element.css('width', iFrameWidth + 'px');
				element.css('height', iFrameHeight + 'px');
			}

			function setSizeWithZoom() {
				var iFrameWidth = element[0].contentWindow.parent.document.body.offsetWidth - 50;
				var iFrameHeight = window.document.body.clientHeight - 61;

				element.css('width', iFrameWidth + 'px');
				element.css('height', iFrameHeight + 'px');
				element.css('transform-origin', "0 0");
				element.css('overflow', "hidden");

				if(element[0].contentWindow.parent.document.body.offsetWidth - 50 > 993){
					element.css('margin-top', "61px");
					element.css('margin-left', "50px");
				}else{
					element.css('width', element[0].contentWindow.parent.document.body.offsetWidth + 'px');
					element.css('margin-left', "0px");	
					element.css('margin-top', "61px");				
				}
			}

			}
		}
	});
	
    pixeditApp.controller('PixeditController', function(toaster,$window,$timeout) {	

        $window.saveProcess = function(){
            $timeout(function () {
                toaster.pop('info', "Processing", "Trying to Save....");
              }, 0);
        }

    $window.saveSuccess = function(){
        $timeout(function () {
            toaster.pop('success', "Success", "Picture Saved in your Media Store");
          }, 0);
    }

    $window.saveError = function(){
        $timeout(function () {
            toaster.pop('error', "Error", "Could not Save");
          }, 0);
    }

    });
