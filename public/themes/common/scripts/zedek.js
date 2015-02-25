(function(){
	/*framework initialization*/
	_zedek = function(){
		this.directive();
	}
	//var _$ = jQuery;
	/*method to add directives to array as they are created*/
	var directives = [];

	_zedek.prototype.repeat = function (data){
		//console.log(data.items);
		jQuery(document).ready(function($){
			var html_data = $("[zedek-repeat]"); /*get elements with data binding for repeat*/
			for(var i=0; i<html_data.length; i++){ /*loop on elements with repeat binding*/
				var details = $(html_data[i]).attr("zedek-repeat").split(/[\s]+/g); /*get items as item designation*/
				if(data.name == details[0]){ /*if items == designation in definition proceed*/
					var item = html_data[i]; /*element to loop on*/
					var clone = $(item).clone(true).removeAttr("zedek-repeat").html(); /*loop cotentents*/
					var output = ""; /*instantiate the looped markup*/ 
					for(var j=0; j<data.data.length; j++){ /*loop based on JSON length ie data.items*/
						var xclone = clone; /*instantiate clone for loop*/
						$.each(data.data[j], function(k, v){ /*replace based on JSON keys replacing all per object*/
							var re = new RegExp("{{"+details[2]+"."+k+"}}", "g"); /*ensuring global replace*/
							xclone = xclone.replace(re, v);
						});	
						output += xclone; /*concatenate the strings*/		
					}
					$(html_data[i]).empty().append(output);	/*append the looped elements and output*/
				}
			}			
		});
	}

	function _n(){
		console.log("_n");
	}
	directives.push(_n);

	_zedek.prototype.directive = function(){
		for(var i=0; i<directives.length; i++){
			directives[i]();
		}

	}

	window.zedek = new _zedek;
	zedek = window.zedek;
})();



/*
How To use the zedek repeat data binding
=========================================
<script type="text/javascript">
	var pphotos = {
		name: "pphotos",
		data: [
			{image: "foo", some: "bar"},
			{image: "foot", some: "barium"},
			{image: "footer", some: "barrel"}
		]
	}

	zedek.repeat(pphotos);
</script>*/

jQuery(document).ready(function(){
	/*sscript starts here*/
	zf = function(){
		/*constructor*/
		this.dir = ""; /*set this to get the best out of zedek*/
		this.zf_pathname = this.dir + window.location.pathname;
		this.zf_origin = window.location.origin;
		this.zf_current_path = this.zf_origin + this.zf_pathname;
		this.nav_ul_identifier = ".navigation"; /*set nav identifier*/
	}

	zf.prototype.detectActiveNavItem = function(){
		var nav_links = $("ul"+this.nav_ul_identifier+" li");
		for(var i=0; i<nav_links.length; i++){
			if(this.zf_current_path == $($(nav_links[i]).children("a"))[0].href){
				$(nav_links[i]).addClass("active");	
				$(nav_links[i]).parents("ul.navigation li").addClass("active");	
				$(nav_links[i]).parents("ul.navigation li").addClass("open");					
			}			
		}
	}

	app = new zf;
	app.detectActiveNavItem();

	/*sscript ends here*/
});