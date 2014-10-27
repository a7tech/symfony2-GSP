;var App = App || {};

(function($, window, document, Math, undefined) {

    App = $.extend(true, {}, App, {

        /**
         * Original makeRequest function by Webility.
         * @param  {Object} conf
         */
        makeRequest: function(conf){
            var default_conf = {
                type: 'GET',
                dataType: 'json',
                data: false,
                additionalData: {}
            };



            if(conf.data instanceof jQuery){
                conf.data = App.serializePost(conf.data);
            }

            default_conf.type = App.objectSize(conf.data) > 0 ? 'POST' : 'GET';

            conf = $.extend(default_conf, conf);
            conf.data = $.extend(conf.data, conf.additionalData);

            return $.ajax(conf);
        },

        serializePost: function($object){
            if(!$object.is('form')){
                $object = $object.clone().wrap('<form></form>').parent();
            }

            var data = $object.serializeArray();

            var postData = {};
            $.map(data, function(n, i){
                if(n['name'].indexOf('[]') != -1){
                    //unindexed array
                    var value_name = n['name'].substr(0, n['name'].length-2);
                    if(typeof(postData[value_name]) == 'undefined'){
                        postData[value_name] = [];
                    }

                    postData[value_name].push(n['value']);
                }
                else{
                    //indexed value
                    postData[n['name']] = n['value'];
                }
            });

            return postData;
        },

        objectSize : function(o) {
            var s = 0;
            for (var k in o) if (o.hasOwnProperty(k)) s++;
            return s;
        }
    });
})(jQuery, window, document, Math);
