

///////////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function(){
	
	// Run the init method on document ready:
	chat.init();
	
});

var chat = {
	
	// data holds variables for use in the class:
	
	data : {
		lastID 		: 0,
		noActivity	: 0,
		id_grupo    : 0
	},
	
	// Init binds event listeners and sets up timers:
	
	init : function(){
		
		// CAMPOS DEL FORMULARIO PARA EL LOGIN
		//$('#name').defaultText('Usuario');
		//$('#email').defaultText('Contraseña');

		
		// Converting the #chatLineHolder div into a jScrollPane,
		// and saving the plugin's API in chat.data:
		
		chat.data.jspAPI = $('#chatLineHolder').jScrollPane({
			verticalDragMinHeight: 12,
			verticalDragMaxHeight: 12
		}).data('jsp');
		
		// We use the working variable to prevent
		// multiple form submissions:
		
		var working = false;
		
		// INICIAR SESION
		
		$('#loginForm').submit(function(){
			
			if(working) return false;
			working = true;
			
			// Using our tzPOST wrapper function
			// (defined in the bottom):
			
			$.tzPOST('login',$(this).serialize(),function(r){
				working = false;
				//alert(r);
				if(r.error){
					chat.displayError(r.error);
				}else{

					chat.login(r.name,r.gravatar);
				} 
			});
			
			return false;
		});
		
		// ENVIAR MENSAJE
		
		$('#submitForm').submit(function(){
			
			var text = $('#chatText').val();
			
			if(text.length == 0){
				return false;
			}
			
			if(working) return false;
			working = true;
			
			// Assigning a temporary ID to the chat:
			var tempID = 't'+Math.round(Math.random()*1000000),
				params = {
					codigo_conversacion		: tempID,
					usuario					: chat.data.name,
					gravatar				: chat.data.gravatar,
					mensaje_texto			: text.replace(/</g,'&lt;').replace(/>/g,'&gt;')
				};


			// Using our addChatLine method to add the chat
			// to the screen immediately, without waiting for
			// the AJAX request to complete:
			
			chat.addChatLine($.extend({},params));
			
			// Using our tzPOST wrapper method to send the chat
			// via a POST AJAX request:
			
			$.tzPOST('submitChat',{chatText:$('#chatText').val(), id_grupo:chat.data.id_grupo},function(r){
				
				working = false;
			
				$('#chatText').val('');
				$('div.chat-'+tempID).remove();
				
				params['codigo_conversacion'] = r.codigo_conversacion['codigo_conversacion'];
				chat.addChatLine($.extend({},params));
			});
			
			return false;
		});
		
		// CERRAR SESION
		
		$('a.logoutButton').live('click',function(){
			chat.data.lastID=0;
			chat.data.id_grupo=0;
			$('#chatTopBar > span').fadeOut(function(){
				$(this).remove();
			});
			
			$('#submitForm').fadeOut(function(){
				$('#loginForm').fadeIn();
			});

			$('.jspPane').attr('style', 'padding: 0px; top: 0px; width: 350px;');
    		$('.jspVerticalBar').remove();
			$('.jspPane').fadeOut(function(){

				chat.data.jspAPI.getContentPane().html('<p class="noChats">Inicia sesión para ver los mensajes...</p>');
			});
			$('.MisGrupos >div').remove();

			
			
			$.tzPOST('logout');
			
			return false;
		});
		

		// VERIFICAR LOGGIN AL RECARGAR LA PAGINA
		
		$.tzGET('checkLogged',function(r){
			

			if(r.logged){
				chat.login(r.loggedAs.name,r.loggedAs.gravatar);
			}
		});
		
		// Self executing timeout functions
		
		(function getChatsTimeoutFunction(){
			
			chat.getChats(getChatsTimeoutFunction);
			chat.CargarGrupos();
		})();
		
		(function getUsersTimeoutFunction(){
			
			//chat.getUsers(getUsersTimeoutFunction);
			
		})();



		///////////////////////////////////////MIS AGREGADOS---->>>>>>>
		$('#crearGrupo').click(function(){
		$.tzPOST('crearGrupo_addUsuaurio',$("#addUsuario,#nombreGrupo").serialize(),function(r){

			if(r.error){
					chat.displayError(r.error);
				}else{

					
			        if(r.result==0){
		                alert("ERROR...!");

		            }else if(r.result==1){
		                alert("Grupo Creado Con Exito - Usuario Agregado");

		            }else if(r.result==2){
		                alert("Ya estas Agregado a Este Grupo");
		                
		            }else if(r.result==3){
		                alert("El Usuaurio Que Intenta Add not Existe");

		                
		            }

				chat.CargarGrupos();
				} 

   
         


		});
	});


	},

/////////////////////////////////////////////////--------I N I T---------///////////////////////////////////

	




	CargarGrupos : function(){
		
		$.tzGET('CargarMisGrupos',{},function(r){

            $(".MisGrupos").html("");

            $.each(r.Grupos, function(index, value){
                 

                $(".MisGrupos").append("<div class='Grupo'><br><center><button name='grupo' class='btnGrupo' id='"+value['codigo_grupo']+"' value='"+value['codigo_grupo']+"'>"+value['nombre_grupo']+"</button></center></div>");
             

            });

            CargarEvento();

		});
		


		
	},







	// The login method hides displays the
	// user's login data and shows the submit form
	
	login : function(name,gravatar){
		//chat.data.lastID=0;
		chat.data.name = name;
		chat.data.gravatar = gravatar;
		$('#chatTopBar').html(chat.render('loginTopBar',chat.data));
		
		$('#loginForm').fadeOut(function(){

			$('.jspPane').fadeIn();
			$('#mientras').remove();
			$('#submitForm').fadeIn();
			$('#chatText').focus();
		});
		
	},
	
	// The render method generates the HTML markup 
	// that is needed by the other methods:
	
	render : function(template,params){
		
		var arr = [];

		switch(template){
			
			case 'loginTopBar':
				arr = [
				'<span><img src="',params.gravatar,'" width="23" height="23" />',
				'<span class="name">',params.name,
				'</span><a href="" class="logoutButton rounded">Logout</a></span>'];
			break;
			
			case 'chatLine':
				arr = [
					'<div class="chat chat-',params.codigo_conversacion,' rounded"><span class="gravatar"><img src="',params.gravatar,
					'" width="23" height="23" onload="this.style.visibility=\'visible\'" />','</span><span class="author">',params.usuario,
					':</span><span class="text">',params.mensaje_texto,'</span><span class="time">',params.time,'</span></div>'];
			break;
			
			case 'user':
				arr = [
					'<div class="user" title="',params.name,'"><img src="',
					params.gravatar,'" width="30" height="30" onload="this.style.visibility=\'visible\'" /></div>'
				];
			break;
		}
		
		// A single array join is faster than
		// multiple concatenations
		
		return arr.join('');
		
	},
	
	// The addChatLine method ads a chat entry to the page
	
	addChatLine : function(params){
		
		// All times are displayed in the user's timezone
		//alert(params.id);
		var d = new Date();
		if(params.time) {
			//alert(d.getUTCHours());
			params.time = (params.time.hours+':'+params.time.minutes);
		}else{

			//----HORA DEL SISTEMA EN JS
		}
		
		
		
		var markup = chat.render('chatLine',params),
			exists = $('#chatLineHolder .chat-'+params.codigo_conversacion);

		if(exists.length){
			exists.remove();
		}
		
		if(!chat.data.lastID){
			// If this is the first chat, remove the
			// paragraph saying there aren't any:
			
			$('#chatLineHolder p').remove();
		}
		
		// If this isn't a temporary chat:
		if(params.codigo_conversacion.toString().charAt(0) != 't'){
			var previous = $('#chatLineHolder .chat-'+(+params.codigo_conversacion - 1));
			if(previous.length){
				previous.after(markup);
			}
			else chat.data.jspAPI.getContentPane().append(markup);
		}
		else chat.data.jspAPI.getContentPane().append(markup);
		
		// As we added new content, we need to
		// reinitialise the jScrollPane plugin:
		
		chat.data.jspAPI.reinitialise();
		chat.data.jspAPI.scrollToBottom(true);
		
	},
	
	// This method requests the latest chats
	// (since lastID), and adds them to the page.
	
	getChats : function(callback){
	//alert(chat.data.lastID);
		$.tzGET('getChats',{lastID: chat.data.lastID, id_grupo: chat.data.id_grupo},function(r){

			
			//alert(r.chats);
			for(var i=0;i<r.chats.length;i++){
				
				chat.addChatLine(r.chats[i]);
			}
			
			if(r.chats.length){

				chat.data.noActivity = 0;
				chat.data.lastID = r.chats[i-1].codigo_conversacion;
			}
			else{
				// If no chats were received, increment
				// the noActivity counter.
				
				chat.data.noActivity++;
			}
			
			if(r.chats==-1){
				//alert("entro");
				chat.data.jspAPI.getContentPane().html('<p class="noChats">Inicia sesión para ver los mensajes...</p>');
			}else
			if(!chat.data.lastID){
			 	
				chat.data.jspAPI.getContentPane().html('<p class="noChats">No hay mensajes...</p>');
				//alert(chat.data.lastID);
			}
			
			// Setting a timeout for the next request,
			// depending on the chat activity:
			
			var nextRequest = 1000;
			
			// 2 seconds
			if(chat.data.noActivity > 3){
				nextRequest = 2000;
			}
			
			if(chat.data.noActivity > 10){
				nextRequest = 5000;
			}
			
			// 15 seconds
			if(chat.data.noActivity > 20){
				nextRequest = 15000;
			}
		
			setTimeout(callback,nextRequest);
		});
	},
	
	// Requesting a list with all the users.
	
	getUsers : function(callback){
		$.tzGET('getUsers',function(r){


			var users = [];
			
			for(var i=0; i< r.users.length;i++){
				if(r.users[i]){
					users.push(chat.render('user',r.users[i]));
				}
			}
			
			var message = '';
			
			if(r.total['total']<1){
				message = '(0) Personas conectadas';
			}
			else {
				message = r.total['total']+' '+(r.total['total'] == 1 ? 'Persona(s)':'people')+' conectada(s)';
			}
			
			users.push('<p class="count">'+message+'</p>');
			
			$('#chatUsers').html(users.join(''));
			
			setTimeout(callback,15000);
		});
	},
	
	// This method displays an error message on the top of the page:
	
	displayError : function(msg){
		var elem = $('<div>',{
			id		: 'chatErrorMessage',
			html	: msg
		});
		
		elem.click(function(){
			$(this).fadeOut(function(){
				$(this).remove();
			});
		});
		
		setTimeout(function(){
			elem.click();
		},5000);
		
		elem.hide().appendTo('body').slideDown();
	}
};


//////////////////////////////////////------- C H A T ----------///////////////////////////////////////





// METODOS GET Y POST PARA ENVIAR A LA BASE DE DATOS:

$.tzPOST = function(action,data,callback){
	
	$.post('php/ajax.php?action='+action,data,callback,'json');
}

$.tzGET = function(action,data,callback){

	$.get('php/ajax.php?action='+action,data,callback,'json');
}
/////////////////////////////////////////////////////////


// A custom jQuery method for placeholder text:

$.fn.defaultText = function(value){
	
	var element = this.eq(0);
	element.data('defaultText',value);
	
	element.focus(function(){
		if(element.val() == value){
			element.val('').removeClass('defaultText');
		}
	}).blur(function(){
		if(element.val() == '' || element.val() == value){
			element.addClass('defaultText').val(value);
		}
	});
	
	return element.blur();
}
















//-----------------------------------MI AGREGADO------------------------------------------------------->>>>>


function CargarEvento(){
	//----SELECCIONAR GRUPO´PARA CHATEAR--->>>
////////////P E N D I N E N T E -----------------------------------
    $('.btnGrupo').click(function(e){ //click en eliminar campo
            
            var result=$(this).parent().find('.btnGrupo').val();//----POR MEDIO DEL BOTON ELIMINAR OBTENGO EL NUMERO PARA SACAR EL ID DE LA SECCION QUE QUIERO ELIMINAR
            //alert(result);
            $('#id_grupo').val(result);

            //----CARGO EL CHAT--->>>
            SeleccionarGrupo_CargarChat(result);

        return false;
    });

}

function SeleccionarGrupo_CargarChat(id_grupo) 
{ 
    //alert(id_grupo);
    if(id_grupo==''){

        //alert(id_grupo);

    }else{

    	if(chat.data.id_grupo!=id_grupo){
				
				$('.jspPane').attr('style', 'padding: 0px; top: 0px; width: 350px;');
    			$('.jspVerticalBar').remove();
    			$('#chatLineHolder p').remove();


    			chat.data.lastID=0;
				chat.data.id_grupo=id_grupo;
		    	chat.getChats('');
    	}else{

    	}
        
              

    }


    }
    
 