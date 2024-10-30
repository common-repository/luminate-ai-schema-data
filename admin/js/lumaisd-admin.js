(function( $ ) {
	'use strict';
	$.widget( "custom.lumaisdCombobox", {
		_create: function() {
			this.wrapper = $( "<span>" )
			.addClass( "lumaisd-custom-combobox" )
			.insertAfter( this.element );
			this.element.hide();
			this._createAutocomplete();
			this._createShowAllButton();
		},
		_createAutocomplete: function() {
			var selected = this.element.children( ":selected" ),
			value = selected.val() ? selected.text() : "";
			this.input = $( "<input>" )
			.appendTo( this.wrapper )
			.val( value )
			.attr( "title", "" )
			.addClass( "lumaisd-custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
			.autocomplete({
				delay: 0,
				minLength: 0,
				source: $.proxy( this, "_source" ),
				classes: {
					"ui-autocomplete": 'lumaisd-custom-combobox-menu'}
			})
			.tooltip({
				classes: {
					"ui-tooltip": "ui-state-highlight"
				}
			});
			this._on( this.input, {
				autocompleteselect: function( event, ui ) {
					ui.item.option.selected = true;
					this._trigger( "select", event, {
						item: ui.item.option
					});
					this.element.trigger('select');
				},
				autocompletechange: "_removeIfInvalid"
			});
		},
		_createShowAllButton: function() {
			var input = this.input,
			wasOpen = false;
			$( "<a>" )
			.attr( "tabIndex", -1 )
			.attr( "title", "Show All Items" )
			.tooltip()
			.appendTo( this.wrapper )
			.button({
				icons: {
					primary: "ui-icon-triangle-1-s"
				},
				text: false
			})
			.removeClass( "ui-corner-all" )
			.addClass( "lumaisd-custom-combobox-toggle ui-corner-right" )
			.on( "mousedown", function() {
				wasOpen = input.autocomplete( "widget" ).is( ":visible" );
			})
			.on( "click", function() {
				input.trigger( "focus" );
				// Close if already visible
				if ( wasOpen ) {
					return;
				}
				input.autocomplete( "search", "" );
			});
		},
		_source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
			response( this.element.children( "option" ).map(function() {
				var text = $( this ).text();
				if ( this.value && ( !request.term || matcher.test(text) ) )
					return {
						label: text,
						value: text,
						option: this
					};
				}) );
		},
		_removeIfInvalid: function( event, ui ) {
			if ( ui.item ) {
				return;
			}
			var value = this.input.val(),
			valueLowerCase = value.toLowerCase(),
			valid = false;
			this.element.children( "option" ).each(function() {
				if ( $( this ).text().toLowerCase() === valueLowerCase ) {
					this.selected = valid = true;
					return false;
				}
			});
			if ( valid ) {
				return;
			}
			this.input
			.val( "" )
			.attr( "title", value + " didn't match any item" )
			.tooltip( "open" );
			this.element.val( "" );
			this._delay(function() {
				this.input.tooltip( "close" ).attr( "title", "" );
			}, 2500 );
			this.input.autocomplete( "instance" ).term = "";
		},
		_destroy: function() {
			this.wrapper.remove();
			this.element.show();
		}
	});
	jQuery(document).ready(function($) {
		$(document).on('heartbeat-send', function(event, data) {
		    var schema_meta = $('.lumaisd-post-data').html();
		    var schema_related = $('.lumaisd-post-data').attr('data-related_post');
		    data.schema_meta = schema_meta;
		    data.schema_related = schema_related;
		});
		$(document).on('heartbeat-tick', function(event, data) {
		    if (!data.schema_meta) {
		        return;
		    }
		    $('.lumaisd-post-data').html(data.schema_meta).removeClass('lumaisd-post-loader');
		    lumaisd_edit_link_removal('.lumaisd-post-data p.lumaisd-post-edit-link');
		});
		function lumaisd_edit_link_removal(covering_elem) {
			$(covering_elem).each(function(index, val) {
				$(this).append('<span class="dashicons dashicons-edit" data-target="'+$(this).attr('data-target')+'"></span><span class="dashicons dashicons-trash" data-target="'+$(this).attr('data-target')+'"></span>');
			});
		}
		$('.lumaisd-post-data').on('click', 'p.lumaisd-post-edit-link .dashicons.dashicons-trash', function(event) {
			event.preventDefault();
			var post_id = $(this).attr('data-target');
			var uri = $(this).find('#lumaisd_uri').attr('value');
			var parent_post = $('#lumaisd_post_id').attr('value');
			var current_parent = $(this).parents('p.lumaisd-post-edit-link');
			var current_grandparent = $(this).parents('.lumaisd-post-data');
			$(this).parents('.lumaisd-post-data').addClass('lumaisd-post-loader');
			$(this).parents('.lumaisd-post-data').find('h3').addClass('hidden');
			$(this).parents('.lumaisd-post-data').find('p').addClass('hidden');
			$.ajax({
				url: ajax_object.ajax_url,
				type: 'POST',
				data: {'action': 'lumaisd_remove_linked_schemas', 'post_id': post_id, 'parent_post': parent_post, 'uri': uri},
			})
			.done(function(response) {
				var result = response;
				if (result.status == 'success') {
					if ($(current_parent).hasClass('lumaisd-post-delete-link')) {
						$('.lumaisd-post-data').html('').addClass('lumaisd-post-loader');
						$('.lumaisd-post-data .lumaisd-post-edit-link[data-target="'+post_id+'"]').remove();
						$('.lumaisd-modal .lumaisd-modal-content').html('<span class="lumaisd-close-modal">&times;</span>');
						$('.lumaisd-modal .lumaisd-modal-content').hide();
					}
					$(current_parent).remove();
				}
			})
			.fail(function() {
			})
			.always(function() {
				$(current_grandparent).removeClass('lumaisd-post-loader');
				$(current_grandparent).find('h3').removeClass('hidden');
				$(current_grandparent).find('p').removeClass('hidden');
			});
		});
		var modal = document.getElementById('lumaisdModal');
		$('.lumaisd-post-data').on('click', 'p.lumaisd-post-edit-link .dashicons.dashicons-edit', function(event) {
			event.preventDefault();
		    $('.lumaisd-modal').css('display', 'block');
		    var targetPost = $(this).attr('data-target');
		    var parentPost = $(this).parents('.lumaisd-post-data').find('#lumaisd_post_id').attr('value');
		    var parentType = $(this).parents('p.lumaisd-post-edit-link').find('span.lumaisd-toggle-tag-status span').text();
		    parentType = parentType.replace('(', '');
		    parentType = parentType.replace(')', '');
		    $.ajax({
		    	url: ajax_object.ajax_url,
		    	type: 'POST',
		    	data: {'action': 'lumaisd_get_specified_post', 'post_id': targetPost, 'parent_post': parentPost, 'parent_type': parentType},
		    })
		    .done(function(response) {
		    	var result = response;
		    	if (result.status == 'success') {
		    		$('.lumaisd-modal .lumaisd-modal-content').html('<span class="lumaisd-close-modal">&times;</span>'+result.html);
		    		$("#lumaisd_at_type_selector").lumaisdCombobox();
		    	}
		    })
		    .fail(function() {
		    })
		    .always(function() {
		    });
		});
		$('.lumaisd-post-data').on('click', '.lumaisd-addstrucuturedtag.dashicons.dashicons-plus', function(event) {
			event.preventDefault();
			var targetPost = $(this).attr('data-target');
			$('.lumaisd-modal').css('display', 'block');
			var appendHtml = '<span class="lumaisd-close-modal">&times;</span>'
			appendHtml += '<h3>Add Structured Data</h3>';
			appendHtml += '<div class="wrap lumaisd-wrap">';
				appendHtml += '<div class="wp-list-table widefat fixed striped">';
					appendHtml += '<div class="lumaisd-specified-post structured-tag">';
						appendHtml += '<div class="lumaisd_username">';
							appendHtml += '<p class="username">';
								appendHtml += '<label for="lumaisd_add_tag">Title</label>';
								appendHtml += '<input type="text" name="lumaisd_post_title" id="lumaisd_post_title">';
							appendHtml += '</p>';
						appendHtml += '</div>';
						appendHtml += '<div class="lumaisd_username">';
							appendHtml += '<p class="username">';
								appendHtml += '<label for="lumaisd_desc">Description</label>';
								appendHtml += '<textarea name="lumaisd_desc" id="lumaisd_desc"></textarea>';
							appendHtml += '</p>';
						appendHtml += '</div>';
						appendHtml += '<div class="lumaisd_username ui-widget">';
							appendHtml += '<p class="username">';
								appendHtml += '<label for="lumaisd_at_type_selector">type</label>';
								appendHtml += '<select name="lumaisd_at_type_selector" id="lumaisd_at_type_selector" data-target="'+targetPost+'">';
									appendHtml += '<option value="">Select</option>';
									appendHtml += '<option value="http://schema.org/Article">Article</option>';
									appendHtml += '<option value="http://schema.org/AggregateRating">AggregateRating</option>';
									appendHtml += '<option value="http://schema.org/Blog">Blog</option>';
									appendHtml += '<option value="http://schema.org/Book">Book</option>';
									appendHtml += '<option value="http://schema.org/BreadcrumbList">BreadcrumbList</option>';
									appendHtml += '<option value="http://schema.org/CreativeWork">CreativeWork</option>';
									appendHtml += '<option value="http://schema.org/Event">Event</option>';
									appendHtml += '<option value="http://schema.org/ImageObject">ImageObject</option>';
									appendHtml += '<option value="http://schema.org/LocalBusiness">LocalBusiness</option>';
									appendHtml += '<option value="http://schema.org/Offer">Offer</option>';
									appendHtml += '<option value="http://schema.org/Organization">Organization</option>';
									appendHtml += '<option value="http://schema.org/Person">Person</option>';
									appendHtml += '<option value="http://schema.org/Place">Place</option>';
									appendHtml += '<option value="http://schema.org/Product">Product</option>';
									appendHtml += '<option value="http://schema.org/Rating">Rating</option>';
									appendHtml += '<option value="http://schema.org/Recipe">Recipe</option>';
									appendHtml += '<option value="http://schema.org/Review">Review</option>';
									appendHtml += '<option value="http://schema.org/SearchAction">SearchAction</option>';
									appendHtml += '<option value="http://schema.org/VideoObject">VideoObject</option>';
									appendHtml += '<option value="http://schema.org/WebPage">WebPage</option>';
								appendHtml += '</select>';
							appendHtml += '</p>';
						appendHtml += '</div>';
						appendHtml += '<div class="lumaisd_attr_holder">';
						appendHtml += '</div>';
					appendHtml += '</div>'
				appendHtml += '</div>'
			appendHtml += '</div>';
			$('.lumaisd-modal .lumaisd-modal-content').html(appendHtml);
			$("#lumaisd_at_type_selector").lumaisdCombobox();
		});
		function splitsource( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return splitsource( term ).pop();
		}
		$('.lumaisd-post-data').on('keypress', '#lumaisd_tag_search', function(event) {
			if ( (event.keyCode === $.ui.keyCode.TAB || event.keyCode === $.ui.keyCode.ENTER) && $( this ).autocomplete( "instance" ).menu.active ) {
				event.preventDefault();
			}
			$(this).autocomplete({
				source: function( request, response ) {
					var re = $.ui.autocomplete.escapeRegex(request.term);
			        var matcher = new RegExp("^" + re, "i");
					$.getJSON( ajax_object.ajax_url+"?action=lumaisd_search_vocab_names&term="+re, function(data){
						response($.map(data, function (v, i) {
			                return {
			                    label: v.value,
			                    value: v.value,
			                    id: v.id
			                };
			            }));
					} );
				},
				search: function() {
					var term = extractLast( this.value );
					if ( term.length < 2 ) {
						return false;
					}
				},
				focus: function() {
					return false;
				},
				select: function (event, ui){
					var parent_post = $(this).parents('.lumaisd-post-data').attr('data-related_post');
					var targetPost = ui.item.id;
					$('.lumaisd-post-data').addClass('lumaisd-post-loader');
					$('.lumaisd-post-data.lumaisd-post-loader > div, .lumaisd-post-data.lumaisd-post-loader > p, .lumaisd-post-data.lumaisd-post-loader > h3').css({
					    'display': 'none'
					});
					$.ajax({
						url: ajax_object.ajax_url,
			    		type: 'POST',
			    		data: {'action': 'lumaisd_append_user_tag', 'parent_post': parent_post, 'post_id': targetPost},
					})
					.done(function(response) {
						if (response.status == 'success') {
							if (response.message == 'Tag already exists') {
								alert(response.message);
							}
							$('.lumaisd-post-data').empty();
							$(document).trigger('heartbeat-send');
						} else {
							$('.lumaisd-post-data').removeClass('lumaisd-post-loader');
							$('.lumaisd-post-data.lumaisd-post-loader div, .lumaisd-post-data.lumaisd-post-loader p, .lumaisd-post-data.lumaisd-post-loader h3').removeAttr('style');	
						}
					})
					.fail(function() {
					})
					.always(function() {
					});
				},
				minLength: 3,
				classes: {
					"ui-autocomplete": 'lumaisd-custom-combobox-menu'
				}
			});
		});
		$('.lumaisd-post-data').on('select', '#lumaisd_at_type_selector', function(event) {
			event.preventDefault();
			var targetPost = $(this).attr('data-target');
			var targetDiv = $(this).val();
			var targetParent = $(this).parents('.lumaisd-post-data');
			if (targetDiv != 'default') {
				$.ajax({
			    	url: ajax_object.ajax_url,
			    	type: 'POST',
			    	data: {'action': 'lumaisd_fetch_attr_tags', 'setType': targetDiv, 'post_id': targetPost},
			    })
			    .done(function(response) {
			    	var result = response;
			    	if (result.status == 'success') {
			    		$(targetParent).find('.lumaisd_attr_holder').html(result.html);
			    	}
			    })
			    .fail(function() {
			    })
			    .always(function() {
			    });
			}
		});
		$('.lumaisd-post-data').on('click', '.lumaisd-modal-content [name="lumaisd_post_save"]', function(event) {
			event.preventDefault();
			var targetParent = $(this).parents('#lumaisdModal');
			var keylist = $(targetParent).find('#lumaisd_post_keylist').attr('value');
			keylist = keylist.split(',');
			var valuelist = {};
			$(keylist).each(function(index, el) {
				valuelist[el] = $(targetParent).find('#lumaisd_'+el).attr('value');
			});
			var data = {
				'action'		: 'lumaisd_save_update_post',
				'parent_post'	: $(targetParent).find('#lumaisd_parent_post_id').attr('value'), 
				'target_post'	: $(targetParent).find('#lumaisd_post_id').attr('value'), 
				'post_action'	: $(targetParent).find('#lumaisd_post_action').attr('value'), 
				'post_title'	: $(targetParent).find('#lumaisd_post_title').attr('value'), 
				'post_desc'		: $(targetParent).find('#lumaisd_desc').val(),
				'valuelist'		: valuelist,
			};
			$('.lumaisd-modal .lumaisd-modal-content').html('<span class="lumaisd-close-modal">&times;</span><h1 data-content="Luminate" class="lumaisd-post-loader">Luminate</h1>');
			$.ajax({
		    	url: ajax_object.ajax_url,
		    	type: 'POST',
		    	data: data,
		    })
		    .done(function(response) {
		    	var result = response;
		    	if (result.status == 'success') {
		    		$('.lumaisd-modal').css('display', 'none');
		    		$('.lumaisd-modal .lumaisd-modal-content').html('<span class="lumaisd-close-modal">&times;</span><h1 data-content="Luminate" class="lumaisd-post-loader">Luminate</h1>');
		    		$('.lumaisd-post-data').html('').addClass('lumaisd-post-loader');
		    	}
		    })
		    .fail(function() {
		    })
		    .always(function() {
		    });
		});
		$('.lumaisd-post-data').on('click', '.lumaisd-close-modal', function(event) {
			event.preventDefault();
		    $('.lumaisd-modal').css('display', 'none');
		    $('.lumaisd-modal .lumaisd-modal-content').html('<span class="lumaisd-close-modal">&times;</span><h1 data-content="Luminate" class="lumaisd-post-loader">Luminate</h1>');
		});
		window.onclick = function(event) {
		    if (event.target == modal) {
		        $('.lumaisd-modal').css('display', 'none');
		    }
		}
		$('.lumaisd-switch .lumaisd-slider').on('click', function(event) {
			event.preventDefault();
			var checkedStatus = $(this).parent('.lumaisd-switch').find('input').attr('checked');
			if (checkedStatus) {
				$(this).parent('.lumaisd-switch').find('input').removeAttr('checked');
				$(this).parent('.lumaisd-switch').find('input').val('');
			} else {
				$(this).parent('.lumaisd-switch').find('input').attr('checked', 'checked');
				$(this).parent('.lumaisd-switch').find('input').val('on');
			}
		});
		$('.lumaisd-post-data').on('click', '.lumaisd-toggle-tag-status', function(event) {
			event.preventDefault();
			var setActivation = 0;
			var vocab = $(this).parent('.lumaisd-post-edit-link').attr('data-target');
			var parentPost = $(this).parent('.lumaisd-post-edit-link').attr('data-parent');
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
			} else {
				$(this).addClass('active');
				setActivation = 1;
			}
			$.ajax({
				url: ajax_object.ajax_url,
				type: 'POST',
				data: {'action': 'lumaisd_set_tag_status', 'post_id': vocab, 'tag_status': setActivation, 'parent_post' : parentPost},
			})
			.done(function(response) {
				var result = response;
			})
			.fail(function() {
			})
			.always(function() {
			});
		});
	});
})( jQuery );
