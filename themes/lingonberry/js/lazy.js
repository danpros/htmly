( function() {

    var youtube = document.querySelectorAll( ".youtube" );
    
    for (var i = 0; i < youtube.length; i++) {
        
        var source = "https://img.youtube.com/vi/"+ youtube[i].dataset.embed +"/sddefault.jpg";
        
        var image = new Image();
                image.src = source;
                image.addEventListener( "load", function() {
                    youtube[ i ].appendChild( image );
                }( i ) );
        
                youtube[i].addEventListener( "click", function() {

                    var iframe = document.createElement( "iframe" );

                            iframe.setAttribute( "frameborder", "0" );
                            iframe.setAttribute( "allowfullscreen", "" );
                            iframe.setAttribute( "src", "https://www.youtube.com/embed/"+ this.dataset.embed +"?rel=0&showinfo=0&autoplay=1" );

                            this.innerHTML = "";
                            this.appendChild( iframe );
                } );    
    };
	
    var vimeo = document.querySelectorAll( ".vimeo" );
    for (var i = 0; i < vimeo.length; i++) {
		var vimeoid = vimeo[i].dataset.embed;
		getJSON('http://vimeo.com/api/v2/video/'+ vimeoid +'.json',
			function(err, data) {
				if (err != null) {
					var source = "https://i.vimeocdn.com/video/"+ vimeoid +"_640.jpg";
				} else {
					var source = data[0].thumbnail_large;
				}
				
				var vimeoelement = document.querySelectorAll('[data-embed~="'+vimeoid+'"]')[0];
				
				var image = new Image();
				image.src = source;
				image.addEventListener( "load", function() {
					vimeoelement.appendChild( image );
				}(i) );
		
				vimeoelement.addEventListener( "click", function() {

					var iframe = document.createElement( "iframe" );

							iframe.setAttribute( "frameborder", "0" );
							iframe.setAttribute( "webkitallowfullscreen", "" );
							iframe.setAttribute( "mozallowfullscreen", "" );
							iframe.setAttribute( "allowfullscreen", "" );
							iframe.setAttribute( "src", "https://player.vimeo.com/video/"+ this.dataset.embed + "?autoplay=1" );

							this.innerHTML = "";
							this.appendChild( iframe );
				} );			
			});
    };
	function getJSON(url, callback) {
		var xhr = new XMLHttpRequest();
		xhr.open('GET', url, true);
		xhr.responseType = 'json';
		xhr.onload = function() {
		  var status = xhr.status;
		  if (status == 200) {
			callback(null, xhr.response);
		  } else {
			callback(status);
		  }
		};
		xhr.send();
	};
	
} )();