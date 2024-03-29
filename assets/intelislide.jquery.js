(function($){
	
	/**
	 * A plugin for rotating images and content with an optional caption. No additional
	 * styles need to be established and will work 'out of the box' by assigning to an
	 * element.
	 * 
	 * @author 		Kevin Bradwick
	 * @copyright	(c) 2010 Kevin Bradwick <kbradwick.gmail.com>
	 * @version		1.0
	 * @licence		http://www.opensource.org/licenses/mit-license.php The MIT Licence
	 * @example		demo/index.html
	 * 
	 * The MIT License
	 *
	 * Copyright (c) 2010 Kevin Bradwick <kbradwick@gmail.com>
	 *
	 * Permission is hereby granted, free of charge, to any person obtaining a copy
	 * of this software and associated documentation files (the "Software"), to deal
	 * in the Software without restriction, including without limitation the rights
	 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	 * copies of the Software, and to permit persons to whom the Software is
	 * furnished to do so, subject to the following conditions:
	 *
	 * The above copyright notice and this permission notice shall be included in
	 * all copies or substantial portions of the Software.
	 *
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	 * THE SOFTWARE.
	 */
	
	$.fn.intelislide = function(options) {
		
		// defaults
		var defaults = {
			width:				400,
			height:				200,
			timeout:			6000,
			transitionSpeed:	1000,
			tagType:			'div'
		};
		
		var opts = $.extend(defaults, options);
		var _e = $(this);
		var _tag = '#' + _e.attr('id');
		var _caption = $(_tag + ' ' + opts.tagType + '.caption');
		var _first = _e.children('.intelslide:first');
		var _text = _first.attr('rel');
		var _content = _e.next(opts.tagType + '.content');
		var _index = 0;
		
		// set the CSS for the holding element
		_e.css({'position':'relative','display':'block','width':opts.width,'height':opts.height,'overflow':'hidden'});
		
		// set the CSS of the caption	
		_caption.css({padding:'8px',position:'absolute','z-index':600}).hide();
		
		// set the CSS of all <a> elements
		_e.children(_tag + ' .intelslide').css({'float':'left','position':'absolute'}).hide();
		
		// unhide the first element and fire caption if present
		_e.children(_tag + ' .intelslide:first').fadeIn(400,function() {
			if( _first.attr('title') != undefined && _first.attr('title').length > 0) {
				setCaptionStyle(_first);
				_caption.slideDown().html(_first.attr('title'));
			}
		});
		
		// hide cursor hand for all <a> that do not have a value
		_e.children('a').each(function() {
			if($(this).attr('href') != undefined  && $(this).attr('href').length <= 1) {
				$(this).click(function() { return false; });
				$(this).mouseover(function() { $(this).css({'cursor':'default'}); });
			}
		});
		
		// start the loop
		var _loop = startLoop();
		
		
		/**
		 * The main loop function
		 * @access	private
		 * @return	object
		 */
		function startLoop() {
			var _loop = setInterval(function() {
				
				// get current element
				var current = _e.children('.intelslide').eq(_index);
				
				// get next element
				var next = ((current.index() + 1) == _e.children('.intelslide').length) ? _e.children('.intelslide').eq(0) : _e.children('.intelslide').eq((_index + 1));
				
				// determine if next element has caption
				var nextCaption = (next.attr('title') != undefined && next.attr('title').length > 0) ? next.attr('title') : false;
				
				// hide caption if present
				if(_caption.is(':visible')) {
					_caption.slideUp((opts.transitionSpeed / 2),function() {
						current.css('z-index','').fadeOut(opts.transitionSpeed);
					});
				} else {
					current.css('z-index','').fadeOut(opts.transitionSpeed);
				}
				
				// fade in next image and follow with caption if available
				next.css('z-index',500).fadeIn(opts.transitionSpeed, function() {
					if(nextCaption) {
						setCaptionStyle(next);
						_caption.slideDown().html(nextCaption);
					}
					_index = next.index();
				});
				
			},opts.timeout);
			return _loop;
		}
		

		/**
		 * Set the caption style for current image
		 * 
		 * @access	private
		 * @param	object
		 * @return	null
		 */
		function setCaptionStyle( e )
		{
			var att = new Array();
			att[0] = '#000';
			att[1] = '#FFF';
			att[2] = 0.7;
			att[3] = 'bottom';
			
			// if rel attribute specified, set @var att
			if(e.attr('rel') != 'undefined') {
				var opt = e.attr('rel').split(';');
				for(var i=0; i<opt.length; i++) {
					var attribute = opt[i].split(':');
					
					switch(attribute[0]) {
						case 'bg' : att[0] = attribute[1]; break;
						case 'txt' : att[1] = attribute[1]; break;
						case 'opacity' : att[2] = attribute[1]; break;
						case 'pos' : att[3] = attribute[1]; break;
					}
				}
			}
			
			// set CSS of caption including positions
			_caption.css({'background-color':att[0],'color':att[1],'opacity':att[2]});
			if(att[3]=='top') {
				_caption.css({
					'width'		: (opts.width - 16),
					'height'	: '',
					'top'		: 0,
					'left'		: 0,
					'bottom'	: '',
					'right'		: ''
				});
			} else if(att[3] == 'right') {
				_caption.css({
					'width'		: Math.round(opts.width / 4),
					'height'	: (opts.height - 16),
					'top'		: 0,
					'left'		: '',
					'bottom'	: '',
					'right'		: 0
				});
			} else if(att[3]=='left') {
				_caption.css({
					'width'		: Math.round(opts.width / 4),
					'height'	: (opts.height - 16),
					'top'		: 0,
					'left'		: 0,
					'bottom'	: '',
					'right'		: ''
				});
			} else {
				_caption.css({
					'width'		:(opts.width - 16),
					'height'	: '',
					'top'		: '',
					'left'		: '',
					'bottom'	: 0,
					'right'		: ''
				});
			}
		}
					
	};
})(jQuery);