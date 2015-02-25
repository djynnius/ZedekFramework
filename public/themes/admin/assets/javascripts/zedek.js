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