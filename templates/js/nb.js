/**
 * NB Communication JS
 *
 * Site-specific extensions of $nb.
 *
 * @copyright 2019 NB Communication Ltd
 *
 */

$nb.defaults.ukNotification.pos = "bottom-center";

// Process [data-nb-block] elements based on type
$.extend($nb.blocks, {

	content: function($block) {

		// Apply UIkit table classes
		$block.find("table")
			.addClass("uk-table")
			.wrap("<div class='uk-overflow-auto'></div>");

		// Inline Images UIkit Lightbox/Scrollspy
		$.each($block.find("a").filter(function() {
			return $(this).attr("href") ? $(this).attr("href").match(/\.(jpg|jpeg|png|gif)/i) : null;
		}), function() {

			var $a = $(this),
				$figure = $a.parent();

			// uk-lightbox
			UIkit.lightbox($figure, {animation: "fade"});
			$a.attr("data-caption", $figure.find("figcaption").html());

			// uk-scrollspy
			if($figure.hasClass("align_left")) UIkit.scrollspy($figure, {cls: "uk-animation-slide-left-small"});
			if($figure.hasClass("align_right")) UIkit.scrollspy($figure, {cls: "uk-animation-slide-right-small"});
			if($figure.hasClass("align_center")) UIkit.scrollspy($figure, {cls: "uk-animation-slide-bottom-small"});
		});

		// Font Awesome File Icons
		var fileTypes = {
			"pdf": ["pdf"],
			"word": ["doc", "docx"],
			"excel": ["xls", "xlsx"],
			"powerpoint": ["ppt", "pptx"],
			"archive": ["zip", "tar"]
		};

		for(var icon in fileTypes) {
			for(var i = 0; i < fileTypes[icon].length; i++) {
				var $links = $block.find("a[href$='." + fileTypes[icon][i] + "']");
				if($links.length) {
					$links.each(function() {
						var $link = $(this);
						if(!$link.hasClass("nb-file-icon") && !$link.children().length) {
							$link.prepend($nb.faIcon("file-" + icon, "far")).addClass("nb-file-icon nb-file-icon-" + icon);
						}
					});
				}
			}
		}

		// Font Awesome external link icon
		var $links = $block.find("a[target='_blank']");
		if($links.length) {
			$links.each(function() {
				var $link = $(this);
				if(!$link.hasClass("nb-ext-icon") && !$link.children().length) {
					$link.append($nb.faIcon("external-link-alt", "fas")).addClass("nb-ext-icon");
				}
			})
		}
	},

	embed: function($block) {
		$block.find("iframe").attr("data-uk-responsive", true);
		UIkit.update();
	}
});

$nb.json.more = function(label) {
	return $nb.wrap(
		$nb.wrap((label ? label : $nb.ukIcon("more")), {
			type: "button",
			class: ["uk-button", "uk-button-default", "button-ghost", "uk-button-large"]
		}, "button"),
		"nb-json-more uk-text-center"
	);
};

// Render functions for [data-nb-json]
$.extend($nb.json.render, {

	items: function(items, config) {

		var out = "",
			isActivity = items[0].template == "activity",
			isAttraction = items[0].template == "attraction";

		$.each(items, function(i) {

			var tags = [],
				tagsr = [],
				meta = [],
				info = [],
				iconAnchor = "<i class='icon-anchor-solid'></i>",
				url = this.url,
				target = false,
				wl = "a",
				mj = "";

			if($nb.x(url)) {
				if($nb.x(this.link)) {
					url = false;
					wl = "span"
				} else {
					url = this.link;
					target = "_blank";
				}
			}

			if(this.trip_type) {
				tags.push($nb.wrap(this.trip_type, "uk-card-badge uk-background-primary"));
			}

			if($nb.isArray(this.port) && this.port.length) {
				tags.push($nb.wrap(iconAnchor + this.port.join(", "), "uk-card-badge"));
			}

			if($nb.isArray(this.type_transport)) {
				for(var i = 0; i < this.type_transport.length; i++) {
					tagsr.push($nb.wrap("<i class='icon-" + this.type_transport[i] + "'></i>", "<span class='travel-mode'>"))
				}
			}

			if(this.duration) {
				var h = Math.floor(this.duration / 60),
					m = this.duration - (h * 60);
				meta.push($nb.wrap($nb.faIcon("clock") + h + "h" + (m ? " " + m + "min" : ""), "<span class='date-time'>"));
			}

			if(this.info_prices) {
				meta.push($nb.wrap($nb.faIcon("credit-card") + this.info_prices.replace("per person", "pp"), "<span class='price-range'>"));
			}

			if(this.template == "attraction") {
				mj = "<br>";
				if(this.tel) meta.push($nb.faIcon("phone") + " " + this.tel);
				if(this.email) meta.push($nb.faIcon("envelope") + " " + this.email);
			}

			if(this.info_departure) {
				info.push(iconAnchor + this.info_departure);
			}

			if(this.info_season) {
				info.push($nb.faIcon("calendar-alt") + this.info_season);
			}

			if(this.info_days) {

				var days = this.info_days;

				if(!$nb.isArray(days) && days.includes("/")) {
					var d = days.split("/"),
						days = [];
					for(var i = 0; i < d.length; i++) {
						days.push(d[i].trim().substr(0, 3));
					}
				}

				if($nb.isArray(days)) days = days.join(" / ");

				info.push($nb.faIcon("calendar-check") + days);
			}

			var gaAction = (target ? "Ext" : "Int") + "ernal Link: Click";

			out += $nb.wrap(
				$nb.wrap(
					// Image
					(this.getImage ? $nb.wrap(
						(tags.length ? $nb.wrap(
							tags.join(""),
							"uk-overlay uk-position-top-left"
						) : "") +
						(tagsr.length ? $nb.wrap(
							tagsr.join(""),
							"uk-overlay uk-position-top-right"
						) : "") +
						(url ? $nb.attr(theme.ga.eventAttr(gaAction, this.title, url, {href: url, class: "read-more", target: target}), wl, true) : ""),
						$nb.imgBg(this.getImage, {class: ["uk-background-cover", "uk-card-media-" + (isActivity || isAttraction ? "top" : "left")]})
					) : "") +

					$nb.wrap(
						$nb.wrap(
							$nb.wrap(this.title, theme.ga.eventAttr(gaAction, this.title, url, {
								href: url,
								class: "uk-link-reset",
								target: target,
								id: "title" + this.id
							}), wl),
							{class: "uk-card-title"},
							"h3"
						) +
						($nb.isArray(this.provider) ? theme.wrapMeta("Provided by '" + $nb.wrap(this.provider.join("</em>', '<em>"), "em") + "'") : "") +
						(this.address ? theme.wrapMeta(this.address) : "") +
						(meta.length ? $nb.wrap(meta.join(mj), "<div class='entry-meta'>") : "") +
						$nb.wrap(
							(info.length ? $nb.wrap($nb.wrap(info, "li"), "ul") : "") +
							$nb.wrap((this.getSummary ? this.getSummary : ""), "uk-card-summary"),
							(isActivity || isAttraction ? "<div class='on-hover'>" : "")
						) +
						$nb.wrap(
							(isActivity ? theme.shortlist.button(this.id, this.title) : "") + 
							(url ? $nb.wrap(
								$nb.faIcon("arrow-circle-right") + ($nb.x(config.more) ? $nb.ukIcon("more") : config.more),
								theme.ga.eventAttr(gaAction, this.title, url, {
									href: url,
									target: target,
									class: [
										"uk-button",
										"uk-button-small",
										"uk-button-default",
										"button-ghost",
										"push-icon-right" + (isActivity ? "" : " uk-button-small"),
									]
								}),
								wl
							) : ""),
							(isActivity || isAttraction ? "uk-flex uk-flex-between uk-margin-top" : "") // uk-background-muted
						),
						"uk-card-body"
					),
					"uk-card uk-card-default v" + (isActivity || isAttraction ? 2 : 1)
				),
				"div"
			);
		});

		return $nb.wrap(out, {
			"class": ($nb.x(config.grid) ? [
				"card-grid",
				"uk-child-width-1-3@l",
				"uk-child-width-1-2@s",
				"uk-grid-xsmall",
				"uk-grid-match",
			] : config.grid),
			"data-uk-grid": true,
			"data-uk-scrollspy": {
				target: "> div",
				cls: "uk-animation-slide-bottom-small",
				delay: $nb.defaults.speed,
			}
		}, "div");
	},

	default: function(items, config) {
		return this.items(items, config);
	},

	posts: function(items, config) {
		return this.items(items, config);
	},

	search: function(items, config) {
		return this.items(items, config);
	}
});
