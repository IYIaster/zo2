var Zo2Social=window.Zo2Social||{};!function(a){a.extend(Zo2Social,{toggleButtons:function(){a(".social-onoff > label").click(function(){var c=a(this);var b=c.parent();var d=b.find("input[type=radio]");if(b.hasClass("toggle-off")){b.find(".off").removeClass("active btn-danger");b.find(".on").addClass("active btn-success");d.prop("value",1);b.removeClass("toggle-off")}else{b.find(".off").addClass("active btn-danger");b.find(".on").removeClass("active btn-success");d.prop("value",0);b.addClass("toggle-off")}})},updateIndex:function(c,b){a("td.index",b.item.parent()).each(function(d){a(this).html(d+1)})},saveConfig:function(d,c){var f=[];a(c.item.parent().children()).each(function(h){var g=Zo2Social.getRow(this);f.push(g)});var b=Zo2Social.encodeJSON(f);if(b){a("#jform_params_social_order").val(b)}return true},getRow:function(d){var c={};var b=a(d).children();c.name=a(b[0]).attr("name");c.index=parseInt(a(b[1]).text());c.website=a(b[2]).find("a").text();c.link=a(b[2]).find("a").attr("href");c.enable=parseInt(a(b[3]).find("input[type=radio]").val());c.button_design=a(b[4]).find("select[name="+c.name+"_button_design]").val();return c},encodeJSON:function(b){if(JSON&&JSON.stringify){b=JSON.stringify(b);return b}else{return b}},decodeJSON:function(b){return a.parseJSON(b)},onSubmit:function(){var b=document.adminForm;if(!b){return false}b.onsubmit=function(d){var f=[];a("#social_options > tbody > tr").each(function(h){var g=Zo2Social.getRow(this);f.push(g)});var c=Zo2Social.encodeJSON(f);if(c){a("#jform_params_social_order").val(c)}return false}},loadFields:function(){a("#jform_params_normal_position").closest(".control-group").hide();a("#jform_params_floating_position").closest(".control-group").hide();a("#jform_params_box_top").closest(".control-group").hide();a("#jform_params_box_left").closest(".control-group").hide();a("#jform_params_box_right").closest(".control-group").hide();a("#jform_params_box_style").closest(".control-group").hide();var b=a("#jform_params_display_type").val();Zo2Social.toggleType(b)},init:function(){a("#jform_params_display_type").on("change",function(){var b=a(this).val();Zo2Social.toggleType(b)});a("#checkAll").click(function(){a(".treeCategories input").attr("checked","checked")});a("#uncheckAll").click(function(){a(".treeCategories input").attr("checked",false)})},toggleType:function(b){if(b=="normal"){a("#jform_params_normal_position").closest(".control-group").show();a("#jform_params_floating_position").closest(".control-group").hide();a("#jform_params_box_top").closest(".control-group").hide();a("#jform_params_box_left").closest(".control-group").hide();a("#jform_params_box_right").closest(".control-group").hide();a("#jform_params_box_style").closest(".control-group").hide()}else{if(b=="floating"){a("#jform_params_normal_position").closest(".control-group").hide();a("#jform_params_floating_position").closest(".control-group").show();a("#jform_params_box_top").closest(".control-group").show();a("#jform_params_box_left").closest(".control-group").show();a("#jform_params_box_right").closest(".control-group").show();a("#jform_params_box_style").closest(".control-group").show()}}}});a(document).ready(function(){Zo2Social.loadFields();Zo2Social.init();Zo2Social.onSubmit();Zo2Social.toggleButtons()})}(jQuery);