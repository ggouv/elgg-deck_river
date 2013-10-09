/**
 * [scrapWebpage description]
 * @param  [string]            url of the webpage to parse
 * @param  [object]            options
 * @return [object]            parsed datas
 */
elgg.deck_river.scrapWebpage = function(url, options) {
	options = $.extend({
					minSize: 250,                       // [string]            Title of the popup
					beforeSend: $.noop,                 // [function]          function will be executed just before request
					success: $.noop,                    // [function]          function will be executed when success
					error: $.noop,                      // [function]          function will be executed on error
				}, options);

	elgg.get(elgg.get_site_url() + 'mod/elgg-deck_river/lib/scraper.php', {
		data: {
			url: url
		},
		dataType: 'json',
		beforeSend: options.beforeSend,
		success: function(response) {

			var Images = [],
				imgsLength = response.images.length,
				nbrLoads = 0,
				imgLoaded = function(img) {
					nbrLoads++;
					if (nbrLoads >= imgsLength) {
						Images.sort(function(a, b) {
							return (a.nDim > b.nDim) ? -1 : (a.nDim < b.nDim) ? 1 : 0;
						});
						// put og:image first
						$.grep(response.metatags, function(e){
							if (e[0] == 'og:image') Images.unshift({'src': e[1]});
						});
						response.images = Images;
						options.success(response);
					}
				};

			$.each(response.images, function(i, e) {
				var img = new Image(),
					iD = {};

				iD.src = img.src = e;
				img.onload = function() {
					iD.width = this.width;
					iD.height = this.height;
					iD.nDim = parseFloat(iD.width) * parseFloat(iD.height);
					if (options.minSize != 0 && options.minSize <= iD.width && options.minSize <= iD.height) {
						Images.push(iD);
					} else if (options.minSize == 0) {
						Images.push(iD);
					}
					imgLoaded(img);
				};
				img.onerror = function() {nbrLoads++;};
			});

		},
		error: options.error
	});
};


elgg.deck_river.scrapWebpageYQL = function(url) {
	$.ajax({
		url: 'http://query.yahooapis.com/v1/public/yql?q=' + encodeURIComponent('select * from html where url="' + url + '" and xpath="*"') + '&callback=?',
		type: 'get',
		dataType: 'json',
		success: function(data) {
			// load the response into jquery element
			// form tags are needed to get the entire html,head and body
			var $foop = $('<form>' + data.results[0] + '</form>'),
				output = {
						metas: {},
						imgs: [],
						links: []
					};
console.log(data.results[0]);
			// find meta tags
			$.each($foop.find('meta[content]'), function(i, e) {
				var name = $(e).attr('name');
				if (name !== undefined) output.metas[name] = $(e).attr('content');
			});

			// find images bigger than 250x250
			$.each($foop.find('img[src]'), function(i, e) {
				var src = $(e).attr('src');
				if (!/^https?:\/\//.test(src)) {
					src = url + src;
				}

				var img = new Image();
				img.src = src;
				img.onload = function() {
					if (this.width > 10 && this.height > 10) {
						output.imgs.push(img.src);
					}
				}

			});

			// find links
			$.each($foop.find('a[href]'), function(i, e) {
				var href = $(e).attr('href');
				if (/^https?:\/\//.test(href)) output.links.push(href);
			});

console.log(output, 'output');
/*console.log(data, 'data');
			// load the response into jquery element
			// form tags are needed to get the entire html,head and body
			$foop = $('<form>' + data.responseText + '</form>');
			//console.log(data.responseText);

			// find meta tags
			$.each($foop.find("meta[content]"), function(i, e) {
				lnk = $(e).attr("content");
console.log(lnk);
				//$('<option>' + lnk + '</option>').appendTo($('#meta'));
			});

			// find links
			$.each($foop.find('a[href]'), function(i, e) {
				lnk = $(e).attr("href");
				console.log(lnk);
				//$('<option>' + lnk + '</option>').appendTo($('#links'));
			});

			// find images bigger than 250x250
			$.each($foop.find('img[src]'), function(i, e) {
				src = $(e).attr("src");
				if (src.indexOf('http://') == -1) {
					src = url + src;
				}

				var img = new Image();
				img.src = src;
				img.onload = function() {
					//alert(this.width + 'x' + this.height);
					if (this.width > 250 && this.height > 250) {
						console.log($(this));
				//$(this).appendTo($('#images'));
					}
				}

			});

			// find contents of divs
			$.each($foop.find('div'), function(i, e) {
				mytext = $(e).children().remove().text();
				//$('<div>'+mytext+'</div>').appendTo($('#divs'));
			});*/

		},
		error: function(status) {
			console.log("request error:"+ url);
		}
	});
};