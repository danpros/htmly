<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>" class="__variable_space scroll-smooth light" id="html-id">
<head>
	<?php echo head_contents();?>
	<?php echo $metatags;?>
	<link rel="stylesheet" href="<?php echo theme_path();?>css/typography.css" data-precedence="next" />
	<link rel="stylesheet" href="<?php echo theme_path();?>css/tailwind.css" data-precedence="next" />
	<link rel="stylesheet" href="<?php echo theme_path();?>css/style.css" data-precedence="next" />
</head>
<body class="bg-white pl-[calc(100vw-100%)] text-black antialiased dark:bg-gray-950 dark:text-white">
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>
<script>
	var html = document.getElementById("html-id");
    if (localStorage.getItem("tw-theme") === "dark") {
		html.classList.remove('light');
		html.classList.add('dark');
    } else if (localStorage.getItem("tw-theme") === "light") {
        html.classList.remove('dark');
		html.classList.add('light');
    }
</script>
	<section class="mx-auto max-w-3xl px-4 sm:px-6 xl:max-w-5xl xl:px-0">
		<div class="flex h-screen flex-col justify-between font-sans">
			<header class="flex items-center justify-between py-10">
				<div>
					<a aria-label="<?php echo blog_title();?>" href="<?php  echo site_url();?>">
						<div class="flex items-center justify-between">
							<div class="mr-3">
								<img width="45" src="<?php echo theme_path();?>logo.png" alt="<?php echo blog_title();?>"/>
							</div>
							<div class="hidden h-6 text-2xl font-semibold sm:block"><?php echo blog_title();?></div>
						</div>
					</a>
				</div>
				<div class="flex items-center space-x-4 leading-5 sm:space-x-6">
					<?php echo menu('nav-top');?>
					<button aria-label="Search" class="search-open">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-gray-900 dark:text-gray-100">
							<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
						</svg>
					</button>
					<div class="mr-5">
						<div class="relative inline-block text-left">
							<div>
								<button id="theme-toggle" type="button" aria-label="Theme Mode" aria-haspopup="menu" aria-expanded="false">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 text-gray-900 dark:text-gray-100">
										<path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
									</svg>
								</button>
							</div>
						</div>
					</div>
					<button aria-label="Toggle Menu" class="sm:hidden menu-open" id="menu-toggle">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-8 w-8 text-gray-900 dark:text-gray-100">
							<path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
						</svg>
					</button>
					<div class="menu-mobile" role="dialog" tabindex="-1">
						<div class="fixed inset-0 z-60 bg-black/25"></div>
						<div class="fixed left-0 top-0 z-70 h-full w-full bg-white opacity-95 duration-300 dark:bg-gray-950 dark:opacity-[0.98]">
							<nav class="mt-8 flex h-full basis-0 flex-col items-start overflow-y-auto pl-12 pt-2 text-left">
							<?php echo menu('nav-mobile');?>
							</nav>
							<button class="fixed right-4 top-7 z-80 h-16 w-16 p-4 text-gray-900 hover:text-primary-500 dark:text-gray-100 dark:hover:text-primary-400 menu-close" aria-label="Toggle Menu">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
									<path
										fill-rule="evenodd"
										d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
										clip-rule="evenodd"
									></path>
								</svg>
							</button>
						</div>
					</div>
				</div>
			</header>
			<main class="mb-auto">
			<?php if (is_index() || isset($is_profile)) {?>
				<div class="divide-y divide-gray-200 dark:divide-gray-700">
				<?php echo content();?>
				</div>
			<?php } else {?>
			<?php echo content();?>
			<?php } ?>
			</main>
			<footer>
				<div class="mt-16 flex flex-col items-center">
					<div class="mb-3 flex space-x-4">
						<?php echo social();?>
					</div>
					<div class="mb-2 flex space-x-2 text-sm text-gray-500 dark:text-gray-400">
						<div><?php echo copyright();?></div>
					</div>
					<div class="mb-8 text-sm text-gray-500 dark:text-gray-400">Design by <a style="te" target="_blank" rel="nofollow" href="https://www.timlrx.com/">Timlrx</a></div>
				</div>
			</footer>
		</div>
	</section>
	<div style="position: fixed; align-items: flex-start; justify-content: center; width: 100%; inset: 0px; padding: 14vh 16px 16px;" class="z-50 bg-gray-300/50 p-4 backdrop-blur backdrop-filter dark:bg-black/50 search-form">
		<div style="opacity: 1; transform: scale(0.99); pointer-events: auto;" class="w-full max-w-xl">
			<div>
				<div class="overflow-hidden rounded-2xl border border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
					<div class="flex items-center space-x-4 p-4">
						<span class="block w-5">
							<svg class="text-gray-400 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
						</span>
						<form role="search" class="w-full"><input name="search" class="h-8 w-full bg-transparent text-gray-600 placeholder-gray-400 focus:outline-none dark:text-gray-200 dark:placeholder-gray-500" autocomplete="off" role="combobox" spellcheck="false" aria-expanded="true" aria-controls="kbar-listbox" aria-activedescendant="kbar-listbox-item-1" placeholder="<?php echo i18n('type_to_search');?>" value=""></form>
						<button class="z-80 h-16 w-16 p-4 text-gray-900 hover:text-primary-500 dark:text-gray-100 dark:hover:text-primary-400 search-close" aria-label="Toggle Menu">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
								<path
									fill-rule="evenodd"
									d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
									clip-rule="evenodd"
								></path>
							</svg>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="fixed bottom-8 right-8 hidden flex-col gap-3" id="scroll-up">
        <button aria-label="Scroll To Top" class="rounded-full bg-gray-200 p-2 text-gray-500 transition-all hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
            <a href="#"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg></a>
        </button>
    </div>
	<script>
		var mybutton = document.getElementById("scroll-up");
		window.onscroll = function () {
			if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
				mybutton.style.display = "flex";
				mybutton.style.opacity = "1";
			} else {
				mybutton.style.display = "none";
				mybutton.style.opacity = "0";
			}
		};
	</script>
	<script>
		var html = document.getElementById("html-id");
		document.getElementById("theme-toggle").addEventListener("click", () => {
			if (html.className.includes("dark")) {
				html.classList.remove('dark');
				html.classList.add('light');
				localStorage.setItem("tw-theme", 'light');
			} else {
				html.classList.remove('light');
				html.classList.add('dark');
				localStorage.setItem("tw-theme", 'dark');
			}
		})

	</script>
	<script src="<?php echo theme_path();?>js/functions.js"></script>
	<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>
