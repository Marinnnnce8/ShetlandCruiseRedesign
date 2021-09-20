/**
 * Theme JS
 *
 * @copyright 2019 NB Communication Ltd
 *
 */

var theme = {

	init: function() {
		this.okayNav();
		this.protectLinks();
		this.floatingTools();
		this.shortlist.init();
		this.search.init();
		this.scrollspy();
		this.datetimePicker();
		this.submissionForm();
	},

	cookie: {

		get: function(name, asString) {
			if($nb.x(name)) return "";
			var value = Cookies.get(name);
			return asString ? value : ($nb.x(value) || value == null ? [] : value.split("|"));
		},

		in: function(id, name) {
			return (this.get(name)).includes(id.toString());
		},

		remove: function(name, id, expires) {

			if($nb.x(id)) {

				Cookies.remove(name);

			} else if(this.in(id, name)) {

				var data = this.get(name),
					value = [];

				for(var i = 0; i < data.length; i++) {
					if(parseInt(id) !== parseInt(data[i])) {
						value.push(data[i]);
					}
				}

				this.set(value.join("|"), name, expires)
			}
		},

		set: function(value, name, expires) {

			if(Number.isInteger(value)) {
				var data = this.get(name);
				if(!data.includes(value.toString())) data.push(value);
				value = data.join("|");
			}

			Cookies.set(name, value, {
				expires: $nb.xy(expires, 30)
			});
		}
	},

	datetimePicker: function() {

		if(!$("[data-datetimepicker]").length) return;

		$("[data-datetimepicker]").each(function() {
			var $item = $(this),
				dtp = new DateTimePicker("#" + $item.attr("id"), $item.data("datetimepicker"));
		});
	},

	floatingTools: function() {
		$(".hero-section, .page-header").waypoint({
			handler: function(direction) {
				if(direction == 'down') {
					$(".floating-tool").addClass("uk-active");
				} else {
					$(".floating-tool").removeClass("uk-active");
				}
			},
			offset: "0%"
		});
	},

	format: {

		/**
		 * Return a numerical value as a price
		 *
		 * @param int|float value The value to format
		 * @param string locale The BCP 47 locale to use (default=en-GB)
		 * @param string currency The ISO 4217 currency code to use
		 * @return string
		 *
		 */
		money: function(value, locale, currency) {

			locale = locale == undefined ? "en-GB" : locale;
			currency = currency == undefined ? "GBP" : currency;

			var $options = {
				style: "currency",
				currency: currency
			};

			if((typeof Intl == "object" && Intl && typeof Intl.NumberFormat == "function")) {
				value = new Intl.NumberFormat(locale, $options).format(value);
			} else {
				value = value.toLocaleString(locale, $options);
			}

			var n = value.split("."),
				whole = n[0],
				decimal = n[1];

			if(decimal.length == 1) decimal += "0";

			return decimal == "00" ? whole : [whole, decimal].join(".");
		}
	},

	ga: {

		eventAttr: function(action, label, value, attr) {

			if($nb.x(action)) return false;

			var attr = $nb.x(attr) ? {} : attr;
			
			$.extend(attr, {
				"data-ga-event": true,
				"data-ga-event-category": "Shetland Cruise",
				"data-ga-event-action": action,
				"aria-label": "Visit " + label,
			});

			if(!$nb.x(label)) attr["data-ga-event-label"] = label;
			if(!$nb.x(value)) attr["data-ga-event-value"] = value;

			return attr;
		},

		sendEvent: function(action, label, value) {

			if(!("gtag" in window)) return;

			var data = {"event_category": "Shetland Cruise"};

			if(label !== undefined) data["event_label"] = label;
			if(value !== undefined) data["value"] = value;

			gtag("event", action, data);
		}
	},

	map: function() {

		if(!$(".map-container").length) return;

		Popup = createPopupClass();

		$(".map-container").each(function() {

			var $element = $(this),
				mapID = $element.attr("id"),
				data = $element.data("map");

			// Setup up our theme.mapData var
			theme.mapData[mapID] = {
				map: {}, // The map
				marker: {}, // Markers
			};

			// Create map
			theme.mapData[mapID].map = new google.maps.Map(this, data.options);

			// Load markers
			for(var itemID in data.marker) {

				var marker = data.marker[itemID],
					tmp = document.createElement("div");
					tmp.innerHTML = $nb.wrap(marker.title, "<span class='title'>");
					$element[0].appendChild(tmp);

				theme.mapData[mapID]["marker"][itemID] = new Popup(new google.maps.LatLng(marker.position), tmp);
				theme.mapData[mapID]["marker"][itemID].setMap(theme.mapData[mapID].map)
			}
		})
	},

	mapData: {},

	okayNav: function() {
		$(".okayNav").okayNav({
			swipe_enabled: false
		});
	},

	protectLinks: function() {

		var $links = $("a[target='_blank']");
		if(!$links.length) return;

		// Make sure external links have the appropriate rel attributes
		$links.each(function() {

			var $a = $(this),
				rel = $a.attr("rel"),
				protect = ["noopener"];

			rel = !$nb.x(rel) ? rel.split(" ") : [];
			for(var i = 0; i < protect.length; i++) {
				if(rel.indexOf(protect[i]) < 0) rel.push(protect[i]);
			}

			$a.attr("rel", rel.join(" "));
		});
	},

	scrollspy: function() {

		if($(".section-header").length) {
			UIkit.scrollspy(".section-header", {
				"target": " > *",
				"delay": 150,
				"cls": "uk-animation-slide-bottom-medium"
			});
		}
	},

	search: {

		init: function() {

			if(!$(".search-form-wrapper").length) return;

			$nb.json.query($("#activity-results"), $("#search-form"));

			this.clearForm();
			this.rangeSlider();
			this.toggleForm();
			this.toggleCheckboxes();
		},

		clearForm: function() {

			$("[data-filter-clear]").on("click", function() {

				$("#search-form [data-filter]").each(function() {
					$(this).find(":input").each(function() {
						if($(this).val()) {
							$(this).prop("checked", false);
						} else {
							$(this).prop("checked", true);
						}
					});
				});

				var min = $("#min").data("value"),
					max = $("#max").data("value");

				$("#min").val(min);
				$("#max").val(max);

				$(".slider")[0].noUiSlider.set([min, max]);

				$(this).addClass("uk-hidden");

				$("#search-form").submit();
			});
		},

		isDefault: function() {
			return $("#trip_type_any").is(":checked") &&
				$("#cats_any").is(":checked") &&
				//!$(":input[name=port]:checked").length &&
				$("#duration_any").is(":checked") &&
				(parseFloat($("#min").val()) == parseFloat($("#min").data("value"))) &&
				(parseFloat($("#max").val()) == parseFloat($("#max").data("value")));
		},

		rangeSlider: function() {

			$(".range-input").each(function() {

				var $slider = $(this),
					min = $slider.find(".min"),
					max = $slider.find(".max"),
					slider =  $slider.find(".slider");

				noUiSlider.create(slider[0], {
					start: [min.data("value"), max.data("value")],
					connect: true,
					range: {
						min: min.data("value"),
						max: max.data("value")
					}
				});

				slider[0].noUiSlider.on("update", function(values, handle) {
					var value = values[handle];
					if(handle) {
						max[0].value = value;
					} else{
						min[0].value = value;
					}
				});

				min[0].addEventListener("change", function() {
					slider[0].noUiSlider.set([this.value, null]);
				});

				max[0].addEventListener("change", function() {
					slider[0].noUiSlider.set([null, this.value]);
				});
			});
		},

		renderFilter: function(label) {
			return $nb.wrap(label, {
				class: [
					"uk-button",
					"active-filter",
					//"push-icon-right",
					"uk-margin-xsmall-right",
				],
			}, "span");
			//$nb.faIcon("times") +
		},

		resultFilters: function() {

			var $e = $(".active-filters");
			$e.html("");

			$("#search-form [data-filter]").each(function() {
				$(this).find(":input").each(function() {
					if($(this).is(":checked")) {
						$e.append(theme.search.renderFilter($(this).data("filter-text")))
					}
				});
			});

			$e.append(this.renderFilter([
				theme.format.money($("#min").val()),
				theme.format.money($("#max").val())
			].join(" - ") + " pp"));
		},

		toggleCheckboxes: function() {

			$("#search-form input[type=checkbox]").on("change", function() {

				var $input = $(this),
					$set = $input.closest("[data-filter]");

				if($input.val()) {
					$set.find(":input[value='']:checked").prop("checked", false);
				} else {
					$set.find(":input[value!='']:checked").prop("checked", false);
				}
			})
		},

		toggleForm: function() {

			$(".search-form-wrapper").on("show", function() {
				$(".search-form-toggler").removeClass("off");
			});

			$(".search-form-wrapper").on("hide", function() {
				$(".search-form-toggler").addClass("off");
			});
		}
	},

	shortlist: {

		$button: {},
		config: {},

		init: function() {
			this.$button = $("#shortlisted");
			this.config = this.$button.data("shortlist-config");
			this.counter();
			this.remove();
			this.suggestVisits();
			this.notifySwitch.init();
			this.providerSelect();

			$("[data-shortlist-button-render]").each(function() {
				var $btn = $(this),
					$parent = $(this).parent(),
					id = $btn.data("shortlist-button-render");
				$btn.replaceWith(theme.shortlist.button(id, $("#title" + id).text(), true));
				theme.shortlist.buttons($parent);
			});
		},

		button: function(id, title, add) {

			if($nb.x(this.config.name)) this.config = $("#shortlisted").data("shortlist-config");

			var on = theme.cookie.in(id, this.config.name),
				cls = [
					"uk-button",
					"uk-button-default",
					"button-ghost",
					"push-icon-right",
				];

			if(on) cls.push("uk-active");
			
			return $nb.wrap(
				this.config.icon + this.config[(on ? "on" : "off" + ($nb.x(add) ? "" : "Add"))], 
				theme.ga.eventAttr(
					"Shortlist",
					title,
					id,
					{
						type: (on ? false : "button"),
						href: (on ? this.config.url : false),
						class: cls,
						"data-shortlist-add": id,
						"data-uk-tooltip": (on ? this.config.view : this.config.add)
					}
				),
				(on ? "a" : "button")
			)
		},

		buttons: function($element) {

			if(!$element.length) return;

			var action = "shortlist-add";

			$element.off("click", "button[data-" + action + "]");

			$element.on("click", "button[data-" + action + "]", function() {

				var $btn = $(this),
					id = $btn.data(action),
					$parent = $btn.closest(".uk-card-body"),
					isListing = $parent.length ? true : false;

				$nb.ukNotification({
					message: $nb.wrap(
						$nb.faIcon("plus-circle") + " " + theme.shortlist.config.added.replace(
							"{name}",
							$nb.wrap((isListing ? $parent.children("h3") : $("h1")).text(), "strong")
						),
						"uk-text-center"
					)
				})

				theme.cookie.set(id, theme.shortlist.config.name);
				theme.shortlist.counter(true);
				$btn.replaceWith(theme.shortlist.button(id, $("#title" + id).text()));
				$btn.off("click");
			});
		},

		counter: function(animate) {

			var n = theme.cookie.get(this.config.name).length;
			this.$button.find(".num").text(n);

			if($nb.x(animate)) {
				var cls = "counter-animation-" + (animate ? "add" : "remove");
				this.$button.addClass(cls);
				setTimeout(function() {
					theme.shortlist.$button.removeClass(cls);
				}, 1024);
			}

			var $rnb = $("#right-nav-button");
			if(n) $rnb.attr("href", this.config.url).children("span").html(this.config.view);
			if($rnb.hasClass("uk-hidden")) $("#right-nav-button").removeClass("uk-hidden");
		},

		notifySwitch: {

			init: function() {

				var action = "shortlist-notify-switch";

				if(!$("[data-" + action + "]").length) return;

				$("[data-" + action + "]").each(function() {

					var $btn = $(this);

					theme.shortlist.notifySwitch.toggle($btn, $btn.data(action).mode);

					$btn.on("click", function() {

						var $btn = $(this);
							data = $btn.data(action);

						UIkit.modal.confirm(theme.shortlist.config["emailsO" + (data.mode ? "ff" : "n")]).then(function() {

							$.post(window.location.href, {
								action: "notify-switch",
								id: data.id,
							}, function(result) {
								if(result.response == 200) {
									theme.shortlist.notifySwitch.toggle($btn, result.message);
									data.mode = result.message;
									$btn.data(action, data);
								}
							})
							.fail(function(jqXHR, textStatus, errorThrown) {
								UIkit.modal.alert(errorThrown);
							});

						}, function() {});
					})
				})
			},

			toggle: function($btn, mode) {

				mode = mode == true ? 1 : 0;

				var config = theme.shortlist.config,
					toggle = [
						[
							config.textOff,
							"primary",
							"secondary",
						],
						[
							config.textOn,
							"success",
							"danger",
						],
					],
					edom = (mode ? 0 : 1),
					$parent = $btn.parent(),
					$alert = $parent.parent();

				$parent.find("[data-shortlist-notify-switch-text]").text(toggle[mode][0].toLowerCase());
				$btn.html(config.textTurn + " " + toggle[edom][0]);

				$alert.removeClass("uk-alert-" + toggle[edom][1]);
				$alert.addClass("uk-alert-" + toggle[mode][1]);

				$btn.removeClass("uk-button-" + toggle[edom][2]);
				$btn.addClass("uk-button-" + toggle[mode][2]);
			}
		},

		providerSelect: function() {

			var action = "shortlist-provider-select";
			if(!$("[data-" + action + "]").length) return;

			$("[data-" + action + "]").each(function() {

				console.log(theme.shortlist.config.msgSelect)

				$(this).on("click", function() {

					var $btn = $(this);
					UIkit.modal.confirm(theme.shortlist.config.msgSelect).then(function() {

						$.post(window.location.href, {
							action: "provider-select",
							id: $btn.data(action),
						}, function(result) {
							if(result.response == 200) window.location.reload();
						})
						.fail(function(jqXHR, textStatus, errorThrown) {
							UIkit.modal.alert(errorThrown);
						});

					}, function() {});
				})
			})
		},

		remove: function($element) {

			if(!$("[data-shortlist-remove]").length) return;

			$("[data-shortlist-remove]").on("click", function() {

				var $btn = $(this),
					id = $btn.data("shortlist-remove");

				UIkit.modal.confirm(theme.shortlist.config.removeConfirm).then(function() {

					var config = theme.shortlist.config;

					$nb.ukNotification({
						message: $nb.wrap(
							$nb.faIcon("minus-circle") + " " + config.removed.replace(
								"{name}",
								$nb.wrap($btn.closest("tr").children("td:first").text(), "strong")
							),
							"uk-text-center"
						)
					});

					if($btn.closest("tbody").children("tr").length == 1) {

						theme.cookie.remove(config.name);
						window.location.reload();

					} else {

						$btn.closest("tr").fadeOut($nb.defaults.speed * 2, function() {
							$(this).remove();
						})

						theme.cookie.remove(config.name, id);
						theme.shortlist.counter(false);
					}

				}, function() {});
			});
		},

		suggestVisits: function() {

			if(!$("[data-visit-suggestions]").length) return;

			$("[data-visit-suggestions]").on("focus change paste keyup", $nb.debounce(function() {

				var $input = $(this),
					q = $input.val();

				$input.next(".visit-suggestions").remove();

				if($input.is(":focus") && $("#port").val() == "1064" && q && q.length > 2) {
					$.getJSON($input.data("visit-suggestions"), {q: q}, function(data) {

						var c = Object.keys(data).length,
							items = "",
							item;

						if(c) {

							for(var i = 0; i < c; i++) {
								item = data[i];
								items += $nb.wrap(item.ship + ", " + item.date_from + " " + item.time_from, {
									class: "visit-suggestion",
									"data-visit-suggestion": item
								}, "div");
							}

							var $dropdown = $($nb.wrap(items, {
								class: "visit-suggestions",
							}, "div"));

							$input.after($dropdown);

							$dropdown.on("click", ".visit-suggestion", function() {
								$.each($(this).data("visit-suggestion"), function(key, value) {
									var $i = $("#" + key);
									if($i.length) $i.val(value);
								});
								$input.blur();
								$(this).parent().remove();
							});
						}
					});
				}
			}, 256));
		}
	},

	submissionForm: function() {

		var $form = $("#nb-form-submission");
		if(!$form.length) return;

		$("[data-provider-field][title!='Optional']").attr("required", true)
			.closest("[class^='Inputfield']").addClass("nb-form-required");

		$("#nb-form-submission_select_provider").on("change", function() {
			if(!$(this).val()) {
				$("[data-provider-field][title!='Optional'][type!=file]").attr("required", true)
				$("[data-provider-field][title!='Optional']").closest("[class^='Inputfield']").addClass("nb-form-required");
				$("[data-provider-field]").closest("[class^='Inputfield']").removeClass("uk-hidden")
			} else {
				$("[data-provider-field]").attr("required", false)
					.closest("[class^='Inputfield']").addClass("uk-hidden").removeClass("nb-form-required");
			}
		});

		$("#nb-form-submission_activity_info_times").on("blur", function() {

			var $source = $(this),
				v = $source.val().replace("to", "").replace("  ", " "),
				values = v.includes("-") ? v.split("-") : v.split(" "),
				value = "";

			if(values.length > 1) {

				var from = parseFloat(values[0].replace(":", ".")),
					fromInt = parseInt(from),
					fromRem = from - fromInt,
					to = parseFloat(values[1].replace(":", ".")),
					toInt = parseInt(to),
					toRem = to - toInt;

				if(from && to) { // Time range should be between 7am and 7pm
					if(fromInt < 7) fromInt = fromInt + 12; // 1-6 becomes 13-18
					if(toInt < 7) toInt = toInt + 12;  // 1-6 becomes 13-18
					value = ((toInt + (toRem ? (toRem * 100 / 60) : 0)) - (fromInt + (fromRem ? (fromRem * 100 / 60) : 0))) * 60;
					//value = ((toInt - fromInt) * 60) + ((fromRem * 100) + (toRem * 100));
					value = value > 1 ? Math.round(value) : "";
				}
			}

			$("#nb-form-submission_activity_duration").val(value);
		});

		if($("[data-ckeditor]").length) {
			$("[data-ckeditor]").each(function() {
				CKEDITOR.replace($(this).attr("id"));
			})
		}
	},

	wrapMeta: function(meta) {
		return $nb.wrap($nb.wrap(meta, "span"), "<div class='entry-meta'>");
	}
};

$(document).ready(function() {
	theme.init();
});

$nb.form.onSuccess = function(result) {

	var hasCaptcha = $(".g-recaptcha").length,
		message = result.message;

	switch(result.response) {
		case 200: // Successful
			if(message.notification) {
				$nb.ukNotification(message.notification);
				if(result.action == "addEnquiry") theme.cookie.remove(theme.shortlist.config.name);
				if(message.redirect) {
					window.location.href = message.redirect;
				} else if(message.reload) {
					setTimeout(function() {
						window.location.reload();
					}, 1024)
				}
			} else {
				this.button.remove();
				this.complete(message);
			}
			if(hasCaptcha) $(".g-recaptcha").remove();
			break;
		case 401: // Unauthorised
		case 412: // Precondition failed
			this.resetButton();
			UIkit.modal.alert(message);
			if(hasCaptcha) grecaptcha.reset();
			break;
		default:
			this.button.remove();
			this.complete(message);
			break;
	}

	this.reset();
};

$nb.form.onFail = function(jqXHR, textStatus, errorThrown) {;
	UIkit.modal.alert(textStatus);
	this.resetButton();
	this.reset();
},

$nb.json.onRender = function($element, result) {

	if($element.attr("id") == "activity-results") {

		theme.shortlist.buttons($element)

		theme.ga.sendEvent(
			"Activity Finder",
			"Search",
			$.param($("#search-form").serializeArray())
		);
	}
};

$nb.json.onReturn = function($element, result) {

	if($element.attr("id") == "activity-results") {

		$("#search-form button[type=submit]")
			.append("<i class='icon-search-1'></i>")
			.find(".uk-spinner").remove();

		theme.search.resultFilters();

		if(!theme.search.isDefault()) {
			$("html, body").animate({
				scrollTop: $element.offset().top - ($nb.defaults.offset * 2.56)
			}, ($nb.defaults.speed * 2));
			$("[data-filter-clear]").removeClass("uk-hidden");
		} else {
			$("[data-filter-clear]").addClass("uk-hidden");
		}
	}

	if($("html").hasClass("template-attractions") || $("html").hasClass("template-home")) {
		$nb.mailto();
		$nb.tel();
	}
};
