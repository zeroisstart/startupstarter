 function addLink(inUrl)
 {
 
   var data=$("#LinkForm").serialize()+'&ajax=1';

  $.ajax({
   type: 'POST',
   url: inUrl,
   data:data,
        success:function(indata){
          data = JSON.parse(indata);
          if (!data.status){
            link = '<div data-alert class="alert-box radius secondary" id="link_div_'+data.data.id+'">';
            link += data.data.title+': <a href="http://'+data.data.url+'" target="_blank">'+data.data.url+'</a>';
            link += '<a href="#" class="close" onclick="removeLink(\''+data.data.id+'\',\''+data.data.location+'\')">&times;</a>';
            link += '</div>';
            $('.linkList').append(link);
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


function removeLink(link_id, inUrl){
    $.ajax({
   type: 'POST',
   url: inUrl,//'<?php echo Yii::app()->createAbsoluteUrl("profile/removeLink"); ?>',
   data:{ id: link_id, ajax: 1},
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
            /*if (data.data.multi == 1) */skill += '<a href="#" class="close" onclick="removeSkill(\''+data.data.id+'\',\''+data.data.location+'\')">&times;</a>';
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
   data:{id: skill_id, ajax: 1},
        success:function(indata){
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
    
    
    if ($('.finduser').length != 0)
    $( ".finduser" )
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
					
					$.getJSON( userSuggest_url, { term: extractLast( request.term ) }, function( data, status, xhr ) {
						if (data.status == 0){
							cityCache[ extractLast( request.term ) ] = data.data;
							response( data.data );
						}else alert(data.message);
					});
        }
      });    
    
    if ($('.skill').length != 0)
    $( ".skill" )
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
					
          var term = extractLast( request.term );
          if ( term in cache ) {
            response( cache[ term ] );
            return;
          }

          if (term.length > 2){
            $.getJSON( skillSuggest_url, { term: term, category:$("#skillset").val() }, function( data, status, xhr ) {
              if (data.status == 0){
                cache[ term ] = data.data;
                response( data.data );
              }else alert(data.message);
            });
          }
        },
        /*search: function() {
          // custom minLength
          var term = extractLast( this.value );
          if ( term.length < 2 ) {
            return false;
          }
        },*/
        focus: function( event, ui ) {
          $( "#project" ).val( ui.item.skill );
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          var terms = splitComa( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.skill );

      		$('.skillset').val(ui.item.skillset_id); 
    			Foundation.libs.forms.refresh_custom_select($('.skillset'),true);
          $( "#project-id" ).val( ui.item.id );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        }
      })
     .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a>" + item.skill + "<br><small>" + item.skillset + "</small></a>" )
        .appendTo( ul );
    };
    
    if ($('.city').length != 0)
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
					
					$.getJSON( citySuggest_url, {term: extractLast( request.term )}, function( data, status, xhr ) {
						if (data.status == 0){
							cityCache[ extractLast( request.term ) ] = data.data;
							response( data.data );
						}else alert(data.message);
					});
        }
      });    
      
    var inviteCache = {};
    if ($('.invite-member-email').length != 0)
    $( ".invite-member-email" )
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
					
          //var term = extractLast( request.term );
          var term = request.term;
          if ( term in inviteCache ) {
            response( inviteCache[ term ] );
            return;
          }

          $.getJSON( inviteMember_url, { term: term}, function( data, status, xhr ) {
            if (data.status == 0){
              cache[ term ] = data.data;
              response( data.data );
            }else alert(data.message);
          });
        },
        /*search: function() {
          // custom minLength
          var term = extractLast( this.value );
          if ( term.length < 2 ) {
            return false;
          }
        },*/
        focus: function( event, ui ) {
          //$('.skillset').val( ui.item.skill );
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          //var terms = splitComa( this.value );

          this.value = ui.item.fullname;

          $('#invite-user-id').val(ui.item.user_id); 
          return false;
        }
      })
     .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a><img src=\"" + item.img + "\" height=\"30\" class=\"card-avatar\">" + item.fullname + "</a>" )
        .appendTo( ul );
    };      
        
  });
