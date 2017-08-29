jQuery(document).ready(function(){
	jQuery(".posts").click(function(){
		var chk=jQuery(this).attr('checked');
		if(chk=='checked')
		{
			jQuery(".as-post").css("display","block");
		}
		else
		{
			jQuery(".as-post").css("display","none");
		}
	});
	jQuery(".pages").click(function(){
		var chk=jQuery(this).attr('checked');
		if(chk=='checked')
		{
			jQuery(".as-page").css("display","block");
		}
		else
		{
			jQuery(".as-page").css("display","none");
		}
	});
});