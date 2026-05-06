/**
 * cbpBGSlideshow.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */
var cbpBGSlideshow = (function() {

	var $timer = $( '#box-timer' ),
		$slideshow = $( '#cbp-bislideshow' ),
		$items = $slideshow.children( 'li' ),
		itemsCount = $items.length,
		$controls = $( '#cbp-bicontrols' ),
		navigation = {
			$navPrev : $controls.find( 'span.cbp-biprev' ),
			$navNext : $controls.find( 'span.cbp-binext' ),
			$navPlayPause : $controls.find( 'span.cbp-bipause' )
		},
		// current item´s index
		current = 0,

		pause = 5,
		seconds = 60 ,
		interval = 1000 * seconds,
		clock = seconds + pause,
		countdownId = 0,
		// timeout
		slideshowtime,
		// true if the slideshow is active
		isSlideshowActive = true;

	function init( p, s ) {
		console.log("s"+ s);
		console.log("p"+ p);
		pause = p;
		seconds = s + pause;
		interval = 1000 * seconds;
		// preload the images
		$slideshow.imagesLoaded( function() {
			
			if( Modernizr.backgroundsize ) {
				$items.each( function() {
					var $item = $( this );
					$item.css( 'background-image', 'url(' + $item.find( 'img' ).attr( 'src' ) + ')' );

				} );
			}
			else {
				$slideshow.find( 'img' ).show();
				// for older browsers add fallback here (image size and centering)
			}
			// show first item
			$items.eq( current ).css( 'opacity', 1 );
			// initialize/bind the events
			initEvents();
			initKeyboard();
			// start the slideshow
			startSlideshow();

		} );
	}


function playSound(){
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', 'audio/beep.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        //audioElement.load()

        $.get();

        audioElement.addEventListener("load", function() {
            audioElement.play();
        }, true);


}	

	function initKeyboard() {
		// alert('init');
		$(document).keydown(function(e){
		    if (e.keyCode == 37) { 
		       // alert( "left pressed" );
					navigate( 'prev' ); 
					if( isSlideshowActive ) { 
						startSlideshow(); 
					} 		       
		       return false;
		    }

			if (e.keyCode == 39) { 
		       // alert( "left pressed" );
					navigate( 'next' ); 
					if( isSlideshowActive ) { 
						startSlideshow(); 
					}   
		       return false;
		    }
		});



	}

	function initEvents() {

		navigation.$navPlayPause.on( 'click', function() {

			var $control = $( this );
			if( $control.hasClass( 'cbp-biplay' ) ) {
				$control.removeClass( 'cbp-biplay' ).addClass( 'cbp-bipause' );
				startSlideshow();
			}
			else {
				$control.removeClass( 'cbp-bipause' ).addClass( 'cbp-biplay' );
				stopSlideshow();
			}

		} );

		navigation.$navPrev.on( 'click', function() { 
			navigate( 'prev' ); 
			if( isSlideshowActive ) { 
				startSlideshow(); 
			} 
		} );
		navigation.$navNext.on( 'click', function() { 
			navigate( 'next' ); 
			if( isSlideshowActive ) { 
				startSlideshow(); 
			}
		} );

	}

	function navigate( direction ) {



		// current item
		var $oldItem = $items.eq( current );
		
		if( direction === 'next' ) {
			current = current < itemsCount - 1 ? ++current : 0;
		}
		else if( direction === 'prev' ) {
			current = current > 0 ? --current : itemsCount - 1;
		}

		// new item
		var $newItem = $items.eq( current );

		// show / hide items
		$oldItem.css( 'opacity', 0 );
		$newItem.css( 'opacity', 1 );



	}

	function sformat(s) {
	      var fm = [
	            // Math.floor(s / 60 / 60 / 24), // DAYS
	            // Math.floor(s / 60 / 60) % 24, // HOURS
	            Math.floor(s / 60) % 60, // MINUTES
	            s % 60 // SECONDS
	      ];
	      return $.map(fm, function(v, i) { return ((v < 10) ? '0' : '') + v; }).join(':');
	}

	function countdown(){
		// console.log(clock);
		var $currentItem = $items.eq( current );
		console.log($currentItem.find( 'img' ).attr( 'src' ));
		$('#file_name').text($currentItem.find('img').attr( 'title' ));

		if(clock > 0){
			// only show the clock after pause
			if(clock + pause < seconds + 1 ){
				$("#box-timer").html("Imagem " + (current + 1) + " | " + sformat(clock));
			}else{
				$("#box-timer").html("Inicia em " +  (clock - seconds + pause) + " segundos");
			}
			clock = clock - 1;

		}else{
			clearInterval(countdownId);
		}
	}

	function startSlideshow() {

		isSlideshowActive = true;
		clearTimeout( slideshowtime );
		clearInterval( countdownId );
		clock = seconds;

		countdownId = setInterval(function(){
			countdown();

		}, 1000);

		slideshowtime = setTimeout( function() {
			navigate( 'next' );
			startSlideshow();
		}, interval );
		// playSound();

	}

	function stopSlideshow() {
		isSlideshowActive = false;
		clearTimeout( slideshowtime );
		clearInterval(countdownId);
	}

	return { init : init };

})();