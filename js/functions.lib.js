// JavaScript Document
/**
 *
 * PROTOTYPES
 *
 **/
Number.PAD_LEFT  = 0;
Number.PAD_RIGHT = 1;
Number.PAD_BOTH  = 2;

/**
 *
 */
String.prototype.isEmpty = function() {
	return ( this.lenght == 0 ) || ( this.trim().length == 0 );
};

/**
 *
 */
String.prototype.toInt = function() {
	return this.isEmpty() ? 0 : parseInt( this.replaceAll( ".", "" ), 10 );
};

/**
 *
 * @param oldVal
 * @param newVal
 * @return
 */
String.prototype.replaceAll = function( oldVal, newVal ) {
	var str = this;

	while ( str.indexOf( oldVal ) > -1 ) {
		str = str.replace( oldVal, newVal );
	};

	return str;
};

if (!String.prototype.startsWith) {
    String.prototype.startsWith = function(searchString, position){
      position = position || 0;
      return this.substr(position, searchString.length) === searchString;
  };
}

/**
 *
 * @param size
 * @param pad
 * @param side
 */
Number.prototype.toPadString = function( size, pad, side ) {
	if ( !pad ) {
		pad = "0";
	};

	if ( !side ) {
		side = Number.PAD_LEFT;
	};

  	var str    = "" + this,
  	    append = "",
  	    size   = ( size - str.length );
 	var pad = ( ( pad != null ) ? pad : " " );

  	if ( side == Number.PAD_BOTH ) {
    	str = str.pad((Math.floor(size / 2) + str.length), pad, String.PAD_LEFT);

    	return str.pad((Math.ceil(size / 2) + str.length), pad, String.PAD_RIGHT);
  	};

  	while ((size -= pad.length) > 0) {
    	append += pad;
  	};

  	append += pad.substr(0, (size + pad.length));

  	return ((side == Number.PAD_LEFT) ? append.concat(str) : str.concat(append));
};

/**
 *
 */
Date.prototype.toFormattedDate = function() {
	var month = this.getMonth() + 1;

	return this.getDate().toPadString(2) + "/" +
		   month.toPadString(2) + "/" +
		   this.getFullYear();
};

/**
 *
 */
Date.prototype.toDateTime = function() {
	var month = this.getMonth() + 1;

	return this.getFullYear() + "-" +
	       month.toPadString(2) + "-" +
		   this.getDate().toPadString(2) + " " +
		   this.getHours().toPadString(2) + ":" +
		   this.getMinutes().toPadString(2) + ":" +
		   this.getSeconds().toPadString(2);
		   // + "." +this.getMilliseconds().toPadString(3)
};

var SPMaskBehavior = function (val) {
  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
},
spOptions = {
  onKeyPress: function(val, e, field, options) {
	  //field.mask(SPMaskBehavior.apply({}, arguments), options);
	}
};

$.fn.visible = function(lVisible) {
	if (lVisible) {
		this.show();
	} else {
		this.hide();
	}
	return this;
};
$.fn.isEnabled = function() {
	return this.attr( "disabled" ) == undefined || this.attr( "disabled" ).isEmpty();
};
$.fn.hasAttr = function(attr) {
	return this.attr( attr ) !== undefined && !this.attr( attr ).isEmpty();
};
$.fn.enable = function(lEnable) {
	if (lEnable) {
		this.removeAttr('disabled');
	} else {
		this.attr('disabled','disabled');
	}
	return this;
};
$.fn.selectpicker.defaults = {
	noneSelectedText: '(NENHUM)',
	noneResultsText: 'Nada encontrado contendo {0}',
	countSelectedText: 'Selecionado {0} de {1}',
	maxOptionsText: ['Limite excedido (máx. {n} {var})', 'Limite do grupo excedido (máx. {n} {var})', ['itens', 'item']],
	multipleSeparator: ', ',
	deselectAllText: 'Desmarcar Todos',
	selectAllText: 'Marcar Todos'
};

var jsLIB = {
	rootDir : "/",
	parameters : {},

	watingDialog : function(){

	},

	modalWaiting : function( show ) {
		if ( !jsLIB.watingDialog.opened ) {
			//jsLIB.watingDialog.realize();
			//jsLIB.watingDialog.getModalHeader().hide();
			//jsLIB.watingDialog.getModalFooter().hide();
			//jsLIB.watingDialog.getModalBody().css('background-color', '#0088cc');
			//jsLIB.watingDialog.getModalBody().css('color', '#fff');
		}
		if (show) {
			//jsLIB.watingDialog.open();
		} else {
			//jsLIB.watingDialog.close();
		}
	},

	ajaxCall : function( objParam ) {
		var retorno = undefined;
		if (objParam.waiting === true){
			jsLIB.modalWaiting(true);
		}
		$.ajax({
			url			: objParam.url,
			async		: (objParam.async !== undefined ? objParam.async : true),
			type		: (objParam.type !== undefined ? objParam.type : 'POST'),
			data		: objParam.data,
			dataType	: 'json',
			success		: ( data, textStatus, jqxhr ) => {
				if (objParam.waiting === true){
					jsLIB.modalWaiting(false);
				}
				if ( typeof( objParam.success ) == 'function' ) {
					objParam.success( data, jqxhr );
				} else if ( objParam.success === undefined ) {
					retorno = data;
				}
			},
			error	: ( jqxhr, textStatus, message ) => {
				if (objParam.waiting === true){
					jsLIB.modalWaiting(false);
				}
				if ( typeof( objParam.error ) == 'function' ) {
					objParam.error( jqxhr, message );
				}
			}
		});
		return retorno;
	},

	getJSONFields : function( frm ) {
		var retorno = {};
		frm.find( $('[field]') ).each( function() {
			retorno[$(this).attr("field")] = jsLIB.getValueFromField($(this));
		});
		return retorno;
	},

	getURIFields : function( frm ) {
		var retorno = "";
		frm.find( $('[field]') ).each( function() {
			var value = jsLIB.getValueFromField($(this));
			if (value){
				retorno += (retorno.length == 0?"":"&") + $(this).attr("field") +"="+ value;
			}
		});
		return retorno;
	},

	getValueFromField : function( inputField ) {
		var value = "";
		switch ( inputField.attr("type") ) {
			case "radio":
			case "checkbox":
				if ( inputField.prop('checked') ) {
					value = inputField.attr('value-on');
				} else {
					value = inputField.attr('value-off');
				};
				break;
			case "wysiwyg":
				value = tinymce.get(inputField.get(0).id).getContent();
				break;
			default:
				value = inputField.val();
		}
		return value;
	},

	resetForm : function( frm ) {
		frm.find( $('[field]') ).each( function() {
			$(this).parents('.form-group').removeClass('has-success');
			var value = '';
			if ( $(this).attr('default-value') !== undefined && $(this).attr('default-value') != '' ) {
				value = $(this).attr('default-value');
			}
			switch ( $(this).attr("type") ) {
				case "radio":
				case "checkbox":
					$(this).prop('checked', false).change();
					break;
				case "wysiwyg":
					tinymce.get($(this).get(0).id).setContent('');
					break;
				case "text":
					if ( $(this).parent().attr("datatype") == 'datetimepicker' ) {
						$(this).parent().data("DateTimePicker").setDate( null );
						$(this).val(value);
						$(this).change();
						break;
					}
				default:
					$(this).val(value);
					$(this).change();
					if ( $(this).hasClass("selectpicker") ) {
						$(this).selectpicker('refresh');
					}
					break;
			}
		});
	},

	populateForm : function( frm, data ) {
		jsLIB.resetForm(frm);
		$.each( data, function( key, value ) {
			var ctrl = $('[field='+key+']', frm.id );
			switch ( ctrl.attr("type") ) {
				case "radio":
				case "checkbox":
					if ( ctrl.attr("value-on") == value ) {
						ctrl.prop('checked', true).change();
					} else {
						ctrl.prop('checked', false).change();
					}
					break;
				case "wysiwyg":
					tinymce.get(ctrl.get(0).id).setContent(value);
					break;
				case "text":
					if ( ctrl.parent().attr("datatype") == 'datetimepicker' ) {
						ctrl.parent().data("DateTimePicker").setDate( new Date(value.toInt()) );
						break;
					}
				case "hidden":
				default:
					if (ctrl.hasClass("selectpicker")){
						ctrl.selectpicker('val',value).change();
					} else {
						ctrl.val(value).change();
					}
			}
		});
	},

	populateOptions : function( objSelect, source ) {
		var value = ( objSelect.hasAttr("opt-value") ? objSelect.attr("opt-value") : "id" );
		var label = ( objSelect.hasAttr("opt-label") ? objSelect.attr("opt-label") : "ds" );
		var search = ( objSelect.hasAttr("opt-search") ? objSelect.attr("opt-search") : label );
		var subtext = ( objSelect.hasAttr("opt-subtext") ? objSelect.attr("opt-subtext") : null );
		var selected = ( objSelect.hasAttr("opt-selected") ? objSelect.attr("opt-selected") : null );
		var links = ( objSelect.hasAttr("opt-links") ? objSelect.attr("opt-links").split(";") : null );

		var oLinkIcon = null;
		if (objSelect.hasAttr("opt-link-icons")){
			oLinkIcon = [];
			objSelect.attr("opt-link-icons").split(";").forEach(function(linkIcon){
				var lk = linkIcon.split('=');
				oLinkIcon[lk[0]] = lk[1];
			});
		}

		var oLinkClass = null;
		if (objSelect.hasAttr("opt-link-class")){
			oLinkClass = [];
			objSelect.attr("opt-link-class").split(";").forEach(function(linkClass){
				var lk = linkClass.split('=');
				oLinkClass[lk[0]] = lk[1];
			});
		}

		objSelect.children().remove();
		if ( !objSelect.hasClass("selectpicker") || objSelect.attr("add-none") == "true" ) {
			 objSelect.append( $("<option></option>")
				.attr("value","").text("(NENHUM)"));
		}
		$.each(source, function(idx, option) {
			obj = $("<option></option>")
					.attr("value",option[value])
					.text(option[label]);
			if (search && search != label){
				obj.attr("data-tokens",option[search]+' '+option[label]);
			}
			if (subtext){
				obj.attr("data-subtext",option[subtext]);
			}
			if (selected && option[selected] == 'S'){
				obj.attr("selected","selected");
			}
			if (links) {
				links.forEach(function(link){
					obj.attr(link,option[link]);
					if (oLinkClass){
						obj.attr('class', oLinkClass[option[link]] );
					}
					if (oLinkIcon){
						obj.attr('data-icon', oLinkIcon[option[link]] );
					}
				});
			}
			objSelect.append(obj)
		});
		if ( objSelect.hasClass("selectpicker") ) {
			if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
				objSelect.selectpicker('mobile');
			} else {
				objSelect.selectpicker('refresh');
			}
		}
	}
};

var _params = window.location.search
		.substring(1, window.location.search.length).split('&');
for ( var index = 0, param; (param = _params[index]); index++) {
	var parts = param.split('=');
	var name = parts[0].trim();
	if (!name.isEmpty()) {
		jsLIB.parameters[name] = parts[1];
	};
};

var jsFilter = {
	filtered : false,

	apply: function (){
		jsFilter.filtered = true;
		var obj = $( $("#divFilters").attr("filter-to") );
		if ( obj ) {
			if ( obj.is( "SELECT" ) ){
				obj.trigger("reload.options.bs.select");
			} else if ( obj.is( "TABLE" ) ){
				obj.DataTable().ajax.reload();
			}
		}
	},

	jSON: function (){
		var retorno = {};
		$("#divFilters select").each(function(i,obj){
			var reg = {
				vl : $(obj).val(),
				fg : $("#notFilter"+$(obj).attr("filter-field")).prop('checked')
			};
			retorno[$(obj).attr("filter-field")] = reg;
		});
		jsFilter.filtered = (retorno !== {});
		return retorno;
	},

	removeAll : function(){
		$("[filter-value]").each(function(){
			jsFilter.removeFilter(this);
		});
	},

	removeFilter : function (objFilter){
		var obj = $(objFilter);
		var value = obj.attr("filter-value");
		var label = obj.attr("filter-label");
		var icon = obj.attr("filter-icon");

		var obj = $("<option></option>")
			.attr("value",value)
			.text(label);
		if (icon){
			obj.attr('data-icon',icon);
		}

		$("#addFilter").append(obj);
		$("#addFilter").html($("#addFilter").children('option').sort(function(x, y) {
			return $(x).text().toUpperCase() < $(y).text() ? - 1 : 1;
		}));
		$("#addFilter").val("").selectpicker('refresh');
		$("#optFilter"+value).selectpicker('destroy');
		$("#divFilter"+value).remove();
		if ( jsFilter.filtered ) {
			jsFilter.apply();
		}
		if ( $("#divFilters select").length == 0 ) {
			$("#applyFilter").hide();
			jsFilter.filtered = false;
		}
	},

	addFilter : function (objFilter){
		var obj = $(objFilter);
		var objSelected = obj.find('option:selected');
		var label = objSelected.text();
		var icon = objSelected.attr('data-icon');
		var value = obj.val();

		if (value != ""){
			jsLIB.ajaxCall({
				waiting : false,
				async: false,
				type: "GET",
				url: jsLIB.rootDir+"rules/addFilter.php",
				data: { MethodName : 'addFilter', data : { type : value, desc : label, icon } },
				success: function(flt){
					if ( flt.result ) {
						$("#divFilters").append(flt.obj);
						$("#optFilter"+value).selectpicker();
						$("#addFilter option[value='"+value+"']").remove();
						$("#addFilter").selectpicker('refresh');
						$("#applyFilter").show();
					}
				}
			});
		}
	}
};
