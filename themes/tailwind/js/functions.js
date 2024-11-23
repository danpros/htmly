(function () {
	var e = document.querySelector(".menu-open");
	e &&
		e.addEventListener("click", function () {
			document.body.classList.contains("is-menu-open") ? document.body.classList.remove("is-menu-open") : document.body.classList.add("is-menu-open");
		});
		
	var c = document.querySelector(".menu-close");
	c &&
		c.addEventListener("click", function () {
			document.body.classList.contains("is-menu-open") ? document.body.classList.remove("is-menu-open") : document.body.classList.add("is-menu-open");
		});

	var s = document.querySelector(".search-open");
	s &&
		s.addEventListener("click", function () {
			document.body.classList.contains("is-search-open") ? document.body.classList.remove("is-search-open") : document.body.classList.add("is-search-open");
		});	
		
	var sc = document.querySelector(".search-close");
	sc &&
		sc.addEventListener("click", function () {
			document.body.classList.contains("is-search-open") ? document.body.classList.remove("is-search-open") : document.body.classList.add("is-search-open");
		});	
		
})();