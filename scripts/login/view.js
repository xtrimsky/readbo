var View = {
        templates: null,
    
	loadTemplates: function(){
            this.templates = {};

            var html = $('<div>'+js_templates+'</div>');

            html.children().each(function(){
                var name = $(this).attr('id');

                View.templates[name] = $(this).html();
            });
        },

        getTemplate: function(name){
            if(this.templates === null){
                this.loadTemplates();
            }

            return this.templates[name];
        },

	fillTemplate: function(content, args){
            var i;
            for(i in args){
                if(args.hasOwnProperty(i)) {
                    var string = '['+i+']';
                    var replaced = new RegExp(string.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"), "gi");
                    content = content.replace(replaced, args[i]);
                }
            }

            return content;
        }
};