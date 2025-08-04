(function($) {

	// On click anywhere ('cept filters), hide filters
	$('body').click(function () { $(".list-group").animate({'max-height': 0}, 350); });
	$(".searchbox").click(function (e) { e.stopImmediatePropagation(); });


	function resizeAlba() {
		var albawidth = $('album-content').width();
		$('.coverimg').each(function() {
			$(this).css('height',albawidth+'px');
		});
		$('img-container').each(function() {
			$(this).css('height',albawidth+'px');
		});

		var searchWidth = $('.searchbox .form-group').innerWidth();
		$('.list-group').each(function(){
			$(this).css('width',searchWidth+'px');
		});
	}

	function menuLists(list,filter) {
		// Engage filter
		$(list).btsListFilter(filter, {itemChild: 'span'});

		// Animate open
		$(filter).focus(function() { 
			console.log('focus');
			$(list).animate({'max-height': 300}, 350); 
		});
		
		// Animate close
		$(filter).focusout(function() {
			console.log('focusout');
			if (! $(list).focus) {
				$(list).animate({'max-height': 0}, 350);
			}
		});
	}

	$(document).ready(function() {

		$("img").unveil();
		resizeAlba();
		menuLists('#list-artists', '#filter-artists');
		menuLists('#list-genres', '#filter-genres');
		menuLists('#list-labels', '#filter-labels');
		menuLists('#list-jaren', '#filter-jaren');

	});

	$(window).on('scroll resize', function(){
		resizeAlba();
	});

	// http://stackoverflow.com/questions/15090942/jquery-event-handler-not-working-on-dynamic-content
	$(document.body).on('mouseover mouseout', 'album' ,function(){

		var iframe = $(this).find('iframe');
		if (iframe.length > 0){
			var framebase = $(this).find('frame-base').text();
			var framesrc = $(this).find('frame-src').text();
			if (! iframe.attr('src').length ) {
				iframe.attr('src', framebase+framesrc);
			}
		}
	});

})(jQuery);

// When Ajax content is loaded
function ajaxLoad() {
	closeTags();
	setTagSizes();
	openTab();
	openMobileMenu();
	openIntro();
}

// Hide all tags based on height
function setTagSizes() {
	var tagContent = document.querySelectorAll("tag");
	tagContent.forEach(function(tagContent) {
		var tagContentHeight = tagContent.scrollHeight;
		tagContent.style.bottom="-"+tagContentHeight+"px";
	});
}

// Close all open tags
function closeTags() {
	var tag = document.querySelectorAll("tag");
	var tagBtn = document.querySelectorAll(".tagBtn");

	tag.forEach(function(tag) {
		if (tag.classList.contains('open')) {
			tag.classList.remove('open');
		}
	});

	tagBtn.forEach(function(tagBtn) {
		if (tagBtn.classList.contains('active')) {
			tagBtn.classList.remove('active');
		}
	});
}

// On click tagBtn, open corresponding tag
function openTab() {
	var tagBtn = document.querySelectorAll(".tagBtn");
	tagBtn.forEach(function(tagBtn) {
		tagBtn.addEventListener('click', function() {
			var tag = this.getAttribute('tag');
			var albumId = this.getAttribute('albumId');
			var currentTab = document.getElementById('tag-'+albumId+'-'+tag);

			if (currentTab.classList.contains('open')) { closeTags(); } else {
				closeTags(); currentTab.classList.add('open');
			}
			if(!tagBtn.classList.contains('active')) { tagBtn.classList.add('active');} 
		});
	});
}


// On mobileMenu ffs
function openMobileMenu() {
	var menuBtn = document.getElementById('mobileOpener');
	var menuContent = document.getElementById('menubar');
	menuBtn.addEventListener('click', function() {
		if (menuContent.classList.contains('open')) {
			menuContent.classList.remove('open');
		} else {
			menuContent.classList.add('open');
		}
	});
}

// Open intro
function openIntro() {
	var intro = document.getElementById('intro');
	intro.addEventListener('click', function() {
		if (intro.classList.contains('open')) {
			intro.classList.remove('open');
		} else {
			intro.classList.add('open');
		}	
	});
}

// JS On Load
document.addEventListener('DOMContentLoaded', function() {
	ajaxLoad();
}, false);