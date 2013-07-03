
 function addSkill(inUrl)
 {
 
   var data=$("#SkillForm").serialize()+'&ajax=1';

	$.ajax({
   type: 'POST',
   url: inUrl,
   data:data,
        success:function(indata){
          data = JSON.parse(indata);
					if (!data.status){
            skill = '<span data-alert class="label alert-box radius secondary profile-skils" id="skill_'+data.data.id+'">';
            skill += data.data.title+"<br /><small class='meta'>"+data.data.desc+"</small>";
            skill += '<a href="#" class="close" onclick="removeSkill('+data.data.id+')">&times;</a>';
            skill += '</div>';
            $('.skillList').append(skill);
          }
					if (data.message) alert(data.message);
        },
        error: function(data,e,t) { // if error occured
           alert(e+': '+t);
           //alert(data);
        },
 
  dataType:'html'
  });
 
}


function removeSkill(skill_id){
    $.ajax({
   type: 'POST',
   url: skillRemove_url,
   data:{ id: skill_id, ajax: 1},
        success:function(data){
          data = JSON.parse(indata);
          if (data.message != '') alert(data.message);
          else {
            //$('#link_div_'+data.data.id).fadeOut('slow');
          }
        },
        error: function(data,e,t) { // if error occured
           alert(e+': '+t);
        },
 
  dataType:'html'
  });

}


	var cache = {};
	//var skillSuggest_url = 'profile/sugestSkill';
	
  $(function() {
    $( "#skill" ).autocomplete({
      //minLength: 1,
			delay:300,
      source: function( request, response ) {
        var term = request.term;
        if ( term in cache ) {
          response( cache[ term ] );
          return;
        }
 
        $.getJSON( skillSuggest_url, request, function( data, status, xhr ) {
					if (data.status == 0){
						cache[ term ] = data.data;
						response( data.data );
					}else alert(data.message);
        });
      },
			//source:projects,
      focus: function( event, ui ) {
        $( "#project" ).val( ui.item.skill );
        return false;
      },
      select: function( event, ui ) {
        $( "#skill" ).val( ui.item.skill );
				$('#skillset').val(ui.item.skillset_id); 
				Foundation.libs.forms.refresh_custom_select($('#skillset'),true);
				
        $( "#project-id" ).val( ui.item.id );
 
        return false;
      }
    })
    .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a>" + item.skill + "<br><small>" + item.skillset + "</small></a>" )
        .appendTo( ul );
    };
  });