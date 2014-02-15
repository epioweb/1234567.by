    
    /*
     *  jquery.suggest 1.2 - 2007-08-21
     *  
     *  Original Script by Peter Vulgaris (www.vulgarisoip.com)
     *  Updates by Chris Schuld (http://chrisschuld.com/)
     *
     */
    
    (function($) {

        $.suggest = function(input, options) {
    
            var $input = $(input).attr("autocomplete", "off");
            var $results;

            var timeout = false;        // hold timeout ID for suggestion results to appear 
            var prevLength = 0;         // last recorded length of $input.val()
            var cache = [];             // cache MRU list
            var cacheSize = 0;          // size of cache in chars (bytes?)
            var hideResultTimer = false;
            var closeResult = 1;

            if( ! options.attachObject )
                options.attachObject = $(document.createElement("ul")).appendTo('body');

            $results = $(options.attachObject);
            $results.addClass(options.resultsClass);
            
            resetPosition();
            $(window)
                .load(resetPosition)        // just in case user is changing size of page while loading
                .resize(resetPosition);


            $input.blur(function() {
                if(closeResult) {
                    hideResultTimer = setTimeout(function() {
                        $results.hide();
                        $(options.dataContainer).hide();
                    }, 200);
                }
            });

            $(options.dataContainer).on("mousedown", function(event){
               if(event.which == 2 || event.which == 3) {
                   closeResult = 0;

                    if(hideResultTimer) {
                        clearTimeout(hideResultTimer);
                        $results.show();
                        $(options.dataContainer).show();
                    }
               } /*else {
                   selectCurrentResult();
               }*/
            });

            $input.focus(function() {
                closeResult = 1;
                $results.show();
                $(options.dataContainer).show();
            });
            // fix for hidden searchBar
            $(".go-search a").bind("click", function(){
                resetPosition();
            });
            
            // help IE users if possible
            try {
                $results.bgiframe();
            } catch(e) { }


            // I really hate browser detection, but I don't see any other way
            if ($.browser.mozilla)
                $input.keypress(processKey);    // onkeypress repeats arrow keys in Mozilla/Opera
            else
                $input.keydown(processKey);     // onkeydown repeats arrow keys in IE/Safari
            

            function resetPosition() {
                // requires jquery.dimension plugin
                var offset = $input.offset();
                $results.css({
                    top:'26px',
                    left: '40%'
                });
            }
            
            
            function processKey(e) {
                
                // handling up/down/escape requires results to be visible
                // handling enter/tab requires that AND a result to be selected
                if ((/27$|38$|40$/.test(e.keyCode) && $results.is(':visible')) ||
                    (/^13$|^9$/.test(e.keyCode) && getCurrentResult())) {
                    
                    if (e.preventDefault)
                        e.preventDefault();
                    if (e.stopPropagation)
                        e.stopPropagation();

                    e.cancelBubble = true;
                    e.returnValue = false;
                
                    switch(e.keyCode) {
    
                        case 38: // up
                            prevResult();
                            break;
                
                        case 40: // down
                            nextResult();
                            break;
    
                        case 9:  // tab
                        case 13: // return
                            selectCurrentResult(1);
                            break;
                            
                        case 27: // escape
                            $results.hide();
                            $(options.dataContainer).hide();
                            break;
    
                    }
                    
                } else if ($input.val().length != prevLength) {

                    if (timeout) 
                        clearTimeout(timeout);
                    timeout = setTimeout(suggest, options.delay);
                    prevLength = $input.val().length;
                    
                }           
                    
                
            }
            
            var q = "";
            function suggest() {
            
                var q = $.trim($input.val());

                if (q.length >= options.minchars) {
                    
                    cached = checkCache(q);
                    
                    if (cached) {
                    
                        displayItems(cached['items']);
                        
                    } else {
                    
                        if(options.loaderAnimObj) {
                            //$(options.loaderAnimObj).css("display", "inline");
                            $(options.loaderAnimObj).fadeIn("quick");
                        }
                        $.get(options.source, {q: q}, function(txt) {

                            $results.hide();
                            $(options.dataContainer).hide();

                            var items = parseTxt(txt, q);
                            displayItems(items);
                            addToCache(q, items, txt.length);
                            if(options.loaderAnimObj)
                                $(options.loaderAnimObj).fadeOut("quick");
                        });
                        
                    }
                    
                } else {
                
                    $results.hide();
                    $(options.dataContainer).hide();
                }
                    
            }
            
            
            function checkCache(q) {

                for (var i = 0; i < cache.length; i++)
                    if (cache[i]['q'] == q) {
                        cache.unshift(cache.splice(i, 1)[0]);
                        return cache[0];
                    }
                
                return false;
            
            }
            
            function addToCache(q, items, size) {

                while (cache.length && (cacheSize + size > options.maxCacheSize)) {
                    var cached = cache.pop();
                    cacheSize -= cached['size'];
                }
                
                cache.push({
                    q: q,
                    size: size,
                    items: items
                    });
                    
                cacheSize += size;
            
            }
            
            function displayItems(items) {
                
                if (!items)
                    return;
                    
                if (!items.length) {
                    $results.hide();
                    $(options.dataContainer).hide();
                    return;
                }
                
                var html = '';
                for (var i = 0; i < items.length; i++)
                    html += '<tr' + (items[i]['key'] != '' ? ' rel="'+ items[i]['key']+'"' : '' ) + '>' + items[i]['value'] + '</tr>';
                
                $results.html(html).show();
                $(options.dataContainer).show();

                $results
                    .find('tr')
                    .mouseover(function() {
                        $results.find('tr').removeClass(options.selectClass);
                        $(this).addClass(options.selectClass);
                    })
                    .click(function(e) {
                        //e.preventDefault();
                        //e.stopPropagation();
                        //selectCurrentResult();
                    });
                            
            }
            
            function parseTxt(txt, q) {
                
                var items = [];
                var tokens = txt.split(options.delimiter);
                
                // parse returned data for non-empty items
                for (var i = 0; i < tokens.length; i++) {
                    
                    var data = $.trim(tokens[i]).split(options.dataDelimiter);
                    if( data.length > 1 ) {
                        token = data[0];
                        key = data[1];
                    }
                    else {
                        token = data[0]
                        key = '';
                    }
                    
                    if (token) {
                        items[items.length] = {'value':token,'key':key};
                    }
                }
                
                return items;
            }
            
            function getCurrentResult() {
            
                if (!$results.is(':visible'))
                    return false;
            
                var $currentResult = $results.find('tr.' + options.selectClass);
                
                if (!$currentResult.length)
                    $currentResult = false;
                    
                return $currentResult;

            }
            
            function selectCurrentResult(force) {

                if(!force)
                    force = 0;

                $currentResult = getCurrentResult();
            
                if ($currentResult) {
                    
                    if (options.onSelect) {
                        var rel = $currentResult.attr("rel");
                        if(typeof(rel) != "undefined") {
                            $input.val($currentResult.find("td p").text());
                            options.onSelect(rel, force);
                        }
                    }
                    $results.hide();
                    $(options.dataContainer).hide();
                }
            }
            
            function nextResult() {
            
                $currentResult = getCurrentResult();
            
                if ($currentResult)
                    $currentResult
                        .removeClass(options.selectClass)
                        .next()
                            .addClass(options.selectClass);
                else
                    $results.find('tr:first-child').addClass(options.selectClass);
            
            }
            
            function prevResult() {
            
                $currentResult = getCurrentResult();
            
                if ($currentResult)
                    $currentResult
                        .removeClass(options.selectClass)
                        .prev()
                            .addClass(options.selectClass);
                else
                    $results.find('tr:last-child').addClass(options.selectClass);
            
            }
    
        }
        
        $.fn.suggest = function(source, options) {
        
            if (!source)
                return;
        
            options = options || {};
            options.source = source;
            options.delay = options.delay || 150;
            options.resultsClass = options.resultsClass || 'ac_results';
            options.selectClass = options.selectClass || 'ac_over';
            options.matchClass = options.matchClass || 'ac_match';
            options.minchars = options.minchars || 2;
            options.delimiter = options.delimiter || '\n';
            options.onSelect = options.onSelect || false;
            options.maxCacheSize = options.maxCacheSize || 65536;
            options.dataDelimiter = options.dataDelimiter || '\t';
            options.dataContainer = options.dataContainer || '#SuggestResult';
            options.attachObject = options.attachObject || null;
            options.loaderAnimObj = options.loaderAnimObj || null;
            this.each(function() {
                new $.suggest(this, options);
            });
    
            return this;
            
        };
        
    })(jQuery);
    
