var cpfb_started=false;
(function($) {
	$.fn.fbuilder = function(options){
		var opt = $.extend({},
				{
	   				typeList:new Array({id:"ftext",name:"Single Line Text"},{id:"fnumber",name:"Number"},{id:"femail",name:"Email"},{id:"fdate",name:"Date"},{id:"ftextarea",name:"Paragraph Text"},{id:"fcheck",name:"Checkboxes"},{id:"fradio",name:"Multiple Choice"},{id:"fdropdown",name:"Dropdown"},{id:"ffile",name:"Upload file"},{id:"fSectionBreak",name:"Section break"},{id:"fPhone",name:"Phone field"},{id:"fCommentArea",name:"Comment Area"}),
					pub:false,
					title:""
				},options, true);
		if (opt.pub)
		{
			opt = $.extend({
					messages: {
						required: "This field is required.",
						email: "Please enter a valid email address.",
						datemmddyyyy: "Please enter a valid date with this format(mm/dd/yyyy)",
						dateddmmyyyy: "Please enter a valid date with this format(dd/mm/yyyy)",
						number: "Please enter a valid number.",
						digits: "Please enter only digits.",
						maxlength: $.validator.format("Please enter no more than {0} characters"),
                        minlength: $.validator.format("Please enter at least {0} characters."),
						max: $.validator.format("Please enter a value less than or equal to {0}."),
						min: $.validator.format("Please enter a value greater than or equal to {0}.")
					}
				},opt);
			opt.messages.max = $.validator.format(opt.messages.max);
			opt.messages.min = $.validator.format(opt.messages.min);
			$.extend($.validator.messages, opt.messages);
		}
		getNameByIdFromType = function(id){
			for (var i=0;i<opt.typeList.length;i++)
				if (opt.typeList[i].id == id)
					return opt.typeList[i].name;
			return "";
		}
		for (var i=0;i<opt.typeList.length;i++)
			$("#tabs-1").append('<div class="button itemForm width40" id="'+opt.typeList[i].id+'">'+opt.typeList[i].name+'</div>');
		$("#tabs-1").append('<div class="clearer"></div>');
		if (!opt.pub) $( ".button").button();
		var items = new Array();
		var itemSelected = -2;
		editItem = function(id) {
			if (!opt.pub) $('#tabs').tabs('select', 1);
			try { $('#tabs-2').html(items[id].showAllSettings()); } catch (e) {}
			itemSelected = id;
			$(".helpfbuilder").click(function(){
                alert($(this).attr("text"));
			});
            $("#sMinDate").change(function(){
                items[id].minDate = $(this).val();
                reloadItems();
            });
            $("#sMaxDate").change(function(){
                items[id].maxDate = $(this).val();
                reloadItems();
            });
            $("#sDefaultDate").change(function(){
                items[id].defaultDate = $(this).val();
                reloadItems();
            });
			$("#sTitle").keyup(function(){
				var str = $(this).val();
				items[id].title = str.replace(/\n/g,"<br />");
				reloadItems();
			});
			$("#sName").keyup(function(){
				items[id].name = $(this).val(); 
				reloadItems();
			});
			$("#sPredefined").keyup(function(){
				items[id].predefined = $(this).val();
				reloadItems();
			});
			$("#sDropdownRange").keyup(function(){
				items[id].dropdownRange = $(this).val();
				reloadItems();
			});			
			$("#sRequired").click(function(){
				items[id].required = $(this).is(':checked');
				reloadItems();
			});
			$("#sShowDropdown").click(function(){
				items[id].showDropdown = $(this).is(':checked');
				if ($(this).is(':checked'))
				    $("#divdropdownRange").css("display","");
				else
				    $("#divdropdownRange").css("display","none");    
				reloadItems();
			});
			$("#sSize").change(function(){
				items[id].size = $(this).val();
				reloadItems();
			});
			$("#sFormat").change(function(){
				items[id].dformat = $(this).val();
				reloadItems();
			});
			$("#sLayout").change(function(){
				items[id].layout = $(this).val();
				reloadItems();
			});
			$("#sMin").change(function(){
				items[id].min = $(this).val();
				reloadItems();
			});
			$("#sMax").change(function(){
				items[id].max = $(this).val();
				reloadItems();
			});
			$(".choice_remove").click(function(){
				if (items[id].choices.length==1)
					items[id].choices[0]="";
				else
					items[id].choices.splice($(this).attr("i"),1);
				if (items[id].ftype=="fcheck")
				{
					if (items[id].choiceSelected.length==1)
						items[id].choiceSelected[0]="";
					else
						items[id].choiceSelected.splice($(this).attr("i"),1);
				}
				editItem(id);
				reloadItems();
			});
			$(".choice_add").click(function(){
				items[id].choices.splice($(this).attr("i")+1,0,"");
				if (items[id].ftype=="fcheck")
					items[id].choiceSelected.splice($(this).attr("i")+1,0,false);
				editItem(id);
				reloadItems();
			});
			$(".choice_text").keyup(function(){
				items[id].choices[$(this).attr("i")]= $(this).val();
				reloadItems();
			});
			$(".choice_radio").click(function(){
				if ($(this).is(':checked'))
					items[id].choiceSelected = items[id].choices[$(this).attr("i")];
				reloadItems();
			});
			$(".choice_select").click(function(){
				if ($(this).is(':checked'))
					items[id].choiceSelected = items[id].choices[$(this).attr("i")];
				reloadItems();
			});
			$(".choice_check").click(function(){
				if ($(this).is(':checked'))
					items[id].choiceSelected[$(this).attr("i")] = true;
				else
					items[id].choiceSelected[$(this).attr("i")] = false;
				reloadItems();
			});
			$("#sUserhelp").keyup(function(){
				items[id].userhelp = $(this).val();
				reloadItems();
			});
			$("#sCsslayout").keyup(function(){
				items[id].csslayout = $(this).val();
				reloadItems();
			});
		};
		editForm = function() {
			$('#tabs-3').html(theForm.showAllSettings());
			itemSelected = -1;
			$("#fTitle").keyup(function(){
				theForm.title = $(this).val();
				reloadItems();
			});
			$("#fDescription").keyup(function(){
				theForm.description = $(this).val();
				reloadItems();
			});
			$("#fLayout").change(function(){
				theForm.formlayout = $(this).val();
				reloadItems();
			});

		};
		removeItem = function(index) {
			items.splice(index,1);
			for (var i=0;i<items.length;i++)
				items[i].index = i;
			$('#tabs').tabs('select', 0);
			reloadItems();
		}
		reloadItems = function() {
		    if (cpfb_started){alert('* Note: The Form Builder is read-only in this version.');return;}else {cpfb_started=true;}
			for (var i=0;i<showSettings.formlayoutList.length;i++)
				$("#fieldlist").removeClass(showSettings.formlayoutList[i].id);
			$("#fieldlist").addClass(theForm.formlayout);
			$("#formheader").html(theForm.display());
			$("#fieldlist").html("");
			if (parseInt(itemSelected)==-1)
				$(".fform").addClass("ui-selected");
			else
				$(".fform").removeClass("ui-selected");
			for (var i=0;i<items.length;i++)
			{
				items[i].index = i;
				$("#fieldlist").append(items[i].display());
				if (i==itemSelected)
					$("#field-"+i).addClass("ui-selected");
				else
					$("#field-"+i).removeClass("ui-selected");
				$(".fields").mouseover(function() {
					$(this).addClass("ui-over");
				}).mouseout(function(){
					$(this).removeClass("ui-over")
				}).click(function(){
					editItem($(this).attr("id").replace("field-",""));
					$(this).siblings().removeClass("ui-selected");
					$(this).addClass("ui-selected");
				});
				$(".field").focus(function(){
					$(this).blur();
				});
				$("#field-"+i+" .remove").click(function(){
					removeItem($(this).parent().attr("id").replace("field-",""));
				});
			}
			if ($("#fieldlist").html() == "")
				$("#saveForm").css("display","none");
			else
				$("#saveForm").css("display","none"); // changed "inline" to "none"
			$(".fform").mouseover(function() {
				$(this).addClass("ui-over");
			}).mouseout(function(){
				$(this).removeClass("ui-over")
			}).click(function(){
				$('#tabs').tabs('select', 2);
				editForm();
				$(this).siblings().removeClass("ui-selected");
				$(this).addClass("ui-selected");
			});
			
			//email list
			var str = "";
			for (var i=0;i<items.length;i++)
				if (items[i].ftype=="femail")
					str += '<option value="'+items[i].name+'" '+((items[i].name == $('#cu_user_email_field').attr("def"))?"selected":"")+'>'+(items[i].title)+'</option>';
					//getNameByIdFromType
			$('#cu_user_email_field').html(str);


		}
		function htmlEncode(value){
		  value = $('<div/>').text(value).html()  
          value = value.replace(/"/g, "&quot;");;
          return value;
        }
		reloadItemsPublic = function() {
			for (var i=0;i<showSettings.formlayoutList.length;i++)
				$("#fieldlist").removeClass(showSettings.formlayoutList[i].id);
			$("#fieldlist").addClass(theForm.formlayout);
			$("#formheader").html(theForm.show());
			//$("#fieldlist").append('<form action="" method="post" name="form1" id="form1"></form>');
			for (var i=0;i<items.length;i++)
			{
				items[i].index = i;
				$("#fieldlist").append(items[i].show());
				$(".fields").mouseover(function() {
					$(this).addClass("ui-over");
				}).mouseout(function(){
					$(this).removeClass("ui-over")
				}).click(function(){
					editItem($(this).attr("id").replace("field-",""));
					$(this).siblings().removeClass("ui-selected");
					$(this).addClass("ui-selected");
				});
				if (items[i].ftype=="fdate")
				{
				    if (items[i].showDropdown) 				
					    $( "#"+items[i].name ).datepicker({changeMonth: true,changeYear: true,yearRange: items[i].dropdownRange,dateFormat: items[i].dformat.replace(/yyyy/g,"yy")});
					else
					    $( "#"+items[i].name ).datepicker({ dateFormat: items[i].dformat.replace(/yyyy/g,"yy")});
                    $( "#"+items[i].name ).datepicker( "option", "minDate", items[i].minDate );
                    $( "#"+items[i].name ).datepicker( "option", "maxDate", items[i].maxDate );
                    $( "#"+items[i].name ).datepicker( "option", "defaultDate", items[i].defaultDate );
				}	
					
			}
			if (i>0)
			{

				//$("#form1").append('<div class="fields"><label>&nbsp;</label><div class="dfield"><input type="submit" class="button submit" id="btnSave" value="Save"/></div><div class="clearer"></div></div>');
				//$( ".button").button();
                $.validator.addMethod("dateddmmyyyy", function(value, element) {				    
				  return this.optional(element) || /^(?:[1-9]|0[1-9]|1[0-9]|2[0-9]|3[0-1])[\/\-](?:[1-9]|0[1-9]|1[0-2])[\/\-]\d{4}$/.test(value);
				});

				$.validator.addMethod("datemmddyyyy", function(value, element) {
				  return this.optional(element) || /^(?:[1-9]|0[1-9]|1[0-2])[\/\-](?:[1-9]|0[1-9]|1[0-9]|2[0-9]|3[0-1])[\/\-]\d{4}$/.test(value);
				});//{required: true, range: [11, 22]}




			}
		}
		var showSettings= {
			sizeList:new Array({id:"small",name:"Small"},{id:"medium",name:"Medium"},{id:"large",name:"Large"}),
			layoutList:new Array({id:"one_column",name:"One Column"},{id:"two_column",name:"Two Column"},{id:"three_column",name:"Three Column"},{id:"side_by_side",name:"Side by Side"}),
			formlayoutList:new Array({id:"top_aligned",name:"Top Aligned"},{id:"left_aligned",name:"Left Aligned"},{id:"right_aligned",name:"Right Aligned"}),
			showTitle: function(f,v) {
				return '<label>Field Type: '+getNameByIdFromType(f)+'</label><br /><br /><label>Field Label</label><textarea class="large" name="sTitle" id="sTitle">'+v+'</textarea>';
			},
			showName: function(v) {
				return '<div><label>Field tag for the message (optional):</label><input readonly="readonly" class="large" name="sNametag" id="sNametag" value="&lt;%'+v+'%&gt;" />'+
				       '<input style="display:none" readonly="readonly" class="large" name="sName" id="sName" value="'+v+'" /></div>';
			},
			showPredefined: function(v) {
				return '<label>Predefined Value</label><textarea class="large" name="sPredefined" id="sPredefined">'+v+'</textarea>';
			},
			showRequired: function(v) {
				return '<div><input type="checkbox" name="sRequired" id="sRequired" '+((v)?"checked":"")+'><label>Required</label></div>';
			},
			showSize: function(v) {
				var str = "";
				for (var i=0;i<this.sizeList.length;i++)
					str += '<option value="'+this.sizeList[i].id+'" '+((this.sizeList[i].id==v)?"selected":"")+'>'+this.sizeList[i].name+'</option>';
				return '<label>Field Size</label><br /><select name="sSize" id="sSize">'+str+'</select>';
			},
			showLayout: function(v) {
				var str = "";
				for (var i=0;i<this.layoutList.length;i++)
					str += '<option value="'+this.layoutList[i].id+'" '+((this.layoutList[i].id==v)?"selected":"")+'>'+this.layoutList[i].name+'</option>';
				return '<label>Field Layout</label><br /><select name="sLayout" id="sLayout">'+str+'</select>';
			},
			showUserhelp: function(v) {
				return '<label>Instruccions for User</label><textarea class="large" name="sUserhelp" id="sUserhelp">'+v+'</textarea>';
			},
			showCsslayout: function(v) {
				return '<label>Add Css Layout Keywords</label><input class="large" name="sCsslayout" id="sCsslayout" value="'+v+'" />';
			}
		};
		var fform=function(){};
		$.extend(fform.prototype,{
				title:"Untitled Form",
				description:"This is my form. Please fill it out. It's awesome!",
				formlayout:"top_aligned",
				display:function(){
					return '<div class="fform" id="field"><div class="arrow ui-icon ui-icon-play "></div><h1>'+this.title+'</h1><span>'+this.description+'</span></div>';
				},
				show:function(){
					return '<div class="fform" id="field"><h1>'+this.title+'</h1><span>'+this.description+'</span></div>';
				},
				showAllSettings:function(){
					var str = "";
					for (var i=0;i<showSettings.formlayoutList.length;i++)
						str += '<option value="'+showSettings.formlayoutList[i].id+'" '+((showSettings.formlayoutList[i].id==this.formlayout)?"selected":"")+'>'+showSettings.formlayoutList[i].name+'</option>';
					return '<div><label>Form Name</label><input class="large" name="fTitle" id="fTitle" value="'+htmlEncode(this.title)+'" /></div><div><label>Description</label><textarea class="large" name="fDescription" id="fDescription">'+this.description+'</textarea></div><div><label>Label Placement</label><br /><select name="fLayout" id="fLayout">'+str+'</select></div>';
				}

		});
		var theForm = new fform();
		var ffields=function(){};
		$.extend(ffields.prototype, {
				name:"",
				index:-1,
				ftype:"",
				userhelp:"",
				csslayout:"",
				init:function(){
				},
				showSpecialData:function(){
					if(typeof this.showSpecialDataInstance != 'undefined')
						return this.showSpecialDataInstance();
					else
						return "";
				},
				showPredefined:function(){
					if(typeof this.predefined != 'undefined')
						return showSettings.showPredefined(this.predefined);
					else
						return "";
				},
				showRequired:function(){
				    if(typeof this.required != 'undefined')
						return showSettings.showRequired(this.required);
					else
						return "";
				},
				showSize:function(){
					if(typeof this.size != 'undefined')
						return showSettings.showSize(this.size);
					else
						return "";
				},
				showLayout:function(){
					if(typeof this.layout != 'undefined')
						return showSettings.showLayout(this.layout);
					else
						return "";
				},
				showRange:function(){
					if(typeof this.min != 'undefined')
						return this.showRangeIntance();
					else
						return "";
				},
				showFormat:function(){
					if(typeof this.dformat != 'undefined')
						try {
							return this.showFormatIntance();
						} catch(e) {return "";}
					else
						return "";
				},
				showChoice:function(){
					if(typeof this.choices != 'undefined')
						return this.showChoiceIntance();
					else
						return "";
				},
				showUserhelp:function(){
						return showSettings.showUserhelp(this.userhelp);
				},
				showCsslayout:function(){
						return showSettings.showCsslayout(this.csslayout);
				},
				showAllSettings:function(){
						return this.showTitle()+this.showName()+this.showSize()+this.showLayout()+this.showFormat()+this.showRange()+this.showRequired()+this.showSpecialData()+this.showPredefined()+this.showChoice()+this.showUserhelp()+this.showCsslayout();
				},
				showTitle:function(){
						return showSettings.showTitle(this.ftype,this.title);
				},
				showName:function(){
						return showSettings.showName(this.name);
				},
				display:function(){
					return 'Not available yet';
				},
				show:function(){
					return 'Not available yet';
				},
				toJSON:function(){
					str = '';
					$.each( this, function(i, n){
						if (typeof n!="function")
						{
							if (str!="")
								str += ",";
							str += '"'+i+'":'+n ;
						}
					});
					return str;
				}
		});
		var ftext=function(){};
		$.extend(ftext.prototype,ffields.prototype,{
				title:"Untitled",
				ftype:"ftext",
				predefined:"",
				required:false,
				size:"medium",
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input class="field disabled '+this.size+'" type="text" value="'+htmlEncode(this.predefined)+'"/><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input id="'+this.name+'" name="'+this.name+'" class="field '+this.size+((this.required)?" required":"")+'" type="text" value="'+htmlEncode(this.predefined)+'"/><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';	
				}
		});
		var femail=function(){};
		$.extend(femail.prototype,ffields.prototype,{
				title:"Email",
				ftype:"femail",
				predefined:"",
				required:false,
				size:"medium",
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input class="field disabled '+this.size+'" type="text" value="'+htmlEncode(this.predefined)+'"/><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input id="'+this.name+'" name="'+this.name+'" class="field email '+this.size+((this.required)?" required":"")+'" type="text" value="'+htmlEncode(this.predefined)+'"/><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';	
				}
		});
		var fnumber=function(){};
		$.extend(fnumber.prototype,ffields.prototype,{
				title:"Number",
				ftype:"fnumber",
				predefined:"",
				required:false,
				size:"small",
				min:"",
				max:"",
				dformat:"digits",
				formats:new Array("digits","number"),
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input class="field disabled '+this.size+'" type="text" value="'+htmlEncode(this.predefined)+'"/><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input id="'+this.name+'" name="'+this.name+'" min="'+this.min+'" max="'+this.max+'" class="field '+this.dformat+' '+this.size+((this.required)?" required":"")+'" type="text" value="'+htmlEncode(this.predefined)+'"/><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';	
				},
				showFormatIntance: function() {
					var str = "";
					for (var i=0;i<this.formats.length;i++)
						str += '<option value="'+this.formats[i]+'" '+((this.formats[i]==this.dformat)?"selected":"")+'>'+this.formats[i]+'</option>';
					return '<div><label>Number Format</label><br /><select name="sFormat" id="sFormat">'+str+'</select></div>';
				},
				showRangeIntance: function() {
					return '<div class="column"><label>Min</label><br /><input name="sMin" id="sMin" value="'+this.min+'"></div><div class="column"><label>Max</label><br /><input name="sMax" id="sMax" value="'+this.max+'"></div><div class="clearer"></div>';
				}
		});
		var fdate=function(){};
		$.extend(fdate.prototype,ffields.prototype,{
				title:"Date",
				ftype:"fdate",
				predefined:"",
				size:"medium",
				required:false,
				dformat:"mm/dd/yyyy",
				showDropdown:false,
				dropdownRange:"-10,+10",
                minDate:"",
                maxDate:"",
                defaultDate:"",
				formats:new Array("mm/dd/yyyy","dd/mm/yyyy"),
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+' ('+this.dformat+')</label><div class="dfield"><input class="field disabled '+this.size+'" type="text" value="'+htmlEncode(this.predefined)+'"/><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+' ('+this.dformat+')</label><div class="dfield"><input id="'+this.name+'" name="'+this.name+'" class="field date'+this.dformat.replace(/\//g,"")+' '+this.size+((this.required)?" required":"")+'" type="text" value="'+htmlEncode(this.predefined)+'"/><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';	
				},
				showFormatIntance: function() {
					var str = "";
					for (var i=0;i<this.formats.length;i++)
						str += '<option value="'+this.formats[i]+'" '+((this.formats[i]==this.dformat)?"selected":"")+'>'+this.formats[i]+'</option>';
					return '<div><label>Date Format</label><br /><select name="sFormat" id="sFormat">'+str+'</select></div>';
				},
                showSpecialDataInstance: function() {
                    var str = "";
                    str += '<div><label>Default date [<a class="helpfbuilder" text="You can put one of the following type of values into this field:\n\nEmpty: Leave empty for current date.\n\nDate: A Fixed date with the same date format indicated in the &quot;Date Format&quot; drop-down field.\n\nNumber: A number of days from today. For example 2 represents two days from today and -1 represents yesterday.\n\nString: A smart text indicating a relative date. Relative dates must contain value (number) and period pairs; valid periods are &quot;y&quot; for years, &quot;m&quot; for months, &quot;w&quot; for weeks, and &quot;d&quot; for days. For example, &quot;+1m +7d&quot; represents one month and seven days from today.">help?</a>]</label><br /><input class="medium" name="sDefaultDate" id="sDefaultDate" value="'+this.defaultDate+'" /></div>';
                    str += '<div><label>Min date [<a class="helpfbuilder" text="You can put one of the following type of values into this field:\n\nEmpty: No min Date.\n\nDate: A Fixed date with the same date format indicated in the &quot;Date Format&quot; drop-down field.\n\nNumber: A number of days from today. For example 2 represents two days from today and -1 represents yesterday.\n\nString: A smart text indicating a relative date. Relative dates must contain value (number) and period pairs; valid periods are &quot;y&quot; for years, &quot;m&quot; for months, &quot;w&quot; for weeks, and &quot;d&quot; for days. For example, &quot;+1m +7d&quot; represents one month and seven days from today.">help?</a>]</label><br /><input class="medium" name="sMinDate" id="sMinDate" value="'+this.minDate+'" /></div>';
                    str += '<div><label>Max date [<a class="helpfbuilder" text="You can put one of the following type of values into this field:\n\nEmpty: No max Date.\n\nDate: A Fixed date with the same date format indicated in the &quot;Date Format&quot; drop-down field.\n\nNumber: A number of days from today. For example 2 represents two days from today and -1 represents yesterday.\n\nString: A smart text indicating a relative date. Relative dates must contain value (number) and period pairs; valid periods are &quot;y&quot; for years, &quot;m&quot; for months, &quot;w&quot; for weeks, and &quot;d&quot; for days. For example, &quot;+1m +7d&quot; represents one month and seven days from today.">help?</a>]</label><br /><input class="medium" name="sMaxDate" id="sMaxDate" value="'+this.maxDate+'" /></div>';
                    str += '<div><input type="checkbox" name="sShowDropdown" id="sShowDropdown" '+((this.showDropdown)?"checked":"")+'/><label>Show Dropdown Year and Month</label><div id="divdropdownRange" style="display:'+((this.showDropdown)?"":"none")+'">Year Range [<a class="helpfbuilder" text="The range of years displayed in the year drop-down: either relative to today\'s year (&quot;-nn:+nn&quot;), absolute (&quot;nnnn:nnnn&quot;), or combinations of these formats (&quot;nnnn:-nn&quot;)">help?</a>]: <input type="text" name="sDropdownRange" id="sDropdownRange" value="'+htmlEncode(this.dropdownRange)+'"/></div></div>';
                    return str;
                }
		});
		var ftextarea=function(){};
		$.extend(ftextarea.prototype,ffields.prototype,{
				title:"Untitled",
				ftype:"ftextarea",
				predefined:"",
				required:false,
				size:"medium",
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><textarea class="field disabled '+this.size+'">'+this.predefined+'</textarea><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><textarea id="'+this.name+'" name="'+this.name+'" class="field '+this.size+((this.required)?" required":"")+'">'+this.predefined+'</textarea><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				}
		});
		var ffile=function(){};
		$.extend(ffile.prototype,ffields.prototype,{
				title:"Untitled",
				ftype:"ffile",
				required:false,
				size:"medium",
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input type="file" class="field disabled '+this.size+'" /><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input type="file" id="'+this.name+'" name="'+this.name+'" class="field '+this.size+((this.required)?" required":"")+'" /><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				}
		});
		var fSectionBreak=function(){};
		$.extend(fSectionBreak.prototype,ffields.prototype,{
				title:"Section Break",
				ftype:"fSectionBreak",
				userhelp:"A description of the section goes here.",
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><div class="section_break"></div><label>'+this.title+'</label><span class="uh">'+this.userhelp+'</span><div class="clearer"></div></div>';
				},
				show:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="section_break"></div><label>'+this.title+'</label><span class="uh">'+this.userhelp+'</span><div class="clearer"></div></div>';
				}
		});
		var fPhone=function(){};
		$.extend(fPhone.prototype,ffields.prototype,{
				title:"Phone",
				ftype:"fPhone",
				required:false,
				dformat:"### ### ####",
				predefined:"888 888 8888",
				display:function(){
				    var str = "";
				    var tmp = this.dformat.split(' ');
				    var tmpv = this.predefined.split(' ');
				    for (var i=0;i<tmpv.length;i++)
				        if ($.trim(tmpv[i])=="")
				            tmpv.splice(i,1);    
				    for (var i=0;i<tmp.length;i++)
				        if ($.trim(tmp[i])!="")
				            str += '<div class="uh_phone" ><input type="text" class="field disabled" style="width:'+(15*$.trim(tmp[i]).length)+'px" value="'+((tmpv[i])?tmpv[i]:"")+'" maxlength="'+$.trim(tmp[i]).length+'" /><br />'+$.trim(tmp[i])+'</div>';
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield">'+str+'<span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
				    var str = "";
				    var tmp = this.dformat.split(' ');
				    var tmpv = this.predefined.split(' ');
				    for (var i=0;i<tmpv.length;i++)
				        if ($.trim(tmpv[i])=="")
				            tmpv.splice(i,1);    
				    for (var i=0;i<tmp.length;i++)
				        if ($.trim(tmp[i])!="")
				            str += '<div class="uh_phone" ><input type="text" id="'+this.name+'_'+i+'" name="'+this.name+'_'+i+'" class="field digits '+((this.required)?" required":"")+'" style="width:'+(15*$.trim(tmp[i]).length)+'px" value="'+((tmpv[i])?tmpv[i]:"")+'" maxlength="'+$.trim(tmp[i]).length+'" minlength="'+$.trim(tmp[i]).length+'"/><br />'+$.trim(tmp[i])+'</div>';
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><input type="hidden" id="'+this.name+'" name="'+this.name+'" class="field " />'+str+'<span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				showFormatIntance: function() {
					return '<div><label>Number Format</label><br /><input type="text" name="sFormat" id="sFormat" value="'+this.dformat+'" /></div>';
				}
		});
		var fCommentArea=function(){};
		$.extend(fCommentArea.prototype,ffields.prototype,{
				title:"Comments here",
				ftype:"fCommentArea",
				userhelp:"A description of the section goes here.",
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+'</label><span class="uh">'+this.userhelp+'</span><div class="clearer"></div></div>';
				},
				show:function(){
					return '<div class="fields" id="field-'+this.index+'"><label>'+this.title+'</label><span class="uh">'+this.userhelp+'</span><div class="clearer"></div></div>';
				}
		});
		var fcheck=function(){};
		$.extend(fcheck.prototype,ffields.prototype,{
				title:"Check All That Apply",
				ftype:"fcheck",
				layout:"one_column",
				required:false,
				init:function(){
					this.choices = new Array("First Choice","Second Choice","Third Choice");
					this.choiceSelected = new Array(false,false,false);
				},
				display:function(){
					var str = "";
					for (var i=0;i<this.choices.length;i++)
						str += '<div class="'+this.layout+'"><input class="field" disabled="true" type="checkbox" '+((this.choiceSelected[i])?"checked":"")+'/> '+this.choices[i]+'</div>';
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield">'+str+'<span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					var str = "";
					for (var i=0;i<this.choices.length;i++)
						str += '<div class="'+this.layout+'"><input name="'+this.name+'[]" id="list'+i+'" class="field group '+((this.required)?" required":"")+'" value="'+htmlEncode(this.choices[i])+'" type="checkbox" '+((this.choiceSelected[i])?"checked":"")+'/> <span>'+this.choices[i]+'</span></div>';
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield">'+str+'<span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				showChoiceIntance: function() {
					var l = this.choices;
					var v = this.choiceSelected;
					var str = "";
					for (var i=0;i<l.length;i++)
					{
						str += '<div class="choicesEdit"><input class="choice_check" i="'+i+'" type="checkbox" '+((this.choiceSelected[i])?"checked":"")+'/><input class="choice_text" i="'+i+'" type="text" name="sChoice" id="sChoice" value="'+htmlEncode(l[i])+'"/><a class="choice_add ui-icon ui-icon-circle-plus" i="'+i+'" title="Add another choice."></a><a class="choice_remove ui-icon ui-icon-circle-minus" i="'+i+'" title="Delete this choice."></a></div>';
					}
					return '<div class="choicesSet"><label>Choices</label>'+str+'</div>';
				}
		});
		var fradio=function(){};
		$.extend(fradio.prototype,ffields.prototype,{
				title:"Select a Choice",
				ftype:"fradio",
				layout:"one_column",
				required:false,
				choiceSelected:null,
				init:function(){
					this.choices = new Array("First Choice","Second Choice","Third Choice");
				},
				display:function(){
					var str = "";
					for (var i=0;i<this.choices.length;i++)
						str += '<div class="'+this.layout+'"><input class="field" disabled="true" type="radio" i="'+i+'"  '+((this.choices[i]==this.choiceSelected)?"checked":"")+'/> '+this.choices[i]+'</div>';
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield">'+str+'<span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					var str = "";
					for (var i=0;i<this.choices.length;i++)
						str += '<div class="'+this.layout+'"><input name="'+this.name+'" class="field group '+((this.required)?" required":"")+'" value="'+htmlEncode(this.choices[i])+'" type="radio" i="'+i+'"  '+((this.choices[i]==this.choiceSelected)?"checked":"")+'/> <span>'+this.choices[i]+'</span></div>';
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield">'+str+'<span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';  
				},
				showChoiceIntance: function() {
					var l = this.choices;
					var v = this.choiceSelected;
					var str = "";
					for (var i=0;i<l.length;i++)
					{
						str += '<div class="choicesEdit"><input class="choice_radio" i="'+i+'" type="radio" '+((this.choiceSelected==l[i])?"checked":"")+' name="choice_radio" /><input class="choice_text" i="'+i+'" type="text" name="sChoice" id="sChoice" value="'+htmlEncode(l[i])+'"/><a class="choice_add ui-icon ui-icon-circle-plus" i="'+i+'" title="Add another choice."></a><a class="choice_remove ui-icon ui-icon-circle-minus" i="'+i+'" title="Delete this choice."></a></div>';
					}
					return '<div class="choicesSet"><label>Choices</label>'+str+'</div>';
				}
		});
		var fdropdown=function(){};
		$.extend(fdropdown.prototype,ffields.prototype,{
				title:"Select a Choice",
				ftype:"fdropdown",
				size:"medium",
				required:false,
				choiceSelected:"",
				init:function(){
					this.choices = new Array("First Choice","Second Choice","Third Choice");
				},
				display:function(){
					return '<div class="fields" id="field-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div class="remove ui-icon ui-icon-trash "></div><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><select class="field disabled '+this.size+'" ><option>'+this.choiceSelected+'</option></select><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div></div>';
				},
				show:function(){
					var l = this.choices;
					var v = this.choiceSelected;
					var str = "";
					for (var i=0;i<l.length;i++)
					{
						str += '<option '+((this.choiceSelected==l[i])?"selected":"")+' value="'+htmlEncode(l[i])+'">'+l[i]+'</option>';
					}
					return '<div class="fields '+this.csslayout+'" id="field-'+this.index+'"><label>'+this.title+''+((this.required)?"*":"")+'</label><div class="dfield"><select id="'+this.name+'" name="'+this.name+'" class="field '+this.size+((this.required)?" required":"")+'" >'+str+'</select><span class="uh">'+this.userhelp+'</span></div><div class="clearer"></div><div class="clearer"></div></div>';
				},
				showChoiceIntance: function() {
					var l = this.choices;
					var v = this.choiceSelected;
					var str = "";
					for (var i=0;i<l.length;i++)
					{
						str += '<div class="choicesEdit"><input class="choice_select" i="'+i+'" type="radio" '+((this.choiceSelected==l[i])?"checked":"")+' name="choice_select" /><input class="choice_text" i="'+i+'" type="text" name="sChoice" id="sChoice" value="'+htmlEncode(l[i])+'"/><a class="choice_add ui-icon ui-icon-circle-plus" i="'+i+'" title="Add another choice."></a><a class="choice_remove ui-icon ui-icon-circle-minus" i="'+i+'" title="Delete this choice."></a></div>';
					}
					return '<div class="choicesSet"><label>Choices</label>'+str+'</div>';
				}
		});
		if (!opt.pub)
		{
			$("#fieldlist").sortable({
			   start: function(event, ui) {
				   var start_pos = ui.item.index();
				   ui.item.data('start_pos', start_pos);
			   },
			   stop: function(event, ui) {
				   var end_pos = parseInt(ui.item.index());
				   var start_pos = parseInt(ui.item.data('start_pos'));
				   var tmp = items[start_pos];
				   if (end_pos > start_pos)
				   {
					   for (var i = start_pos; i<end_pos; i++)
						   items[i] = items[i+1];
				   }
				   else
				   {
					   for (var i = start_pos; i>end_pos; i--)
						   items[i] = items[i-1];
				   }
				   items[end_pos] = tmp;


				   reloadItems();
			   }
			});
		}
		if (!opt.pub)
		{
			$('#tabs').tabs({select: function(event, ui) {
				   if (ui.index!=1)
				   {
					   $(".fields").removeClass("ui-selected");
					   itemSelected = -2;
					   if (ui.index==2)
					   {
						   $(".fform").addClass("ui-selected");
						   editForm();
					   }
					   else
						   $(".fform").removeClass("ui-selected");
				   }
				   else
				   {
					   $(".fform").removeClass("ui-selected");
					   if (itemSelected<0)
						   $('#tabs-2').html('<b>No Field Selected</b><br />Please click on a field in the form preview on the right to change its properties.');
				   }
			   }
		   });
		}
	   loadtmp = function(p)
	   {

		   if ( d = $.parseJSON(p))
		   {
			   if (d.length==2)
			   {
				   items = new Array();
				   for (var i=0;i<d[0].length;i++)
				   {
					   var obj = eval("new "+d[0][i].ftype+"();");
					   obj = $.extend(obj,d[0][i]);
					   items[items.length] = obj;
				   }
				   theForm = new fform();
				   theForm = $.extend(theForm,d[1][0]);
				   if (opt.pub)
					   reloadItemsPublic();
				   else
					   reloadItems();
			   }
		   }
	   }
	   var ffunct = {
		   getItems: function() {
			   return items;
		   },
		   addItem: function(id) {
			   var obj = eval("new "+id+"();")
			   obj.init();
			   var n = 0;
			   for (var i=0;i<items.length;i++)
			   {
				   n1 = parseInt(items[i].name.replace(/fieldname/g,""));
				   if (n1>n)
					   n = n1;
			   }
			   $.extend(obj,{name:"fieldname"+(n+1)});
			   items[items.length] = obj;
			   reloadItems();
		   },
		   saveData:function(f){
			   if (f!="")
				   $("#"+f).val("["+ $.stringifyXX(items,false)+",["+ $.stringifyXX(theForm,false)+"]]");
			   else
			   {
				   $.ajax({
					   type: "POST",
					   url: "process.php?act=save",
					   data: "items="+ $.stringifyXX(items,true)+"&theForm="+ $.stringifyXX(theForm,true),
					   dataType: "json",
					   success: function (result) {
						   alert("The form has been saved!!!");
					   }
				   });
			   }
		   },
		   loadData:function(f){
			   if (f!="")
				   loadtmp($("#"+f).val());
			   else
			   {
				   $.ajax({async:false,
					   url: "process.php?act=load",
					   success: function (result) {
						   loadtmp(result.toString());
					   }
				   });
			   }
		   },
		   removeItem: removeItem,
		   editItem:editItem
	   }
	   this.fBuild = ffunct;
	   return this;
	}

	if (typeof cp_contactformtoemail_fbuilder_config != 'undefined')
	{
		var f = $("#fbuilder").fbuilder($.parseJSON(cp_contactformtoemail_fbuilder_config.obj));
	  	f.fBuild.loadData("form_structure");
		$("#cp_contactformtoemail_pform").validate({
			errorElement: "div",
			errorPlacement: function(e, element) {
				if (element.hasClass('group')){
					if (element.parent().siblings().size() > 0)
						element = element.parent().siblings(":last");
					else
						element = element.parent();	
				}
				offset = element.offset();
				e.insertBefore(element)
				e.addClass('message');  // add a class to the wrapper
				e.css('position', 'absolute');
				e.css('left',0 );
				e.css('top',element.outerHeight(true));
			}
		});
	}
})(jQuery);
$easyFormQuery = jQuery.noConflict();