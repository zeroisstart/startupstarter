
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
            skill += '<a href="#" class="close" onclick="removeSkill('+data.data.id+',\''+data.data.location+'\')">&times;</a>';
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


function removeSkill(skill_id, inUrl){
    $.ajax({
   type: 'POST',
   url: inUrl,
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
  var cityCache = {};  
  	
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
    
    $( ".city" )
      // don't navigate away from the field on tab when selecting an item
      .bind( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).data( "ui-autocomplete" ).menu.active ) {
          event.preventDefault();
        }
      })
			.autocomplete({
				delay:300,
				minLength: 2,
        source: function( request, response ) {
					
					$.getJSON( citySuggest_url, { term: extractLast( request.term ) }, function( data, status, xhr ) {
						if (data.status == 0){
							cityCache[ extractLast( request.term ) ] = data.data;
							response( data.data );
						}else alert(data.message);
					});
        }
      });    
    
  });